<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        
        // Month filter (format YYYY-MM)
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        // Date range filter for detail list
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // If single date filter provided via 'tanggal' query
        if ($request->filled('tanggal')) {
            $startDate = $request->input('tanggal');
            $endDate = $request->input('tanggal');
        }

        // Active user scoping: Teknisi sees only their own data; Admin sees all or filtered technician
        $targetUserId = null;
        if ($user->isTeknisi()) {
            $targetUserId = $user->id;
        } elseif ($user->isAdmin() && $request->filled('teknisi_id') && $request->input('teknisi_id') !== 'all') {
            $targetUserId = $request->input('teknisi_id');
        }

        $baseQuery = function () use ($targetUserId) {
            $q = JobOrder::query();
            if ($targetUserId) {
                $q->where('user_id', $targetUserId);
            }
            return $q;
        };

        // Today metrics
        $pendapatanHariIni = $baseQuery()->whereDate('tanggal', $today)->sum('tarif');
        $totalJobHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'like', 'Piket%')
            ->count();

        // Selected Month metrics
        $pendapatanBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('tarif');
        $totalJobBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->count();
        $pendapatanPiketBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->sum('tarif');

        // Daily recap for selected month
        $rekapHarian = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->selectRaw("tanggal, COUNT(CASE WHEN kategori NOT LIKE 'Piket%' THEN 1 END) as total_job, COUNT(CASE WHEN kategori LIKE 'Piket%' THEN 1 END) as total_piket, SUM(tarif) as total_pendapatan")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Detail Job Orders query
        $detailQuery = $baseQuery();

        if ($startDate && $endDate) {
            $detailQuery->whereBetween('tanggal', [$startDate, $endDate]);
        } else {
            $detailQuery->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        $detailJobOrders = $detailQuery->with('user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Tarifs for quick job order input
        $tarifs = Tarif::orderBy('kategori', 'asc')->get();

        // All technicians for Admin filter dropdown
        $allTeknisi = $user->isAdmin() ? \App\Models\User::where('role', 'teknisi')->orderBy('name', 'asc')->get() : collect();

        // Admin specific insights & analytics
        $totalTeknisiCount = 0;
        $totalSuccessJobs = 0;
        $totalFailedJobs = 0;
        $successRate = 100;
        $teknisiLeaderboard = collect();
        $kategoriBreakdown = collect();

        if ($user->isAdmin()) {
            $totalTeknisiCount = \App\Models\User::where('role', 'teknisi')->count();

            $totalSuccessJobs = JobOrder::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status', 'berhasil')
                ->count();

            $totalFailedJobs = JobOrder::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status', 'gagal')
                ->count();

            $totalAllJobs = $totalSuccessJobs + $totalFailedJobs;
            $successRate = ($totalAllJobs > 0) ? round(($totalSuccessJobs / $totalAllJobs) * 100, 1) : 100;

            // Teknisi Leaderboard for selected month
            $teknisiLeaderboard = \App\Models\User::where('role', 'teknisi')
                ->withCount(['jobOrders as total_job' => function ($query) use ($year, $month) {
                    $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
                }])
                ->withSum(['jobOrders as total_pendapatan' => function ($query) use ($year, $month) {
                    $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
                }], 'tarif')
                ->orderByDesc('total_pendapatan')
                ->limit(5)
                ->get();

            // Category breakdown for selected month
            $kategoriBreakdown = JobOrder::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->selectRaw('kategori, COUNT(*) as count, SUM(tarif) as total_tarif')
                ->groupBy('kategori')
                ->orderByDesc('count')
                ->get();
        }

        return view('dashboard', compact(
            'today',
            'selectedBulan',
            'year',
            'month',
            'startDate',
            'endDate',
            'pendapatanHariIni',
            'totalJobHariIni',
            'totalPiketHariIni',
            'pendapatanBulanIni',
            'totalJobBulanIni',
            'totalPiketBulanIni',
            'pendapatanPiketBulanIni',
            'rekapHarian',
            'detailJobOrders',
            'tarifs',
            'allTeknisi',
            'targetUserId',
            'totalTeknisiCount',
            'totalSuccessJobs',
            'totalFailedJobs',
            'successRate',
            'teknisiLeaderboard',
            'kategoriBreakdown'
        ));
    }

    public function apiStats(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        $targetUserId = null;
        if ($user->isTeknisi()) {
            $targetUserId = $user->id;
        } elseif ($user->isAdmin() && $request->filled('teknisi_id') && $request->input('teknisi_id') !== 'all') {
            $targetUserId = $request->input('teknisi_id');
        }

        $baseQuery = function () use ($targetUserId) {
            $q = JobOrder::query();
            if ($targetUserId) {
                $q->where('user_id', $targetUserId);
            }
            return $q;
        };

        $pendapatanHariIni = $baseQuery()->whereDate('tanggal', $today)->sum('tarif');
        $totalJobHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'like', 'Piket%')
            ->count();

        $pendapatanBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('tarif');
        $totalJobBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->count();
        $pendapatanPiketBulanIni = $baseQuery()->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->sum('tarif');

        return response()->json([
            'success' => true,
            'pendapatan_hari_ini' => 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'),
            'total_job_hari_ini' => $totalJobHariIni . ' JO',
            'total_piket_hari_ini' => $totalPiketHariIni . ' Kali',
            'pendapatan_bulan_ini' => 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'),
            'total_job_bulan_ini' => $totalJobBulanIni . ' JO',
            'total_piket_bulan_ini' => $totalPiketBulanIni . ' Kali',
            'pendapatan_piket_bulan_ini' => '(Rp ' . number_format($pendapatanPiketBulanIni, 0, ',', '.') . ')',
        ]);
    }
}

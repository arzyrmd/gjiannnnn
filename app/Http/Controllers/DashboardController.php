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

        // Check if custom date range filter is active
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // If single date filter provided via 'tanggal' query
        if ($request->filled('tanggal')) {
            $startDate = $request->input('tanggal');
            $endDate = $request->input('tanggal');
        }

        $hasCustomRange = !empty($startDate) && !empty($endDate);

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

        // Today metrics (Always for today)
        $pendapatanHariIni = $baseQuery()->whereDate('tanggal', $today)->sum('tarif');
        $totalJobHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketHariIni = $baseQuery()->whereDate('tanggal', $today)
            ->where('kategori', 'like', 'Piket%')
            ->count();

        // Helper query builder for active period (custom range OR selected month)
        $periodQuery = function () use ($baseQuery, $hasCustomRange, $startDate, $endDate, $year, $month) {
            $q = $baseQuery();
            if ($hasCustomRange) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            } else {
                $q->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            }
            return $q;
        };

        // Period Labels
        if ($hasCustomRange) {
            if ($startDate === $endDate) {
                $periodLabel = Carbon::parse($startDate)->translatedFormat('d M Y');
            } else {
                $periodLabel = Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
            }
            $periodTitleCard = "Akumulasi Periode";
        } else {
            $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
            $periodTitleCard = "Akumulasi Bulan Ini";
        }

        // Active Period metrics
        $pendapatanBulanIni = $periodQuery()->sum('tarif');
        $totalJobBulanIni = $periodQuery()->where('kategori', 'not like', 'Piket%')->count();
        $totalPiketBulanIni = $periodQuery()->where('kategori', 'like', 'Piket%')->count();
        $pendapatanPiketBulanIni = $periodQuery()->where('kategori', 'like', 'Piket%')->sum('tarif');

        // Daily recap for active period
        $rekapHarian = $periodQuery()
            ->selectRaw("tanggal, COUNT(CASE WHEN kategori NOT LIKE 'Piket%' THEN 1 END) as total_job, COUNT(CASE WHEN kategori LIKE 'Piket%' THEN 1 END) as total_piket, SUM(tarif) as total_pendapatan")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Detail Job Orders list
        $detailJobOrders = $periodQuery()
            ->with('user')
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

            $adminPeriodQuery = function () use ($hasCustomRange, $startDate, $endDate, $year, $month) {
                $q = JobOrder::query();
                if ($hasCustomRange) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                } else {
                    $q->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
                }
                return $q;
            };

            $totalSuccessJobs = $adminPeriodQuery()->where('status', 'berhasil')->count();
            $totalFailedJobs = $adminPeriodQuery()->where('status', 'gagal')->count();

            $totalAllJobs = $totalSuccessJobs + $totalFailedJobs;
            $successRate = ($totalAllJobs > 0) ? round(($totalSuccessJobs / $totalAllJobs) * 100, 1) : 100;

            // Teknisi Leaderboard for active period
            $teknisiLeaderboard = \App\Models\User::where('role', 'teknisi')
                ->withCount(['jobOrders as total_job' => function ($query) use ($hasCustomRange, $startDate, $endDate, $year, $month) {
                    if ($hasCustomRange) {
                        $query->whereBetween('tanggal', [$startDate, $endDate]);
                    } else {
                        $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
                    }
                }])
                ->withSum(['jobOrders as total_pendapatan' => function ($query) use ($hasCustomRange, $startDate, $endDate, $year, $month) {
                    if ($hasCustomRange) {
                        $query->whereBetween('tanggal', [$startDate, $endDate]);
                    } else {
                        $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
                    }
                }], 'tarif')
                ->orderByDesc('total_pendapatan')
                ->limit(5)
                ->get();

            // Category breakdown for active period
            $kategoriBreakdown = $adminPeriodQuery()
                ->selectRaw('kategori, COUNT(*) as count, SUM(tarif) as total_tarif')
                ->groupBy('kategori')
                ->orderByDesc('count')
                ->get();
        }

        // Target Pendapatan Calculations
        $activeUser = $targetUserId ? \App\Models\User::find($targetUserId) : $user;
        $targetPendapatan = $activeUser ? (float) ($activeUser->target_pendapatan ?? 5000000) : 5000000;
        $tercapaiPendapatan = (float) $pendapatanBulanIni;
        $sisaTarget = max(0, $targetPendapatan - $tercapaiPendapatan);
        $persenTarget = ($targetPendapatan > 0) ? min(100, round(($tercapaiPendapatan / $targetPendapatan) * 100, 1)) : 0;

        $now = Carbon::now();
        if ($selectedBulan === $now->format('Y-m') && !$hasCustomRange) {
            $daysInMonth = $now->daysInMonth;
            $sisaHari = max(1, $daysInMonth - $now->day + 1);
        } elseif ($hasCustomRange) {
            $startC = Carbon::parse($startDate);
            $endC = Carbon::parse($endDate);
            $sisaHari = max(1, $startC->diffInDays($endC) + 1);
        } else {
            $sisaHari = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        }

        $rataRataHarianDibutuhkan = ($sisaTarget > 0) ? ceil($sisaTarget / $sisaHari) : 0;

        // Daily Trend Chart Data
        $chartLabels = $rekapHarian->map(fn($row) => Carbon::parse($row->tanggal)->format('d M'))->values()->toArray();
        $chartIncomeData = $rekapHarian->map(fn($row) => (float) $row->total_pendapatan)->values()->toArray();
        $chartJobData = $rekapHarian->map(fn($row) => (int) $row->total_job)->values()->toArray();

        return view('dashboard', compact(
            'today',
            'selectedBulan',
            'year',
            'month',
            'startDate',
            'endDate',
            'hasCustomRange',
            'periodLabel',
            'periodTitleCard',
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
            'kategoriBreakdown',
            'targetPendapatan',
            'tercapaiPendapatan',
            'sisaTarget',
            'persenTarget',
            'sisaHari',
            'rataRataHarianDibutuhkan',
            'chartLabels',
            'chartIncomeData',
            'chartJobData'
        ));
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'target_pendapatan' => 'required|numeric|min:100000|max:100000000',
        ]);

        $user = auth()->user();
        
        if ($user->isAdmin() && $request->filled('teknisi_id')) {
            $targetUser = \App\Models\User::find($request->input('teknisi_id'));
            if ($targetUser) {
                $targetUser->update(['target_pendapatan' => $request->input('target_pendapatan')]);
                if ($request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Target pendapatan teknisi berhasil diperbarui!']);
                }
                return redirect()->back()->with('success', 'Target pendapatan teknisi berhasil diperbarui!');
            }
        }

        $user->update(['target_pendapatan' => $request->input('target_pendapatan')]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Target pendapatan berhasil diperbarui!']);
        }

        return redirect()->back()->with('success', 'Target pendapatan berhasil diperbarui!');
    }

    public function apiStats(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hasCustomRange = !empty($startDate) && !empty($endDate);

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

        $periodQuery = function () use ($baseQuery, $hasCustomRange, $startDate, $endDate, $year, $month) {
            $q = $baseQuery();
            if ($hasCustomRange) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            } else {
                $q->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
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

        $pendapatanBulanIni = $periodQuery()->sum('tarif');
        $totalJobBulanIni = $periodQuery()->where('kategori', 'not like', 'Piket%')->count();
        $totalPiketBulanIni = $periodQuery()->where('kategori', 'like', 'Piket%')->count();
        $pendapatanPiketBulanIni = $periodQuery()->where('kategori', 'like', 'Piket%')->sum('tarif');

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

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

        // Target Pendapatan & Productivity Calculations
        $activeUser = $targetUserId ? \App\Models\User::find($targetUserId) : $user;
        $targetPendapatan = $activeUser ? (float) ($activeUser->target_pendapatan ?? 5000000) : 5000000;
        $tercapaiPendapatan = (float) $pendapatanBulanIni;
        $sisaTarget = max(0, $targetPendapatan - $tercapaiPendapatan);
        $persenTarget = ($targetPendapatan > 0) ? min(100, round(($tercapaiPendapatan / $targetPendapatan) * 100, 1)) : 0;

        // Pure JO Income (excluding Piket)
        $pendapatanJoHariIni = $baseQuery()->whereDate('tanggal', $today)->where('kategori', 'not like', 'Piket%')->sum('tarif');
        $pendapatanJoBulanIni = $periodQuery()->where('kategori', 'not like', 'Piket%')->sum('tarif');

        $todayC = Carbon::today();

        if ($hasCustomRange) {
            $startC = Carbon::parse($startDate)->startOfDay();
            $endC = Carbon::parse($endDate)->startOfDay();

            if ($todayC->gt($endC)) {
                $sisaHari = 0;
                $passedDays = max(1, (int) $startC->diffInDays($endC) + 1);
            } elseif ($todayC->lt($startC)) {
                $sisaHari = (int) $startC->diffInDays($endC) + 1;
                $passedDays = 1;
            } else {
                $sisaHari = (int) $todayC->diffInDays($endC) + 1;
                $passedDays = max(1, (int) $startC->diffInDays($todayC) + 1);
            }
        } else {
            $periodStart = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfDay();
            $periodEnd = $periodStart->copy()->endOfMonth()->startOfDay();

            if ($todayC->gt($periodEnd)) {
                $sisaHari = 0;
                $passedDays = $periodStart->daysInMonth;
            } elseif ($todayC->lt($periodStart)) {
                $sisaHari = $periodStart->daysInMonth;
                $passedDays = 1;
            } else {
                $sisaHari = (int) $todayC->diffInDays($periodEnd) + 1;
                $passedDays = max(1, (int) $todayC->day);
            }
        }

        $rataRataHarianDibutuhkan = ($sisaTarget > 0 && $sisaHari > 0) ? (int) ceil($sisaTarget / $sisaHari) : 0;

        // Productivity JO Metrics (Target 330 JO per Bulan)
        $targetJoVolume = 330;
        $tercapaiJoVolume = (int) $totalJobBulanIni;
        $sisaJoVolume = max(0, $targetJoVolume - $tercapaiJoVolume);
        $persenJoVolume = ($targetJoVolume > 0) ? min(100, round(($tercapaiJoVolume / $targetJoVolume) * 100, 1)) : 0;

        $rataRataJoPerHari = round($tercapaiJoVolume / $passedDays, 1);
        $rataRataPendapatanJoPerHari = (int) round($pendapatanJoBulanIni / $passedDays);

        // Kebutuhan Target JO per Hari berdasarkan Target 330 JO
        $targetJoQtyPerHari = ($sisaJoVolume > 0 && $sisaHari > 0) ? (int) ceil($sisaJoVolume / $sisaHari) : 0;
        $avgTarifPerJo = $tercapaiJoVolume > 0 ? ($pendapatanJoBulanIni / $tercapaiJoVolume) : 0;
        $targetJoRevenuePerHari = (int) ceil($targetJoQtyPerHari * $avgTarifPerJo);

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
            'pendapatanJoHariIni',
            'pendapatanJoBulanIni',
            'passedDays',
            'targetJoVolume',
            'tercapaiJoVolume',
            'sisaJoVolume',
            'persenJoVolume',
            'rataRataJoPerHari',
            'rataRataPendapatanJoPerHari',
            'targetJoQtyPerHari',
            'targetJoRevenuePerHari',
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

        // Target metrics
        $activeUser = $targetUserId ? \App\Models\User::find($targetUserId) : $user;
        $targetPendapatan = $activeUser ? (float) ($activeUser->target_pendapatan ?? 5000000) : 5000000;
        $tercapaiPendapatan = (float) $pendapatanBulanIni;
        $sisaTarget = max(0, $targetPendapatan - $tercapaiPendapatan);
        $persenTarget = ($targetPendapatan > 0) ? min(100, round(($tercapaiPendapatan / $targetPendapatan) * 100, 1)) : 0;

        // Pure JO Income (excluding Piket)
        $pendapatanJoHariIni = $baseQuery()->whereDate('tanggal', $today)->where('kategori', 'not like', 'Piket%')->sum('tarif');
        $pendapatanJoBulanIni = $periodQuery()->where('kategori', 'not like', 'Piket%')->sum('tarif');

        $todayC = Carbon::today();

        if ($hasCustomRange) {
            $startC = Carbon::parse($startDate)->startOfDay();
            $endC = Carbon::parse($endDate)->startOfDay();

            if ($todayC->gt($endC)) {
                $sisaHari = 0;
                $passedDays = max(1, (int) $startC->diffInDays($endC) + 1);
            } elseif ($todayC->lt($startC)) {
                $sisaHari = (int) $startC->diffInDays($endC) + 1;
                $passedDays = 1;
            } else {
                $sisaHari = (int) $todayC->diffInDays($endC) + 1;
                $passedDays = max(1, (int) $startC->diffInDays($todayC) + 1);
            }
        } else {
            $periodStart = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfDay();
            $periodEnd = $periodStart->copy()->endOfMonth()->startOfDay();

            if ($todayC->gt($periodEnd)) {
                $sisaHari = 0;
                $passedDays = $periodStart->daysInMonth;
            } elseif ($todayC->lt($periodStart)) {
                $sisaHari = $periodStart->daysInMonth;
                $passedDays = 1;
            } else {
                $sisaHari = (int) $todayC->diffInDays($periodEnd) + 1;
                $passedDays = max(1, (int) $todayC->day);
            }
        }

        $rataRataHarianDibutuhkan = ($sisaTarget > 0 && $sisaHari > 0) ? (int) ceil($sisaTarget / $sisaHari) : 0;

        // Productivity JO Metrics (Target 330 JO per Bulan)
        $targetJoVolume = 330;
        $tercapaiJoVolume = (int) $totalJobBulanIni;
        $sisaJoVolume = max(0, $targetJoVolume - $tercapaiJoVolume);
        $persenJoVolume = ($targetJoVolume > 0) ? min(100, round(($tercapaiJoVolume / $targetJoVolume) * 100, 1)) : 0;

        $rataRataJoPerHari = round($tercapaiJoVolume / $passedDays, 1);
        $targetJoQtyPerHari = ($sisaJoVolume > 0 && $sisaHari > 0) ? (int) ceil($sisaJoVolume / $sisaHari) : 0;
        $avgTarifPerJo = $tercapaiJoVolume > 0 ? ($pendapatanJoBulanIni / $tercapaiJoVolume) : 0;
        $targetJoRevenuePerHari = (int) ceil($targetJoQtyPerHari * $avgTarifPerJo);

        return response()->json([
            'success' => true,
            'pendapatan_hari_ini' => 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'),
            'total_job_hari_ini' => $totalJobHariIni . ' JO',
            'total_piket_hari_ini' => $totalPiketHariIni . ' Kali',
            'pendapatan_bulan_ini' => 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'),
            'total_job_bulan_ini' => $totalJobBulanIni . ' JO',
            'total_piket_bulan_ini' => $totalPiketBulanIni . ' Kali',
            'pendapatan_piket_bulan_ini' => '(Rp ' . number_format($pendapatanPiketBulanIni, 0, ',', '.') . ')',
            'pendapatan_jo_bulan_ini' => 'Rp ' . number_format($pendapatanJoBulanIni, 0, ',', '.'),
            'target_pendapatan' => 'Rp ' . number_format($targetPendapatan, 0, ',', '.'),
            'tercapai_pendapatan' => 'Rp ' . number_format($tercapaiPendapatan, 0, ',', '.'),
            'sisa_target' => $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉',
            'persen_target' => $persenTarget . '%',
            'sisa_hari' => $sisaHari,
            'label_sisa_hari' => $sisaHari > 0 ? "Perlu/Hari ({$sisaHari} Hari Sisa)" : "Perlu/Hari (Selesai)",
            'rata_rata_harian_dibutuhkan' => ($sisaTarget > 0 && $sisaHari > 0) ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0',
            'target_jo_volume' => $targetJoVolume . ' JO',
            'tercapai_jo_volume' => $tercapaiJoVolume . ' JO',
            'sisa_jo_volume' => $sisaJoVolume > 0 ? $sisaJoVolume . ' JO' : 'Tercapai! 🎉',
            'persen_jo_volume' => $persenJoVolume . '%',
            'target_jo_qty_per_hari' => ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO / Hari' : '0 JO',
            'target_jo_revenue_per_hari' => ($sisaJoVolume > 0 && $sisaHari > 0) ? 'Rp ' . number_format($targetJoRevenuePerHari, 0, ',', '.') : 'Rp 0',
            'rata_rata_jo_per_hari' => $rataRataJoPerHari . ' JO/Hari',
        ]);
    }
}

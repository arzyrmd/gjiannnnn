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

        // Today metrics (Pendapatan total termasuk piket, Total Job menghitung JO murni)
        $pendapatanHariIni = JobOrder::whereDate('tanggal', $today)->sum('tarif');
        $totalJobHariIni = JobOrder::whereDate('tanggal', $today)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketHariIni = JobOrder::whereDate('tanggal', $today)
            ->where('kategori', 'like', 'Piket%')
            ->count();

        // Selected Month metrics
        $pendapatanBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('tarif');
        $totalJobBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->count();
        $pendapatanPiketBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->sum('tarif');

        // Daily recap for selected month (total_job menghitung JO murni, total_piket menghitung Piket)
        $rekapHarian = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->selectRaw("tanggal, COUNT(CASE WHEN kategori NOT LIKE 'Piket%' THEN 1 END) as total_job, COUNT(CASE WHEN kategori LIKE 'Piket%' THEN 1 END) as total_piket, SUM(tarif) as total_pendapatan")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Detail Job Orders query
        $detailQuery = JobOrder::query();

        if ($startDate && $endDate) {
            $detailQuery->whereBetween('tanggal', [$startDate, $endDate]);
        } else {
            // Default show detail for selected month
            $detailQuery->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        $detailJobOrders = $detailQuery->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Tarifs for quick job order input
        $tarifs = Tarif::orderBy('kategori', 'asc')->get();

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
            'tarifs'
        ));
    }

    public function apiStats(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        $pendapatanHariIni = JobOrder::whereDate('tanggal', $today)->sum('tarif');
        $totalJobHariIni = JobOrder::whereDate('tanggal', $today)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketHariIni = JobOrder::whereDate('tanggal', $today)
            ->where('kategori', 'like', 'Piket%')
            ->count();

        $pendapatanBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('tarif');
        $totalJobBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'not like', 'Piket%')
            ->count();
        $totalPiketBulanIni = JobOrder::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('kategori', 'like', 'Piket%')
            ->count();
        $pendapatanPiketBulanIni = JobOrder::whereYear('tanggal', $year)
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

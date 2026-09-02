<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class JobOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarif_id' => ['required', 'exists:tarifs,id'],
            'status' => ['required', 'in:berhasil,gagal'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'custom_tarif' => ['nullable', 'integer', 'min:0'],
        ]);

        $tarifModel = Tarif::findOrFail($validated['tarif_id']);

        // Snapshot rate logic
        $rate = ($validated['status'] === 'berhasil')
            ? $tarifModel->tarif_berhasil
            : ($tarifModel->tarif_gagal ?? 0);

        // Allow custom tariff override (e.g. Piket Event or manual rate)
        if ($request->filled('custom_tarif') && $request->custom_tarif !== '') {
            $rate = (int)$request->custom_tarif;
        }

        JobOrder::create([
            'tarif_id' => $tarifModel->id,
            'kategori' => $tarifModel->kategori, // Snapshot category name
            'status' => $validated['status'],
            'tarif' => $rate, // Snapshot tariff rate
            'tanggal' => $validated['tanggal'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Job order berhasil dicatat!',
            ]);
        }

        return redirect()->back()->with('success', 'Job order berhasil dicatat!');
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:berhasil,gagal'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'tarif' => ['nullable', 'integer', 'min:0'],
        ]);

        // If manual tariff rate is directly edited
        if ($request->filled('tarif')) {
            $jobOrder->tarif = (int)$request->tarif;
        }

        // If category changed, recalculate snapshot
        if ($request->filled('tarif_id') && $request->tarif_id != $jobOrder->tarif_id) {
            $tarifModel = Tarif::findOrFail($request->tarif_id);
            $rate = ($validated['status'] === 'berhasil')
                ? $tarifModel->tarif_berhasil
                : ($tarifModel->tarif_gagal ?? 0);
            
            $jobOrder->tarif_id = $tarifModel->id;
            $jobOrder->kategori = $tarifModel->kategori;
            if (!$request->filled('tarif')) {
                $jobOrder->tarif = $rate;
            }
        } else {
            // Recompute snapshot rate if status changed
            if ($validated['status'] !== $jobOrder->status) {
                if ($jobOrder->tarifRef) {
                    $jobOrder->tarif = ($validated['status'] === 'berhasil')
                        ? $jobOrder->tarifRef->tarif_berhasil
                        : ($jobOrder->tarifRef->tarif_gagal ?? 0);
                }
            }
        }

        $jobOrder->status = $validated['status'];
        $jobOrder->tanggal = $validated['tanggal'];
        $jobOrder->catatan = $validated['catatan'] ?? null;
        $jobOrder->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Job order berhasil diperbarui!',
            ]);
        }

        return redirect()->back()->with('success', 'Job order berhasil diperbarui!');
    }

    public function destroy(Request $request, JobOrder $jobOrder)
    {
        $jobOrder->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Job order berhasil dihapus!',
            ]);
        }

        return redirect()->back()->with('success', 'Job order berhasil dihapus!');
    }

    public function exportCsv(Request $request)
    {
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = JobOrder::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            $filename = "job_orders_{$startDate}_sd_{$endDate}.csv";
        } else {
            $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            $filename = "job_orders_{$selectedBulan}.csv";
        }

        $jobOrders = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($jobOrders) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header column
            fputcsv($file, ['No', 'Tanggal', 'Kategori Tugas', 'Status', 'Tarif Snapshot (Rp)', 'Catatan']);

            $no = 1;
            $totalPendapatan = 0;

            foreach ($jobOrders as $job) {
                fputcsv($file, [
                    $no++,
                    $job->tanggal->format('Y-m-d'),
                    $job->kategori,
                    ucfirst($job->status),
                    $job->tarif,
                    $job->catatan ?? '-',
                ]);
                $totalPendapatan += $job->tarif;
            }

            $totalJobCount = $jobOrders->filter(fn($j) => !str_starts_with(strtolower($j->kategori), 'piket'))->count();

            // Summary row
            fputcsv($file, []);
            fputcsv($file, ['TOTAL JOB ORDER (EXCL. PIKET)', $totalJobCount, '', 'TOTAL PENDAPATAN', $totalPendapatan, '']);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $selectedBulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedBulan);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = JobOrder::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            $titlePeriod = "Periode " . Carbon::parse($startDate)->format('d M Y') . " s/d " . Carbon::parse($endDate)->format('d M Y');
        } else {
            $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            $titlePeriod = "Bulan " . Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        }

        $jobOrders = $query->orderBy('tanggal', 'asc')->get();

        $rekapHarian = (clone $query)
            ->selectRaw("tanggal, COUNT(CASE WHEN kategori NOT LIKE 'Piket%' THEN 1 END) as total_job, COUNT(CASE WHEN kategori LIKE 'Piket%' THEN 1 END) as total_piket, SUM(tarif) as total_pendapatan")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalJob = $jobOrders->filter(fn($j) => !str_starts_with(strtolower($j->kategori), 'piket'))->count();
        $totalPendapatan = $jobOrders->sum('tarif');

        return view('reports.pdf_report', compact(
            'jobOrders',
            'rekapHarian',
            'totalJob',
            'totalPendapatan',
            'titlePeriod',
            'selectedBulan'
        ));
    }
}

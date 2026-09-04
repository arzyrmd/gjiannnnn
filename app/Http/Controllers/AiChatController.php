<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => ['required', 'string', 'max:1000'],
                'history' => ['nullable', 'array'],
            ]);

            $userMessage = trim($request->input('message'));
            $apiKey = config('services.gemini.key');

            // 1. Gather live technician context
            $today = Carbon::today()->toDateString();
            $now = Carbon::now();
            $year = $now->year;
            $month = sprintf('%02d', $now->month);

            $pendapatanHariIni = JobOrder::whereDate('tanggal', $today)->sum('tarif');
            $totalJobHariIni = JobOrder::whereDate('tanggal', $today)->where('kategori', 'not like', 'Piket%')->count();
            $totalPiketHariIni = JobOrder::whereDate('tanggal', $today)->where('kategori', 'like', 'Piket%')->count();

            $pendapatanBulanIni = JobOrder::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('tarif');
            $totalJobBulanIni = JobOrder::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->where('kategori', 'not like', 'Piket%')->count();
            $totalPiketBulanIni = JobOrder::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->where('kategori', 'like', 'Piket%')->count();

            $tarifs = Tarif::orderBy('kategori', 'asc')->get();
            $tarifListStr = $tarifs->map(function ($t) {
                return "- ID " . $t->id . ": " . $t->kategori . " (Berhasil: Rp " . number_format($t->tarif_berhasil, 0, ',', '.') . ", Gagal: Rp " . number_format($t->tarif_gagal ?? 0, 0, ',', '.') . ")";
            })->implode("\n");

            $recentJobs = JobOrder::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $recentJobsStr = $recentJobs->isEmpty()
                ? "Belum ada transaksi di bulan ini."
                : $recentJobs->map(function ($j) {
                    return "- [" . $j->tanggal->format('d/m/Y') . "] " . $j->kategori . " (" . $j->status . ") = Rp " . number_format($j->tarif, 0, ',', '.') . ($j->catatan ? " (Catatan: " . $j->catatan . ")" : "");
                })->implode("\n");

            // 2. Build System Prompt safely with natural conversational tone & batch support
            $systemPrompt = "Kamu adalah 'Asisten Gajian ARMN', seorang asisten pribadi teknisi lapangan yang ramah, sopan, manusiawi, dan sigap.\n\n"
                . "DATA REAL-TIME TEKNISI SAAT INI:\n"
                . "- Tanggal Hari Ini: " . $today . " (" . Carbon::now()->translatedFormat('l, d F Y') . ")\n"
                . "- Pendapatan Hari Ini: Rp " . number_format($pendapatanHariIni, 0, ',', '.') . " (" . $totalJobHariIni . " JO, " . $totalPiketHariIni . " Piket)\n"
                . "- Pendapatan Bulan Ini: Rp " . number_format($pendapatanBulanIni, 0, ',', '.') . "\n"
                . "- Volume Job Order (JO Murni) Bulan Ini: " . $totalJobBulanIni . " JO\n"
                . "- Total Piket (Mall & Event) Bulan Ini: " . $totalPiketBulanIni . " kali\n\n"
                . "DAFTAR MASTER KATEGORI & TARIF OFFICIAL:\n" . $tarifListStr . "\n\n"
                . "10 RIWAYAT TRANSAKSI TERAKHIR:\n" . $recentJobsStr . "\n\n"
                . "GAYA BAHASA & PETUNJUK RESPONS:\n"
                . "1. Gunakan bahasa Indonesia yang santai, manusiawi, ramah, dan sopan (seperti rekan kerja lapangan yang sigap).\n"
                . "2. Hindari penggunaan emoji atau ikon yang berlebihan.\n"
                . "3. Jika teknisi bermaksud MENCATAT JOB ORDER / PIKET BARU via percakapan (termasuk input jumlah banyak seperti 'proaktif 10', 'faktur 5', 'qris 8', dll):\n"
                . "   - Analisis kategori, jumlah (quantity), dan statusnya.\n"
                . "   - Jawab dengan konfirmasi ramah yang natural.\n"
                . "   - Di akhir jawabanmu, SERTAKAN JSON ACTION dalam blok kode json persis dengan format berikut:\n"
                . "     ```json\n"
                . "     {\n"
                . "       \"action\": \"create_job\",\n"
                . "       \"tarif_id\": ID_KATEGORI,\n"
                . "       \"kategori\": \"NAMA_KATEGORI\",\n"
                . "       \"status\": \"berhasil_atau_gagal\",\n"
                . "       \"tanggal\": \"YYYY-MM-DD\",\n"
                . "       \"catatan\": \"Catatan singkat jika ada\",\n"
                . "       \"custom_tarif\": nominal_angka_jika_piket_event_atau_null,\n"
                . "       \"quantity\": jumlah_angka_integer_misal_10\n"
                . "     }\n"
                . "     ```\n"
                . "4. Jika teknisi meminta rekap WhatsApp, buatkan format pesan ringkas yang rapi tanpa berlebihan.";

            // 3. Try Gemini API
            $models = ['gemini-1.5-flash', 'gemini-2.0-flash-exp', 'gemini-1.5-pro'];
            $replyText = null;

            if (!empty($apiKey)) {
                $userContents = [];
                if (!empty($request->input('history'))) {
                    foreach ($request->input('history') as $h) {
                        if (isset($h['role'], $h['text'])) {
                            $userContents[] = [
                                'role' => ($h['role'] === 'assistant') ? 'model' : 'user',
                                'parts' => [['text' => $h['text']]],
                            ];
                        }
                    }
                }
                $userContents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]],
                ];

                foreach ($models as $model) {
                    try {
                        $response = Http::withoutVerifying()
                            ->timeout(12)
                            ->withHeaders(['Content-Type' => 'application/json'])
                            ->post("https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey, [
                                'system_instruction' => [
                                    'parts' => [['text' => $systemPrompt]]
                                ],
                                'contents' => $userContents,
                                'generationConfig' => [
                                    'temperature' => 0.4,
                                    'maxOutputTokens' => 1000,
                                ],
                            ]);

                        if ($response->successful()) {
                            $resData = $response->json();
                            $replyText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? null;
                            if ($replyText) {
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning("Gemini API attempt exception on " . $model . ": " . $e->getMessage());
                    }
                }
            }

            // 4. Smart Local Fallback Engine (with Batch/Quantity Support)
            $autoCreated = false;
            $createdJobInfo = null;

            if (empty($replyText)) {
                $msgLower = strtolower($userMessage);
                // Normalize spaced "pro aktif" / "pro-aktif" to "proaktif"
                $msgNorm = preg_replace('/pro[\s\-]*aktif/i', 'proaktif', $msgLower);

                // 1. Comprehensive Master Category & Slang Detection
                $foundTarif = null;

                if (str_contains($msgNorm, 'proaktif mall') 
                    || str_contains($msgNorm, 'proaktif dalam') 
                    || str_contains($msgNorm, 'proaktif didalam') 
                    || str_contains($msgNorm, 'pm dalam') 
                    || str_contains($msgNorm, 'pm didalam') 
                    || str_contains($msgNorm, 'maintenance dalam')
                    || str_contains($msgNorm, 'maintenance didalam')
                    || str_contains($msgNorm, 'pm mall')
                    || str_contains($msgNorm, 'pm mal')
                ) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'dalam mall'));
                } elseif (str_contains($msgNorm, 'proaktif luar') 
                    || str_contains($msgNorm, 'pm luar') 
                    || str_contains($msgNorm, 'maintenance luar')
                    || str_contains($msgNorm, 'proaktif maintenance luar')
                ) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'luar mall'));
                } elseif (str_contains($msgNorm, 'proaktif') || str_contains($msgNorm, 'maintenance') || preg_match('/\bpm\b/i', $msgNorm)) {
                    if (str_contains($msgNorm, 'mall') || str_contains($msgNorm, 'mal') || str_contains($msgNorm, 'dalam') || str_contains($msgNorm, 'didalam')) {
                        $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'dalam mall'));
                    } else {
                        $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'luar mall'));
                    }
                } elseif (str_contains($msgNorm, 'tarik edc') || str_contains($msgNorm, 'penarikan') || str_contains($msgNorm, 'cabut edc')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'penarikan'));
                } elseif (str_contains($msgNorm, 'pasang edc') || str_contains($msgNorm, 'pemasangan') || str_contains($msgNorm, 'edc') || str_contains($msgNorm, 'instalasi')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'pemasangan edc') || str_contains(strtolower($t->kategori), 'edc'));
                } elseif (str_contains($msgNorm, 'qris')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'qris'));
                } elseif (str_contains($msgNorm, 'piket mall') || str_contains($msgNorm, 'piket mal')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'piket mall'));
                } elseif (str_contains($msgNorm, 'piket event') || str_contains($msgNorm, 'event') || str_contains($msgNorm, 'piket acara')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'piket event'));
                } elseif (str_contains($msgNorm, 'piket')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'piket'));
                } elseif (str_contains($msgNorm, 'faktur')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'faktur'));
                } elseif (str_contains($msgNorm, 'kunjungan') || str_contains($msgNorm, 'visit')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'kunjungan'));
                } elseif (str_contains($msgNorm, 'init')) {
                    $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'init'));
                }

                if (!$foundTarif) {
                    foreach ($tarifs as $t) {
                        $katLower = strtolower($t->kategori);
                        $cleanKatLower = str_replace(['(', ')'], '', $katLower);
                        if (str_contains($msgNorm, $katLower) || str_contains($msgNorm, $cleanKatLower)) {
                            $foundTarif = $t;
                            break;
                        }
                    }
                }

                $isJobRequest = ($foundTarif !== null)
                    || str_contains($msgNorm, 'catat') 
                    || str_contains($msgNorm, 'input') 
                    || str_contains($msgNorm, 'tambah');

                // Scenario A: Auto-record job order (supports batch / quantity e.g. "proaktif 10", "faktur 5")
                if ($isJobRequest) {
                    if (!$foundTarif) {
                        $foundTarif = $tarifs->first();
                    }

                    $isFailed = str_contains($msgNorm, 'gagal') 
                        || str_contains($msgNorm, 'batal') 
                        || str_contains($msgNorm, 'cancel')
                        || str_contains($msgNorm, 'unsuccessful');

                    $status = $isFailed ? 'gagal' : 'berhasil';
                    $rate = ($status === 'berhasil') ? $foundTarif->tarif_berhasil : ($foundTarif->tarif_gagal ?? 0);

                    // Check custom fee for Piket Mall, Piket Event, or shorthand numbers (e.g. 75rb, 75k, 60.000, 75000)
                    if (str_contains($msgNorm, 'piket') || str_contains($foundTarif->kategori, 'Piket')) {
                        if (preg_match('/(\d+)\s*(rb|k)\b/i', $msgNorm, $shorthandMatch)) {
                            $rate = (int)$shorthandMatch[1] * 1000;
                        } elseif (preg_match('/(\d{1,3}(?:\.\d{3})+|\d{4,7})/', $msgNorm, $amtMatch)) {
                            $rawAmt = str_replace('.', '', $amtMatch[1]);
                            if ((int)$rawAmt >= 1000) {
                                $rate = (int)$rawAmt;
                            }
                        }
                    }

                    // Smart Quantity / Batch Extractor (e.g., "proaktif 10", "10 proaktif", "faktur 5", "qris 8")
                    $quantity = 1;
                    if (preg_match('/(?:catat|input|tambah)?\s*(\d{1,2})\s*(?:x|kali|buah|unit|jo)?\s*(?:pekerjaan|tugas|transaksi)?\s*(?:proaktif|pm|faktur|edc|qris|piket|visit|kunjungan|init)/i', $msgNorm, $qtyMatch)) {
                        $quantity = (int)$qtyMatch[1];
                    } elseif (preg_match('/(?:proaktif|pm|faktur|edc|qris|piket|visit|kunjungan|init|jo)\s*(?:berhasil|gagal)?\s*(\d{1,2})\s*(?:x|kali|buah|unit|jo)?\b/i', $msgNorm, $qtyMatch)) {
                        $quantity = (int)$qtyMatch[1];
                    } elseif (preg_match('/\b(\d{1,2})\s*(?:x|kali|buah|unit|jo)\b/i', $msgNorm, $qtyMatch)) {
                        $quantity = (int)$qtyMatch[1];
                    }

                    $quantity = max(1, min(100, $quantity));

                    // Smart Store / Merchant / Location Note Extractor
                    $cleanNote = $userMessage;
                    $removeWords = [
                        'catat', 'input', 'tambah', 'berhasil', 'gagal', 'batal', 'cancel', 'unsuccessful',
                        'hari ini', 'kemarin', 'besok', 'nominal', 'sebesar', 'kategori', 'kirim faktur', 'faktur',
                        'kunjungan', 'visit', 'pemasangan edc', 'penarikan edc', 'pasang baru qris', 'qris', 'edc', 'init',
                        'pasang', 'tarik', 'cabut', 'instalasi',
                        'piket mall (diluar jo)', 'piket mall', 'piket mal', 'piket event', 'piket acara', 'piket', 'event',
                        'proaktif maintenance dalam mall', 'proaktif maintenance luar mall', 'proaktif maintenance',
                        'proaktif dalam mall', 'proaktif luar mall', 'proaktif mall', 'proaktif luar', 'proaktif',
                        'pro aktif dalam mall', 'pro aktif luar mall', 'pro aktif mall', 'pro aktif luar', 'pro aktif', 'pro', 'aktif',
                        'pm dalam mall', 'pm luar mall', 'pm dalam', 'pm luar', 'pm mall', 'pm', 'maintenance',
                        'toko', 'merchant', 'store', 'di'
                    ];

                    $cleanNote = preg_replace('/\b\d+(?:\.\d+)?(?:rb|k)?\b/i', '', $cleanNote);

                    foreach ($removeWords as $word) {
                        $cleanNote = preg_replace('/\b' . preg_quote($word, '/') . '\b/ui', '', $cleanNote);
                    }

                    $extractedNote = trim(preg_replace('/\s+/', ' ', $cleanNote));
                    $finalCatatan = (!empty($extractedNote) && strlen($extractedNote) >= 2) 
                        ? ucwords(strtolower($extractedNote)) 
                        : null;

                    $createdJobIds = [];
                    $totalBatchTarif = 0;

                    for ($i = 0; $i < $quantity; $i++) {
                        $newJob = JobOrder::create([
                            'tarif_id' => $foundTarif->id,
                            'kategori' => $foundTarif->kategori,
                            'status' => $status,
                            'tarif' => $rate,
                            'tanggal' => $today,
                            'catatan' => $finalCatatan,
                        ]);
                        $createdJobIds[] = $newJob->id;
                        $totalBatchTarif += $rate;
                    }

                    $autoCreated = true;
                    $createdJobInfo = [
                        'id' => implode(',', $createdJobIds),
                        'count' => $quantity,
                        'kategori' => $foundTarif->kategori,
                        'tarif' => $totalBatchTarif,
                        'tanggal' => Carbon::parse($today)->format('d/m/Y'),
                    ];

                    $noteText = $finalCatatan ? " (" . $finalCatatan . ")" : "";
                    if ($quantity > 1) {
                        $replyText = "Siap mas, " . $quantity . " pekerjaan " . $foundTarif->kategori . " (" . ucfirst($status) . ")" . $noteText . " senilai Rp " . number_format($rate, 0, ',', '.') . "/JO (Total: Rp " . number_format($totalBatchTarif, 0, ',', '.') . ") untuk hari ini telah berhasil dicatatkan sekaligus ke sistem.";
                    } else {
                        $replyText = "Siap mas, pekerjaan " . $foundTarif->kategori . " (" . ucfirst($status) . ")" . $noteText . " sebesar Rp " . number_format($rate, 0, ',', '.') . " untuk hari ini telah dicatatkan ke sistem.";
                    }
                }
                // Scenario B: WhatsApp Recap Generator
                elseif (str_contains($msgLower, 'wa') || str_contains($msgLower, 'whatsapp') || str_contains($msgLower, 'format')) {
                    $replyText = "REKAP PENDAPATAN HARIAN TEKNISI\nTanggal: " . Carbon::now()->translatedFormat('d F Y') . "\n----------------------------------------\nTotal Job Order (JO): " . $totalJobHariIni . " JO\nTotal Piket: " . $totalPiketHariIni . " Kali\nTotal Pendapatan Hari Ini: Rp " . number_format($pendapatanHariIni, 0, ',', '.') . "\n----------------------------------------\nAplikasi GajianARMN";
                }
                // Scenario C: Smart Date Inquiry ("besok", "kemarin", "tanggal 31 agustus", "tgl 1", "hari ini", etc.)
                elseif (($targetCarbon = $this->resolveDateFromMessage($userMessage)) !== null) {
                    $targetDateStr = $targetCarbon->toDateString();
                    $dateJobs = JobOrder::whereDate('tanggal', $targetDateStr)->get();
                    $dateIncome = $dateJobs->sum('tarif');
                    $dateJoCount = $dateJobs->filter(fn($j) => !str_starts_with($j->kategori, 'Piket'))->count();
                    $datePiketCount = $dateJobs->filter(fn($j) => str_starts_with($j->kategori, 'Piket'))->count();

                    if ($dateJobs->isEmpty()) {
                        $replyText = "Untuk tanggal " . $targetCarbon->translatedFormat('d F Y') . ", belum ada transaksi atau piket yang tercatat mas.";
                    } else {
                        $detailsStr = $dateJobs->map(function ($j) {
                            return "- " . $j->kategori . " (" . ucfirst($j->status) . ") = Rp " . number_format($j->tarif, 0, ',', '.') . ($j->catatan ? " [" . $j->catatan . "]" : "");
                        })->implode("\n");

                        $replyText = "Untuk tanggal " . $targetCarbon->translatedFormat('d F Y') . ", total pendapatanmu adalah Rp " . number_format($dateIncome, 0, ',', '.') . " (" . $dateJoCount . " JO, " . $datePiketCount . " Piket).\n\nRincian pekerjaan:\n" . $detailsStr;
                    }
                }
                // Scenario D: Monthly Inquiry ("bulan kemarin", "bulan kemaren", "bulan lalu", "agustus", "bulan ini", etc.)
                elseif (str_contains($msgLower, 'kemaren') || str_contains($msgLower, 'kemarin') || str_contains($msgLower, 'lalu') || str_contains($msgLower, 'bulan')) {
                    $targetMonthCarbon = Carbon::now();

                    if (str_contains($msgLower, 'kemaren') || str_contains($msgLower, 'kemarin') || str_contains($msgLower, 'lalu') || str_contains($msgLower, 'last month')) {
                        $targetMonthCarbon = Carbon::now()->subMonth();
                    } else {
                        $monthsMap = [
                            'januari' => 1, 'jan' => 1, 'februari' => 2, 'feb' => 2,
                            'maret' => 3, 'mar' => 3, 'april' => 4, 'apr' => 4,
                            'mei' => 5, 'may' => 5, 'juni' => 6, 'jun' => 6,
                            'juli' => 7, 'jul' => 7, 'agustus' => 8, 'agt' => 8, 'aug' => 8,
                            'september' => 9, 'sep' => 9, 'oktober' => 10, 'okt' => 10,
                            'november' => 11, 'nov' => 11, 'desember' => 12, 'des' => 12,
                        ];

                        foreach ($monthsMap as $mName => $mNum) {
                            if (str_contains($msgLower, $mName)) {
                                $targetMonthCarbon = Carbon::createFromDate($year, $mNum, 1);
                                break;
                            }
                        }
                    }

                    $tYear = $targetMonthCarbon->year;
                    $tMonth = sprintf('%02d', $targetMonthCarbon->month);

                    $mJobs = JobOrder::whereYear('tanggal', $tYear)->whereMonth('tanggal', $tMonth)->get();
                    $mIncome = $mJobs->sum('tarif');
                    $mJoCount = $mJobs->filter(fn($j) => !str_starts_with($j->kategori, 'Piket'))->count();
                    $mPiketCount = $mJobs->filter(fn($j) => str_starts_with($j->kategori, 'Piket'))->count();

                    $replyText = "Total pendapatanmu untuk bulan " . $targetMonthCarbon->translatedFormat('F Y') . " adalah Rp " . number_format($mIncome, 0, ',', '.') . " dari " . $mJoCount . " Job Order dan " . $mPiketCount . " kali Piket.\n\nAda pengerjaan job atau piket lagi yang mau diinput mas?";
                }
                // Scenario E: General / Monthly Earnings Inquiry
                else {
                    $replyText = "Total pendapatanmu untuk bulan " . Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') . " saat ini mencapai Rp " . number_format($pendapatanBulanIni, 0, ',', '.') . " dari " . $totalJobBulanIni . " Job Order dan " . $totalPiketBulanIni . " kali Piket.\n\nSemangat terus mas! Ada pengerjaan job atau piket lagi yang mau diinput?";
                }
            } else {
                // If Gemini API succeeded and provided action JSON
                if (preg_match('/```json\s*(\[\s*\{.*?\}\s*\]|\{.*?\})\s*```/s', $replyText, $matches)) {
                    $jsonStr = $matches[1];
                    $actionData = json_decode($jsonStr, true);

                    if ($actionData && isset($actionData['action']) && $actionData['action'] === 'create_job') {
                        try {
                            $tarifId = $actionData['tarif_id'] ?? null;
                            $tarifModel = Tarif::find($tarifId);

                            if (!$tarifModel && isset($actionData['kategori'])) {
                                $tarifModel = Tarif::where('kategori', 'like', '%' . $actionData['kategori'] . '%')->first();
                            }

                            if ($tarifModel) {
                                $status = in_array(strtolower($actionData['status'] ?? ''), ['berhasil', 'gagal'])
                                    ? strtolower($actionData['status'])
                                    : 'berhasil';

                                $rate = ($status === 'berhasil')
                                    ? $tarifModel->tarif_berhasil
                                    : ($tarifModel->tarif_gagal ?? 0);

                                if (isset($actionData['custom_tarif']) && is_numeric($actionData['custom_tarif'])) {
                                    $rate = (int)$actionData['custom_tarif'];
                                }

                                $quantity = isset($actionData['quantity']) && is_numeric($actionData['quantity']) && $actionData['quantity'] > 0
                                    ? (int)$actionData['quantity']
                                    : 1;

                                $createdJobIds = [];
                                $totalBatchTarif = 0;

                                for ($i = 0; $i < min($quantity, 100); $i++) {
                                    $newJob = JobOrder::create([
                                        'tarif_id' => $tarifModel->id,
                                        'kategori' => $tarifModel->kategori,
                                        'status' => $status,
                                        'tarif' => $rate,
                                        'tanggal' => $actionData['tanggal'] ?? $today,
                                        'catatan' => $actionData['catatan'] ?? 'Dicatat via AI Assistant',
                                    ]);
                                    $createdJobIds[] = $newJob->id;
                                    $totalBatchTarif += $rate;
                                }

                                $autoCreated = true;
                                $createdJobInfo = [
                                    'id' => implode(',', $createdJobIds),
                                    'count' => count($createdJobIds),
                                    'kategori' => $tarifModel->kategori,
                                    'tarif' => $totalBatchTarif,
                                    'tanggal' => Carbon::parse($actionData['tanggal'] ?? $today)->format('d/m/Y'),
                                ];
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to auto-create job order from Gemini: " . $e->getMessage());
                        }
                    }
                }
            }

            // Clean JSON block from user facing reply text
            $cleanReply = preg_replace('/```json\s*\{.*?\}\s*```/s', '', $replyText);

            return response()->json([
                'success' => true,
                'reply' => trim($cleanReply),
                'auto_created' => $autoCreated,
                'created_job' => $createdJobInfo,
            ]);

        } catch (\Throwable $ex) {
            Log::error("Uncaught AiChatController Error: " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Terjadi kesalahan sistem: ' . $ex->getMessage(),
            ], 500);
        }
    }

    public function undo(Request $request, $id = null)
    {
        try {
            $rawId = $request->input('id', $id);

            $ids = array_filter(array_map('trim', explode(',', (string)$rawId)), function ($v) {
                return $v !== '' && is_numeric($v);
            });

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan atau format ID salah.',
                ], 404);
            }

            $jobs = JobOrder::whereIn('id', $ids)->get();

            if ($jobs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan atau sudah dihapus.',
                ], 404);
            }

            $count = $jobs->count();
            $sampleCategory = $jobs->first()->kategori;

            JobOrder::whereIn('id', $ids)->delete();

            $msg = ($count > 1)
                ? "Pencatatan {$count} data {$sampleCategory} telah berhasil dibatalkan sekaligus."
                : "Pencatatan {$sampleCategory} telah berhasil dibatalkan.";

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function resolveDateFromMessage(string $message): ?Carbon
    {
        $msg = strtolower($message);
        $now = Carbon::now();

        // 1. Relative keywords
        if (str_contains($msg, 'besok') || str_contains($msg, 'esok') || str_contains($msg, 'tomorrow')) {
            return Carbon::tomorrow();
        }
        if (str_contains($msg, 'kemarin') || str_contains($msg, 'yesterday')) {
            return Carbon::yesterday();
        }
        if (str_contains($msg, 'lusa')) {
            return Carbon::today()->addDays(2);
        }
        if (str_contains($msg, 'hari ini') || str_contains($msg, 'sekarang') || str_contains($msg, 'today')) {
            return Carbon::today();
        }

        // 2. Indonesian & English Month Name Mapping
        $monthsMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agt' => 8, 'aug' => 8, 'august' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10, 'october' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12, 'december' => 12,
        ];

        $targetMonth = $now->month;
        $targetYear = $now->year;
        $targetDay = null;

        // Check explicit 4-digit year
        if (preg_match('/\b(20\d{2})\b/', $msg, $yearMatch)) {
            $targetYear = (int)$yearMatch[1];
        }

        // Check month name
        foreach ($monthsMap as $monthName => $monthNum) {
            if (preg_match('/\b' . $monthName . '\b/i', $msg)) {
                $targetMonth = $monthNum;
                break;
            }
        }

        // Check day number e.g. "31 agustus", "tanggal 1", "tgl 15", "1 september"
        if (preg_match('/(?:tanggal|tgl)?\s*(\d{1,2})\b/i', $msg, $dayMatch)) {
            $dayVal = (int)$dayMatch[1];
            if ($dayVal >= 1 && $dayVal <= 31) {
                $targetDay = $dayVal;
            }
        }

        if ($targetDay !== null) {
            try {
                $maxDays = Carbon::createFromDate($targetYear, $targetMonth, 1)->daysInMonth;
                $validDay = min($targetDay, $maxDays);
                return Carbon::createFromDate($targetYear, $targetMonth, $validDay);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}

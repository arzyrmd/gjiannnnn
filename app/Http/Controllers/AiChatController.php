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

            // 1. Gather live technician context for current user
            $userId = auth()->id();
            $today = Carbon::today()->toDateString();
            $now = Carbon::now();
            $year = $now->year;
            $month = sprintf('%02d', $now->month);

            $baseQuery = fn() => JobOrder::where('user_id', $userId);

            $pendapatanHariIni = $baseQuery()->whereDate('tanggal', $today)->sum('tarif');
            $totalJobHariIni = $baseQuery()->whereDate('tanggal', $today)->where('kategori', 'not like', 'Piket%')->count();
            $totalPiketHariIni = $baseQuery()->whereDate('tanggal', $today)->where('kategori', 'like', 'Piket%')->count();

            $pendapatanBulanIni = $baseQuery()->whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('tarif');
            $totalJobBulanIni = $baseQuery()->whereYear('tanggal', $year)->whereMonth('tanggal', $month)->where('kategori', 'not like', 'Piket%')->count();
            $totalPiketBulanIni = $baseQuery()->whereYear('tanggal', $year)->whereMonth('tanggal', $month)->where('kategori', 'like', 'Piket%')->count();

            $tarifs = Tarif::orderBy('kategori', 'asc')->get();
            $tarifListStr = $tarifs->map(function ($t) {
                return "- ID " . $t->id . ": " . $t->kategori . " (Berhasil: Rp " . number_format($t->tarif_berhasil, 0, ',', '.') . ", Gagal: Rp " . number_format($t->tarif_gagal ?? 0, 0, ',', '.') . ")";
            })->implode("\n");

            $recentJobs = $baseQuery()->whereYear('tanggal', $year)
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
                . "3. ATURAN PENTING PENCATATAN JOB ORDER / PIKET BARU (termasuk input jumlah banyak misal: 'projek 15', '15 projek', 'proaktif 10', 'faktur 5', 'qris 8', dll):\n"
                . "   - BILA PENGGUNA MENULIS NAMA KATEGORI DIIKUTI ANGKA 1-100 (CONTOH: 'projek 15', 'faktur 10', 'qris 8', '15 projek'), ANGKA TERSEBUT ADALAH JUMLAH/QUANTITY PEKERJAAN (quantity: 15), BUKAN NOMINAL TARIF (custom_tarif: null)!\n"
                . "   - Gunakan tarif resmi dari DAFTAR MASTER KATEGORI & TARIF OFFICIAL di atas. Set custom_tarif = null, KECUALI bila piket event atau jika pengguna mengetik angka ribuan/nominal misal '75k', '100rb', '50000'.\n"
                . "   - ATURAN CATATAN (catatan): JANGAN pernah mengisi 'catatan' dengan nama kategori itu sendiri (contoh: JANGAN isi 'catatan': 'Projek' jika kategorinya 'Projek'). Jika pengguna tidak menyebut nama merchant/lokasi toko spesifik, isi 'catatan': null.\n"
                . "   - Di akhir jawabanmu, SERTAKAN JSON ACTION dalam blok kode json persis dengan format berikut:\n"
                . "     ```json\n"
                . "     {\n"
                . "       \"action\": \"create_job\",\n"
                . "       \"tarif_id\": ID_KATEGORI,\n"
                . "       \"kategori\": \"NAMA_KATEGORI\",\n"
                . "       \"status\": \"berhasil_atau_gagal\",\n"
                . "       \"tanggal\": \"YYYY-MM-DD\",\n"
                . "       \"catatan\": \"Merchant/lokasi toko spesifik jika ada, atau null\",\n"
                . "       \"custom_tarif\": null_atau_nominal_angka,\n"
                . "       \"quantity\": JUMLAH_ANGKA_INTEGER_MISAL_15\n"
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
                                    'temperature' => 0.3,
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
                // Normalize spaced "pro aktif" / "pro-aktif" / "project" to "proaktif"
                $msgNorm = preg_replace('/pro[\s\-]*aktif/i', 'proaktif', $msgLower);

                // 1. Comprehensive Master Category & Slang Detection
                $foundTarif = null;

                // First check exact DB category names match
                foreach ($tarifs as $t) {
                    $katLower = strtolower($t->kategori);
                    $cleanKatLower = trim(preg_replace('/\(.*?\)/', '', $katLower));
                    if (str_contains($msgNorm, $katLower) || ($cleanKatLower !== '' && str_contains($msgNorm, $cleanKatLower))) {
                        $foundTarif = $t;
                        break;
                    }
                }

                // If not matched, check shorthand slang keywords
                if (!$foundTarif) {
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
                    } elseif (str_contains($msgNorm, 'proaktif') || str_contains($msgNorm, 'projek') || str_contains($msgNorm, 'project') || str_contains($msgNorm, 'maintenance') || preg_match('/\bpm\b/i', $msgNorm)) {
                        $foundTarif = $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'proaktif') || str_contains(strtolower($t->kategori), 'projek') || str_contains(strtolower($t->kategori), 'maintenance'))
                                    ?? $tarifs->first(fn($t) => str_contains(strtolower($t->kategori), 'dalam mall'));
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
                }

                $isJobRequest = ($foundTarif !== null)
                    || str_contains($msgNorm, 'catat') 
                    || str_contains($msgNorm, 'input') 
                    || str_contains($msgNorm, 'tambah');

                // Scenario A: Auto-record job order
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

                    // Smart Universal Quantity / Batch Extractor (e.g., "projek 15", "15 projek", "proaktif 10", "faktur 5", "qris 8")
                    $quantity = 1;
                    if (preg_match('/\b(\d{1,2})\b/i', $msgNorm, $qtyMatch)) {
                        $candidateQty = (int)$qtyMatch[1];
                        if ($candidateQty >= 1 && $candidateQty <= 100) {
                            if (!preg_match('/\b' . $candidateQty . '\s*(?:rb|k|000)\b/i', $msgNorm)) {
                                $quantity = $candidateQty;
                            }
                        }
                    }

                    $quantity = max(1, min(100, $quantity));

                    // Smart Store / Merchant / Location Note Extractor
                    $cleanNote = $userMessage;
                    if ($quantity > 1) {
                        $cleanNote = preg_replace('/\b' . $quantity . '\b/', '', $cleanNote);
                    }

                    $removeWords = [
                        'catat', 'input', 'tambah', 'berhasil', 'gagal', 'batal', 'cancel', 'unsuccessful',
                        'hari ini', 'kemarin', 'besok', 'nominal', 'sebesar', 'kategori', 'projek', 'project',
                        'kirim faktur', 'faktur', 'kunjungan', 'visit', 'pemasangan edc', 'penarikan edc',
                        'pasang baru qris', 'qris', 'edc', 'init', 'pasang', 'tarik', 'cabut', 'instalasi',
                        'piket mall (diluar jo)', 'piket mall', 'piket mal', 'piket event', 'piket acara', 'piket', 'event',
                        'proaktif maintenance dalam mall', 'proaktif maintenance luar mall', 'proaktif maintenance',
                        'proaktif dalam mall', 'proaktif luar mall', 'proaktif mall', 'proaktif luar', 'proaktif',
                        'pro aktif dalam mall', 'pro aktif luar mall', 'pro aktif mall', 'pro aktif luar', 'pro aktif', 'pro', 'aktif',
                        'pm dalam mall', 'pm luar mall', 'pm dalam', 'pm luar', 'pm mall', 'pm', 'maintenance',
                        'toko', 'merchant', 'store', 'di'
                    ];
                    if ($foundTarif) {
                        $removeWords = array_merge($removeWords, explode(' ', strtolower($foundTarif->kategori)));
                    }

                    $cleanNote = preg_replace('/\b\d+(?:\.\d+)?(?:rb|k)?\b/i', '', $cleanNote);

                    foreach ($removeWords as $word) {
                        $word = trim($word);
                        if ($word !== '' && strlen($word) >= 2) {
                            $cleanNote = preg_replace('/\b' . preg_quote($word, '/') . '\b/ui', '', $cleanNote);
                        }
                    }

                    $extractedNote = trim(preg_replace('/\s+/', ' ', $cleanNote));
                    $finalCatatan = (!empty($extractedNote) && strlen($extractedNote) >= 2 && !is_numeric($extractedNote) && strtolower($extractedNote) !== strtolower($foundTarif->kategori)) 
                        ? ucwords(strtolower($extractedNote)) 
                        : null;

                    $createdJobIds = [];
                    $totalBatchTarif = 0;

                    for ($i = 0; $i < $quantity; $i++) {
                        $newJob = JobOrder::create([
                            'user_id' => auth()->id(),
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

                            if (!$tarifModel) {
                                $tarifModel = Tarif::first();
                            }

                            if ($tarifModel) {
                                $status = in_array(strtolower($actionData['status'] ?? ''), ['berhasil', 'gagal'])
                                    ? strtolower($actionData['status'])
                                    : 'berhasil';

                                $rate = ($status === 'berhasil')
                                    ? $tarifModel->tarif_berhasil
                                    : ($tarifModel->tarif_gagal ?? 0);

                                if (isset($actionData['custom_tarif']) && is_numeric($actionData['custom_tarif']) && (int)$actionData['custom_tarif'] >= 1000) {
                                    $rate = (int)$actionData['custom_tarif'];
                                }

                                $quantity = isset($actionData['quantity']) && is_numeric($actionData['quantity']) && $actionData['quantity'] > 0
                                    ? (int)$actionData['quantity']
                                    : 1;

                                // Clean notes from Gemini so category name isn't duplicated
                                $finalCatatan = $actionData['catatan'] ?? null;
                                if ($finalCatatan) {
                                    $cleanCat = strtolower(trim($finalCatatan));
                                    $cleanKat = strtolower(trim($tarifModel->kategori));
                                    if ($cleanCat === $cleanKat || str_contains($cleanCat, 'dicatat via') || $cleanCat === 'null' || $cleanCat === 'projek') {
                                        $finalCatatan = null;
                                    }
                                }

                                $createdJobIds = [];
                                $totalBatchTarif = 0;

                                for ($i = 0; $i < min($quantity, 100); $i++) {
                                    $newJob = JobOrder::create([
                                        'user_id' => auth()->id(),
                                        'tarif_id' => $tarifModel->id,
                                        'kategori' => $tarifModel->kategori,
                                        'status' => $status,
                                        'tarif' => $rate,
                                        'tanggal' => $actionData['tanggal'] ?? $today,
                                        'catatan' => $finalCatatan,
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
                    'message' => 'Data job order tidak ditemukan atau sudah dihapus.',
                ], 404);
            }

            $count = $jobs->count();
            $kategoriName = $jobs->first()->kategori;

            JobOrder::whereIn('id', $ids)->delete();

            $message = ($count > 1)
                ? "Berhasil membatalkan & menghapus " . $count . " entri pekerjaan " . $kategoriName . "."
                : "Berhasil membatalkan & menghapus pencatatan job " . $kategoriName . ".";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $count,
            ]);

        } catch (\Throwable $ex) {
            Log::error("Uncaught AiChatController undo Error: " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pencatatan: ' . $ex->getMessage(),
            ], 500);
        }
    }

    private function resolveDateFromMessage(string $message): ?Carbon
    {
        $msg = strtolower($message);

        if (str_contains($msg, 'hari ini') || str_contains($msg, 'today')) {
            return Carbon::today();
        }
        if (str_contains($msg, 'kemarin') || str_contains($msg, 'kemaren') || str_contains($msg, 'yesterday')) {
            return Carbon::yesterday();
        }
        if (str_contains($msg, 'besok') || str_contains($msg, 'tomorrow')) {
            return Carbon::tomorrow();
        }

        $monthsMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agt' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12,
        ];

        // Format: "31 agustus", "tgl 15 maret 2026", "1 september", "tanggal 5 agustus"
        if (preg_match('/(?:tgl|tanggal)?\s*(\d{1,2})\s+([a-z]+)(?:\s+(\d{4}))?/i', $msg, $matches)) {
            $day = (int)$matches[1];
            $monthStr = strtolower($matches[2]);
            $year = !empty($matches[3]) ? (int)$matches[3] : Carbon::now()->year;

            if (isset($monthsMap[$monthStr])) {
                $month = $monthsMap[$monthStr];
                try {
                    return Carbon::createFromDate($year, $month, $day);
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        return null;
    }
}

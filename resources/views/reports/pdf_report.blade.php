<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gajian Teknisi Lapangan - {{ $titlePeriod }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono for PDF Print -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700&family=Plus+Jakarta+Sans:wght@500;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 24px;
            font-size: 11px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .page-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }
        
        /* Top Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-icon {
            width: 42px;
            height: 42px;
            background: #fbbf24;
            color: #0f172a;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 22px;
        }
        .brand-title h1 {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            line-height: 1.2;
        }
        .brand-title p {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-box {
            text-align: right;
            font-size: 10px;
            color: #475569;
            line-height: 1.6;
        }
        .meta-period {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }

        /* Executive Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .stat-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            background: #f8fafc;
        }
        .stat-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .stat-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            margin-top: 4px;
        }
        .stat-highlight {
            color: #059669;
        }
        .stat-sub {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Section Titles */
        .section-header {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-top: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #cbd5e1;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border-radius: 8px;
            overflow: hidden;
            border: 1.5px solid #cbd5e1;
        }
        th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 9px 12px;
            text-align: left;
            border: none;
        }
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 10px;
            color: #1e293b;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        tbody tr:hover {
            background-color: #f1f5f9;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'JetBrains Mono', monospace; font-weight: 700; }
        .font-black { font-weight: 900; }

        /* Badges */
        .badge-berhasil {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
            font-weight: 900;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
        }
        .badge-gagal {
            background-color: #ffe4e6;
            color: #be123c;
            border: 1px solid #fca5a5;
            font-weight: 900;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
        }

        /* Footer Summary Row */
        tfoot tr td {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 900;
            font-size: 11px;
            border: none;
            padding: 10px 12px;
        }
        tfoot .total-gold {
            color: #fbbf24 !important;
        }
        tfoot .total-emerald {
            color: #34d399 !important;
        }

        /* Signature & Disclaimer Block */
        .doc-footer {
            margin-top: 30px;
            pt-4;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        .disclaimer {
            font-size: 9px;
            color: #94a3b8;
            max-width: 450px;
            line-height: 1.4;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-box p {
            font-size: 10px;
            color: #475569;
            font-weight: 700;
        }
        .signature-line {
            margin-top: 45px;
            border-top: 1.5px solid #0f172a;
            font-weight: 800;
            font-size: 10px;
            padding-top: 4px;
            color: #0f172a;
        }

        /* Controls */
        .control-bar {
            max-width: 900px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: flex-end;
        }
        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 11px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: background 0.2s;
        }
        .btn-print:hover {
            background-color: #1e293b;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: #ffffff; }
            .page-container { border: none; border-radius: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

    <!-- Floating Top Print Action Bar -->
    <div class="control-bar no-print">
        <button onclick="window.print()" class="btn-print">CETAK / SIMPAN KE PDF</button>
    </div>

    <div class="page-container">
        <!-- Document Header -->
        <div class="doc-header">
            <div class="brand-box">
                <div class="brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/>
                        <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/>
                        <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                    </svg>
                </div>
                <div class="brand-title">
                    <h1>LAPORAN GAJIAN TEKNISI LAPANGAN</h1>
                    <p>GAJIANARMN FIELDWORK SYSTEM</p>
                </div>
            </div>
            <div class="meta-box">
                <div>Dicetak: <strong>{{ now()->translatedFormat('d F Y H:i') }} WIB</strong></div>
                <div class="meta-period">{{ $titlePeriod }}</div>
            </div>
        </div>

        <!-- Executive Summary 4-Column Grid -->
        <div class="summary-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value stat-highlight">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="stat-sub">Snapshot Akumulasi</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Volume Job (JO)</div>
                <div class="stat-value">{{ $totalJob }} <span style="font-size:11px; font-weight:700;">JO</span></div>
                <div class="stat-sub">Excl. Piket</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Piket</div>
                <div class="stat-value">
                    {{ $jobOrders->filter(fn($j) => str_starts_with(strtolower($j->kategori), 'piket'))->count() }} 
                    <span style="font-size:11px; font-weight:700;">Kali</span>
                </div>
                <div class="stat-sub">Mall &amp; Event</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Entri Data</div>
                <div class="stat-value">{{ $jobOrders->count() }} <span style="font-size:11px; font-weight:700;">Entri</span></div>
                <div class="stat-sub">Keseluruhan Pekerjaan</div>
            </div>
        </div>

        <!-- Section 1: Rekapitulasi Harian -->
        <div class="section-header">1. REKAPITULASI HARIAN</div>
        <table>
            <thead>
                <tr>
                    <th width="40%">TANGGAL PEKERJAAN</th>
                    <th width="30%" class="text-center">VOLUME JOB ORDER (JO)</th>
                    <th width="30%" class="text-right">PENDAPATAN HARIAN (RP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapHarian as $rekap)
                    <tr>
                        <td class="font-black">{{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center">
                            <div class="font-mono font-black" style="color:#d97706;">{{ $rekap->total_job }} JO</div>
                            @if(($rekap->total_piket ?? 0) > 0)
                                <div class="font-mono" style="color:#0284c7; font-size:9.5px; font-weight:700;">+ {{ $rekap->total_piket }} Piket</div>
                            @endif
                        </td>
                        <td class="text-right font-mono font-black" style="color:#059669;">
                            Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="color:#94a3b8; font-weight:700; padding:16px;">
                            Belum ada data transaksi tercatat pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td class="total-gold">TOTAL AKUMULASI PERIODE INI</td>
                    <td class="text-center total-gold font-mono">
                        <div>{{ $totalJob }} JO</div>
                        @php
                            $totalPiketInPdf = $rekapHarian->sum('total_piket');
                        @endphp
                        @if($totalPiketInPdf > 0)
                            <div style="color:#38bdf8; font-size:9.5px; font-weight:700;">+ {{ $totalPiketInPdf }} Piket</div>
                        @endif
                    </td>
                    <td class="text-right font-mono total-emerald">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Section 2: Rincian Detail Transaksi -->
        <div class="section-header">2. RINCIAN DETAIL TRANSAKSI JOB ORDER</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">NO</th>
                    <th width="14%">TANGGAL</th>
                    <th width="33%">KATEGORI TUGAS</th>
                    <th width="14%" class="text-center">STATUS</th>
                    <th width="16%" class="text-right">TARIF SNAPSHOT</th>
                    <th width="18%">CATATAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrders as $index => $job)
                    <tr>
                        <td class="text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                        <td class="font-mono font-black">{{ $job->tanggal->format('d/m/Y') }}</td>
                        <td class="font-black" style="color:#0f172a;">{{ $job->kategori }}</td>
                        <td class="text-center">
                            @if($job->status === 'berhasil')
                                <span class="badge-berhasil">BERHASIL</span>
                            @else
                                <span class="badge-gagal">GAGAL</span>
                            @endif
                        </td>
                        <td class="text-right font-mono font-black" style="color:#0f172a;">
                            Rp {{ number_format($job->tarif, 0, ',', '.') }}
                        </td>
                        <td style="color:#64748b; font-size:9.5px;">{{ $job->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="color:#94a3b8; font-weight:700; padding:16px;">
                            Tidak ada rincian transaksi job order.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Document Footer & Signature -->
        <div class="doc-footer">
            <div class="disclaimer">
                <strong>Catatan Laporan:</strong><br>
                Laporan ini dicetak secara otomatis dari Sistem Kalkulator Gajian Teknisi Lapangan (GajianARMN). Tarif yang tercantum merupakan snapshot nilai resmi saat pekerjaan dicatat.
            </div>

            <div class="signature-box">
                <p>Mengetahui / Penanggung Jawab</p>
                <div class="signature-line">
                    Teknisi Lapangan
                </div>
            </div>
        </div>
    </div>

</body>
</html>

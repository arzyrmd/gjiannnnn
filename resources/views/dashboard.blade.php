@extends('layouts.app')

@section('title', 'Dashboard & Rekap Gajian')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- 1. TOP METRICS SPOTLIGHT (2 MASTER COLUMNS: HARI INI & BULAN INI) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
        <!-- Master Card 1: Hari Ini -->
        <div class="bg-slate-900 border-2 border-emerald-500/50 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl relative overflow-hidden flex flex-col justify-between space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-400">Ringkasan Hari Ini</span>
                </div>
                <span class="text-[11px] sm:text-xs font-mono-num font-bold text-slate-400 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800 shrink-0">
                    {{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end pt-1">
                <div class="sm:col-span-7">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Pendapatan</span>
                    <div id="metricPendapatanHariIni" class="text-2xl sm:text-3xl md:text-4xl font-black font-mono-num text-emerald-400 tracking-tight mt-1 transition-all break-words">
                        Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="sm:col-span-5 text-left sm:text-right sm:border-l border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0 sm:pl-4 space-y-1">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Volume Job (JO)</span>
                        <div id="metricTotalJobHariIni" class="text-xl sm:text-2xl font-black font-mono-num text-amber-400 tracking-tight transition-all">
                            {{ $totalJobHariIni }} <span class="text-xs text-slate-400 font-bold">JO</span>
                        </div>
                    </div>
                    <div class="pt-1 border-t border-slate-800/60">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Piket</span>
                        <div id="metricTotalPiketHariIni" class="text-sm font-black font-mono-num text-cyan-300 transition-all">
                            {{ $totalPiketHariIni }} <span class="text-[11px] text-slate-400 font-bold">Kali</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Card 2: Bulan Ini -->
        <div class="bg-slate-900 border-2 border-cyan-500/50 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl relative overflow-hidden flex flex-col justify-between space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 shrink-0"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-cyan-400">Akumulasi Bulan Ini</span>
                </div>
                <span class="text-[11px] sm:text-xs font-mono-num font-bold text-slate-400 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800 shrink-0">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end pt-1">
                <div class="sm:col-span-7">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Pendapatan</span>
                    <div id="metricPendapatanBulanIni" class="text-2xl sm:text-3xl md:text-4xl font-black font-mono-num text-cyan-400 tracking-tight mt-1 transition-all break-words">
                        Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="sm:col-span-5 text-left sm:text-right sm:border-l border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0 sm:pl-4 space-y-1">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Volume Job (JO)</span>
                        <div id="metricTotalJobBulanIni" class="text-xl sm:text-2xl font-black font-mono-num text-indigo-300 tracking-tight transition-all">
                            {{ $totalJobBulanIni }} <span class="text-xs text-slate-400 font-bold">JO</span>
                        </div>
                    </div>
                    <div class="pt-1 border-t border-slate-800/60">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Piket</span>
                        <div id="metricTotalPiketBulanIni" class="text-sm font-black font-mono-num text-amber-300 transition-all">
                            {{ $totalPiketBulanIni }} <span class="text-[11px] text-slate-400 font-bold">Kali</span>
                            <span id="metricPendapatanPiketBulanIni" class="text-[10px] text-slate-400 font-bold block">(Rp {{ number_format($pendapatanPiketBulanIni, 0, ',', '.') }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 2. QUICK INPUT JOB ORDER FORM -->
    <div class="bg-slate-900 border-2 border-amber-400/90 rounded-2xl p-4 sm:p-5 md:p-6 shadow-2xl space-y-4 sm:space-y-5">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 gap-2">
            <div>
                <h2 class="text-base sm:text-lg font-black uppercase tracking-wider text-white">
                    Catat Job Order Baru
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Form input cepat pekerjaan atau piket harian teknisi.</p>
            </div>
            <span class="px-2.5 py-1 rounded-md bg-amber-400/10 border border-amber-400/30 text-amber-400 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider shrink-0">
                Input Cepat
            </span>
        </div>

        <form action="{{ route('job-orders.store') }}" method="POST" class="space-y-4" id="quickJobForm" onsubmit="handleQuickJobFormSubmit(event)">
            @csrf

            <!-- Form Row 1: Select Kategori, Status Radio, Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-start">
                <!-- 1. Select Kategori Tugas (Width 5 col) -->
                <div class="space-y-1.5 md:col-span-5">
                    <label for="tarif_id" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Kategori Tugas <span class="text-amber-400">*</span>
                    </label>
                    <select 
                        name="tarif_id" 
                        id="tarif_id" 
                        required 
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-bold focus:border-amber-400 focus:outline-none transition-colors text-sm min-h-[46px] cursor-pointer"
                        onchange="updatePricePreview()"
                    >
                        <option value="" disabled selected>Pilih Kategori Pekerjaan</option>
                        @foreach($tarifs as $tarif)
                            <option 
                                value="{{ $tarif->id }}" 
                                data-berhasil="{{ $tarif->tarif_berhasil }}" 
                                data-gagal="{{ $tarif->tarif_gagal ?? 0 }}"
                                {{ old('tarif_id') == $tarif->id ? 'selected' : '' }}
                            >
                                {{ $tarif->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Status Radio Options (Width 4 col) -->
                <div class="space-y-1.5 md:col-span-4">
                    <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Status Job <span class="text-amber-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input 
                                type="radio" 
                                name="status" 
                                value="berhasil" 
                                class="peer sr-only" 
                                checked 
                                onchange="updatePricePreview()"
                            >
                            <div class="w-full py-3 px-3 rounded-xl bg-slate-950 border-2 border-slate-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-950/80 peer-checked:text-emerald-300 text-slate-400 font-black text-center text-xs uppercase tracking-wider transition-all shadow-sm min-h-[46px] flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                                <span>BERHASIL</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input 
                                type="radio" 
                                name="status" 
                                value="gagal" 
                                class="peer sr-only" 
                                {{ old('status') === 'gagal' ? 'checked' : '' }}
                                onchange="updatePricePreview()"
                            >
                            <div class="w-full py-3 px-3 rounded-xl bg-slate-950 border-2 border-slate-800 peer-checked:border-rose-500 peer-checked:bg-rose-950/80 peer-checked:text-rose-300 text-slate-400 font-black text-center text-xs uppercase tracking-wider transition-all shadow-sm min-h-[46px] flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">cancel</span>
                                <span>GAGAL</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Tanggal Input (Width 3 col) -->
                <div class="space-y-1.5 md:col-span-3">
                    <div class="flex items-center justify-between gap-1">
                        <label for="tanggal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                            Tanggal <span class="text-amber-400">*</span>
                        </label>
                        <div class="flex items-center gap-1 text-[10px]">
                            <button type="button" onclick="setQuickFormDate('today')" class="px-1.5 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-colors cursor-pointer" title="Set tanggal hari ini">Today</button>
                            <button type="button" onclick="setQuickFormDate('yesterday')" class="px-1.5 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-colors cursor-pointer" title="Set tanggal kemarin">Kemarin</button>
                        </div>
                    </div>
                    <input 
                        type="date" 
                        name="tanggal" 
                        id="tanggal" 
                        value="{{ old('tanggal', $today) }}" 
                        required 
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold focus:border-amber-400 focus:outline-none transition-colors text-sm min-h-[46px]"
                    >
                </div>
            </div>

            <!-- Form Row 2: Catatan & Custom Nominal Input -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-1">
                <div class="sm:col-span-8">
                    <input 
                        type="text" 
                        name="catatan" 
                        placeholder="Catatan tambahan / No. Merchant / Lokasi (opsional)" 
                        value="{{ old('catatan') }}"
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-amber-400 focus:outline-none min-h-[44px]"
                    >
                </div>

                <!-- Custom Nominal Input (Tampil jika Piket Event dipilih) -->
                <div id="customTarifContainer" class="sm:col-span-4 hidden">
                    <input 
                        type="number" 
                        name="custom_tarif" 
                        id="custom_tarif" 
                        min="0" 
                        step="1000"
                        placeholder="Isi Nominal (Rp)" 
                        oninput="updatePricePreview()"
                        class="w-full px-3.5 py-3 rounded-xl bg-slate-950 border-2 border-amber-400 text-amber-400 font-mono-num font-bold text-xs focus:outline-none min-h-[44px]"
                    >
                </div>
            </div>

            <!-- Action & Preview Row -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 pt-3 border-t border-slate-800/80">
                <div class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-950 border-2 border-amber-400/50 flex items-center justify-between sm:justify-start gap-4">
                    <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Tarif Snapshot:</span>
                    <span id="pricePreview" class="text-xl font-black font-mono-num text-amber-400">Rp 0</span>
                </div>

                <button 
                    type="submit" 
                    class="w-full sm:w-auto py-3.5 px-8 rounded-xl bg-amber-400 hover:bg-amber-300 active:scale-[0.99] text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg shadow-amber-400/20 transition-all cursor-pointer border-2 border-amber-300 min-h-[48px] flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined text-lg font-bold">save</span>
                    <span>SIMPAN JOB ORDER</span>
                </button>
            </div>
        </form>
    </div>


    <!-- 3. REKAP HARIAN DALAM BULAN & FILTER / EKSPOR UNIFIED CARD -->
    <div class="bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl space-y-4 sm:space-y-5">
        <!-- Unified Header: Title & Export Buttons -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 border-b border-slate-800 pb-3.5">
            <div>
                <h3 class="text-base md:text-lg font-black uppercase text-white tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 text-xl">date_range</span>
                    <span>Rekap Harian</span>
                    <span class="text-amber-400 text-sm sm:text-base">({{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }})</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Akumulasi total job order murni dan pendapatan harian.</p>
            </div>

            <!-- Export Buttons -->
            <div class="grid grid-cols-2 gap-2 w-full md:w-auto">
                <a 
                    href="{{ route('export.csv', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                    class="px-3 py-2.5 rounded-xl bg-emerald-950 border-2 border-emerald-500/80 hover:bg-emerald-900 text-emerald-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-colors"
                >
                    <span class="material-symbols-outlined text-base">download</span>
                    <span>Export CSV</span>
                </a>

                <a 
                    href="{{ route('export.pdf', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                    target="_blank"
                    class="px-3 py-2.5 rounded-xl bg-cyan-950 border-2 border-cyan-500/80 hover:bg-cyan-900 text-cyan-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-colors"
                >
                    <span class="material-symbols-outlined text-base">print</span>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>

        <!-- Integrated Filter Controls -->
        <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-3 sm:p-3.5 space-y-3">
            <div class="grid grid-cols-2 sm:flex items-center gap-2 border-b border-slate-800/80 pb-2">
                <button 
                    type="button"
                    onclick="switchFilterMode('bulan')"
                    id="tabBulan"
                    class="px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all cursor-pointer text-center {{ (!$startDate && !$endDate) ? 'bg-amber-400 text-slate-950 font-black shadow' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}"
                >
                    Per Bulan
                </button>
                <button 
                    type="button"
                    onclick="switchFilterMode('rentang')"
                    id="tabRentang"
                    class="px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all cursor-pointer text-center {{ ($startDate || $endDate) ? 'bg-amber-400 text-slate-950 font-black shadow' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}"
                >
                    Rentang Tanggal
                </button>
                
                @if($startDate || $endDate || $selectedBulan !== \Carbon\Carbon::now()->format('Y-m'))
                    <a href="{{ route('dashboard') }}" class="col-span-2 sm:col-span-1 sm:ml-auto text-center text-xs text-rose-400 hover:underline font-bold uppercase tracking-wider py-1">
                        Reset Filter
                    </a>
                @endif
            </div>

            <!-- Form Filter Per Bulan -->
            <form id="formFilterBulan" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'hidden' : 'block' }}">
                <div class="flex flex-col sm:flex-row items-end gap-3">
                    <div class="w-full sm:w-64 space-y-1">
                        <label for="bulan" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Pilih Bulan &amp; Tahun
                        </label>
                        <input 
                            type="month" 
                            name="bulan" 
                            id="bulan" 
                            value="{{ $selectedBulan }}" 
                            onchange="this.form.submit()"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]"
                        >
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 transition-colors border-2 border-amber-300 min-h-[42px]">
                        Tampilkan Rekap
                    </button>
                </div>
            </form>

            <!-- Form Filter Rentang Tanggal -->
            <form id="formFilterRentang" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'block' : 'hidden' }}">
                <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="space-y-1">
                        <label for="start_date" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Dari Tanggal
                        </label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            value="{{ $startDate }}" 
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]"
                        >
                    </div>

                    <div class="space-y-1">
                        <label for="end_date" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Sampai Tanggal
                        </label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            value="{{ $endDate }}" 
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]"
                        >
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="w-full py-2.5 px-5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 transition-colors border-2 border-amber-300 min-h-[42px]"
                        >
                            Terapkan Rentang
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal Pekerjaan</th>
                        <th class="py-3.5 px-4 text-center">Volume Job Order (JO)</th>
                        <th class="py-3.5 px-4 text-right">Pendapatan Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    @forelse($rekapHarian as $rekap)
                        <tr class="hover:bg-slate-850/60 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-200">
                                <a href="{{ route('dashboard', ['bulan' => $selectedBulan, 'tanggal' => $rekap->tanggal]) }}" class="hover:text-amber-400 underline decoration-amber-400/40">
                                    {{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('j F Y') }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="font-mono-num font-bold text-amber-300">
                                    {{ $rekap->total_job }} JO
                                </div>
                                @if(($rekap->total_piket ?? 0) > 0)
                                    <div class="font-mono-num font-semibold text-cyan-400 text-[11px] mt-0.5">
                                        + {{ $rekap->total_piket }} Piket
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num font-black text-emerald-400 text-sm">
                                Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500 uppercase tracking-widest font-bold">
                                Belum ada transaksi job order di bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Bottom Summary Total Row -->
                <tfoot class="bg-slate-950 border-t-2 border-amber-400/60 font-black text-sm text-slate-100">
                    <tr>
                        <td class="py-4 px-4 uppercase tracking-wider text-amber-400">
                            TOTAL AKUMULASI BULAN THIS
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="font-mono-num text-amber-300 text-base font-black">
                                {{ $rekapHarian->sum('total_job') }} JO
                            </div>
                            @if($rekapHarian->sum('total_piket') > 0)
                                <div class="font-mono-num text-cyan-400 text-xs font-bold mt-0.5">
                                    + {{ $rekapHarian->sum('total_piket') }} Piket
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right font-mono-num text-emerald-400 text-lg">
                            Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Card View (For Small Screens) -->
        <div class="block sm:hidden space-y-2.5">
            @forelse($rekapHarian as $rekap)
                <a href="{{ route('dashboard', ['bulan' => $selectedBulan, 'tanggal' => $rekap->tanggal]) }}" class="block p-3.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-400/50 transition-colors space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="font-extrabold text-slate-200 text-xs flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-amber-400 text-sm">event</span>
                            <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('j F Y') }}</span>
                        </span>
                        <span class="text-xs font-mono-num font-black text-emerald-400">
                            Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <span class="text-slate-400 font-medium">Volume Harian:</span>
                        <div class="flex items-center gap-2 font-mono-num font-bold">
                            <span class="text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20 text-[11px]">
                                {{ $rekap->total_job }} JO
                            </span>
                            @if(($rekap->total_piket ?? 0) > 0)
                                <span class="text-cyan-300 bg-cyan-400/10 px-2 py-0.5 rounded border border-cyan-400/20 text-[11px]">
                                    + {{ $rekap->total_piket }} Piket
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-xl bg-slate-950 border border-slate-800">
                    Belum ada transaksi job order di bulan ini.
                </div>
            @endforelse

            @if($rekapHarian->count() > 0)
                <!-- Total Accumulation Card on Mobile -->
                <div class="p-4 rounded-xl bg-slate-950 border-2 border-amber-400/60 space-y-2">
                    <div class="text-xs font-black uppercase text-amber-400 tracking-wider">
                        Total Akumulasi Bulan Ini
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-800 pt-2">
                        <div class="font-mono-num text-xs font-bold text-amber-300">
                            {{ $rekapHarian->sum('total_job') }} JO
                            @if($rekapHarian->sum('total_piket') > 0)
                                <span class="text-cyan-400 text-[11px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                            @endif
                        </div>
                        <div class="text-base font-black font-mono-num text-emerald-400">
                            Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- 5. DETAIL JOB ORDERS LIST (WITH EDIT & DELETE) -->
    <div class="bg-slate-900 border-2 border-slate-800 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 text-xl">receipt_long</span>
                    <span>Detail Transaksi Job Order</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian entri per kategori tugas beserta status dan nilai tarif snapshot.</p>
            </div>
            @if($startDate && $endDate)
                <span class="text-xs bg-amber-400/10 border border-amber-400/40 text-amber-400 px-2.5 py-1 rounded font-bold shrink-0">
                    Rentang: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </span>
            @endif
        </div>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kategori Tugas</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Tarif (Snapshot)</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    @forelse($detailJobOrders as $index => $job)
                        <tr class="hover:bg-slate-850/60 transition-colors">
                            <td class="py-3.5 px-4 text-slate-500 font-mono-num">
                                {{ $detailJobOrders->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-mono-num text-slate-300 font-bold whitespace-nowrap">
                                {{ $job->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white">
                                {{ $job->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($job->status === 'berhasil')
                                    <span class="px-3 py-1 rounded-md bg-emerald-950 border border-emerald-500/80 text-emerald-300 font-black text-[10px] uppercase tracking-wider">
                                        BERHASIL
                                    </span>
                                @else
                                    <span class="px-3.5 py-1 rounded-md bg-rose-950 border border-rose-500/80 text-rose-300 font-black text-[10px] uppercase tracking-wider">
                                        GAGAL
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num font-black text-amber-400 text-sm whitespace-nowrap">
                                Rp {{ number_format($job->tarif, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">
                                {{ $job->catatan ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        onclick="openEditModal({{ json_encode($job) }})" 
                                        class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-bold text-[11px] uppercase tracking-wider transition-colors flex items-center gap-1 min-h-[34px]"
                                    >
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-700/60 font-bold text-[11px] uppercase tracking-wider transition-colors flex items-center gap-1 min-h-[34px]">
                                            <span class="material-symbols-outlined text-xs">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 uppercase tracking-widest font-bold">
                                Tidak ada data job order ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="block sm:hidden space-y-3">
            @forelse($detailJobOrders as $index => $job)
                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono-num text-slate-500 font-bold">#{{ $detailJobOrders->firstItem() + $index }}</span>
                            <span class="font-mono-num font-bold text-slate-300 text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs text-slate-500">calendar_today</span>
                                <span>{{ $job->tanggal->format('d/m/Y') }}</span>
                            </span>
                        </div>
                        <div>
                            @if($job->status === 'berhasil')
                                <span class="px-2.5 py-0.5 rounded bg-emerald-950 border border-emerald-500/80 text-emerald-300 font-black text-[9px] uppercase tracking-wider">
                                    BERHASIL
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded bg-rose-950 border border-rose-500/80 text-rose-300 font-black text-[9px] uppercase tracking-wider">
                                    GAGAL
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h4 class="font-bold text-white text-xs">{{ $job->kategori }}</h4>
                        @if($job->catatan)
                            <p class="text-[11px] text-slate-400 bg-slate-900 p-2 rounded-lg border border-slate-800/80">
                                {{ $job->catatan }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <div class="font-mono-num font-black text-amber-400 text-sm">
                            Rp {{ number_format($job->tarif, 0, ',', '.') }}
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button 
                                onclick="openEditModal({{ json_encode($job) }})" 
                                class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]"
                            >
                                <span class="material-symbols-outlined text-xs">edit</span>
                                <span>Edit</span>
                            </button>

                            <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-700/60 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-xl bg-slate-950 border border-slate-800">
                    Tidak ada data job order ditemukan untuk filter ini.
                </div>
            @endforelse
        </div>

        @if($detailJobOrders->hasPages())
            <div class="pt-2">
                {{ $detailJobOrders->links() }}
            </div>
        @endif
    </div>

</div>


<!-- EDIT JOB ORDER MODAL -->
<div id="editModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-slate-900 border-2 border-amber-400 rounded-2xl max-w-lg w-full p-4 sm:p-6 space-y-4 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400">edit</span>
                <span>Edit Job Order</span>
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white font-bold text-xs uppercase tracking-wider p-1">
                Tutup
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="edit_kategori_info" class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                    Kategori Tugas
                </label>
                <input type="text" id="edit_kategori_info" readonly class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 font-bold text-xs" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Status</label>
                    <select name="status" id="edit_status" required class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]">
                        <option value="berhasil">BERHASIL</option>
                        <option value="gagal">GAGAL</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="edit_tanggal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="edit_tarif" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Nominal Tarif (Rp)</label>
                <input type="number" name="tarif" id="edit_tarif" required min="0" step="500" class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border-2 border-slate-700 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
            </div>

            <div class="space-y-1.5">
                <label for="edit_catatan" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Catatan</label>
                <input type="text" name="catatan" id="edit_catatan" class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-300 border-2 border-amber-300">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setQuickFormDate(target) {
        const input = document.getElementById('tanggal');
        if (!input) return;
        const d = new Date();
        if (target === 'yesterday') {
            d.setDate(d.getDate() - 1);
        }
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        input.value = `${yyyy}-${mm}-${dd}`;
    }

    function switchFilterMode(mode) {
        const formBulan = document.getElementById('formFilterBulan');
        const formRentang = document.getElementById('formFilterRentang');
        const tabBulan = document.getElementById('tabBulan');
        const tabRentang = document.getElementById('tabRentang');

        if (mode === 'bulan') {
            formBulan.classList.remove('hidden');
            formBulan.classList.add('block');
            formRentang.classList.remove('block');
            formRentang.classList.add('hidden');

            tabBulan.className = "px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow text-center";
            tabRentang.className = "px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-800 text-slate-400 hover:bg-slate-700 text-center";
        } else {
            formRentang.classList.remove('hidden');
            formRentang.classList.add('block');
            formBulan.classList.remove('block');
            formBulan.classList.add('hidden');

            tabRentang.className = "px-3 py-2 rounded-lg text-xs font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow text-center";
            tabBulan.className = "px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-800 text-slate-400 hover:bg-slate-700 text-center";
        }
    }

    function updatePricePreview() {
        const select = document.getElementById('tarif_id');
        const selectedOption = select.options[select.selectedIndex];
        const status = document.querySelector('input[name="status"]:checked')?.value || 'berhasil';
        const customContainer = document.getElementById('customTarifContainer');
        const customInput = document.getElementById('custom_tarif');
        
        if (!selectedOption || !selectedOption.value) {
            document.getElementById('pricePreview').innerText = 'Rp 0';
            if (customContainer) customContainer.classList.add('hidden');
            return;
        }

        const categoryText = selectedOption.text.trim().toLowerCase();
        const feeBerhasil = parseInt(selectedOption.getAttribute('data-berhasil') || 0);
        const feeGagal = parseInt(selectedOption.getAttribute('data-gagal') || 0);

        let activeFee = (status === 'berhasil') ? feeBerhasil : feeGagal;

        // Show custom nominal input if "Piket Event" is selected or category fee is 0
        if (categoryText.includes('piket event') || (feeBerhasil === 0 && feeGagal === 0)) {
            if (customContainer) customContainer.classList.remove('hidden');
            if (customInput && customInput.value !== '') {
                activeFee = parseInt(customInput.value || 0);
            }
        } else {
            if (customContainer) customContainer.classList.add('hidden');
        }

        document.getElementById('pricePreview').innerText = 'Rp ' + activeFee.toLocaleString('id-ID');
    }

    function openEditModal(job) {
        document.getElementById('editForm').action = `/job-orders/${job.id}`;
        document.getElementById('edit_kategori_info').value = job.kategori;
        document.getElementById('edit_status').value = job.status;
        document.getElementById('edit_tarif').value = job.tarif;
        
        const dateObj = new Date(job.tanggal);
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        document.getElementById('edit_tanggal').value = `${yyyy}-${mm}-${dd}`;
        
        document.getElementById('edit_catatan').value = job.catatan || '';
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    async function handleQuickJobFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-xs animate-spin">refresh</span> <span>MENYIMPAN...</span>';
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                form.reset();
                if (typeof updatePricePreview === 'function') updatePricePreview();
                
                showTemporaryToast(data.message || 'Job order berhasil dicatat!');
                
                if (typeof refreshDashboardData === 'function') {
                    refreshDashboardData();
                }
            } else {
                alert(data.message || 'Gagal menyimpan job order.');
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'SIMPAN JOB ORDER';
            }
        }
    }

    function showTemporaryToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 left-6 z-50 px-4 py-3 rounded-xl bg-emerald-950 border-2 border-emerald-500 text-emerald-300 font-bold text-xs shadow-2xl flex items-center gap-2 animate-bounce';
        toast.innerHTML = `<span class="material-symbols-outlined text-base">check_circle</span><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePricePreview();
    });
</script>
@endpush

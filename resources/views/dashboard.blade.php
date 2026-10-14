@extends('layouts.app')

@section('title', 'Kabin Kerja Teknisi - GajianARMN')

@section('content')
<div class="space-y-4 sm:space-y-5">

    <!-- 1. COMPACT HEADER BAR (Kabin Teknisi & Filter Bulan) -->
    <div class="rounded-2xl sm:rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-3.5 sm:p-4 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 relative overflow-hidden">
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex items-center gap-3 relative z-10">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 text-slate-950 flex items-center justify-center font-black shadow-md shadow-cyan-500/20 shrink-0">
                <span class="material-symbols-outlined text-xl">badge</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-cyan-300 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30">TEKNISI</span>
                    <span class="text-xs sm:text-sm font-extrabold text-white font-mono">{{ auth()->user()->name }}</span>
                </div>
                <p class="text-[11px] text-slate-400">Pencatatan Cepat &bull; Rekap Gaji &bull; Target 330 JO</p>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto relative z-10">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 hidden sm:inline">Periode:</span>
            <input 
                type="month" 
                name="bulan" 
                value="{{ $selectedBulan }}" 
                onchange="this.form.submit()" 
                class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-mono font-bold focus:outline-none focus:border-cyan-400 transition-all cursor-pointer"
            >
        </form>
    </div>

    <!-- 2. COMPACT BENTO KPI GRID (4 KARTU RINGKAS DI PALING ATAS - TIDAK PANJANG!) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        
        <!-- Bento 1: Hari Ini -->
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-xl border border-emerald-500/30 p-3.5 sm:p-4 shadow-lg flex flex-col justify-between hover:border-emerald-500/50 transition-all relative overflow-hidden group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-emerald-400">Hari Ini</span>
                </div>
                <span class="text-[10px] font-mono-num font-bold text-slate-400">
                    {{ \Carbon\Carbon::parse($today)->translatedFormat('d M') }}
                </span>
            </div>
            <div>
                <div id="metricPendapatanHariIni" class="text-lg sm:text-2xl font-black font-mono-num text-gradient-emerald tracking-tight truncate">
                    Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 text-[11px] mt-1 text-slate-400">
                    <span id="metricTotalJobHariIni" class="font-extrabold text-amber-400 font-mono-num">
                        {{ $totalJobHariIni }} <span class="text-[10px] text-slate-400 font-bold">JO</span>
                    </span>
                    <span>&bull;</span>
                    <span id="metricTotalPiketHariIni" class="font-extrabold text-cyan-300 font-mono-num">
                        {{ $totalPiketHariIni }} <span class="text-[10px] text-slate-400 font-bold">Piket</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Bento 2: Bulan Ini -->
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-xl border border-cyan-500/30 p-3.5 sm:p-4 shadow-lg flex flex-col justify-between hover:border-cyan-500/50 transition-all relative overflow-hidden group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-cyan-400">{{ $periodTitleCard }}</span>
                </div>
                <span class="text-[10px] font-mono-num font-bold text-slate-400 truncate max-w-[90px]">
                    {{ $periodLabel }}
                </span>
            </div>
            <div>
                <div id="metricPendapatanBulanIni" class="text-lg sm:text-2xl font-black font-mono-num text-gradient-cyan tracking-tight truncate">
                    Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 text-[11px] mt-1 text-slate-400">
                    <span id="metricTotalJobBulanIni" class="font-extrabold text-indigo-300 font-mono-num">
                        {{ $totalJobBulanIni }} <span class="text-[10px] text-slate-400 font-bold">JO</span>
                    </span>
                    <span>&bull;</span>
                    <span id="metricTotalPiketBulanIni" class="font-extrabold text-amber-300 font-mono-num">
                        {{ $totalPiketBulanIni }} <span class="text-[10px] text-slate-400 font-bold">Piket</span>
                    </span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricRataRataJoPerHari" class="hidden">Rata-rata {{ $rataRataJoPerHari }} JO/Hari</span>
            <span id="metricPendapatanJoBulanIni" class="hidden">Rp {{ number_format($pendapatanJoBulanIni, 0, ',', '.') }}</span>
            <span id="metricPendapatanPiketBulanIni" class="hidden">(Rp {{ number_format($pendapatanPiketBulanIni, 0, ',', '.') }})</span>
        </div>

        <!-- Bento 3: Target Gaji Bulanan -->
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 p-3.5 sm:p-4 shadow-lg flex flex-col justify-between hover:border-emerald-500/40 transition-all relative overflow-hidden group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-xs text-emerald-400">workspace_premium</span>
                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-300">Target Gaji</span>
                </div>
                <button type="button" onclick="openTargetModal()" class="text-[10px] text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-0.5" title="Ubah target gaji">
                    <span class="material-symbols-outlined text-[13px]">edit</span>
                    <span>Ubah</span>
                </button>
            </div>
            <div>
                <div id="metricTercapaiPendapatan" class="text-lg sm:text-2xl font-black font-mono-num text-emerald-400 tracking-tight truncate">
                    Rp {{ number_format($tercapaiPendapatan, 0, ',', '.') }}
                </div>
                <!-- Mini Progress Bar -->
                <div class="w-full h-1.5 bg-slate-950 rounded-full overflow-hidden border border-slate-800 my-1.5">
                    <div id="metricProgressBar" class="h-full bg-gradient-to-r from-emerald-500 to-cyan-400 rounded-full transition-all duration-700" style="width: {{ min(100, $persenTarget) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400">
                    <span class="truncate">Goal: <span id="metricTargetPendapatan" class="font-bold font-mono-num text-slate-300">Rp {{ number_format($targetPendapatan, 0, ',', '.') }}</span></span>
                    <span id="metricPersenTarget" class="font-black font-mono-num text-emerald-400 shrink-0 ml-1">{{ $persenTarget }}%</span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricSisaTarget" class="hidden">{{ $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉' }}</span>
            <span id="labelSisaHari" class="hidden">{{ $sisaHari > 0 ? "Perlu/Hari ({$sisaHari} Hari Sisa)" : "Perlu/Hari (Selesai)" }}</span>
            <span id="metricRataRataHarianDibutuhkan" class="hidden">{{ ($sisaTarget > 0 && $sisaHari > 0) ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0' }}</span>
        </div>

        <!-- Bento 4: Target Produktivitas 330 JO -->
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 p-3.5 sm:p-4 shadow-lg flex flex-col justify-between hover:border-amber-500/40 transition-all relative overflow-hidden group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-xs text-amber-400">trending_up</span>
                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-amber-400">Target 330 JO</span>
                </div>
                <span id="metricPersenJoVolume" class="text-[10px] font-black font-mono-num text-amber-400">
                    {{ $persenJoVolume }}%
                </span>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-black font-mono-num text-amber-400 tracking-tight truncate">
                    <span id="metricTercapaiJoVolumeText">{{ $tercapaiJoVolume }}</span> <span class="text-xs text-slate-400 font-bold">/ <span id="metricTargetJoVolume">{{ $targetJoVolume }} JO</span></span>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5">
                    <span id="metricTargetJoQtyPerHari" class="font-bold text-emerald-300 font-mono-num truncate">
                        {{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO / Hari' : 'Tercapai! 🎉' }}
                    </span>
                    <span class="text-slate-500 truncate ml-1">Sisa <span id="metricSisaJoVolume" class="text-amber-300 font-bold">{{ $sisaJoVolume > 0 ? $sisaJoVolume : 0 }}</span> JO</span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricTargetJoRevenuePerHari" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? 'Rp ' . number_format($targetJoRevenuePerHari, 0, ',', '.') . '/Hari' : 'Rp 0' }}</span>
            <span id="metricTargetJoQtyPerHariCard" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO / Hari' : 'Tercapai! 🎉' }}</span>
            <span id="metricTargetJoRevenuePerHariCard" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? 'Rp ' . number_format($targetJoRevenuePerHari, 0, ',', '.') . '/Hari' : 'Rp 0' }}</span>
        </div>

    </div>

    <!-- 3. USER-FRIENDLY SEGMENTED TABS (MENGURANGI PANJANG SCROLL HALAMAN!) -->
    <div class="sticky top-[60px] z-30 pt-1 pb-2">
        <div class="grid grid-cols-4 gap-1.5 sm:gap-2 p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur-xl">
            <!-- Tab 1: Catat Job -->
            <button 
                type="button" 
                id="tabBtn_input" 
                onclick="switchDashboardTab('input')" 
                class="dashboard-tab-btn py-2.5 sm:py-3 px-2 sm:px-4 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1 sm:gap-2 cursor-pointer bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black shadow-lg shadow-amber-500/20"
            >
                <span class="material-symbols-outlined text-base sm:text-lg">post_add</span>
                <span class="truncate">Catat Job</span>
            </button>

            <!-- Tab 2: Rekap Harian -->
            <button 
                type="button" 
                id="tabBtn_rekap" 
                onclick="switchDashboardTab('rekap')" 
                class="dashboard-tab-btn py-2.5 sm:py-3 px-2 sm:px-4 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1 sm:gap-2 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-bold"
            >
                <span class="material-symbols-outlined text-base sm:text-lg">calendar_month</span>
                <span class="truncate">Rekap Harian</span>
            </button>

            <!-- Tab 3: Riwayat Job -->
            <button 
                type="button" 
                id="tabBtn_riwayat" 
                onclick="switchDashboardTab('riwayat')" 
                class="dashboard-tab-btn py-2.5 sm:py-3 px-2 sm:px-4 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1 sm:gap-2 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-bold relative"
            >
                <span class="material-symbols-outlined text-base sm:text-lg">receipt_long</span>
                <span class="truncate">Riwayat</span>
                @if($detailJobOrders->total() > 0)
                    <span class="hidden sm:inline-block px-1.5 py-0.2 rounded-md bg-amber-400/20 text-amber-300 text-[10px] font-mono-num font-extrabold border border-amber-400/30">
                        {{ $detailJobOrders->total() }}
                    </span>
                @endif
            </button>

            <!-- Tab 4: Target & Tren -->
            <button 
                type="button" 
                id="tabBtn_target" 
                onclick="switchDashboardTab('target')" 
                class="dashboard-tab-btn py-2.5 sm:py-3 px-2 sm:px-4 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1 sm:gap-2 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-bold"
            >
                <span class="material-symbols-outlined text-base sm:text-lg">insights</span>
                <span class="truncate">Target &amp; Tren</span>
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB PANEL 1: CATAT JOB (FORMULIR CEPAT + DAFTAR INPUT HARI INI)            -->
    <!-- ========================================================================= -->
    <div id="tabPanel_input" class="dashboard-tab-panel space-y-4">
        
        <!-- Input Job Form Card (Sleek, Compact, User-Friendly) -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 p-4 sm:p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-400/10 border border-amber-400/30 text-amber-400 flex items-center justify-center font-black">
                        <span class="material-symbols-outlined text-lg">bolt</span>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black uppercase tracking-wider text-white">Form Input Job Order</h2>
                        <p class="text-[11px] text-slate-400">Pilih kategori, atur kuantitas, lalu simpan.</p>
                    </div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20">
                    1-Tap Entry
                </span>
            </div>

            <!-- Quick Category Chips (1-Sentuhan) -->
            <div class="space-y-1.5 mb-4">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-amber-400">touch_app</span>
                    <span>Kategori Populer:</span>
                </span>
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth py-1">
                    @foreach($tarifs->take(6) as $chipTarif)
                        <button type="button" 
                            onclick="selectQuickCategory({{ $chipTarif->id }})" 
                            class="quick-category-chip px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-400 text-slate-300 hover:text-amber-300 text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 shadow-sm active:scale-95 cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>{{ $chipTarif->kategori }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('job-orders.store') }}" method="POST" class="space-y-4" id="quickJobForm" onsubmit="handleQuickJobFormSubmit(event)">
                @csrf

                <!-- Form Grid: 4 Kolom di Desktop -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                    
                    <!-- 1. Kategori Dropdown (Col 4) -->
                    <div class="sm:col-span-4 space-y-1">
                        <label for="tarif_id" class="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-1">
                            <span>Kategori Tugas</span>
                            <span class="text-amber-400">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                name="tarif_id" 
                                id="tarif_id" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none transition-all min-h-[44px] cursor-pointer appearance-none pr-8"
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
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">unfold_more</span>
                        </div>
                    </div>

                    <!-- 2. Status Berhasil / Gagal (Col 3) -->
                    <div class="sm:col-span-3 space-y-1">
                        <label class="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-1">
                            <span>Status Job</span>
                            <span class="text-amber-400">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-1.5 min-h-[44px]">
                            <label class="cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="status" 
                                    value="berhasil" 
                                    class="peer sr-only" 
                                    checked 
                                    onchange="updatePricePreview()"
                                >
                                <div class="w-full h-full py-2 px-1 rounded-xl bg-slate-950 border border-slate-800 peer-checked:border-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:text-emerald-300 text-slate-400 font-extrabold text-center text-[11px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
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
                                <div class="w-full h-full py-2 px-1 rounded-xl bg-slate-950 border border-slate-800 peer-checked:border-rose-500 peer-checked:bg-rose-500/15 peer-checked:text-rose-300 text-slate-400 font-extrabold text-center text-[11px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">cancel</span>
                                    <span>GAGAL</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 3. Quantity Stepper (Col 2) -->
                    <div class="sm:col-span-2 space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="quantity" class="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Jumlah</label>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="adjustQuickQuantity(1)" class="px-1 py-0.2 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-bold cursor-pointer" title="Tambah 1">+1</button>
                                <button type="button" onclick="adjustQuickQuantity(5)" class="px-1 py-0.2 rounded bg-slate-800 hover:bg-slate-700 text-amber-300 text-[10px] font-bold cursor-pointer" title="Tambah 5">+5</button>
                            </div>
                        </div>
                        <div class="flex items-center rounded-xl bg-slate-950 border border-slate-700/80 overflow-hidden min-h-[44px]">
                            <button type="button" onclick="stepQuantity(-1)" class="px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors font-black text-sm cursor-pointer">−</button>
                            <input 
                                type="number" 
                                name="quantity" 
                                id="quantity" 
                                value="{{ old('quantity', 1) }}" 
                                min="1" 
                                max="100" 
                                required 
                                oninput="updatePricePreview()"
                                onchange="updatePricePreview()"
                                class="w-full bg-transparent text-white font-mono-num font-black text-center text-xs focus:outline-none py-1"
                            >
                            <button type="button" onclick="stepQuantity(1)" class="px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors font-black text-sm cursor-pointer">+</button>
                        </div>
                    </div>

                    <!-- 4. Tanggal (Col 3) -->
                    <div class="sm:col-span-3 space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="tanggal" class="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-1">
                                <span>Tanggal</span>
                                <span class="text-amber-400">*</span>
                            </label>
                            <div class="flex items-center gap-1 text-[10px]">
                                <button type="button" onclick="setQuickFormDate('today')" class="px-1.5 py-0.5 rounded bg-slate-800 hover:bg-amber-400/20 hover:text-amber-300 text-slate-300 font-bold transition-all cursor-pointer border border-slate-700">Hari Ini</button>
                                <button type="button" onclick="setQuickFormDate('yesterday')" class="px-1.5 py-0.5 rounded bg-slate-800 hover:bg-amber-400/20 hover:text-amber-300 text-slate-300 font-bold transition-all cursor-pointer border border-slate-700">Kemarin</button>
                            </div>
                        </div>
                        <input 
                            type="date" 
                            name="tanggal" 
                            id="tanggal" 
                            value="{{ old('tanggal', $today) }}" 
                            required 
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none transition-all min-h-[44px]"
                        >
                    </div>
                </div>

                <!-- Form Row 2: Catatan (Voice-to-Text) & Custom Tarif (Piket Event) -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-8 relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-base">edit_note</span>
                        <input 
                            type="text" 
                            name="catatan" 
                            id="catatanInput"
                            placeholder="Catatan tambahan / No. Merchant / Lokasi (opsional)" 
                            value="{{ old('catatan') }}"
                            class="w-full pl-9 pr-10 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-amber-400 focus:outline-none transition-all min-h-[42px]"
                        >
                        <button type="button" onclick="toggleVoiceInput('catatanInput', this)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-amber-400 transition-colors cursor-pointer" title="Input suara (Speech-to-Text)">
                            <span class="material-symbols-outlined text-base">mic</span>
                        </button>
                    </div>

                    <!-- Custom Nominal Input (Piket Event) -->
                    <div id="customTarifContainer" class="sm:col-span-4 hidden">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-amber-400 font-bold text-xs">Rp</span>
                            <input 
                                type="number" 
                                name="custom_tarif" 
                                id="custom_tarif" 
                                min="0" 
                                step="1000"
                                placeholder="Nominal Custom (Rp)" 
                                oninput="updatePricePreview()"
                                class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-950 border border-amber-400 text-amber-300 font-mono-num font-extrabold text-xs focus:outline-none min-h-[42px]"
                            >
                        </div>
                    </div>
                </div>

                <!-- Action & Price Preview Row -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-slate-800/80">
                    <div class="px-4 py-2 rounded-xl bg-slate-950 border border-emerald-500/30 flex items-center justify-between sm:justify-start gap-3 shadow-inner">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Snapshot Tarif:</span>
                        </div>
                        <span id="pricePreview" class="text-base sm:text-lg font-black font-mono-num text-gradient-emerald tracking-tight">Rp 0</span>
                    </div>

                    <button 
                        type="submit" 
                        class="py-3 px-6 rounded-xl bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 hover:from-amber-300 hover:to-orange-300 active:scale-[0.98] text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition-all cursor-pointer flex items-center justify-center gap-2 border border-amber-300/30 min-h-[46px]"
                    >
                        <span class="material-symbols-outlined text-lg">save</span>
                        <span>SIMPAN JOB ORDER</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- DAFTAR JOB TERCATAT HARI INI (Langsung terlihat di bawah form tanpa perlu scroll ke bawah!) -->
        <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-4 sm:p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400 text-lg">history_toggle_off</span>
                    <h3 class="text-xs sm:text-sm font-black uppercase text-white tracking-wider">Job Tercatat Hari Ini</h3>
                    <span class="text-[10px] font-mono-num font-bold text-slate-400">({{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }})</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-400/10 text-emerald-300 text-[10px] font-mono-num font-extrabold border border-emerald-400/20">
                    {{ $todayJobOrders->count() }} JO &bull; Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </span>
            </div>

            @if($todayJobOrders->count() > 0)
                <div class="space-y-2">
                    @foreach($todayJobOrders as $tJob)
                        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $tJob->status === 'berhasil' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-white">{{ $tJob->kategori }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wider {{ $tJob->status === 'berhasil' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-300 border border-rose-500/30' }}">
                                            {{ $tJob->status }}
                                        </span>
                                    </div>
                                    @if($tJob->catatan)
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $tJob->catatan }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto pt-1 sm:pt-0 border-t sm:border-t-0 border-slate-800/60">
                                <span class="text-xs font-black font-mono-num text-amber-400">
                                    Rp {{ number_format($tJob->tarif, 0, ',', '.') }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ json_encode($tJob) }})" 
                                        class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 transition-all" 
                                        title="Edit"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <form action="{{ route('job-orders.destroy', $tJob->id) }}" method="POST" onsubmit="return confirm('Hapus job order ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 transition-all" title="Hapus">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 px-4 text-center rounded-2xl bg-slate-950/60 border border-slate-800/60">
                    <span class="material-symbols-outlined text-slate-600 text-3xl mb-1">note_alt</span>
                    <p class="text-xs text-slate-400 font-medium">Belum ada job order yang dicatat hari ini.</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Gunakan formulir di atas untuk mulai mencatat pekerjaan harian Anda.</p>
                </div>
            @endif
        </div>

    </div>


    <!-- ========================================================================= -->
    <!-- TAB PANEL 2: REKAP HARIAN (RINGKASAN TANGGAL & EKSPOR CSV/PDF)            -->
    <!-- ========================================================================= -->
    <div id="tabPanel_rekap" class="dashboard-tab-panel space-y-4 hidden">
        
        <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-4 sm:p-6 shadow-2xl space-y-4">
            
            <!-- Header & Export Action -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-400 text-lg">calendar_month</span>
                        <span>Rekap Harian ({{ $periodLabel }})</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Total job dan akumulasi pendapatan per tanggal.</p>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a 
                        href="{{ route('export.csv', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                        class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl bg-emerald-950/80 hover:bg-emerald-900/80 border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base">download</span>
                        <span>CSV</span>
                    </a>

                    <a 
                        href="{{ route('export.pdf', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                        target="_blank"
                        class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl bg-cyan-950/80 hover:bg-cyan-900/80 border border-cyan-500/40 text-cyan-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base">print</span>
                        <span>PDF</span>
                    </a>
                </div>
            </div>

            <!-- Filter Controls (Bulan vs Rentang Tanggal) -->
            <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 sm:p-4 space-y-3">
                <div class="flex items-center gap-2 border-b border-slate-800/80 pb-2.5">
                    <button 
                        type="button"
                        onclick="switchFilterMode('bulan')"
                        id="tabBulan"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all cursor-pointer {{ (!$startDate && !$endDate) ? 'bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}"
                    >
                        Per Bulan
                    </button>
                    <button 
                        type="button"
                        onclick="switchFilterMode('rentang')"
                        id="tabRentang"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all cursor-pointer {{ ($startDate || $endDate) ? 'bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}"
                    >
                        Rentang Tanggal
                    </button>
                    
                    @if($startDate || $endDate || $selectedBulan !== \Carbon\Carbon::now()->format('Y-m'))
                        <a href="{{ route('dashboard') }}" class="ml-auto text-[11px] text-rose-400 hover:underline font-bold uppercase tracking-wider">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <!-- Form Filter Per Bulan -->
                <form id="formFilterBulan" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'hidden' : 'block' }}">
                    <div class="flex flex-col sm:flex-row items-end gap-2.5">
                        <div class="w-full sm:w-60 space-y-1">
                            <label for="bulan" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Bulan &amp; Tahun</label>
                            <input 
                                type="month" 
                                name="bulan" 
                                id="bulan" 
                                value="{{ $selectedBulan }}" 
                                onchange="this.form.submit()"
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none"
                            >
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition-all">
                            Tampilkan
                        </button>
                    </div>
                </form>

                <!-- Form Filter Rentang Tanggal -->
                <form id="formFilterRentang" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'block' : 'hidden' }}">
                    <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 items-end">
                        <div class="space-y-1">
                            <label for="start_date" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Dari Tanggal</label>
                            <input 
                                type="date" 
                                name="start_date" 
                                id="start_date" 
                                value="{{ $startDate }}" 
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="end_date" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sampai Tanggal</label>
                            <input 
                                type="date" 
                                name="end_date" 
                                id="end_date" 
                                value="{{ $endDate }}" 
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none"
                            >
                        </div>

                        <div>
                            <button 
                                type="submit" 
                                class="w-full py-2 px-5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider transition-all"
                            >
                                Terapkan Rentang
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Desktop -->
            <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Tanggal Pekerjaan</th>
                            <th class="py-3 px-4 text-center">Volume JO</th>
                            <th class="py-3 px-4 text-right">Pendapatan Harian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 font-medium">
                        @forelse($rekapHarian as $rekap)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-200">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-400/80 text-sm">event</span>
                                        <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('d F Y') }}</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-mono-num font-bold text-amber-300">
                                    {{ $rekap->total_job }} JO
                                    @if(($rekap->total_piket ?? 0) > 0)
                                        <span class="text-cyan-400 text-[11px] ml-1">(+{{ $rekap->total_piket }} Piket)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono-num font-black text-emerald-400">
                                    Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-500 uppercase tracking-widest font-bold text-xs">
                                    Belum ada transaksi job order di periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-950/90 border-t-2 border-amber-400/50 font-black text-xs text-slate-100">
                        <tr>
                            <td class="py-3.5 px-4 uppercase tracking-wider text-amber-400">
                                TOTAL AKUMULASI
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono-num text-amber-300 font-black">
                                {{ $rekapHarian->sum('total_job') }} JO
                                @if($rekapHarian->sum('total_piket') > 0)
                                    <span class="text-cyan-400 text-[11px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num text-emerald-400 text-sm sm:text-base font-black">
                                Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Cards Mobile -->
            <div class="block sm:hidden space-y-2">
                @forelse($rekapHarian as $rekap)
                    <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="font-bold text-slate-200 text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-xs">event</span>
                                <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('d M Y') }}</span>
                            </span>
                            <span class="text-xs font-mono-num font-black text-emerald-400">
                                Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Volume:</span>
                            <span class="font-mono-num font-bold text-amber-400">
                                {{ $rekap->total_job }} JO
                                @if(($rekap->total_piket ?? 0) > 0)
                                    <span class="text-cyan-300 ml-1">+ {{ $rekap->total_piket }} Piket</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-xl bg-slate-950/80 border border-slate-800">
                        Belum ada transaksi di periode ini.
                    </div>
                @endforelse

                @if($rekapHarian->count() > 0)
                    <div class="p-3.5 rounded-xl bg-slate-950/90 border border-amber-400/50 space-y-1.5 shadow-lg">
                        <div class="text-[11px] font-black uppercase text-amber-400 tracking-wider">
                            Total Akumulasi Periode
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-800/80 pt-1.5">
                            <span class="font-mono-num text-xs font-bold text-amber-300">
                                {{ $rekapHarian->sum('total_job') }} JO
                                @if($rekapHarian->sum('total_piket') > 0)
                                    <span class="text-cyan-400 text-[10px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                                @endif
                            </span>
                            <span class="text-base font-black font-mono-num text-emerald-400">
                                Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>


    <!-- ========================================================================= -->
    <!-- TAB PANEL 3: RIWAYAT TRANSAKSI (DETAIL JO, EDIT & HAPUS)                  -->
    <!-- ========================================================================= -->
    <div id="tabPanel_riwayat" class="dashboard-tab-panel space-y-4 hidden">
        
        <div class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-4 sm:p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-400 text-lg">receipt_long</span>
                        <span>Detail Riwayat Job Order</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Rincian seluruh transaksi dengan tombol edit &amp; hapus.</p>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 text-xs font-mono font-bold">
                    Total: {{ $detailJobOrders->total() }} Data
                </span>
            </div>

            <!-- Desktop View Table -->
            <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3.5">#</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Kategori</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-right">Tarif</th>
                            <th class="py-3 px-3.5">Catatan</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 font-medium">
                        @forelse($detailJobOrders as $index => $job)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3.5 text-slate-500 font-mono-num">
                                    {{ $detailJobOrders->firstItem() + $index }}
                                </td>
                                <td class="py-3 px-3.5 font-mono-num text-slate-300 font-bold whitespace-nowrap">
                                    {{ $job->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 font-bold text-white">
                                    {{ $job->kategori }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($job->status === 'berhasil')
                                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 font-black text-[9px] uppercase tracking-wider">
                                            BERHASIL
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-lg bg-rose-950/80 border border-rose-500/40 text-rose-300 font-black text-[9px] uppercase tracking-wider">
                                            GAGAL
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono-num font-black text-amber-400 whitespace-nowrap">
                                    Rp {{ number_format($job->tarif, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-400 max-w-xs truncate">
                                    {{ $job->catatan ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            onclick="openEditModal({{ json_encode($job) }})" 
                                            class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 transition-all" 
                                            title="Edit"
                                        >
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>

                                        <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 transition-all" title="Hapus">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 uppercase tracking-widest font-bold text-xs">
                                    Tidak ada data job order ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="block sm:hidden space-y-2.5">
                @forelse($detailJobOrders as $index => $job)
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-mono-num text-slate-500 font-bold">#{{ $detailJobOrders->firstItem() + $index }}</span>
                                <span class="font-mono-num font-bold text-slate-300 text-xs flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs text-slate-500">calendar_today</span>
                                    <span>{{ $job->tanggal->format('d/m/Y') }}</span>
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $job->status === 'berhasil' ? 'bg-emerald-950/80 border border-emerald-500/40 text-emerald-400' : 'bg-rose-950/80 border border-rose-500/40 text-rose-300' }}">
                                {{ $job->status }}
                            </span>
                        </div>

                        <div>
                            <h4 class="font-bold text-white text-xs">{{ $job->kategori }}</h4>
                            @if($job->catatan)
                                <p class="text-[11px] text-slate-400 bg-slate-900/60 p-2 rounded-xl border border-slate-800 mt-1">
                                    {{ $job->catatan }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-800/80">
                            <span class="font-mono-num font-black text-amber-400 text-sm">
                                Rp {{ number_format($job->tarif, 0, ',', '.') }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                <button 
                                    onclick="openEditModal({{ json_encode($job) }})" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1"
                                >
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Hapus job order ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">delete</span>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-2xl bg-slate-950/80 border border-slate-800">
                        Tidak ada data job order.
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


    <!-- ========================================================================= -->
    <!-- TAB PANEL 4: TARGET & GRAFIK TREN (GOAL METER & CHART.JS)                 -->
    <!-- ========================================================================= -->
    <div id="tabPanel_target" class="dashboard-tab-panel space-y-4 hidden">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            
            <!-- Goal Meter Card (5 Col) -->
            <div class="lg:col-span-5 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 shadow-2xl flex flex-col justify-between space-y-4 relative overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-400 to-cyan-500 text-slate-950 flex items-center justify-center font-black shadow-md shadow-emerald-500/20">
                            <span class="material-symbols-outlined text-lg">workspace_premium</span>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-white">Target Gaji Bulanan</h3>
                            <p class="text-[11px] text-slate-400">Personal Goal Meter</p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="openTargetModal()" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs font-extrabold flex items-center gap-1 border border-emerald-500/30 transition-all">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>Ubah</span>
                    </button>
                </div>

                <!-- Main Target Numbers -->
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Tercapai</span>
                        <div class="text-xl sm:text-2xl font-black font-mono-num text-emerald-400">
                            Rp {{ number_format($tercapaiPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Target</span>
                        <div class="text-base sm:text-lg font-extrabold font-mono-num text-slate-300">
                            Rp {{ number_format($targetPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-xs font-black">
                        <span class="text-slate-400 text-[11px]">Pencapaian:</span>
                        <span class="{{ $persenTarget >= 100 ? 'text-emerald-400' : 'text-cyan-400' }} font-mono-num font-extrabold">{{ $persenTarget }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                        <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 rounded-full transition-all duration-1000" style="width: {{ min(100, $persenTarget) }}%"></div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/60">
                    <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/60">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Sisa Target</span>
                        <span class="text-xs sm:text-sm font-black font-mono-num {{ $sisaTarget > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/60">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">
                            {{ $sisaHari > 0 ? "Perlu/Hari ({$sisaHari} Hari)" : "Perlu/Hari" }}
                        </span>
                        <span class="text-xs sm:text-sm font-black font-mono-num text-cyan-300">
                            {{ ($sisaTarget > 0 && $sisaHari > 0) ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                </div>

                <!-- 330 JO Box -->
                <div class="p-3 rounded-2xl bg-slate-950/80 border border-emerald-500/30 flex items-center justify-between gap-2 shadow-inner">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block">
                            Target Produktivitas: 330 JO
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 block mt-0.5">
                            Tercapai <span class="text-white font-extrabold">{{ $tercapaiJoVolume }} JO</span> ({{ $persenJoVolume }}%)
                        </span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black font-mono-num text-emerald-300 block">
                            {{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO/Hari' : 'Tercapai! 🎉' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Trend Line Chart Card (7 Col) -->
            <div class="lg:col-span-7 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 shadow-2xl flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-500 to-indigo-500 text-slate-950 flex items-center justify-center font-black shadow-md shadow-indigo-500/20">
                            <span class="material-symbols-outlined text-base">show_chart</span>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-white">Trend Pendapatan Harian</h3>
                            <p class="text-[11px] text-slate-400">Grafik perolehan ({{ $periodLabel }})</p>
                        </div>
                    </div>
                </div>

                <div class="relative w-full h-56 sm:h-64">
                    <canvas id="trendChartCanvas"></canvas>
                </div>
            </div>

        </div>

    </div>

</div>


<!-- MODAL EDIT TARGET PENDAPATAN -->
<div id="targetModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center hidden p-4 transition-all duration-300">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl relative">
        <button type="button" onclick="closeTargetModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">flag</span>
            </div>
            <div>
                <h3 class="text-base font-black text-white">Set Target Pendapatan</h3>
                <p class="text-xs text-slate-400">Atur target pencapaian gaji bulanan Anda</p>
            </div>
        </div>

        <form action="{{ route('user.target.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Target Pendapatan (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                    <input type="number" step="500000" min="0" name="target_pendapatan" id="targetInput" value="{{ auth()->user()->target_pendapatan ?? 5000000 }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-3 text-white font-mono-num font-bold text-lg focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>
            </div>

            <!-- Quick presets -->
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Pilihan Cepat:</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="setPresetTarget(3000000)" class="py-2 px-2 bg-slate-800 hover:bg-emerald-600/30 border border-slate-700 hover:border-emerald-500/50 rounded-xl text-xs font-bold text-slate-300 hover:text-emerald-400 transition-all">3 Jt</button>
                    <button type="button" onclick="setPresetTarget(5000000)" class="py-2 px-2 bg-slate-800 hover:bg-emerald-600/30 border border-slate-700 hover:border-emerald-500/50 rounded-xl text-xs font-bold text-slate-300 hover:text-emerald-400 transition-all">5 Jt</button>
                    <button type="button" onclick="setPresetTarget(7500000)" class="py-2 px-2 bg-slate-800 hover:bg-emerald-600/30 border border-slate-700 hover:border-emerald-500/50 rounded-xl text-xs font-bold text-slate-300 hover:text-emerald-400 transition-all">7.5 Jt</button>
                    <button type="button" onclick="setPresetTarget(10000000)" class="py-2 px-2 bg-slate-800 hover:bg-emerald-600/30 border border-slate-700 hover:border-emerald-500/50 rounded-xl text-xs font-bold text-slate-300 hover:text-emerald-400 transition-all">10 Jt</button>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTargetModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 font-bold text-xs uppercase tracking-wider">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition-all">Simpan Target</button>
            </div>
        </form>
    </div>
</div>


<!-- MODAL EDIT JOB ORDER -->
<div id="editModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900/95 border border-amber-400/60 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h3 class="text-sm sm:text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-lg">edit</span>
                <span>Edit Job Order</span>
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white font-bold text-xs uppercase tracking-wider p-1 cursor-pointer">
                Tutup
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label for="edit_kategori_info" class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                    Kategori Tugas
                </label>
                <input type="text" id="edit_kategori_info" readonly class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 font-bold text-xs cursor-not-allowed" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Status</label>
                    <select name="status" id="edit_status" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]">
                        <option value="berhasil">BERHASIL</option>
                        <option value="gagal">GAGAL</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="edit_tanggal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
                </div>
            </div>

            <div class="space-y-1">
                <label for="edit_tarif" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Nominal Tarif (Rp)</label>
                <input type="number" name="tarif" id="edit_tarif" required min="0" step="500" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
            </div>

            <div class="space-y-1">
                <label for="edit_catatan" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Catatan</label>
                <input type="text" name="catatan" id="edit_catatan" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700/80 text-white text-xs focus:border-amber-400 focus:outline-none min-h-[42px]" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all cursor-pointer shadow-lg shadow-amber-500/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    let trendChartInstance = null;

    // Tab Switching System with URL Hash & localStorage Support
    function switchDashboardTab(tabId) {
        const panels = document.querySelectorAll('.dashboard-tab-panel');
        panels.forEach(panel => panel.classList.add('hidden'));

        const buttons = document.querySelectorAll('.dashboard-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-gradient-to-r', 'from-amber-400', 'to-orange-400', 'text-slate-950', 'font-black', 'shadow-lg', 'shadow-amber-500/20');
            btn.classList.add('text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800/60', 'font-bold');
        });

        const activePanel = document.getElementById('tabPanel_' + tabId);
        const activeBtn = document.getElementById('tabBtn_' + tabId);

        if (activePanel) {
            activePanel.classList.remove('hidden');
        }
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800/60', 'font-bold');
            activeBtn.classList.add('bg-gradient-to-r', 'from-amber-400', 'to-orange-400', 'text-slate-950', 'font-black', 'shadow-lg', 'shadow-amber-500/20');
        }

        localStorage.setItem('active_technician_tab', tabId);

        // Resize chart if target tab is activated
        if (tabId === 'target' && trendChartInstance) {
            setTimeout(() => {
                trendChartInstance.resize();
            }, 60);
        }
    }

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

            tabBulan.className = "px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 shadow";
            tabRentang.className = "px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-800/80 text-slate-400 hover:text-slate-200";
        } else {
            formRentang.classList.remove('hidden');
            formRentang.classList.add('block');
            formBulan.classList.remove('block');
            formBulan.classList.add('hidden');

            tabRentang.className = "px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 shadow";
            tabBulan.className = "px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-800/80 text-slate-400 hover:text-slate-200";
        }
    }

    function updatePricePreview() {
        const select = document.getElementById('tarif_id');
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        const status = document.querySelector('input[name="status"]:checked')?.value || 'berhasil';
        const customContainer = document.getElementById('customTarifContainer');
        const customInput = document.getElementById('custom_tarif');
        const qtyInput = document.getElementById('quantity');
        const qty = Math.max(1, parseInt(qtyInput?.value || 1));
        
        if (!selectedOption || !selectedOption.value) {
            const pricePreview = document.getElementById('pricePreview');
            if (pricePreview) pricePreview.innerText = 'Rp 0';
            if (customContainer) customContainer.classList.add('hidden');
            return;
        }

        const categoryText = selectedOption.text.trim().toLowerCase();
        const feeBerhasil = parseInt(selectedOption.getAttribute('data-berhasil') || 0);
        const feeGagal = parseInt(selectedOption.getAttribute('data-gagal') || 0);

        let activeFee = (status === 'berhasil') ? feeBerhasil : feeGagal;

        if (categoryText.includes('piket event') || (feeBerhasil === 0 && feeGagal === 0)) {
            if (customContainer) customContainer.classList.remove('hidden');
            if (customInput && customInput.value !== '') {
                activeFee = parseInt(customInput.value || 0);
            }
        } else {
            if (customContainer) customContainer.classList.add('hidden');
        }

        const totalFee = activeFee * qty;
        if (qty > 1) {
            document.getElementById('pricePreview').innerHTML = 'Rp ' + totalFee.toLocaleString('id-ID') + ' <span class="text-xs font-semibold text-amber-300/80">(' + qty + ' JO x Rp ' + activeFee.toLocaleString('id-ID') + ')</span>';
        } else {
            document.getElementById('pricePreview').innerText = 'Rp ' + activeFee.toLocaleString('id-ID');
        }
    }

    function selectQuickCategory(id) {
        const select = document.getElementById('tarif_id');
        if (!select) return;
        select.value = id;

        document.querySelectorAll('.quick-category-chip').forEach(chip => {
            chip.classList.remove('border-amber-400', 'bg-amber-400/20', 'text-amber-300');
            chip.classList.add('border-slate-800', 'bg-slate-950', 'text-slate-300');
        });

        const activeChip = Array.from(document.querySelectorAll('.quick-category-chip')).find(c => c.getAttribute('onclick')?.includes(`(${id})`));
        if (activeChip) {
            activeChip.classList.remove('border-slate-800', 'bg-slate-950', 'text-slate-300');
            activeChip.classList.add('border-amber-400', 'bg-amber-400/20', 'text-amber-300');
        }

        updatePricePreview();
    }

    function stepQuantity(delta) {
        const qtyInput = document.getElementById('quantity');
        if (!qtyInput) return;
        let val = parseInt(qtyInput.value || 1) + delta;
        val = Math.max(1, Math.min(100, val));
        qtyInput.value = val;
        updatePricePreview();
    }

    function adjustQuickQuantity(delta) {
        const qtyInput = document.getElementById('quantity');
        if (!qtyInput) return;
        let val = parseInt(qtyInput.value || 0) + delta;
        val = Math.max(1, Math.min(100, val));
        qtyInput.value = val;
        updatePricePreview();
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

    function openTargetModal() {
        document.getElementById('targetModal').classList.remove('hidden');
    }
    function closeTargetModal() {
        document.getElementById('targetModal').classList.add('hidden');
    }
    function setPresetTarget(val) {
        document.getElementById('targetInput').value = val;
    }

    async function handleQuickJobFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> <span>MENYIMPAN...</span>';
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
                const qtyInput = document.getElementById('quantity');
                if (qtyInput) qtyInput.value = '1';

                if (typeof updatePricePreview === 'function') updatePricePreview();
                showTemporaryToast(data.message || 'Job order berhasil dicatat!');

                if (typeof refreshDashboardData === 'function') {
                    refreshDashboardData();
                }

                // Smooth reload to immediately sync today's jobs table and statistics
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert(data.message || 'Gagal menyimpan job order.');
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span><span>SIMPAN JOB ORDER</span>';
            }
        }
    }

    function showTemporaryToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 left-6 z-50 px-4 py-3 rounded-2xl bg-emerald-950/90 border border-emerald-500/50 text-emerald-300 font-bold text-xs shadow-2xl backdrop-blur-xl flex items-center gap-2 animate-bounce';
        toast.innerHTML = `<span class="material-symbols-outlined text-lg">check_circle</span><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    // Voice-to-Text Speech Recognition
    let recognitionInstance = null;
    let isRecognizing = false;

    function toggleVoiceInput(inputId, btn) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('Browser belum mendukung pengenalan suara. Silakan gunakan Google Chrome / Microsoft Edge.');
            return;
        }

        const input = document.getElementById(inputId);
        if (!input) return;

        if (isRecognizing && recognitionInstance) {
            recognitionInstance.stop();
            return;
        }

        try {
            const recognition = new SpeechRecognition();
            recognitionInstance = recognition;
            recognition.lang = 'id-ID';
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.onstart = function() {
                isRecognizing = true;
                if (btn) {
                    btn.classList.add('text-rose-400', 'animate-pulse');
                    btn.classList.remove('text-slate-400');
                }
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                if (transcript) {
                    const existing = input.value.trim();
                    input.value = existing ? `${existing} ${transcript}` : transcript;
                }
            };

            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
            };

            recognition.onend = function() {
                isRecognizing = false;
                if (btn) {
                    btn.classList.remove('text-rose-400', 'animate-pulse');
                    btn.classList.add('text-slate-400');
                }
            };

            recognition.start();
        } catch (err) {
            console.error('Error starting speech recognition:', err);
            isRecognizing = false;
            if (btn) {
                btn.classList.remove('text-rose-400', 'animate-pulse');
                btn.classList.add('text-slate-400');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePricePreview();

        // Check which tab to activate
        const urlParams = new URLSearchParams(window.location.search);
        const urlHash = window.location.hash.replace('#', '');
        const savedTab = localStorage.getItem('active_technician_tab');

        if (urlParams.has('page') || urlParams.get('tab') === 'riwayat') {
            switchDashboardTab('riwayat');
        } else if (urlParams.has('tanggal') || urlParams.has('start_date') || urlParams.get('tab') === 'rekap') {
            switchDashboardTab('rekap');
        } else if (urlHash && ['input', 'rekap', 'riwayat', 'target'].includes(urlHash)) {
            switchDashboardTab(urlHash);
        } else if (savedTab && ['input', 'rekap', 'riwayat', 'target'].includes(savedTab)) {
            switchDashboardTab(savedTab);
        } else {
            switchDashboardTab('input');
        }

        // Initialize Trend Line Chart
        const ctx = document.getElementById('trendChartCanvas');
        if (ctx) {
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const incomeData = {!! json_encode($chartIncomeData ?? []) !!};

            trendChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: incomeData,
                        borderColor: '#10b981',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#e2e8f0',
                            bodyColor: '#34d399',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Gaji: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 10, weight: 'bold' } }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 10, weight: 'bold' },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + ' Jt';
                                    if (value >= 1000) return (value / 1000) + ' Rb';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

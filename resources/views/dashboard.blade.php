@extends('layouts.app')

@section('title', 'Kabin Kerja Teknisi - GajianARMN')

@push('styles')
<style>
    @keyframes tabFadeIn {
        0% { opacity: 0; transform: translateY(8px) scale(0.995); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-tab-fade {
        animation: tabFadeIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes pricePop {
        0% { transform: scale(0.9); filter: brightness(1.3); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); filter: brightness(1); }
    }
    .animate-price-pop {
        animation: pricePop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes lightSweep {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(250%) skewX(-20deg); }
    }
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: '';
        position: absolute;
        top: -50%;
        bottom: -50%;
        left: 0;
        width: 60%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transform: translateX(-150%) skewX(-20deg);
        pointer-events: none;
    }
    .btn-shine:hover::after {
        animation: lightSweep 0.85s ease-out;
    }

    @keyframes ctaBreathing {
        0%, 100% {
            box-shadow: 0 4px 20px -2px rgba(245, 158, 11, 0.35), 0 0 0 1px rgba(251, 191, 36, 0.25);
        }
        50% {
            box-shadow: 0 8px 30px 2px rgba(245, 158, 11, 0.6), 0 0 0 2px rgba(251, 191, 36, 0.5);
        }
    }
    .animate-cta-glow {
        animation: ctaBreathing 3s ease-in-out infinite;
    }

    @keyframes micRipple {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6), 0 0 12px rgba(239, 68, 68, 0.4);
        }
        70% {
            box-shadow: 0 0 0 12px rgba(239, 68, 68, 0), 0 0 20px rgba(239, 68, 68, 0.2);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0), 0 0 0 rgba(239, 68, 68, 0);
        }
    }
    .animate-mic-active {
        animation: micRipple 1.4s infinite cubic-bezier(0, 0, 0.2, 1);
    }

    .card-hover-glow {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .card-hover-glow:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.6), 0 0 20px -4px rgba(251, 191, 36, 0.15);
    }

    .quick-category-chip {
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .quick-category-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px -4px rgba(245, 158, 11, 0.25);
    }
    .quick-category-chip:active {
        transform: scale(0.96);
    }

    .stepper-btn {
        transition: all 0.15s ease;
    }
    .stepper-btn:hover {
        background-color: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }
    .stepper-btn:active {
        transform: scale(0.85);
    }

    @keyframes modalPop {
        0% { opacity: 0; transform: scale(0.95) translateY(12px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-pop {
        animation: modalPop 0.24s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes toastSlideIn {
        0% {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .toast-enter {
        animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-5">

    <!-- 1. COMPACT HEADER BAR (Kabin Teknisi & Filter Bulan) -->
    <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 relative card-hover-glow">
        <div class="flex items-center gap-3 relative z-10">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-cyan-400/10 border border-cyan-400/25 text-cyan-400 flex items-center justify-center font-semibold shrink-0">
                <span class="material-symbols-outlined text-xl">badge</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-semibold tracking-wider text-cyan-300 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">TEKNISI</span>
                    <span class="text-xs sm:text-sm font-semibold text-white font-mono">{{ auth()->user()->name }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Pencatatan Pekerjaan &bull; Rekap Gaji &bull; Target 330 JO</p>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto relative z-10">
            <span class="text-xs font-medium text-slate-400 hidden sm:inline">Periode:</span>
            <input 
                type="month" 
                name="bulan" 
                value="{{ $selectedBulan }}" 
                onchange="this.form.submit()" 
                class="w-full sm:w-auto px-3.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-mono font-medium focus:outline-none focus:border-cyan-400/80 hover:border-slate-700 transition-all cursor-pointer"
            >
        </form>
    </div>

    <!-- 2. COMPACT BENTO KPI GRID (4 KARTU RINGKAS DI PALING ATAS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        
        <!-- Bento 1: Hari Ini -->
        <div class="card-hover-glow rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex flex-col justify-between hover:border-slate-700 transition-all relative group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <span class="text-xs font-semibold text-slate-300">Hari Ini</span>
                <span class="text-[11px] font-mono-num text-slate-400">
                    {{ \Carbon\Carbon::parse($today)->translatedFormat('d M') }}
                </span>
            </div>
            <div>
                <div id="metricPendapatanHariIni" class="text-lg sm:text-2xl font-bold font-mono-num text-emerald-400 tracking-tight truncate">
                    Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 text-xs mt-1 text-slate-400">
                    <span id="metricTotalJobHariIni" class="font-semibold text-slate-200 font-mono-num">
                        {{ $totalJobHariIni }} <span class="text-slate-400 font-normal">JO</span>
                    </span>
                    <span>&bull;</span>
                    <span id="metricTotalPiketHariIni" class="font-semibold text-slate-200 font-mono-num">
                        {{ $totalPiketHariIni }} <span class="text-slate-400 font-normal">Piket</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Bento 2: Bulan Ini -->
        <div class="card-hover-glow rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex flex-col justify-between hover:border-slate-700 transition-all relative group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <span class="text-xs font-semibold text-slate-300">{{ $periodTitleCard }}</span>
                <span class="text-[11px] font-mono-num text-slate-400 truncate max-w-[90px]">
                    {{ $periodLabel }}
                </span>
            </div>
            <div>
                <div id="metricPendapatanBulanIni" class="text-lg sm:text-2xl font-bold font-mono-num text-sky-400 tracking-tight truncate">
                    Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 text-xs mt-1 text-slate-400">
                    <span id="metricTotalJobBulanIni" class="font-semibold text-slate-200 font-mono-num">
                        {{ $totalJobBulanIni }} <span class="text-slate-400 font-normal">JO</span>
                    </span>
                    <span>&bull;</span>
                    <span id="metricTotalPiketBulanIni" class="font-semibold text-slate-200 font-mono-num">
                        {{ $totalPiketBulanIni }} <span class="text-slate-400 font-normal">Piket</span>
                    </span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricRataRataJoPerHari" class="hidden">Rata-rata {{ $rataRataJoPerHari }} JO/Hari</span>
            <span id="metricPendapatanJoBulanIni" class="hidden">Rp {{ number_format($pendapatanJoBulanIni, 0, ',', '.') }}</span>
            <span id="metricPendapatanPiketBulanIni" class="hidden">(Rp {{ number_format($pendapatanPiketBulanIni, 0, ',', '.') }})</span>
        </div>

        <!-- Bento 3: Target Gaji Bulanan -->
        <div class="card-hover-glow rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex flex-col justify-between hover:border-slate-700 transition-all relative group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <span class="text-xs font-semibold text-slate-300">Target Gaji</span>
                <button type="button" onclick="openTargetModal()" class="text-[11px] text-emerald-400 hover:text-emerald-300 font-medium hover:underline transition-all" title="Ubah target gaji">
                    Ubah
                </button>
            </div>
            <div>
                <div id="metricTercapaiPendapatan" class="text-lg sm:text-2xl font-bold font-mono-num text-emerald-400 tracking-tight truncate">
                    Rp {{ number_format($tercapaiPendapatan, 0, ',', '.') }}
                </div>
                <!-- Mini Progress Bar -->
                <div class="w-full h-1.5 bg-slate-950 rounded-full overflow-hidden border border-slate-800 my-1.5">
                    <div id="metricProgressBar" class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ min(100, $persenTarget) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span class="truncate">Goal: <span id="metricTargetPendapatan" class="font-medium font-mono-num text-slate-300">Rp {{ number_format($targetPendapatan, 0, ',', '.') }}</span></span>
                    <span id="metricPersenTarget" class="font-semibold font-mono-num text-emerald-400 shrink-0 ml-1">{{ $persenTarget }}%</span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricSisaTarget" class="hidden">{{ $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉' }}</span>
            <span id="labelSisaHari" class="hidden">{{ $sisaHari > 0 ? "Perlu/Hari ({$sisaHari} Hari Sisa)" : "Perlu/Hari (Selesai)" }}</span>
            <span id="metricRataRataHarianDibutuhkan" class="hidden">{{ ($sisaTarget > 0 && $sisaHari > 0) ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0' }}</span>
        </div>

        <!-- Bento 4: Target Produktivitas 330 JO -->
        <div class="card-hover-glow rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex flex-col justify-between hover:border-slate-700 transition-all relative group">
            <div class="flex items-center justify-between gap-1 mb-1.5">
                <span class="text-xs font-semibold text-slate-300">Target 330 JO</span>
                <span id="metricPersenJoVolume" class="text-[11px] font-semibold font-mono-num text-amber-400">
                    {{ $persenJoVolume }}%
                </span>
            </div>
            <div>
                <div class="text-lg sm:text-2xl font-bold font-mono-num text-amber-400 tracking-tight truncate">
                    <span id="metricTercapaiJoVolumeText">{{ $tercapaiJoVolume }}</span> <span class="text-xs text-slate-400 font-normal">/ <span id="metricTargetJoVolume">{{ $targetJoVolume }} JO</span></span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                    <span id="metricTargetJoQtyPerHari" class="font-medium text-emerald-400 font-mono-num truncate">
                        {{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO / Hari' : 'Tercapai! 🎉' }}
                    </span>
                    <span class="text-slate-500 truncate ml-1">Sisa <span id="metricSisaJoVolume" class="text-amber-300 font-medium">{{ $sisaJoVolume > 0 ? $sisaJoVolume : 0 }}</span> JO</span>
                </div>
            </div>
            <!-- Hidden elements for dynamic Ajax sync -->
            <span id="metricTargetJoRevenuePerHari" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? 'Rp ' . number_format($targetJoRevenuePerHari, 0, ',', '.') . '/Hari' : 'Rp 0' }}</span>
            <span id="metricTargetJoQtyPerHariCard" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO / Hari' : 'Tercapai! 🎉' }}</span>
            <span id="metricTargetJoRevenuePerHariCard" class="hidden">{{ ($sisaJoVolume > 0 && $sisaHari > 0) ? 'Rp ' . number_format($targetJoRevenuePerHari, 0, ',', '.') . '/Hari' : 'Rp 0' }}</span>
        </div>

    </div>

    <!-- 3. USER-FRIENDLY SEGMENTED TABS -->
    <div class="sticky top-[58px] z-30 pt-1 pb-1">
        <div class="grid grid-cols-4 gap-1 sm:gap-1.5 p-1 rounded-xl bg-slate-900/90 border border-slate-800 shadow-md backdrop-blur-md">
            <!-- Tab 1: Catat Job -->
            <button 
                type="button" 
                id="tabBtn_input" 
                onclick="switchDashboardTab('input')" 
                class="dashboard-tab-btn py-2 px-2 sm:px-3 rounded-lg text-xs font-semibold transition-all duration-150 flex items-center justify-center gap-1.5 cursor-pointer bg-slate-800 text-amber-400 border border-amber-400/30 shadow-sm active:scale-95 select-none"
            >
                <span class="material-symbols-outlined text-[18px]">post_add</span>
                <span class="truncate">Catat Job</span>
            </button>

            <!-- Tab 2: Rekap Harian -->
            <button 
                type="button" 
                id="tabBtn_rekap" 
                onclick="switchDashboardTab('rekap')" 
                class="dashboard-tab-btn py-2 px-2 sm:px-3 rounded-lg text-xs font-medium transition-all duration-150 flex items-center justify-center gap-1.5 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 active:scale-95 select-none border border-transparent"
            >
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                <span class="truncate">Rekap Harian</span>
            </button>

            <!-- Tab 3: Riwayat Job -->
            <button 
                type="button" 
                id="tabBtn_riwayat" 
                onclick="switchDashboardTab('riwayat')" 
                class="dashboard-tab-btn py-2 px-2 sm:px-3 rounded-lg text-xs font-medium transition-all duration-150 flex items-center justify-center gap-1.5 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 active:scale-95 select-none relative border border-transparent"
            >
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                <span class="truncate">Riwayat</span>
                @if($detailJobOrders->total() > 0)
                    <span class="hidden sm:inline-block px-1.5 py-0.2 rounded bg-amber-400/10 text-amber-400 text-[10px] font-mono-num font-semibold border border-amber-400/20">
                        {{ $detailJobOrders->total() }}
                    </span>
                @endif
            </button>

            <!-- Tab 4: Target & Tren -->
            <button 
                type="button" 
                id="tabBtn_target" 
                onclick="switchDashboardTab('target')" 
                class="dashboard-tab-btn py-2 px-2 sm:px-3 rounded-lg text-xs font-medium transition-all duration-150 flex items-center justify-center gap-1.5 cursor-pointer text-slate-400 hover:text-slate-200 hover:bg-slate-800/50 active:scale-95 select-none border border-transparent"
            >
                <span class="material-symbols-outlined text-[18px]">insights</span>
                <span class="truncate">Target &amp; Tren</span>
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TAB PANEL 1: CATAT JOB (FORMULIR CEPAT + DAFTAR INPUT HARI INI)            -->
    <!-- ========================================================================= -->
    <div id="tabPanel_input" class="dashboard-tab-panel space-y-4 animate-tab-fade">
        
        <!-- Input Job Form Card (Clean, Modern, Streamlined Design) -->
        <div class="relative rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-5 sm:p-6 shadow-sm overflow-hidden">
            <!-- Form Card Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3.5 mb-4 relative z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 border border-amber-400/25 text-amber-400 flex items-center justify-center font-semibold shrink-0">
                        <span class="material-symbols-outlined text-lg">post_add</span>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-white">Catat Job Order</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Pilih kategori, atur hasil &amp; kuantitas, lalu simpan.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-400 text-xs font-mono font-medium shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Siap Input</span>
                </div>
            </div>

            <!-- Quick Category Chips -->
            <div class="space-y-1.5 mb-4 relative z-10">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400">
                        Pilihan Cepat Kategori:
                    </span>
                    <span class="text-[11px] text-slate-500 hidden sm:inline">1-klik untuk langsung pilih</span>
                </div>
                
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth py-1 -mx-0.5 px-0.5">
                    @foreach($tarifs->take(6) as $chipTarif)
                        <button type="button" 
                            onclick="selectQuickCategory({{ $chipTarif->id }})" 
                            class="quick-category-chip px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 hover:border-amber-400/40 text-slate-300 hover:text-white text-xs font-medium whitespace-nowrap transition-all flex items-center gap-2 shrink-0 cursor-pointer shadow-sm active:scale-95">
                            <span>{{ $chipTarif->kategori }}</span>
                            <span class="text-[11px] font-mono-num font-semibold text-amber-400/90 bg-slate-900 px-1.5 py-0.2 rounded border border-slate-800">
                                {{ number_format($chipTarif->tarif_berhasil / 1000, 0) }}k
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('job-orders.store') }}" method="POST" class="space-y-3.5 relative z-10" id="quickJobForm" onsubmit="handleQuickJobFormSubmit(event)">
                @csrf

                <!-- FORM BARIS 1: Kategori (Lebar) & Status Hasil (Segmented Switcher) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">
                    
                    <!-- 1. Kategori Dropdown (Col 7 on lg) -->
                    <div class="lg:col-span-7 space-y-1.5">
                        <label for="tarif_id" class="text-xs font-medium text-slate-300 flex items-center justify-between">
                            <span>Kategori Pekerjaan <span class="text-amber-400">*</span></span>
                            <span class="text-[11px] text-slate-500">Tarif Otomatis</span>
                        </label>
                        <div class="relative group/sel">
                            <select 
                                name="tarif_id" 
                                id="tarif_id" 
                                required 
                                class="w-full px-3.5 pr-10 py-2.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-amber-400/80 focus:ring-1 focus:ring-amber-400/30 text-slate-100 font-medium text-xs sm:text-sm min-h-[44px] cursor-pointer appearance-none transition-all outline-none"
                                onchange="updatePricePreview()"
                            >
                                <option value="" disabled selected>Pilih Kategori Pekerjaan...</option>
                                @foreach($tarifs as $tarif)
                                    <option 
                                        value="{{ $tarif->id }}" 
                                        data-berhasil="{{ $tarif->tarif_berhasil }}" 
                                        data-gagal="{{ $tarif->tarif_gagal ?? 0 }}"
                                        {{ old('tarif_id') == $tarif->id ? 'selected' : '' }}
                                    >
                                        {{ $tarif->kategori }} (Berhasil: Rp {{ number_format($tarif->tarif_berhasil, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-lg">unfold_more</span>
                        </div>
                    </div>

                    <!-- 2. Status Hasil Switcher (Col 5 on lg) -->
                    <div class="lg:col-span-5 space-y-1.5">
                        <label class="text-xs font-medium text-slate-300 flex items-center justify-between">
                            <span>Status Hasil <span class="text-amber-400">*</span></span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 min-h-[44px]">
                            <!-- Berhasil -->
                            <label class="cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="status" 
                                    value="berhasil" 
                                    class="peer sr-only" 
                                    checked 
                                    onchange="updatePricePreview()"
                                >
                                <div class="w-full h-full py-2.5 px-3 rounded-xl bg-slate-950 border border-slate-800 peer-checked:border-emerald-500/60 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-400 text-slate-400 font-semibold text-xs transition-all flex items-center justify-center gap-1.5 hover:border-slate-700 select-none">
                                    <span class="material-symbols-outlined text-base">check_circle</span>
                                    <span>Berhasil</span>
                                </div>
                            </label>

                            <!-- Gagal -->
                            <label class="cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="status" 
                                    value="gagal" 
                                    class="peer sr-only" 
                                    {{ old('status') === 'gagal' ? 'checked' : '' }}
                                    onchange="updatePricePreview()"
                                >
                                <div class="w-full h-full py-2.5 px-3 rounded-xl bg-slate-950 border border-slate-800 peer-checked:border-rose-500/60 peer-checked:bg-rose-500/10 peer-checked:text-rose-400 text-slate-400 font-semibold text-xs transition-all flex items-center justify-center gap-1.5 hover:border-slate-700 select-none">
                                    <span class="material-symbols-outlined text-base">cancel</span>
                                    <span>Gagal</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- FORM BARIS 2: Jumlah Stepper, Tanggal, dan Catatan -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                    
                    <!-- 3. Jumlah (Qty) Stepper (Col 3 on sm/lg) -->
                    <div class="sm:col-span-3 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="quantity" class="text-xs font-medium text-slate-300">
                                Jumlah JO
                            </label>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="adjustQuickQuantity(1)" class="px-1.5 py-0.2 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] font-medium border border-slate-700 transition-all cursor-pointer" title="Tambah 1">+1</button>
                                <button type="button" onclick="adjustQuickQuantity(5)" class="px-1.5 py-0.2 rounded bg-slate-800 hover:bg-amber-400/20 text-amber-300 text-[10px] font-medium border border-slate-700 transition-all cursor-pointer" title="Tambah 5">+5</button>
                            </div>
                        </div>
                        <div class="flex items-center rounded-xl bg-slate-950 border border-slate-800 overflow-hidden min-h-[42px] focus-within:border-amber-400/80 focus-within:ring-1 focus-within:ring-amber-400/30 transition-all">
                            <button type="button" onclick="stepQuantity(-1)" class="stepper-btn w-10 h-10 flex items-center justify-center text-slate-400 hover:text-white transition-all font-semibold text-base cursor-pointer select-none">−</button>
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
                                class="w-full bg-transparent text-amber-400 font-mono-num font-bold text-center text-sm focus:outline-none py-1.5"
                            >
                            <button type="button" onclick="stepQuantity(1)" class="stepper-btn w-10 h-10 flex items-center justify-center text-slate-400 hover:text-white transition-all font-semibold text-base cursor-pointer select-none">+</button>
                        </div>
                    </div>

                    <!-- 4. Tanggal Pengerjaan (Col 4 on sm/lg) -->
                    <div class="sm:col-span-4 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="tanggal" class="text-xs font-medium text-slate-300">
                                Tanggal <span class="text-amber-400">*</span>
                            </label>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="setQuickFormDate('today')" class="px-1.5 py-0.2 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-medium border border-slate-700 transition-all cursor-pointer">Hari Ini</button>
                                <button type="button" onclick="setQuickFormDate('yesterday')" class="px-1.5 py-0.2 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] font-medium border border-slate-700 transition-all cursor-pointer">Kemarin</button>
                            </div>
                        </div>
                        <input 
                            type="date" 
                            name="tanggal" 
                            id="tanggal" 
                            value="{{ old('tanggal', $today) }}" 
                            required 
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 font-mono font-medium text-xs focus:border-amber-400/80 focus:ring-1 focus:ring-amber-400/30 focus:outline-none transition-all min-h-[42px]"
                        >
                    </div>

                    <!-- 5. Catatan / Voice-to-Text (Col 5 on sm/lg) -->
                    <div class="sm:col-span-5 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="catatanInput" class="text-xs font-medium text-slate-300">
                                Catatan / Lokasi <span class="text-slate-500 font-normal">(opsional)</span>
                            </label>
                            <span class="text-[11px] text-slate-500 hidden sm:inline">No. Merchant / TID</span>
                        </div>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="catatan" 
                                id="catatanInput"
                                placeholder="Contoh: Toko Berkah / TID 88219..." 
                                value="{{ old('catatan') }}"
                                class="w-full pl-3 pr-10 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 placeholder:text-slate-500 text-xs focus:border-amber-400/80 focus:ring-1 focus:ring-amber-400/30 focus:outline-none transition-all min-h-[42px]"
                            >
                            <button type="button" onclick="toggleVoiceInput('catatanInput', this)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800/80 transition-all cursor-pointer" title="Input suara (Speech-to-Text)">
                                <span class="material-symbols-outlined text-base">mic</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Custom Nominal Input (Piket Event) -->
                <div id="customTarifContainer" class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-400/30 hidden space-y-1.5 animate-fadeIn">
                    <label for="custom_tarif" class="text-xs font-semibold text-amber-300 block">Nominal Tarif Custom Piket / Event (Rp):</label>
                    <div class="relative max-w-xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-amber-400 font-medium text-xs">Rp</span>
                        <input 
                            type="number" 
                            name="custom_tarif" 
                            id="custom_tarif" 
                            min="0" 
                            step="1000"
                            placeholder="Contoh: 75000" 
                            oninput="updatePricePreview()"
                            class="w-full pl-9 pr-3 py-1.5 rounded-lg bg-slate-950 border border-amber-400/50 text-amber-300 font-mono-num font-semibold text-xs focus:outline-none"
                        >
                    </div>
                </div>

                <!-- FORM BARIS 3: Action & Price Preview Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3.5 border-t border-slate-800/80">
                    <!-- Left: Live Tarif Calculation Card -->
                    <div class="flex items-center gap-3 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                            <span class="material-symbols-outlined text-lg">payments</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-medium text-slate-400 block">Total Estimasi Tarif:</span>
                            <div id="pricePreview" class="text-lg sm:text-xl font-bold font-mono-num text-emerald-400 tracking-tight">
                                Rp 0
                            </div>
                        </div>
                    </div>

                    <!-- Right: Submit CTA Button -->
                    <button 
                        type="submit" 
                        class="btn-shine py-2.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs sm:text-sm transition-all duration-150 cursor-pointer min-h-[44px] flex items-center justify-center gap-2 active:scale-98 shadow-sm"
                    >
                        <span class="material-symbols-outlined text-lg">check</span>
                        <span>Simpan Job Order</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- DAFTAR JOB TERCATAT HARI INI (Langsung terlihat di bawah form tanpa perlu scroll ke bawah!) -->
        <!-- DAFTAR JOB TERCATAT HARI INI -->
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-white">Job Tercatat Hari Ini</h3>
                    <span class="text-[11px] font-mono-num text-slate-400">({{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }})</span>
                </div>
                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 text-[11px] font-mono-num font-medium border border-emerald-500/20">
                    {{ $todayJobOrders->count() }} JO &bull; Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </span>
            </div>

            @if($todayJobOrders->count() > 0)
                <div class="space-y-2">
                    @foreach($todayJobOrders as $tJob)
                        <div class="group/job p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full {{ $tJob->status === 'berhasil' ? 'bg-emerald-400' : 'bg-rose-400' }} shrink-0"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-200">{{ $tJob->kategori }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-medium capitalize {{ $tJob->status === 'berhasil' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            {{ $tJob->status }}
                                        </span>
                                    </div>
                                    @if($tJob->catatan)
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $tJob->catatan }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto pt-1 sm:pt-0 border-t sm:border-t-0 border-slate-800/60">
                                <span class="text-xs font-semibold font-mono-num text-amber-400">
                                    Rp {{ number_format($tJob->tarif, 0, ',', '.') }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ json_encode($tJob) }})" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800 transition-all cursor-pointer" 
                                        title="Edit"
                                    >
                                        <span class="material-symbols-outlined text-base">edit</span>
                                    </button>
                                    <form action="{{ route('job-orders.destroy', $tJob->id) }}" method="POST" onsubmit="return confirm('Hapus job order ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all cursor-pointer" title="Hapus">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 px-4 text-center rounded-xl bg-slate-950 border border-slate-800">
                    <span class="material-symbols-outlined text-slate-600 text-2xl mb-1">note_alt</span>
                    <p class="text-xs text-slate-300 font-medium">Belum ada job order yang dicatat hari ini.</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Gunakan formulir di atas untuk mulai mencatat pekerjaan harian Anda.</p>
                </div>
            @endif
        </div>

    </div>


    <!-- ========================================================================= -->
    <!-- TAB PANEL 2: REKAP HARIAN (RINGKASAN TANGGAL & EKSPOR CSV/PDF)            -->
    <!-- ========================================================================= -->
    <div id="tabPanel_rekap" class="dashboard-tab-panel space-y-4 hidden">
        
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4">
            
            <!-- Header & Export Action -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-semibold text-white">
                        Rekap Harian ({{ $periodLabel }})
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Total job dan akumulasi pendapatan per tanggal.</p>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a 
                        href="{{ route('export.csv', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700/80 text-slate-200 hover:text-white font-medium text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base">download</span>
                        <span>CSV</span>
                    </a>

                    <a 
                        href="{{ route('export.pdf', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                        target="_blank"
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-amber-400/10 hover:bg-amber-400/20 border border-amber-400/30 text-amber-300 font-medium text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base">print</span>
                        <span>PDF</span>
                    </a>
                </div>
            </div>

            <!-- Filter Controls (Bulan vs Rentang Tanggal) -->
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 sm:p-4 space-y-3">
                <div class="flex items-center gap-2 border-b border-slate-800/80 pb-2.5">
                    <button 
                        type="button"
                        onclick="switchFilterMode('bulan')"
                        id="tabBulan"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ (!$startDate && !$endDate) ? 'bg-slate-800 text-amber-400 border border-amber-400/30 shadow-sm' : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        Per Bulan
                    </button>
                    <button 
                        type="button"
                        onclick="switchFilterMode('rentang')"
                        id="tabRentang"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ ($startDate || $endDate) ? 'bg-slate-800 text-amber-400 border border-amber-400/30 shadow-sm' : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        Rentang Tanggal
                    </button>
                    
                    @if($startDate || $endDate || $selectedBulan !== \Carbon\Carbon::now()->format('Y-m'))
                        <a href="{{ route('dashboard') }}" class="ml-auto text-xs text-rose-400 hover:underline font-medium">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <!-- Form Filter Per Bulan -->
                <form id="formFilterBulan" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'hidden' : 'block' }}">
                    <div class="flex flex-col sm:flex-row items-end gap-2.5">
                        <div class="w-full sm:w-60 space-y-1">
                            <label for="bulan" class="block text-xs font-medium text-slate-400">Bulan &amp; Tahun</label>
                            <input 
                                type="month" 
                                name="bulan" 
                                id="bulan" 
                                value="{{ $selectedBulan }}" 
                                onchange="this.form.submit()"
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 font-mono font-medium text-xs focus:border-amber-400/80 focus:outline-none"
                            >
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all">
                            Tampilkan
                        </button>
                    </div>
                </form>

                <!-- Form Filter Rentang Tanggal -->
                <form id="formFilterRentang" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'block' : 'hidden' }}">
                    <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 items-end">
                        <div class="space-y-1">
                            <label for="start_date" class="block text-xs font-medium text-slate-400">Dari Tanggal</label>
                            <input 
                                type="date" 
                                name="start_date" 
                                id="start_date" 
                                value="{{ $startDate }}" 
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 font-mono font-medium text-xs focus:border-amber-400/80 focus:outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="end_date" class="block text-xs font-medium text-slate-400">Sampai Tanggal</label>
                            <input 
                                type="date" 
                                name="end_date" 
                                id="end_date" 
                                value="{{ $endDate }}" 
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 font-mono font-medium text-xs focus:border-amber-400/80 focus:outline-none"
                            >
                        </div>

                        <div>
                            <button 
                                type="submit" 
                                class="w-full py-2 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all"
                            >
                                Terapkan Rentang
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Desktop -->
            <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800 shadow-inner">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 font-medium text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Tanggal Pekerjaan</th>
                            <th class="py-3 px-4 text-center font-semibold">Volume JO</th>
                            <th class="py-3 px-4 text-right font-semibold">Pendapatan Harian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 font-normal">
                        @forelse($rekapHarian as $rekap)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-4 font-medium text-slate-200">
                                    {{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono-num font-semibold text-amber-300">
                                    {{ $rekap->total_job }} JO
                                    @if(($rekap->total_piket ?? 0) > 0)
                                        <span class="text-cyan-400 text-[11px] ml-1">(+{{ $rekap->total_piket }} Piket)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono-num font-semibold text-emerald-400">
                                    Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-500 font-medium text-xs">
                                    Belum ada transaksi job order di periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-950/90 border-t border-slate-800 font-semibold text-xs text-slate-100">
                        <tr>
                            <td class="py-3.5 px-4 text-amber-400">
                                TOTAL AKUMULASI
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono-num text-amber-300">
                                {{ $rekapHarian->sum('total_job') }} JO
                                @if($rekapHarian->sum('total_piket') > 0)
                                    <span class="text-cyan-400 text-[11px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono-num text-emerald-400 text-sm sm:text-base font-bold">
                                Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Cards Mobile -->
            <div class="block sm:hidden space-y-2">
                @forelse($rekapHarian as $rekap)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="font-medium text-slate-200 text-xs">
                                {{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-xs font-mono-num font-semibold text-emerald-400">
                                Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Volume:</span>
                            <span class="font-mono-num font-semibold text-amber-400">
                                {{ $rekap->total_job }} JO
                                @if(($rekap->total_piket ?? 0) > 0)
                                    <span class="text-cyan-300 ml-1">+ {{ $rekap->total_piket }} Piket</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-slate-500 font-medium text-xs rounded-xl bg-slate-950 border border-slate-800">
                        Belum ada transaksi di periode ini.
                    </div>
                @endforelse

                @if($rekapHarian->count() > 0)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                        <div class="text-xs font-semibold text-amber-400">
                            Total Akumulasi Periode
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-800/80 pt-1.5">
                            <span class="font-mono-num text-xs font-semibold text-amber-300">
                                {{ $rekapHarian->sum('total_job') }} JO
                                @if($rekapHarian->sum('total_piket') > 0)
                                    <span class="text-cyan-400 text-[10px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                                @endif
                            </span>
                            <span class="text-base font-bold font-mono-num text-emerald-400">
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
        
        <div class="rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-semibold text-white">
                        Detail Riwayat Job Order
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Rincian seluruh transaksi dengan tombol edit &amp; hapus.</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 text-xs font-mono font-medium">
                    Total: {{ $detailJobOrders->total() }} Data
                </span>
            </div>

            <!-- Desktop View Table -->
            <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-800 shadow-inner">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 font-semibold text-[11px] uppercase tracking-wider border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800 font-normal">
                        @forelse($detailJobOrders as $index => $job)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3.5 text-slate-500 font-mono-num">
                                    {{ $detailJobOrders->firstItem() + $index }}
                                </td>
                                <td class="py-3 px-3.5 font-mono-num text-slate-300 font-medium whitespace-nowrap">
                                    {{ $job->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-slate-200">
                                    {{ $job->kategori }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($job->status === 'berhasil')
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-medium text-[10px] capitalize">
                                            Berhasil
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-rose-500/10 border border-rose-500/20 text-rose-300 font-medium text-[10px] capitalize">
                                            Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono-num font-semibold text-amber-400 whitespace-nowrap">
                                    Rp {{ number_format($job->tarif, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-400 max-w-xs truncate">
                                    {{ $job->catatan ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <button 
                                            onclick="openEditModal({{ json_encode($job) }})" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800 transition-all cursor-pointer" 
                                            title="Edit"
                                        >
                                            <span class="material-symbols-outlined text-base">edit</span>
                                        </button>

                                        <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all cursor-pointer" title="Hapus">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 font-medium text-xs">
                                    Tidak ada data job order ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="block sm:hidden space-y-2">
                @forelse($detailJobOrders as $index => $job)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-mono-num text-slate-500">#{{ $detailJobOrders->firstItem() + $index }}</span>
                                <span class="font-mono-num font-medium text-slate-300 text-xs">
                                    {{ $job->tanggal->format('d/m/Y') }}
                                </span>
                            </div>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-medium capitalize {{ $job->status === 'berhasil' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-rose-500/10 border border-rose-500/20 text-rose-300' }}">
                                {{ $job->status }}
                            </span>
                        </div>

                        <div>
                            <h4 class="font-medium text-slate-200 text-xs">{{ $job->kategori }}</h4>
                            @if($job->catatan)
                                <p class="text-[11px] text-slate-400 bg-slate-900/60 p-2 rounded-lg border border-slate-800 mt-1">
                                    {{ $job->catatan }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-800/80">
                            <span class="font-mono-num font-semibold text-amber-400 text-xs sm:text-sm">
                                Rp {{ number_format($job->tarif, 0, ',', '.') }}
                            </span>

                            <div class="flex items-center gap-1">
                                <button 
                                    onclick="openEditModal({{ json_encode($job) }})" 
                                    class="px-2 py-1 rounded-lg bg-slate-800 text-slate-300 hover:text-white text-[11px] font-medium"
                                >
                                    Edit
                                </button>

                                <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Hapus job order ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-lg bg-rose-500/10 text-rose-400 hover:text-rose-200 text-[11px] font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 font-medium text-xs rounded-xl bg-slate-950 border border-slate-800">
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
            <div class="lg:col-span-5 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-5 shadow-sm flex flex-col justify-between space-y-4 relative">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-white">Target Gaji Bulanan</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Personal Goal Meter</p>
                    </div>
                    
                    <button type="button" onclick="openTargetModal()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs font-medium border border-emerald-500/20 transition-all">
                        Ubah Target
                    </button>
                </div>

                <!-- Main Target Numbers -->
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="text-xs text-slate-400 block">Tercapai</span>
                        <div class="text-xl sm:text-2xl font-bold font-mono-num text-emerald-400">
                            Rp {{ number_format($tercapaiPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400 block">Target</span>
                        <div class="text-base sm:text-lg font-semibold font-mono-num text-slate-300">
                            Rp {{ number_format($targetPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-xs font-medium">
                        <span class="text-slate-400">Pencapaian:</span>
                        <span class="{{ $persenTarget >= 100 ? 'text-emerald-400' : 'text-cyan-400' }} font-mono-num font-semibold">{{ $persenTarget }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ min(100, $persenTarget) }}%"></div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/60">
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[11px] text-slate-400 block">Sisa Target</span>
                        <span class="text-xs sm:text-sm font-semibold font-mono-num {{ $sisaTarget > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[11px] text-slate-400 block">
                            {{ $sisaHari > 0 ? "Perlu/Hari ({$sisaHari} Hari)" : "Perlu/Hari" }}
                        </span>
                        <span class="text-xs sm:text-sm font-semibold font-mono-num text-cyan-300">
                            {{ ($sisaTarget > 0 && $sisaHari > 0) ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                </div>

                <!-- 330 JO Box -->
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-medium text-emerald-400 block">
                            Target Produktivitas: 330 JO
                        </span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">
                            Tercapai <span class="text-slate-200 font-semibold">{{ $tercapaiJoVolume }} JO</span> ({{ $persenJoVolume }}%)
                        </span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-semibold font-mono-num text-emerald-300 block">
                            {{ ($sisaJoVolume > 0 && $sisaHari > 0) ? $targetJoQtyPerHari . ' JO/Hari' : 'Tercapai! 🎉' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Trend Line Chart Card (7 Col) -->
            <div class="lg:col-span-7 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-slate-800/80 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-white">Trend Pendapatan Harian</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Grafik perolehan ({{ $periodLabel }})</p>
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
<div id="targetModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 transition-all duration-200">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <button type="button" onclick="closeTargetModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-semibold">
                <span class="material-symbols-outlined text-lg">flag</span>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-semibold text-white">Set Target Pendapatan</h3>
                <p class="text-xs text-slate-400">Atur target pencapaian gaji bulanan Anda</p>
            </div>
        </div>

        <form action="{{ route('user.target.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Target Pendapatan (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-sm">Rp</span>
                    <input type="number" step="500000" min="0" name="target_pendapatan" id="targetInput" value="{{ auth()->user()->target_pendapatan ?? 5000000 }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-white font-mono-num font-semibold text-base focus:border-emerald-500/80 focus:ring-1 focus:ring-emerald-500/30 outline-none">
                </div>
            </div>

            <!-- Quick presets -->
            <div>
                <span class="block text-xs text-slate-400 mb-1.5">Pilihan Cepat:</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="setPresetTarget(3000000)" class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all">3 Jt</button>
                    <button type="button" onclick="setPresetTarget(5000000)" class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all">5 Jt</button>
                    <button type="button" onclick="setPresetTarget(7500000)" class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all">7.5 Jt</button>
                    <button type="button" onclick="setPresetTarget(10000000)" class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all">10 Jt</button>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTargetModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 font-medium text-xs transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-xs shadow-sm transition-all">Simpan Target</button>
            </div>
        </form>
    </div>
</div>


<!-- MODAL EDIT JOB ORDER -->
<div id="editModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <h3 class="text-sm sm:text-base font-semibold text-white">
                Edit Job Order
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label for="edit_kategori_info" class="block text-xs font-medium text-slate-400">
                    Kategori Tugas
                </label>
                <input type="text" id="edit_kategori_info" readonly class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 font-medium text-xs cursor-not-allowed" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-300">Status</label>
                    <select name="status" id="edit_status" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-medium text-xs focus:border-amber-400/80 focus:outline-none min-h-[40px]">
                        <option value="berhasil">Berhasil</option>
                        <option value="gagal">Gagal</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="edit_tanggal" class="block text-xs font-medium text-slate-300">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono font-medium text-xs focus:border-amber-400/80 focus:outline-none min-h-[40px]" />
                </div>
            </div>

            <div class="space-y-1">
                <label for="edit_tarif" class="block text-xs font-medium text-slate-300">Nominal Tarif (Rp)</label>
                <input type="number" name="tarif" id="edit_tarif" required min="0" step="500" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono font-medium text-xs focus:border-amber-400/80 focus:outline-none min-h-[40px]" />
            </div>

            <div class="space-y-1">
                <label for="edit_catatan" class="block text-xs font-medium text-slate-300">Catatan</label>
                <input type="text" name="catatan" id="edit_catatan" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-amber-400/80 focus:outline-none min-h-[40px]" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-medium text-xs hover:bg-slate-700 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs transition-all cursor-pointer shadow-sm">
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
            btn.classList.remove('bg-slate-800', 'text-amber-400', 'border-amber-400/30', 'shadow-sm', 'font-semibold', 'bg-gradient-to-r', 'from-amber-400', 'to-orange-400', 'text-slate-950', 'font-black', 'shadow-lg', 'shadow-amber-500/20');
            btn.classList.add('text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800/50', 'border-transparent', 'font-medium');
        });

        const activePanel = document.getElementById('tabPanel_' + tabId);
        const activeBtn = document.getElementById('tabBtn_' + tabId);

        if (activePanel) {
            activePanel.classList.remove('hidden');
            activePanel.classList.remove('animate-tab-fade');
            void activePanel.offsetWidth; // Force browser reflow to re-trigger smooth entrance animation
            activePanel.classList.add('animate-tab-fade');
        }
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800/50', 'border-transparent', 'font-medium');
            activeBtn.classList.add('bg-slate-800', 'text-amber-400', 'border-amber-400/30', 'shadow-sm', 'font-semibold');
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

        const activeClass = "px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer bg-slate-800 text-amber-400 border border-amber-400/30 shadow-sm";
        const inactiveClass = "px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer text-slate-400 hover:text-slate-200";

        if (mode === 'bulan') {
            formBulan.classList.remove('hidden');
            formBulan.classList.add('block');
            formRentang.classList.remove('block');
            formRentang.classList.add('hidden');

            tabBulan.className = activeClass;
            tabRentang.className = inactiveClass;
        } else {
            formRentang.classList.remove('hidden');
            formRentang.classList.add('block');
            formBulan.classList.remove('block');
            formBulan.classList.add('hidden');

            tabRentang.className = activeClass;
            tabBulan.className = inactiveClass;
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
        const pricePreview = document.getElementById('pricePreview');
        
        if (!selectedOption || !selectedOption.value) {
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
        if (pricePreview) {
            if (qty > 1) {
                pricePreview.innerHTML = 'Rp ' + totalFee.toLocaleString('id-ID') + ' <span class="text-xs font-semibold text-emerald-300/80 block sm:inline sm:ml-1">(' + qty + ' JO x Rp ' + activeFee.toLocaleString('id-ID') + ')</span>';
            } else {
                pricePreview.innerText = 'Rp ' + activeFee.toLocaleString('id-ID');
            }
            // Trigger modern tactile price pop animation
            pricePreview.classList.remove('animate-price-pop');
            void pricePreview.offsetWidth;
            pricePreview.classList.add('animate-price-pop');
        }
    }

    function selectQuickCategory(id) {
        const select = document.getElementById('tarif_id');
        if (!select) return;
        select.value = id;

        document.querySelectorAll('.quick-category-chip').forEach(chip => {
            chip.classList.remove('border-amber-400', 'bg-amber-400/20', 'text-amber-300', 'ring-1', 'ring-amber-400/40', 'shadow-amber-500/10');
            chip.classList.add('border-slate-800', 'bg-slate-950/90', 'text-slate-300');
        });

        const activeChip = Array.from(document.querySelectorAll('.quick-category-chip')).find(c => c.getAttribute('onclick')?.includes(`(${id})`));
        if (activeChip) {
            activeChip.classList.remove('border-slate-800', 'bg-slate-950/90', 'text-slate-300');
            activeChip.classList.add('border-amber-400', 'bg-amber-400/20', 'text-amber-300', 'ring-1', 'ring-amber-400/40', 'shadow-amber-500/10');
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
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        const box = modal.querySelector('div');
        if (box) {
            box.classList.remove('animate-modal-pop');
            void box.offsetWidth;
            box.classList.add('animate-modal-pop');
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openTargetModal() {
        const modal = document.getElementById('targetModal');
        modal.classList.remove('hidden');
        const box = modal.querySelector('div');
        if (box) {
            box.classList.remove('animate-modal-pop');
            void box.offsetWidth;
            box.classList.add('animate-modal-pop');
        }
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
        let originalContent = '';
        
        if (submitBtn) {
            originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.classList.remove('animate-cta-glow');
            submitBtn.innerHTML = `
                <span class="inline-flex items-center gap-2">
                    <svg class="animate-spin -ml-1 mr-1 h-5 w-5 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>MENYIMPAN...</span>
                </span>`;
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
                // Success feedback state on button
                if (submitBtn) {
                    submitBtn.classList.remove('from-amber-400', 'to-orange-400', 'via-amber-500');
                    submitBtn.classList.add('from-emerald-400', 'to-teal-500', 'text-slate-950');
                    submitBtn.innerHTML = `
                        <span class="inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">check_circle</span>
                            <span>Tersimpan!</span>
                        </span>`;
                }

                form.reset();
                const qtyInput = document.getElementById('quantity');
                if (qtyInput) qtyInput.value = '1';

                if (typeof updatePricePreview === 'function') updatePricePreview();
                showTemporaryToast(data.message || 'Job order berhasil dicatat!', 'success');

                if (typeof refreshDashboardData === 'function') {
                    refreshDashboardData();
                }

                // Smooth reload to immediately sync today's jobs table and statistics
                setTimeout(() => {
                    window.location.reload();
                }, 750);
            } else {
                showTemporaryToast(data.message || 'Gagal menyimpan job order.', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                    submitBtn.classList.add('animate-cta-glow');
                }
            }
        } catch (e) {
            showTemporaryToast('Terjadi kesalahan koneksi server.', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
                submitBtn.classList.add('animate-cta-glow');
            }
        }
    }

    function showTemporaryToast(message, type = 'success') {
        const existingToast = document.getElementById('modernFloatingToast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.id = 'modernFloatingToast';
        const isSuccess = type === 'success';

        toast.className = `fixed bottom-6 right-6 sm:bottom-8 sm:right-8 z-50 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl shadow-2xl backdrop-blur-2xl flex items-center gap-3 toast-enter border transition-all duration-300 ${
            isSuccess 
            ? 'bg-slate-950/95 border-emerald-500/40 text-emerald-300 shadow-emerald-500/20' 
            : 'bg-slate-950/95 border-rose-500/40 text-rose-300 shadow-rose-500/20'
        }`;

        const iconName = isSuccess ? 'check_circle' : 'error';
        const iconColor = isSuccess 
            ? 'text-emerald-400 bg-emerald-500/20 ring-1 ring-emerald-400/40' 
            : 'text-rose-400 bg-rose-500/20 ring-1 ring-rose-400/40';

        toast.innerHTML = `
            <div class="w-8 h-8 rounded-xl ${iconColor} flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">${iconName}</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-white">${isSuccess ? 'Sukses' : 'Perhatian'}</p>
                <p class="text-xs text-slate-300 font-normal">${message}</p>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(15px) scale(0.95)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Voice-to-Text Speech Recognition
    let recognitionInstance = null;
    let isRecognizing = false;

    function toggleVoiceInput(inputId, btn) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            showTemporaryToast('Browser belum mendukung pengenalan suara. Silakan gunakan Google Chrome / Microsoft Edge.', 'error');
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
                    btn.classList.add('text-rose-400', 'animate-mic-active', 'border-rose-500/60', 'bg-rose-500/20');
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
                    btn.classList.remove('text-rose-400', 'animate-mic-active', 'border-rose-500/60', 'bg-rose-500/20');
                    btn.classList.add('text-slate-400');
                }
            };

            recognition.start();
        } catch (err) {
            console.error('Error starting speech recognition:', err);
            isRecognizing = false;
            if (btn) {
                btn.classList.remove('text-rose-400', 'animate-mic-active', 'border-rose-500/60', 'bg-rose-500/20');
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

@extends('layouts.app')

@section('title', 'Executive Command Center - Monitoring Admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- 1. ADMIN EXECUTIVE HEADER & GLOBAL FILTER BAR -->
    <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-amber-500/30 p-5 sm:p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-16 -left-16 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
            <!-- Brand & Info Title -->
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 text-slate-950 flex items-center justify-center font-black shadow-lg shadow-amber-500/25 shrink-0 ring-1 ring-amber-300/40">
                    <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/30">
                            COMMAND CENTER ADMIN
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-300">
                            | {{ auth()->user()->name }}
                        </span>
                    </div>
                    <h2 class="text-base sm:text-xl font-black text-white tracking-tight mt-0.5">
                        Monitoring &amp; Rekapitulasi Operasional Teknisi
                    </h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">
                        Pantau real-time aktivitas JO harian, pengeluaran gaji, dan produktivitas tim lapangan
                    </p>
                </div>
            </div>

            <!-- Global Filters & Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
                <!-- Filter Kabin Teknisi Dropdown -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
                    @if(request('bulan'))
                        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    @endif
                    @if(request('start_date'))
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    @endif

                    <select name="teknisi_id" id="admin_teknisi_filter" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-950 border border-amber-500/40 text-amber-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all cursor-pointer shadow-inner">
                        <option value="all" {{ ($targetUserId === null) ? 'selected' : '' }}>👥 Semua Teknisi (Ringkasan Global Tim)</option>
                        @foreach($allTeknisi as $teknisiItem)
                            <option value="{{ $teknisiItem->id }}" {{ ($targetUserId == $teknisiItem->id) ? 'selected' : '' }}>
                                👤 {{ $teknisiItem->name }} ({{ $teknisiItem->email }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Filter Bulan Cepat -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-1.5 flex-1 sm:flex-initial">
                    @if(request('teknisi_id'))
                        <input type="hidden" name="teknisi_id" value="{{ request('teknisi_id') }}">
                    @endif
                    <input type="month" name="bulan" value="{{ $selectedBulan }}" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 rounded-2xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-mono font-bold focus:outline-none focus:border-amber-400 transition-all cursor-pointer">
                </form>

                <!-- Action Export Hub -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('export.csv', ['bulan' => $selectedBulan, 'teknisi_id' => request('teknisi_id'), 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-amber-300 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1 border border-slate-700/60 shadow-sm" title="Download Laporan CSV">
                        <span class="material-symbols-outlined text-sm">download</span>
                        <span>CSV</span>
                    </a>
                    <a href="{{ route('export.pdf', ['bulan' => $selectedBulan, 'teknisi_id' => request('teknisi_id'), 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="px-3 py-2 rounded-xl bg-amber-400/10 hover:bg-amber-400 text-amber-300 hover:text-slate-950 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1 border border-amber-400/30 shadow-sm" title="Cetak / PDF Resmi">
                        <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Active Alert Indicator (If single technician selected) -->
        @if($targetUserId)
            @php $filteredTeknisiObj = $allTeknisi->firstWhere('id', $targetUserId); @endphp
            <div class="mt-4 pt-3 border-t border-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 text-amber-300 font-bold">
                    <span class="material-symbols-outlined text-base">filter_alt</span>
                    <span>Menampilkan data khusus teknisi: <strong class="text-white underline">{{ $filteredTeknisiObj?->name ?? 'Teknisi ID ' . $targetUserId }}</strong> ({{ $filteredTeknisiObj?->email }})</span>
                </div>
                <a href="{{ route('dashboard', ['bulan' => $selectedBulan]) }}" class="px-3 py-1 rounded-xl bg-slate-800 hover:bg-rose-500 hover:text-white text-slate-300 text-[11px] font-extrabold uppercase tracking-wider transition-all inline-flex items-center gap-1 w-fit">
                    <span class="material-symbols-outlined text-xs">close</span>
                    <span>Reset ke Semua Teknisi</span>
                </a>
            </div>
        @endif
    </div>


    <!-- 2. EXECUTIVE KPI SPOTLIGHT (4 MASTER CARDS TIM) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- KPI 1: Total Pengeluaran Gaji Tim -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-cyan-500/30 p-5 shadow-2xl flex flex-col justify-between space-y-3 relative overflow-hidden group hover:border-cyan-500/50 transition-all">
            <div class="absolute -top-12 -right-12 w-28 h-28 bg-cyan-500/10 rounded-full blur-2xl group-hover:bg-cyan-500/20 transition-all pointer-events-none"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-cyan-400">Total Budget Gaji</span>
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-lg shadow-cyan-400/50"></span>
            </div>

            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black font-mono-num text-white tracking-tight">
                    Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-2 flex-wrap text-[10px] text-slate-400 font-semibold mt-1.5 pt-1.5 border-t border-slate-800/80">
                    <span class="text-emerald-400 font-bold">JO: Rp {{ number_format($pendapatanJoBulanIni, 0, ',', '.') }}</span>
                    <span>•</span>
                    <span class="text-cyan-300 font-bold">Piket: Rp {{ number_format($pendapatanPiketBulanIni, 0, ',', '.') }}</span>
                </div>
            </div>

            <span class="text-[10px] font-mono font-medium text-slate-400 block relative z-10">
                Periode: {{ $periodLabel }}
            </span>
        </div>

        <!-- KPI 2: Total Volume JO Selesai Tim -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-amber-500/30 p-5 shadow-2xl flex flex-col justify-between space-y-3 relative overflow-hidden group hover:border-amber-500/50 transition-all">
            <div class="absolute -top-12 -right-12 w-28 h-28 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all pointer-events-none"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-amber-400">Volume Job Order (JO)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-lg shadow-amber-400/50"></span>
            </div>

            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black font-mono-num text-amber-400 tracking-tight">
                    {{ $totalJobBulanIni }} <span class="text-xs text-slate-400 font-bold">JO Selesai</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap text-[10px] text-slate-400 font-semibold mt-1.5 pt-1.5 border-t border-slate-800/80">
                    <span>Rata-rata: <strong class="text-white">{{ $rataRataJoPerHari }} JO/Hari</strong></span>
                    <span>•</span>
                    <span>Piket: <strong class="text-amber-300">{{ $totalPiketBulanIni }} Kali</strong></span>
                </div>
            </div>

            <span class="text-[10px] font-mono font-medium text-slate-400 block relative z-10">
                Akumulasi tim lapangan
            </span>
        </div>

        <!-- KPI 3: Target Produktivitas Tim (330 JO per Teknisi) -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-emerald-500/30 p-5 shadow-2xl flex flex-col justify-between space-y-3 relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="absolute -top-12 -right-12 w-28 h-28 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all pointer-events-none"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-emerald-400">Produktivitas Tim (330 JO/Teknisi)</span>
                <span class="text-[11px] font-black font-mono-num text-emerald-400">{{ $persenJoVolume }}%</span>
            </div>

            <div class="relative z-10 space-y-1.5">
                <div class="flex items-baseline justify-between">
                    <span class="text-xl sm:text-2xl font-black font-mono-num text-emerald-300">
                        {{ $tercapaiJoVolume }} <span class="text-xs text-slate-400 font-normal">/ {{ $targetJoVolume }} JO</span>
                    </span>
                    <span class="text-[10px] font-extrabold font-mono-num {{ $sisaJoVolume > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                        {{ $sisaJoVolume > 0 ? "Sisa {$sisaJoVolume} JO" : "Tercapai! 🎉" }}
                    </span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-2 bg-slate-950 rounded-full overflow-hidden border border-slate-800 shadow-inner">
                    <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 rounded-full transition-all duration-1000" style="width: {{ $persenJoVolume }}%"></div>
                </div>
            </div>

            <span class="text-[10px] font-mono font-medium text-slate-400 block relative z-10">
                Target harian tim: <strong class="text-emerald-400">{{ $targetJoQtyPerHari }} JO/Hari</strong>
            </span>
        </div>

        <!-- KPI 4: Aktivitas Teknisi Hari Ini & Success Rate -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-indigo-500/30 p-5 shadow-2xl flex flex-col justify-between space-y-3 relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            <div class="absolute -top-12 -right-12 w-28 h-28 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all pointer-events-none"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-indigo-400">Teknisi &amp; Success Rate</span>
                <span class="text-[11px] font-black font-mono-num text-emerald-400">{{ $successRate }}% Sukses</span>
            </div>

            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black font-mono-num text-white tracking-tight">
                    {{ $teknisiAktifHariIniCount }} <span class="text-xs text-slate-400 font-semibold">/ {{ $totalTeknisiCount }} Aktif Hari Ini</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap text-[10px] text-slate-400 font-semibold mt-1.5 pt-1.5 border-t border-slate-800/80">
                    <span class="text-emerald-400 font-bold">Berhasil: {{ $totalSuccessJobs }}</span>
                    <span>•</span>
                    <span class="text-rose-400 font-bold">Gagal: {{ $totalFailedJobs }}</span>
                </div>
            </div>

            <span class="text-[10px] font-mono font-medium text-slate-400 block relative z-10">
                Hari ini: <strong class="text-amber-400">{{ $totalJobHariIni }} JO</strong> (Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }})
            </span>
        </div>

    </div>


    <!-- 3. MONITORING UTAMA: PENYELESAIAN JO HARI INI PER TEKNISI -->
    <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-amber-500/30 p-5 sm:p-6 shadow-2xl space-y-5 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4 relative z-10">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black shadow-lg shadow-amber-500/20 shrink-0">
                    <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-xl font-black uppercase tracking-wider text-white">Monitoring JO Selesai Hari Ini per Teknisi</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live Monitoring Hari Ini
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">
                        Rekapitulasi real-time pekerjaan tanggal <span class="text-amber-400 font-bold font-mono">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span> (Target: 330 JO / teknisi)
                    </p>
                </div>
            </div>

            <!-- Quick Summary Badges -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <div class="px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-2 shadow-inner">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Hari Ini:</span>
                    <span class="text-xs font-black font-mono-num text-amber-400">{{ $totalJobHariIni }} JO</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-2 shadow-inner">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Teknisi Aktif:</span>
                    <span class="text-xs font-black font-mono-num text-emerald-400">{{ $teknisiAktifHariIniCount }} / {{ $totalTeknisiCount }}</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-2 shadow-inner">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Gaji Hari Ini:</span>
                    <span class="text-xs font-black font-mono-num text-cyan-300">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Controls: Tab Filter & Real-Time Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-slate-950 border border-slate-800/80 shrink-0 w-fit">
                <button type="button" onclick="setAdminTeknisiTabFilter('all', this)" class="admin-tab-btn active px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all bg-amber-400 text-slate-950 shadow-sm">
                    Semua ({{ $teknisiHariIni->count() }})
                </button>
                <button type="button" onclick="setAdminTeknisiTabFilter('active', this)" class="admin-tab-btn px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all text-slate-400 hover:text-white">
                    Aktif Hari Ini ({{ $teknisiAktifHariIniCount }})
                </button>
                <button type="button" onclick="setAdminTeknisiTabFilter('inactive', this)" class="admin-tab-btn px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all text-slate-400 hover:text-white">
                    Belum Input ({{ $teknisiHariIni->count() - $teknisiAktifHariIniCount }})
                </button>
            </div>

            <!-- Instant Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-base pointer-events-none">search</span>
                <input type="text" id="searchAdminTeknisi" oninput="filterAdminTeknisiCards()" placeholder="Cari nama atau email teknisi..." class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400 transition-all font-medium">
            </div>
        </div>

        <!-- List of Technician Monitoring Cards -->
        <div class="space-y-3.5 relative z-10" id="adminTeknisiCardsContainer">
            @forelse($teknisiHariIni as $tek)
                @php
                    $isAktif = ($tek->total_jo_hari_ini > 0 || $tek->total_piket_hari_ini > 0);
                    $persenProd = min(100, round((($tek->total_jo_bulan_ini ?? 0) / 330) * 100, 1));
                    $sisaProd = max(0, 330 - ($tek->total_jo_bulan_ini ?? 0));
                    
                    // Distinct Initials & Gradients
                    $initials = strtoupper(substr(trim($tek->name), 0, 2));
                    $avatarGradients = [
                        'from-amber-400 to-orange-500 text-slate-950',
                        'from-cyan-400 to-blue-500 text-slate-950',
                        'from-emerald-400 to-teal-500 text-slate-950',
                        'from-purple-400 to-pink-500 text-slate-950',
                        'from-rose-400 to-red-500 text-white',
                        'from-indigo-400 to-cyan-500 text-white',
                    ];
                    $avatarClass = $avatarGradients[$tek->id % count($avatarGradients)];
                @endphp

                <div class="admin-teknisi-card rounded-2xl bg-slate-950/70 border border-slate-800/80 p-4 sm:p-5 transition-all duration-200 hover:border-amber-500/40 hover:bg-slate-950/90 shadow-xl"
                    data-name="{{ strtolower($tek->name) }}"
                    data-email="{{ strtolower($tek->email) }}"
                    data-status="{{ $isAktif ? 'active' : 'inactive' }}"
                    data-total-jo="{{ $tek->total_jo_hari_ini }}">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                        <!-- Col 1: Profil Teknisi (4 cols) -->
                        <div class="lg:col-span-4 flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $avatarClass }} flex items-center justify-center font-black text-sm shrink-0 shadow-md">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm sm:text-base font-bold text-white truncate">{{ $tek->name }}</h3>
                                    @if($targetUserId == $tek->id)
                                        <span class="px-2 py-0.5 text-[9px] rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold uppercase">Terfilter</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 truncate">{{ $tek->email }}</p>
                                <div class="mt-1">
                                    @if($isAktif)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Aktif Hari Ini ({{ $tek->total_jo_hari_ini }} JO)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-800/60 border border-slate-700/50 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                            <span>Belum Ada JO Hari Ini</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Col 2: JO Selesai Hari Ini & Gaji Hari Ini (3 cols) -->
                        <div class="lg:col-span-3 grid grid-cols-2 gap-2 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-3 lg:pt-0 lg:pl-5">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">JO Selesai Hari Ini</span>
                                <div class="text-xl sm:text-2xl font-black font-mono-num {{ $tek->total_jo_hari_ini > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                                    {{ $tek->total_jo_hari_ini }} <span class="text-xs text-slate-400 font-bold">JO</span>
                                </div>
                                <div class="text-[10px] space-y-0.5 mt-0.5">
                                    <span class="text-emerald-400 font-bold block">{{ $tek->jo_berhasil_hari_ini }} Berhasil</span>
                                    @if($tek->jo_gagal_hari_ini > 0)
                                        <span class="text-rose-400 font-bold block">{{ $tek->jo_gagal_hari_ini }} Gagal</span>
                                    @endif
                                    @if($tek->total_piket_hari_ini > 0)
                                        <span class="text-cyan-300 font-bold block">{{ $tek->total_piket_hari_ini }} Piket</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Gaji Hari Ini</span>
                                <div class="text-sm sm:text-base font-black font-mono-num {{ ($tek->pendapatan_hari_ini ?? 0) > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
                                    Rp {{ number_format($tek->pendapatan_hari_ini ?? 0, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Pekerjaan hari ini</span>
                            </div>
                        </div>

                        <!-- Col 3: Produktivitas Bulan Ini (Target 330 JO) (3 cols) -->
                        <div class="lg:col-span-3 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-3 lg:pt-0 lg:pl-5">
                            <div class="flex items-center justify-between text-[11px] font-extrabold mb-1">
                                <span class="text-slate-400 uppercase tracking-wider text-[10px]">Produktivitas (Target 330 JO)</span>
                                <span class="{{ $persenProd >= 100 ? 'text-emerald-400' : 'text-amber-400' }} font-mono-num">{{ $persenProd }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-900 rounded-full overflow-hidden border border-slate-800 shadow-inner">
                                <div class="h-full bg-gradient-to-r {{ $persenProd >= 100 ? 'from-emerald-500 to-teal-400' : 'from-amber-400 to-orange-400' }} rounded-full transition-all duration-700" style="width: {{ $persenProd }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] mt-1">
                                <span class="font-mono-num font-bold text-slate-300">{{ $tek->total_jo_bulan_ini ?? 0 }} / 330 JO</span>
                                <span class="text-slate-400 font-semibold">{{ $sisaProd > 0 ? "Sisa {$sisaProd} JO" : "Target Tercapai! 🎉" }}</span>
                            </div>
                        </div>

                        <!-- Col 4: Action Buttons (2 cols) -->
                        <div class="lg:col-span-2 flex items-center justify-end gap-2 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-3 lg:pt-0 lg:pl-4 flex-wrap">
                            <a href="{{ route('dashboard', ['teknisi_id' => $tek->id, 'tanggal' => $today]) }}" 
                                class="px-2.5 py-1.5 rounded-xl bg-amber-400/10 hover:bg-amber-400 text-amber-300 hover:text-slate-950 text-[10px] font-extrabold uppercase tracking-wider border border-amber-400/30 transition-all flex items-center gap-1 shadow-sm"
                                title="Lihat Job Order Hari Ini di Dashboard">
                                <span class="material-symbols-outlined text-xs">calendar_today</span>
                                <span>Hari Ini</span>
                            </a>

                            <a href="{{ route('dashboard', ['teknisi_id' => $tek->id, 'bulan' => $selectedBulan]) }}" 
                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] font-bold uppercase tracking-wider transition-all"
                                title="Filter Kabin Teknisi">
                                <span>Kabin</span>
                            </a>

                            @if($tek->jobOrders->count() > 0)
                                <button type="button" onclick="toggleAdminTodayJobsList('admin_jobs_{{ $tek->id }}', this)" 
                                    class="px-2 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-[10px] font-bold border border-slate-800 transition-all flex items-center gap-0.5"
                                    title="Rincian Pekerjaan Hari Ini">
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200">expand_more</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Accordion Dropdown Pekerjaan Hari Ini -->
                    @if($tek->jobOrders->count() > 0)
                        <div id="admin_jobs_{{ $tek->id }}" class="hidden pt-3 mt-3 border-t border-slate-800/80">
                            <div class="bg-slate-900/90 rounded-2xl p-3.5 border border-slate-800/80 space-y-2.5">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-300 border-b border-slate-800/60 pb-2">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-amber-400">format_list_bulleted</span>
                                        <span>Rincian {{ $tek->jobOrders->count() }} JO Selesai Hari Ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}):</span>
                                    </span>
                                    <span class="text-[11px] font-mono-num text-emerald-400 font-extrabold">Total: Rp {{ number_format($tek->pendapatan_hari_ini ?? 0, 0, ',', '.') }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 pt-1 max-h-60 overflow-y-auto no-scrollbar">
                                    @foreach($tek->jobOrders as $joItem)
                                        <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/60 flex flex-col justify-between text-xs space-y-1.5">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-extrabold text-white truncate">{{ $joItem->kategori }}</span>
                                                @if($joItem->status === 'berhasil')
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">Berhasil</span>
                                                @else
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 shrink-0">Gagal</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center justify-between text-[11px] font-mono-num">
                                                <span class="text-amber-400 font-bold">Rp {{ number_format($joItem->tarif, 0, ',', '.') }}</span>
                                                <span class="text-slate-400 text-[10px]">{{ $joItem->created_at ? $joItem->created_at->format('H:i') : '-' }}</span>
                                            </div>
                                            @if($joItem->catatan)
                                                <p class="text-[10px] text-slate-400 truncate italic">"{{ $joItem->catatan }}"</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-bold bg-slate-950/50 rounded-2xl border border-slate-800">
                    Belum ada data teknisi terdaftar dalam sistem.
                </div>
            @endforelse
        </div>
    </div>


    <!-- 4. ANALISIS KINERJA & LEADERBOARD TIM (2 COLS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- LEADERBOARD TEKNISI TERBAIK (7 cols) -->
        <div class="lg:col-span-7 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                        <span class="material-symbols-outlined text-lg">trophy</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Leaderboard Performance Teknisi</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Peringkat perolehan gaji terbanyak ({{ $periodLabel }})</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-400/10 text-amber-300 text-[10px] font-bold uppercase tracking-wider border border-amber-400/30">Top 5</span>
            </div>

            <div class="space-y-2.5">
                @forelse($teknisiLeaderboard as $index => $leader)
                    @php
                        $rankColors = [
                            0 => 'from-amber-400 to-orange-400 text-slate-950 border-amber-300',
                            1 => 'from-slate-300 to-slate-400 text-slate-950 border-slate-200',
                            2 => 'from-amber-700 to-amber-800 text-white border-amber-600',
                        ];
                        $badgeClass = $rankColors[$index] ?? 'bg-slate-800 text-slate-400 border-slate-700';
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-3 hover:border-slate-700 transition-all">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $badgeClass }} flex items-center justify-center font-black text-xs shrink-0 shadow-sm border">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white truncate flex items-center gap-1.5">
                                    <span>{{ $leader->name }}</span>
                                    @if($targetUserId == $leader->id)
                                        <span class="px-1.5 py-0.2 text-[9px] rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold uppercase">Terfilter</span>
                                    @endif
                                </h4>
                                <p class="text-[10px] text-slate-400 truncate">{{ $leader->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 text-right">
                            <!-- Target 330 JO Progress -->
                            <div class="hidden sm:block text-left w-28">
                                <div class="flex justify-between text-[10px] font-bold">
                                    <span class="text-slate-400">Target 330 JO</span>
                                    <span class="{{ ($leader->total_job ?? 0) >= 330 ? 'text-emerald-400' : 'text-amber-400' }} font-mono-num">{{ round((($leader->total_job ?? 0) / 330) * 100) }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden mt-0.5 border border-slate-800">
                                    <div class="h-full bg-gradient-to-r {{ ($leader->total_job ?? 0) >= 330 ? 'from-emerald-400 to-teal-400' : 'from-amber-400 to-orange-400' }}" style="width: {{ min(100, round((($leader->total_job ?? 0) / 330) * 100)) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs font-black font-mono-num text-amber-400 block">Rp {{ number_format($leader->total_pendapatan ?? 0, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-slate-400 block">{{ $leader->total_job ?? 0 }} JO</span>
                            </div>
                            <a href="{{ route('dashboard', ['teknisi_id' => $leader->id, 'bulan' => $selectedBulan]) }}" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-[10px] font-bold uppercase tracking-wider transition-all" title="Filter Kabin Teknisi Ini">
                                Kabin
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs font-bold bg-slate-950/40 rounded-2xl border border-slate-800">
                        Belum ada aktivitas job order pada bulan ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- BREAKDOWN KATEGORI PEKERJAAN (5 cols) -->
        <div class="lg:col-span-5 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-400 to-blue-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                        <span class="material-symbols-outlined text-lg">pie_chart</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Distribusi Pekerjaan</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Berdasarkan kategori job order teknisi</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2.5 max-h-72 overflow-y-auto no-scrollbar pr-1">
                @forelse($kategoriBreakdown as $kat)
                    @php
                        $maxCount = $kategoriBreakdown->max('count') ?: 1;
                        $percent = round(($kat->count / $maxCount) * 100);
                    @endphp
                    <div class="space-y-1.5 p-3 rounded-xl bg-slate-950/60 border border-slate-800/60">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-200 truncate max-w-[60%]">{{ $kat->kategori }}</span>
                            <span class="font-black font-mono-num text-cyan-300 text-[11px]">{{ $kat->count }} JO <span class="text-slate-400 font-normal">(Rp {{ number_format($kat->total_tarif, 0, ',', '.') }})</span></span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-900 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 transition-all duration-500" style="width: {{ $percent }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs font-bold bg-slate-950/40 rounded-2xl border border-slate-800">
                        Belum ada data kategori untuk bulan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    <!-- 5. GRAFIK TREN OPERASIONAL HARIAN TIM -->
    <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-indigo-500 text-slate-950 flex items-center justify-center font-black text-base shadow-md shadow-indigo-500/20">
                    <span class="material-symbols-outlined text-lg">show_chart</span>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-white">Tren Perolehan &amp; Pengeluaran Harian Tim</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Grafik total perolehan gaji tim per tanggal ({{ $periodLabel }})</p>
                </div>
            </div>
        </div>

        <div class="relative w-full h-64 sm:h-72">
            <canvas id="adminTrendChartCanvas"></canvas>
        </div>
    </div>


    <!-- 6. MASTER REKAP HARIAN & TABEL LOG AUDIT JOB ORDER TIM -->
    <div class="space-y-6">
        <!-- Rekap Harian Card -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-400 to-teal-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                        <span class="material-symbols-outlined text-lg">calendar_month</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Rekapitulasi Harian Tim Lapangan</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Rangkuman total pekerjaan dan pengeluaran per tanggal</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3 text-center">Volume JO</th>
                            <th class="py-3 px-3 text-center">Piket</th>
                            <th class="py-3 px-3 text-right">Total Gaji</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($rekapHarian as $rekap)
                            <tr class="hover:bg-slate-800/40 transition-colors {{ $rekap->tanggal == $today ? 'bg-amber-500/5' : '' }}">
                                <td class="py-3 px-3 font-mono font-bold text-slate-200">
                                    {{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('d M Y') }}
                                    @if($rekap->tanggal == $today)
                                        <span class="ml-1.5 px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Hari Ini</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center font-mono-num font-bold text-amber-400">{{ $rekap->total_job }} JO</td>
                                <td class="py-3 px-3 text-center font-mono-num font-semibold text-cyan-300">{{ $rekap->total_piket }} Kali</td>
                                <td class="py-3 px-3 text-right font-mono-num font-bold text-emerald-400">Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center">
                                    <a href="{{ route('dashboard', ['tanggal' => $rekap->tanggal, 'teknisi_id' => request('teknisi_id')]) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 text-[10px] font-bold uppercase tracking-wider transition-all inline-flex items-center gap-1">
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 text-xs font-bold">
                                    Tidak ada aktivitas pekerjaan dalam periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Master Log Audit Semua Job Orders -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Master Log Audit Job Orders Tim</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Daftar lengkap transaksi pekerjaan dengan kontrol audit admin</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800">
                    Total: {{ $detailJobOrders->total() }} Data
                </span>
            </div>

            <!-- Tabel Detail Job Orders -->
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Teknisi</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Tarif (Rp)</th>
                            <th class="py-3 px-3">Catatan</th>
                            <th class="py-3 px-3 text-center">Aksi Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($detailJobOrders as $job)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-slate-300 whitespace-nowrap">
                                    {{ $job->tanggal ? \Carbon\Carbon::parse($job->tanggal)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="font-bold text-white">{{ $job->user?->name ?? 'Teknisi' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $job->user?->email }}</div>
                                </td>
                                <td class="py-3 px-3 font-bold text-slate-200">
                                    {{ $job->kategori }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if($job->status === 'berhasil')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Berhasil</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">Gagal</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono-num font-bold text-emerald-400 whitespace-nowrap">
                                    Rp {{ number_format($job->tarif, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-slate-400 text-[11px] max-w-xs truncate">
                                    {{ $job->catatan ?: '-' }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                            onclick="openEditJobModal({{ $job->id }}, '{{ addslashes($job->kategori) }}', '{{ $job->status }}', '{{ $job->tanggal ? \Carbon\Carbon::parse($job->tanggal)->format('Y-m-d') : '' }}', '{{ addslashes($job->catatan ?? '') }}', {{ $job->tarif }})"
                                            class="p-1.5 rounded-lg bg-slate-800 hover:bg-amber-400 hover:text-slate-950 text-slate-300 transition-all cursor-pointer" title="Edit Job Order">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-500 hover:text-white text-slate-400 transition-all cursor-pointer" title="Hapus Job Order">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 text-xs font-bold">
                                    Tidak ada data job order yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($detailJobOrders->hasPages())
                <div class="pt-4 border-t border-slate-800/80">
                    {{ $detailJobOrders->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- MODAL EDIT JOB ORDER UNTUK ADMIN -->
<div id="adminEditJobModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center hidden p-4 transition-all duration-300">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-lg shadow-2xl relative">
        <button type="button" onclick="closeEditJobModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl cursor-pointer">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">edit_note</span>
            </div>
            <div>
                <h3 class="text-base font-black text-white">Koreksi Job Order Teknisi</h3>
                <p class="text-xs text-slate-400">Audit dan update data pekerjaan yang tercatat</p>
            </div>
        </div>

        <form id="adminEditJobForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Kategori Pekerjaan</label>
                <input type="text" id="editJobKategori" disabled class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-400 font-bold text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Status</label>
                    <select name="status" id="editJobStatus" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white font-bold text-sm focus:border-amber-400 outline-none">
                        <option value="berhasil">BERHASIL</option>
                        <option value="gagal">GAGAL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="editJobTanggal" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white font-bold text-sm focus:border-amber-400 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Tarif Snapshot (Rp)</label>
                <input type="number" name="tarif" id="editJobTarif" min="0" step="1000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-emerald-400 font-mono-num font-bold text-base focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Catatan Tambahan</label>
                <textarea name="catatan" id="editJobCatatan" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white text-xs focus:border-amber-400 outline-none placeholder-slate-500" placeholder="Catatan merchant / lokasi"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditJobModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-300 hover:to-orange-300 text-slate-950 font-black text-xs uppercase tracking-wider cursor-pointer shadow-lg shadow-amber-500/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let currentAdminTeknisiTab = 'all';

    function setAdminTeknisiTabFilter(tab, btn) {
        currentAdminTeknisiTab = tab;
        document.querySelectorAll('.admin-tab-btn').forEach(b => {
            b.className = "admin-tab-btn px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all text-slate-400 hover:text-white";
        });
        btn.className = "admin-tab-btn active px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold uppercase tracking-wider transition-all bg-amber-400 text-slate-950 shadow-sm";
        filterAdminTeknisiCards();
    }

    function filterAdminTeknisiCards() {
        const query = (document.getElementById('searchAdminTeknisi')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.admin-teknisi-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const email = card.getAttribute('data-email') || '';
            const status = card.getAttribute('data-status') || '';

            const matchesQuery = !query || name.includes(query) || email.includes(query);
            const matchesTab = (currentAdminTeknisiTab === 'all') || (status === currentAdminTeknisiTab);

            if (matchesQuery && matchesTab) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function toggleAdminTodayJobsList(id, btn) {
        const target = document.getElementById(id);
        if (!target) return;
        const isHidden = target.classList.contains('hidden');
        if (isHidden) {
            target.classList.remove('hidden');
            if (btn) {
                const icon = btn.querySelector('.material-symbols-outlined');
                if (icon) icon.style.transform = 'rotate(180deg)';
            }
        } else {
            target.classList.add('hidden');
            if (btn) {
                const icon = btn.querySelector('.material-symbols-outlined');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        }
    }

    function openEditJobModal(id, kategori, status, tanggal, catatan, tarif) {
        const modal = document.getElementById('adminEditJobModal');
        const form = document.getElementById('adminEditJobForm');
        form.action = `/job-orders/${id}`;
        document.getElementById('editJobKategori').value = kategori;
        document.getElementById('editJobStatus').value = status;
        document.getElementById('editJobTanggal').value = tanggal;
        document.getElementById('editJobTarif').value = tarif;
        document.getElementById('editJobCatatan').value = catatan;
        modal.classList.remove('hidden');
    }

    function closeEditJobModal() {
        document.getElementById('adminEditJobModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Admin Daily Trend Line Chart
        const ctx = document.getElementById('adminTrendChartCanvas');
        if (ctx) {
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const incomeData = {!! json_encode($chartIncomeData ?? []) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Gaji Tim (Rp)',
                        data: incomeData,
                        borderColor: '#f59e0b',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(245, 158, 11, 0.35)');
                            gradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.35,
                        borderWidth: 3,
                        pointBackgroundColor: '#f59e0b',
                        pointHoverRadius: 7
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
                            bodyColor: '#fbbf24',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Total Budget: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
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
@endsection

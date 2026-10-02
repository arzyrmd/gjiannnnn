@extends('layouts.app')

@section('title', 'Dashboard & Rekap Gajian')

@section('content')
<div class="space-y-6 sm:space-y-8">

    @if(auth()->user()->isAdmin())
        <!-- ADMIN PANEL HEADER & TECHNICIAN FILTER BAR -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-amber-500/30 p-4 sm:p-5 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative overflow-hidden">
            <div class="absolute -top-12 -left-12 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-3.5 relative z-10">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black shadow-lg shadow-amber-500/20 shrink-0">
                    <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/30">PANEL UTAMA ADMIN</span>
                        <span class="text-xs font-bold text-slate-300 font-mono">| {{ auth()->user()->name }}</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Monitoring Rekap Gaji &amp; Job Order Seluruh Teknisi Lapangan</p>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto relative z-10">
                @if(request('bulan'))
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                @endif
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif

                <label for="teknisi_id" class="text-xs font-extrabold text-slate-300 uppercase tracking-wider whitespace-nowrap hidden sm:inline">Kabin Teknisi:</label>
                <select name="teknisi_id" id="teknisi_id" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-950 border border-amber-500/40 text-amber-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all cursor-pointer shadow-inner">
                    <option value="all" {{ ($targetUserId === null) ? 'selected' : '' }}>👥 Semua Teknisi (Ringkasan Global)</option>
                    @foreach($allTeknisi as $teknisiItem)
                        <option value="{{ $teknisiItem->id }}" {{ ($targetUserId == $teknisiItem->id) ? 'selected' : '' }}>
                            👤 {{ $teknisiItem->name }} ({{ $teknisiItem->email }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @else
        <!-- TECHNICIAN PANEL HEADER -->
        <div class="rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-cyan-500/30 p-4 sm:p-5 shadow-2xl flex items-center justify-between gap-4 relative overflow-hidden">
            <div class="absolute -top-12 -left-12 w-36 h-36 bg-cyan-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-3.5 relative z-10">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-500 text-slate-950 flex items-center justify-center font-black shadow-lg shadow-cyan-500/20 shrink-0">
                    <span class="material-symbols-outlined text-2xl">badge</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-cyan-300 bg-cyan-500/10 px-2.5 py-0.5 rounded-lg border border-cyan-500/30">KABIN GAJIAN TEKNISI</span>
                        <span class="text-xs font-extrabold text-white font-mono">| {{ auth()->user()->name }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Kalkulator, AI Input &amp; Rekap Pendapatan Pribadi Teknisi</p>
                </div>
            </div>
        </div>
    @endif

    <!-- 1. TOP METRICS SPOTLIGHT (2 MASTER CARDS: HARI INI & BULAN INI) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        
        <!-- Master Card 1: Hari Ini -->
        <div class="group relative rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-emerald-500/30 p-5 sm:p-6 shadow-2xl shadow-emerald-950/20 flex flex-col justify-between space-y-4 overflow-hidden hover:border-emerald-500/50 transition-all duration-300">
            <!-- Ambient Card Glow -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            
            <div class="flex items-center justify-between gap-2 relative z-10">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse shrink-0 shadow-lg shadow-emerald-400/50"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-400">Ringkasan Hari Ini</span>
                </div>
                <span class="text-[11px] sm:text-xs font-mono-num font-bold text-slate-300 bg-slate-950/80 px-3 py-1 rounded-xl border border-slate-800 shrink-0 shadow-inner">
                    {{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end pt-2 relative z-10">
                <div class="sm:col-span-7">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Pendapatan</span>
                    <div id="metricPendapatanHariIni" class="text-2xl sm:text-3xl md:text-4xl font-black font-mono-num text-gradient-emerald tracking-tight mt-1 transition-all break-words">
                        Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="sm:col-span-5 text-left sm:text-right sm:border-l border-t sm:border-t-0 border-slate-800/80 pt-3 sm:pt-0 sm:pl-5 space-y-1.5">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Volume Job (JO)</span>
                        <div id="metricTotalJobHariIni" class="text-xl sm:text-2xl font-black font-mono-num text-amber-400 tracking-tight transition-all">
                            {{ $totalJobHariIni }} <span class="text-xs text-slate-400 font-bold">JO</span>
                        </div>
                    </div>
                    <div class="pt-1.5 border-t border-slate-800/60">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Piket</span>
                        <div id="metricTotalPiketHariIni" class="text-sm font-black font-mono-num text-cyan-300 transition-all">
                            {{ $totalPiketHariIni }} <span class="text-[11px] text-slate-400 font-bold">Kali</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Card 2: Bulan Ini -->
        <div class="group relative rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-cyan-500/30 p-5 sm:p-6 shadow-2xl shadow-cyan-950/20 flex flex-col justify-between space-y-4 overflow-hidden hover:border-cyan-500/50 transition-all duration-300">
            <!-- Ambient Card Glow -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition-all"></div>
            
            <div class="flex items-center justify-between gap-2 relative z-10">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 shrink-0 shadow-lg shadow-cyan-400/50"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-cyan-400">{{ $periodTitleCard }}</span>
                </div>
                <span class="text-[11px] sm:text-xs font-mono-num font-bold text-slate-300 bg-slate-950/80 px-3 py-1 rounded-xl border border-slate-800 shrink-0 shadow-inner">
                    {{ $periodLabel }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end pt-2 relative z-10">
                <div class="sm:col-span-7">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Pendapatan</span>
                    <div id="metricPendapatanBulanIni" class="text-2xl sm:text-3xl md:text-4xl font-black font-mono-num text-gradient-cyan tracking-tight mt-1 transition-all break-words">
                        Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="sm:col-span-5 text-left sm:text-right sm:border-l border-t sm:border-t-0 border-slate-800/80 pt-3 sm:pt-0 sm:pl-5 space-y-1.5">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Volume Job (JO)</span>
                        <div id="metricTotalJobBulanIni" class="text-xl sm:text-2xl font-black font-mono-num text-indigo-300 tracking-tight transition-all">
                            {{ $totalJobBulanIni }} <span class="text-xs text-slate-400 font-bold">JO</span>
                        </div>
                    </div>
                    <div class="pt-1.5 border-t border-slate-800/60">
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

    <!-- GOAL METER & DAILY TREND ANALYTICS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 my-6">
        <!-- 1. GOAL METER & PROGRESS CARD (5 cols) -->
        <div class="lg:col-span-5 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-gradient-to-br from-emerald-500/10 to-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div>
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-400 to-cyan-500 text-slate-950 flex items-center justify-center font-black text-base shadow-md shadow-emerald-500/20">
                            <span class="material-symbols-outlined text-lg">workspace_premium</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-1.5">
                                Target Gaji Bulanan
                            </h3>
                            <p class="text-[11px] text-slate-400 font-medium">Goal Meter Personal</p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="openTargetModal()" class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs font-extrabold flex items-center gap-1 border border-emerald-500/30 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>Ubah</span>
                    </button>
                </div>

                <!-- Main Target Number & Percentage -->
                <div class="flex items-baseline justify-between mb-2">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Tercapai (Gaji Bersih)</span>
                        <div class="text-2xl sm:text-3xl font-black font-mono-num text-emerald-400 tracking-tight">
                            Rp {{ number_format($tercapaiPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Target Bulanan</span>
                        <div class="text-lg font-extrabold font-mono-num text-slate-300">
                            Rp {{ number_format($targetPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1.5 my-4">
                    <div class="flex justify-between items-center text-xs font-black">
                        <span class="text-slate-400">Pencapaian Progress</span>
                        <span class="{{ $persenTarget >= 100 ? 'text-emerald-400' : 'text-cyan-400' }} font-mono-num font-extrabold">{{ $persenTarget }}%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800/80 shadow-inner">
                        <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ min(100, $persenTarget) }}%"></div>
                    </div>
                </div>

                <!-- Stats summary grid -->
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-800/60">
                    <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800/60">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Sisa Target</span>
                        <span class="text-sm sm:text-base font-black font-mono-num {{ $sisaTarget > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $sisaTarget > 0 ? 'Rp ' . number_format($sisaTarget, 0, ',', '.') : 'Tercapai! 🎉' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800/60">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Perlu/Hari ({{ $sisaHari }} H Sisa)</span>
                        <span class="text-sm sm:text-base font-black font-mono-num text-cyan-300">
                            {{ $sisaTarget > 0 ? 'Rp ' . number_format($rataRataHarianDibutuhkan, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. DAILY TREND CHART (7 cols) -->
        <div class="lg:col-span-7 rounded-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 shadow-2xl flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-indigo-500 text-slate-950 flex items-center justify-center font-black text-base shadow-md shadow-indigo-500/20">
                        <span class="material-symbols-outlined text-lg">show_chart</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Trend Pendapatan Harian</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Grafik perolehan per tanggal ({{ $periodLabel }})</p>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-56 sm:h-64">
                <canvas id="trendChartCanvas"></canvas>
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

    @if(auth()->user()->isAdmin())
    <!-- ADMIN EXECUTIVE ANALYTICS SPOTLIGHT -->
    <div class="space-y-6">
        <!-- 1. ADMIN KPI METRIC CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- KPI 1: Total Teknisi -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-xl flex items-center gap-3 relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-black shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">engineering</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block truncate">Total Teknisi</span>
                    <span class="text-base sm:text-xl font-black font-mono-num text-white">{{ $totalTeknisiCount }} <span class="text-xs text-slate-400 font-semibold">Orang</span></span>
                </div>
            </div>

            <!-- KPI 2: Success Rate -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-xl flex items-center gap-3 relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center font-black shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">verified</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block truncate">Rate Success</span>
                    <span class="text-base sm:text-xl font-black font-mono-num text-emerald-400">{{ $successRate }}%</span>
                </div>
            </div>

            <!-- KPI 3: Job Berhasil vs Gagal -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 shadow-xl flex items-center gap-3 relative overflow-hidden group hover:border-cyan-500/40 transition-all">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center font-black shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">task_alt</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-400 block truncate">Berhasil / Gagal</span>
                    <span class="text-xs sm:text-sm font-black font-mono-num text-slate-200">
                        <span class="text-emerald-400 font-extrabold">{{ $totalSuccessJobs }}</span> / <span class="text-rose-400 font-extrabold">{{ $totalFailedJobs }}</span>
                    </span>
                </div>
            </div>

            <!-- KPI 4: Quick Action Nav Hub -->
            <div class="p-3 rounded-2xl bg-slate-900/80 border border-amber-500/30 shadow-xl flex items-center justify-around gap-1">
                <a href="{{ route('users.index') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-amber-400 transition-colors" title="Kelola User &amp; Teknisi">
                    <span class="material-symbols-outlined text-lg">group_add</span>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider">User</span>
                </a>
                <a href="{{ route('tarifs.index') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-amber-400 transition-colors" title="Pengaturan Master Tarif">
                    <span class="material-symbols-outlined text-lg">price_change</span>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider">Tarif</span>
                </a>
                <a href="{{ route('export.csv', ['bulan' => $selectedBulan]) }}" class="flex flex-col items-center gap-1 p-2 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-amber-400 transition-colors" title="Export Rekap CSV">
                    <span class="material-symbols-outlined text-lg">download</span>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider">Export</span>
                </a>
            </div>
        </div>

        <!-- 2. ADMIN GRID: LEADERBOARD TEKNISI & CATEGORY ANALYTICS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- LEADERBOARD TEKNISI TERBAIK (7 cols) -->
            <div class="lg:col-span-7 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                            <span class="material-symbols-outlined text-base">trophy</span>
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
                        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-3 hover:border-slate-700 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 rounded-xl bg-gradient-to-br {{ $badgeClass }} flex items-center justify-center font-black text-xs shrink-0 shadow-sm border">
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

            <!-- ANALYTICS KATEGORI PEKERJAAN (5 cols) -->
            <div class="lg:col-span-5 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-400 to-blue-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                            <span class="material-symbols-outlined text-base">pie_chart</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-white">Breakdown Pekerjaan</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Distribusi kategori job order teknisi</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 max-h-72 overflow-y-auto no-scrollbar pr-1">
                    @forelse($kategoriBreakdown as $kat)
                        @php
                            $maxCount = $kategoriBreakdown->max('count') ?: 1;
                            $percent = round(($kat->count / $maxCount) * 100);
                        @endphp
                        <div class="space-y-1.5 p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/60">
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
    </div>
    @endif

    @unless(auth()->user()->isAdmin())
    <!-- 2. QUICK INPUT JOB ORDER FORM -->
    <div class="relative rounded-3xl bg-slate-900/80 backdrop-blur-2xl border border-slate-800/90 p-5 sm:p-6 md:p-7 shadow-2xl space-y-6 overflow-hidden group hover:border-amber-500/30 transition-all duration-300">
        <!-- Background Ambient Glow Orbs -->
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-amber-500/15 transition-all duration-500"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Top Accent Header Line -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-orange-500 to-emerald-400"></div>

        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 gap-3 relative z-10">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-400/20 to-orange-500/20 border border-amber-400/30 text-amber-400 flex items-center justify-center font-black shrink-0 shadow-md shadow-amber-500/10">
                    <span class="material-symbols-outlined text-2xl">post_add</span>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black uppercase tracking-wider text-white flex items-center gap-2">
                        <span>Catat Job Order Baru</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Input pekerjaan atau piket harian teknisi dengan dukungan akumulasi kuantitas (batch JO).</p>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-400/10 border border-amber-400/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider shrink-0 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Input Cepat</span>
            </div>
        </div>

        <form action="{{ route('job-orders.store') }}" method="POST" class="space-y-5 relative z-10" id="quickJobForm" onsubmit="handleQuickJobFormSubmit(event)">
            @csrf

            <!-- Form Row 1: Select Kategori, Status Radio, Jumlah (Qty), Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                <!-- 1. Select Kategori Tugas (Width 4 col) -->
                <div class="space-y-2 md:col-span-4">
                    <label for="tarif_id" class="flex items-center gap-1.5 text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-sm text-amber-400">category</span>
                        <span>Kategori Tugas</span>
                        <span class="text-amber-400">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            name="tarif_id" 
                            id="tarif_id" 
                            required 
                            class="w-full px-4 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-bold focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all text-sm min-h-[48px] cursor-pointer shadow-inner appearance-none pr-10"
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
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xl">unfold_more</span>
                    </div>
                </div>

                <!-- 2. Status Radio Options (Width 3 col) -->
                <div class="space-y-2 md:col-span-3">
                    <label class="flex items-center gap-1.5 text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-sm text-amber-400">task_alt</span>
                        <span>Status Job</span>
                        <span class="text-amber-400">*</span>
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
                            <div class="w-full py-3 px-2 rounded-2xl bg-slate-950/90 border border-slate-800 peer-checked:border-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:text-emerald-300 peer-checked:shadow-lg peer-checked:shadow-emerald-500/10 text-slate-400 font-extrabold text-center text-xs uppercase tracking-wider transition-all min-h-[48px] flex items-center justify-center gap-1">
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
                            <div class="w-full py-3 px-2 rounded-2xl bg-slate-950/90 border border-slate-800 peer-checked:border-rose-500 peer-checked:bg-rose-500/15 peer-checked:text-rose-300 peer-checked:shadow-lg peer-checked:shadow-rose-500/10 text-slate-400 font-extrabold text-center text-xs uppercase tracking-wider transition-all min-h-[48px] flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-base">cancel</span>
                                <span>GAGAL</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Jumlah (Quantity) Input (Width 2 col) -->
                <div class="space-y-2 md:col-span-2">
                    <label for="quantity" class="flex items-center gap-1.5 text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-sm text-amber-400">tag</span>
                        <span>Jumlah (JO)</span>
                        <span class="text-amber-400">*</span>
                    </label>
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
                        class="w-full px-3 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono-num font-black text-center focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all text-sm min-h-[48px]"
                    >
                </div>

                <!-- 4. Tanggal Input (Width 3 col) -->
                <div class="space-y-2 md:col-span-3">
                    <div class="flex items-center justify-between gap-1">
                        <label for="tanggal" class="flex items-center gap-1.5 text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                            <span class="material-symbols-outlined text-sm text-amber-400">calendar_today</span>
                            <span>Tanggal</span>
                            <span class="text-amber-400">*</span>
                        </label>
                        <div class="flex items-center gap-1 text-[10px]">
                            <button type="button" onclick="setQuickFormDate('today')" class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-amber-400/20 hover:text-amber-300 text-slate-300 font-bold transition-all cursor-pointer border border-slate-700" title="Set tanggal hari ini">Today</button>
                            <button type="button" onclick="setQuickFormDate('yesterday')" class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-amber-400/20 hover:text-amber-300 text-slate-300 font-bold transition-all cursor-pointer border border-slate-700" title="Set tanggal kemarin">Kemarin</button>
                        </div>
                    </div>
                    <input 
                        type="date" 
                        name="tanggal" 
                        id="tanggal" 
                        value="{{ old('tanggal', $today) }}" 
                        required 
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono font-bold focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all text-sm min-h-[48px]"
                    >
                </div>
            </div>

            <!-- Form Row 2: Catatan & Custom Nominal Input -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-1">
                <div class="sm:col-span-8 relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-lg">edit_note</span>
                    <input 
                        type="text" 
                        name="catatan" 
                        placeholder="Catatan tambahan / No. Merchant / Lokasi (opsional)" 
                        value="{{ old('catatan') }}"
                        class="w-full pl-10 pr-4 py-3 rounded-2xl bg-slate-950/90 border border-slate-800 text-slate-200 text-xs focus:border-amber-400 focus:outline-none transition-all min-h-[44px]"
                    >
                </div>

                <!-- Custom Nominal Input (Tampil jika Piket Event dipilih) -->
                <div id="customTarifContainer" class="sm:col-span-4 hidden">
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-400 font-bold text-xs">Rp</span>
                        <input 
                            type="number" 
                            name="custom_tarif" 
                            id="custom_tarif" 
                            min="0" 
                            step="1000"
                            placeholder="Isi Nominal Custom (Rp)" 
                            oninput="updatePricePreview()"
                            class="w-full pl-9 pr-4 py-3 rounded-2xl bg-slate-950/95 border border-amber-400 text-amber-300 font-mono-num font-extrabold text-xs focus:outline-none min-h-[44px] shadow-lg shadow-amber-500/10"
                        >
                    </div>
                </div>
            </div>

            <!-- Action & Preview Row -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-4 border-t border-slate-800/80">
                <div class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-950/95 border border-emerald-500/30 flex items-center justify-between sm:justify-start gap-4 shadow-xl shadow-emerald-500/5 group/snapshot">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Tarif Snapshot:</span>
                    </div>
                    <span id="pricePreview" class="text-2xl font-black font-mono-num text-gradient-emerald tracking-tight">Rp 0</span>
                </div>

                <button 
                    type="submit" 
                    class="w-full sm:w-auto py-3.5 px-8 rounded-2xl bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 hover:from-amber-300 hover:to-orange-300 active:scale-[0.98] text-slate-950 font-black text-sm uppercase tracking-wider shadow-xl shadow-amber-500/25 hover:shadow-amber-500/40 transition-all duration-200 cursor-pointer min-h-[50px] flex items-center justify-center gap-2.5 border border-amber-300/30"
                >
                    <span class="material-symbols-outlined text-xl">save</span>
                    <span>SIMPAN JOB ORDER</span>
                </button>
            </div>
        </form>
    </div>
    @endunless


    <!-- MOBILE TAB SWITCHER (Rekap Harian vs Detail Transaksi) -->
    <div class="block sm:hidden mb-2">
        <div class="grid grid-cols-2 gap-2 p-1.5 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl backdrop-blur-xl">
            <button 
                type="button" 
                id="btnMobileTabRekap"
                onclick="switchMobileTab('rekap')"
                class="py-3 px-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950"
            >
                <span class="material-symbols-outlined text-base">calendar_month</span>
                <span>Rekap Harian</span>
            </button>
            <button 
                type="button" 
                id="btnMobileTabDetail"
                onclick="switchMobileTab('detail')"
                class="py-3 px-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 text-slate-400 bg-slate-950 hover:bg-slate-800"
            >
                <span class="material-symbols-outlined text-base">receipt_long</span>
                <span>Detail Jobs</span>
                @if($detailJobOrders->total() > 0)
                    <span class="px-1.5 py-0.5 rounded-md bg-amber-400/20 text-amber-400 text-[10px] font-mono-num font-bold border border-amber-400/30">
                        {{ $detailJobOrders->total() }}
                    </span>
                @endif
            </button>
        </div>
    </div>


    <!-- 3. REKAP HARIAN DALAM BULAN & FILTER / EKSPOR UNIFIED CARD -->
    <div id="sectionRekapContainer" class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl space-y-5">
        <!-- Unified Header: Title & Export Buttons -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
            <div>
                <h3 class="text-base md:text-lg font-black uppercase text-white tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 text-xl">date_range</span>
                    <span>Rekap Harian</span>
                    <span class="text-amber-400 text-sm sm:text-base">({{ $periodLabel }})</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Akumulasi total job order murni dan pendapatan harian.</p>
            </div>

            <!-- Export Buttons -->
            <div class="grid grid-cols-2 gap-2.5 w-full md:w-auto">
                <a 
                    href="{{ route('export.csv', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                    class="px-4 py-2.5 rounded-xl bg-emerald-950/80 hover:bg-emerald-900/80 border border-emerald-500/40 text-emerald-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all shadow-sm hover:shadow-emerald-950/40"
                >
                    <span class="material-symbols-outlined text-base">download</span>
                    <span>Export CSV</span>
                </a>

                <a 
                    href="{{ route('export.pdf', ['bulan' => $selectedBulan, 'start_date' => $startDate, 'end_date' => $endDate, 'teknisi_id' => request('teknisi_id')]) }}" 
                    target="_blank"
                    class="px-4 py-2.5 rounded-xl bg-cyan-950/80 hover:bg-cyan-900/80 border border-cyan-500/40 text-cyan-300 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all shadow-sm hover:shadow-cyan-950/40"
                >
                    <span class="material-symbols-outlined text-base">print</span>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>

        <!-- Integrated Filter Controls -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4 space-y-4">
            <div class="grid grid-cols-2 sm:flex items-center gap-2 border-b border-slate-800/80 pb-3">
                <button 
                    type="button"
                    onclick="switchFilterMode('bulan')"
                    id="tabBulan"
                    class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all cursor-pointer text-center {{ (!$startDate && !$endDate) ? 'bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-400 hover:bg-slate-700/80 hover:text-slate-200' }}"
                >
                    Per Bulan
                </button>
                <button 
                    type="button"
                    onclick="switchFilterMode('rentang')"
                    id="tabRentang"
                    class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all cursor-pointer text-center {{ ($startDate || $endDate) ? 'bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-400 hover:bg-slate-700/80 hover:text-slate-200' }}"
                >
                    Rentang Tanggal
                </button>
                
                @if($startDate || $endDate || $selectedBulan !== \Carbon\Carbon::now()->format('Y-m') || request('teknisi_id'))
                    <a href="{{ route('dashboard') }}" class="col-span-2 sm:col-span-1 sm:ml-auto text-center text-xs text-rose-400 hover:underline font-bold uppercase tracking-wider py-1">
                        Reset Filter
                    </a>
                @endif
            </div>

            <!-- Form Filter Per Bulan -->
            <form id="formFilterBulan" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'hidden' : 'block' }}">
                @if(request('teknisi_id'))
                    <input type="hidden" name="teknisi_id" value="{{ request('teknisi_id') }}">
                @endif
                <div class="flex flex-col sm:flex-row items-end gap-3">
                    <div class="w-full sm:w-64 space-y-1.5">
                        <label for="bulan" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Pilih Bulan &amp; Tahun
                        </label>
                        <input 
                            type="month" 
                            name="bulan" 
                            id="bulan" 
                            value="{{ $selectedBulan }}" 
                            onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]"
                        >
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all min-h-[44px]">
                        Tampilkan Rekap
                    </button>
                </div>
            </form>

            <!-- Form Filter Rentang Tanggal -->
            <form id="formFilterRentang" method="GET" action="{{ route('dashboard') }}" class="{{ ($startDate || $endDate) ? 'block' : 'hidden' }}">
                <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                @if(request('teknisi_id'))
                    <input type="hidden" name="teknisi_id" value="{{ request('teknisi_id') }}">
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div class="space-y-1.5">
                        <label for="start_date" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Dari Tanggal
                        </label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            value="{{ $startDate }}" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="end_date" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            Sampai Tanggal
                        </label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            value="{{ $endDate }}" 
                            required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]"
                        >
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="w-full py-2.5 px-6 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all min-h-[44px]"
                        >
                            Terapkan Rentang
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-5">Tanggal Pekerjaan</th>
                        <th class="py-4 px-5 text-center">Volume Job Order (JO)</th>
                        <th class="py-4 px-5 text-right">Pendapatan Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-medium">
                    @forelse($rekapHarian as $rekap)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-5 font-bold text-slate-200">
                                <a href="{{ route('dashboard', ['bulan' => $selectedBulan, 'tanggal' => $rekap->tanggal]) }}" class="hover:text-amber-400 transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-400/80 text-base">event</span>
                                    <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->translatedFormat('j F Y') }}</span>
                                </a>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="font-mono-num font-bold text-amber-300 text-sm">
                                    {{ $rekap->total_job }} JO
                                </div>
                                @if(($rekap->total_piket ?? 0) > 0)
                                    <div class="font-mono-num font-semibold text-cyan-400 text-[11px] mt-0.5">
                                        + {{ $rekap->total_piket }} Piket
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right font-mono-num font-black text-emerald-400 text-base">
                                Rp {{ number_format($rekap->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-500 uppercase tracking-widest font-bold">
                                Belum ada transaksi job order di bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Bottom Summary Total Row -->
                <tfoot class="bg-slate-950/90 border-t-2 border-amber-400/50 font-black text-sm text-slate-100">
                    <tr>
                        <td class="py-4 px-5 uppercase tracking-wider text-amber-400">
                            TOTAL AKUMULASI BULAN THIS
                        </td>
                        <td class="py-4 px-5 text-center">
                            <div class="font-mono-num text-amber-300 text-base font-black">
                                {{ $rekapHarian->sum('total_job') }} JO
                            </div>
                            @if($rekapHarian->sum('total_piket') > 0)
                                <div class="font-mono-num text-cyan-400 text-xs font-bold mt-0.5">
                                    + {{ $rekapHarian->sum('total_piket') }} Piket
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right font-mono-num text-emerald-400 text-xl">
                            Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Card View (For Small Screens) -->
        <div class="block sm:hidden space-y-3">
            @forelse($rekapHarian as $rekap)
                <a href="{{ route('dashboard', ['bulan' => $selectedBulan, 'tanggal' => $rekap->tanggal]) }}" class="block p-4 rounded-2xl bg-slate-950/80 border border-slate-800 hover:border-amber-400/50 transition-colors space-y-2.5">
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
                            <span class="text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-lg border border-amber-400/20 text-[11px]">
                                {{ $rekap->total_job }} JO
                            </span>
                            @if(($rekap->total_piket ?? 0) > 0)
                                <span class="text-cyan-300 bg-cyan-400/10 px-2.5 py-0.5 rounded-lg border border-cyan-400/20 text-[11px]">
                                    + {{ $rekap->total_piket }} Piket
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-2xl bg-slate-950/80 border border-slate-800">
                    Belum ada transaksi job order di bulan ini.
                </div>
            @endforelse

            @if($rekapHarian->count() > 0)
                <!-- Total Accumulation Card on Mobile -->
                <div class="p-4 rounded-2xl bg-slate-950/90 border border-amber-400/50 space-y-2 shadow-lg">
                    <div class="text-xs font-black uppercase text-amber-400 tracking-wider">
                        Total Akumulasi Bulan Ini
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-800/80 pt-2">
                        <div class="font-mono-num text-xs font-bold text-amber-300">
                            {{ $rekapHarian->sum('total_job') }} JO
                            @if($rekapHarian->sum('total_piket') > 0)
                                <span class="text-cyan-400 text-[11px] block">+ {{ $rekapHarian->sum('total_piket') }} Piket</span>
                            @endif
                        </div>
                        <div class="text-lg font-black font-mono-num text-emerald-400">
                            Rp {{ number_format($rekapHarian->sum('total_pendapatan'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- 5. DETAIL JOB ORDERS LIST (WITH EDIT & DELETE) -->
    <div id="sectionDetailContainer" class="rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 p-5 sm:p-6 md:p-7 shadow-2xl space-y-5 hidden sm:block">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4">
            <div>
                <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 text-xl">receipt_long</span>
                    <span>Detail Transaksi Job Order</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian entri per kategori tugas beserta status dan nilai tarif snapshot.</p>
            </div>
            @if($startDate && $endDate)
                <span class="text-xs bg-amber-400/10 border border-amber-400/40 text-amber-400 px-3 py-1 rounded-xl font-bold shrink-0 shadow-sm">
                    Rentang: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </span>
            @endif
        </div>

        <!-- Desktop View Table -->
        <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-800/80 shadow-inner">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-4">#</th>
                        <th class="py-4 px-4">Tanggal</th>
                        @if(auth()->user()->isAdmin())
                            <th class="py-4 px-4">Teknisi</th>
                        @endif
                        <th class="py-4 px-4">Kategori Tugas</th>
                        <th class="py-4 px-4 text-center">Status</th>
                        <th class="py-4 px-4 text-right">Tarif (Snapshot)</th>
                        <th class="py-4 px-4">Catatan</th>
                        <th class="py-4 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-medium">
                    @forelse($detailJobOrders as $index => $job)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 text-slate-500 font-mono-num">
                                {{ $detailJobOrders->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 font-mono-num text-slate-300 font-bold whitespace-nowrap">
                                {{ $job->tanggal->format('d/m/Y') }}
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="py-4 px-4 font-bold text-amber-300 whitespace-nowrap">
                                    {{ $job->user ? $job->user->name : 'System' }}
                                </td>
                            @endif
                            <td class="py-4 px-4 font-bold text-white">
                                {{ $job->kategori }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($job->status === 'berhasil')
                                    <span class="px-3 py-1 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 font-black text-[10px] uppercase tracking-wider shadow-sm">
                                        BERHASIL
                                    </span>
                                @else
                                    <span class="px-3.5 py-1 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 font-black text-[10px] uppercase tracking-wider shadow-sm">
                                        GAGAL
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right font-mono-num font-black text-amber-400 text-sm whitespace-nowrap">
                                Rp {{ number_format($job->tarif, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-slate-400 max-w-xs truncate">
                                {{ $job->catatan ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        onclick="openEditModal({{ json_encode($job) }})" 
                                        class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px]"
                                    >
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1 min-h-[34px]">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500 uppercase tracking-widest font-bold">
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
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
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
                                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 font-black text-[9px] uppercase tracking-wider">
                                    BERHASIL
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-lg bg-rose-950/80 border border-rose-500/40 text-rose-300 font-black text-[9px] uppercase tracking-wider">
                                    GAGAL
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h4 class="font-bold text-white text-xs">{{ $job->kategori }}</h4>
                        @if($job->catatan)
                            <p class="text-[11px] text-slate-400 bg-slate-900/80 p-2.5 rounded-xl border border-slate-800">
                                {{ $job->catatan }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
                        <div class="font-mono-num font-black text-amber-400 text-base">
                            Rp {{ number_format($job->tarif, 0, ',', '.') }}
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button 
                                onclick="openEditModal({{ json_encode($job) }})" 
                                class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]"
                            >
                                <span class="material-symbols-outlined text-xs">edit</span>
                                <span>Edit</span>
                            </button>

                            <form action="{{ route('job-orders.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus job order ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-800/60 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 min-h-[36px]">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-bold uppercase tracking-widest text-xs rounded-2xl bg-slate-950/80 border border-slate-800">
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
<div id="editModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900/95 border border-amber-400/60 rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3.5">
            <h3 class="text-base font-black uppercase text-white tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-xl">edit</span>
                <span>Edit Job Order</span>
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white font-bold text-xs uppercase tracking-wider p-1 cursor-pointer">
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
                <input type="text" id="edit_kategori_info" readonly class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-800 text-slate-300 font-bold text-xs cursor-not-allowed" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Status</label>
                    <select name="status" id="edit_status" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]">
                        <option value="berhasil">BERHASIL</option>
                        <option value="gagal">GAGAL</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="edit_tanggal" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="edit_tarif" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Nominal Tarif (Rp)</label>
                <input type="number" name="tarif" id="edit_tarif" required min="0" step="500" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white font-mono font-bold text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="space-y-1.5">
                <label for="edit_catatan" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">Catatan</label>
                <input type="text" name="catatan" id="edit_catatan" class="w-full px-4 py-3 rounded-2xl bg-slate-950 border border-slate-700/80 text-white text-xs focus:border-amber-400 focus:outline-none min-h-[44px]" />
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider hover:bg-slate-700 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:from-amber-300 hover:to-orange-300 transition-all cursor-pointer shadow-lg shadow-amber-500/20">
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

            tabBulan.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 shadow text-center";
            tabRentang.className = "px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-800/80 text-slate-400 hover:bg-slate-700/80 text-center";
        } else {
            formRentang.classList.remove('hidden');
            formRentang.classList.add('block');
            formBulan.classList.remove('block');
            formBulan.classList.add('hidden');

            tabRentang.className = "px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 shadow text-center";
            tabBulan.className = "px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-slate-800/80 text-slate-400 hover:bg-slate-700/80 text-center";
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
            } else {
                alert(data.message || 'Gagal menyimpan job order.');
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-symbols-outlined text-xl">save</span><span>SIMPAN JOB ORDER</span>';
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

    function openTargetModal() {
        document.getElementById('targetModal').classList.remove('hidden');
    }
    function closeTargetModal() {
        document.getElementById('targetModal').classList.add('hidden');
    }
    function setPresetTarget(val) {
        document.getElementById('targetInput').value = val;
    }

    function switchMobileTab(target) {
        const sectionRekap = document.getElementById('sectionRekapContainer');
        const sectionDetail = document.getElementById('sectionDetailContainer');
        const btnRekap = document.getElementById('btnMobileTabRekap');
        const btnDetail = document.getElementById('btnMobileTabDetail');

        if (!sectionRekap || !sectionDetail) return;

        if (target === 'rekap') {
            sectionRekap.classList.remove('hidden');
            sectionDetail.classList.add('hidden');
            sectionDetail.classList.remove('block');
            
            if (btnRekap && btnDetail) {
                btnRekap.className = "py-3 px-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950";
                btnDetail.className = "py-3 px-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 text-slate-400 bg-slate-950 hover:bg-slate-800";
            }
        } else {
            sectionDetail.classList.remove('hidden');
            sectionDetail.classList.add('block');
            sectionRekap.classList.add('hidden');
            sectionRekap.classList.remove('block');

            if (btnRekap && btnDetail) {
                btnDetail.className = "py-3 px-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950";
                btnRekap.className = "py-3 px-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 text-slate-400 bg-slate-950 hover:bg-slate-800";
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePricePreview();

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tanggal') || urlParams.has('page') || urlParams.get('tab') === 'detail') {
            if (window.innerWidth < 640) {
                switchMobileTab('detail');
            }
        }

        // Initialize Daily Trend Line Chart
        const ctx = document.getElementById('trendChartCanvas');
        if (ctx) {
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const incomeData = {!! json_encode($chartIncomeData ?? []) !!};

            new Chart(ctx, {
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
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
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

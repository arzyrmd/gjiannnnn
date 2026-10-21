<!DOCTYPE html>
<html lang="id" class="dark h-full bg-[#0b0f17] text-slate-200 selection:bg-amber-400 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kalkulator Gajian Teknisi') - Gateknis</title>
    
    <!-- Google Fonts: Inter, Plus Jakarta Sans, JetBrains Mono & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,350,0,0" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root, html {
            color-scheme: dark;
        }
        body {
            font-family: 'Inter', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            letter-spacing: -0.01em;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 20;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        /* Mobile Scroll Containment for AI Chatbot */
        #aiChatWindow {
            overscroll-behavior: contain;
            overscroll-behavior-y: contain;
            touch-action: pan-y;
        }
        #aiMessagesContainer {
            overscroll-behavior: contain;
            overscroll-behavior-y: contain;
            -webkit-overflow-scrolling: touch;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-[#0b0f17] text-slate-200 antialiased relative overflow-x-hidden">

    <!-- Subtle Ambient Top Light (Clean, not distracting) -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden no-print">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-slate-800/15 rounded-full blur-[120px]"></div>
    </div>

    <!-- Top Navigation Header -->
    <header class="no-print sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 px-4 sm:px-6 py-2.5">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-400/10 border border-amber-400/25 flex items-center justify-center text-amber-400 font-semibold transition-all group-hover:border-amber-400/50 group-hover:bg-amber-400/15 shrink-0">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-bold tracking-tight text-white flex items-center gap-1">
                        Gajian<span class="text-amber-400">ARMN</span>
                    </h1>
                    <p class="text-[10px] font-medium text-slate-400 -mt-0.5 hidden xs:block">
                        Fieldwork Calculator
                    </p>
                </div>
            </a>

            <!-- User & Nav Controls -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                @auth
                    <!-- User Role Badge & Name -->
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900/80 border border-slate-800 text-xs text-slate-300 mr-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->isAdmin() ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                        <span class="text-slate-200 font-medium font-mono">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-amber-400/10 text-amber-400 border border-amber-400/20">Admin</span>
                        @else
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-400/10 text-emerald-400 border border-emerald-400/20">Teknisi</span>
                        @endif
                    </div>
                @endauth

                <!-- Monitoring / Dashboard Link -->
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-amber-400 border border-amber-400/30 font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}" title="Monitoring System">
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span class="hidden sm:inline">{{ auth()->check() && auth()->user()->isAdmin() ? 'Monitoring' : 'Dashboard' }}</span>
                </a>

                @if(auth()->check() && auth()->user()->isAdmin())
                    <!-- Data User Link -->
                    <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ request()->routeIs('users.*') ? 'bg-slate-800 text-amber-400 border border-amber-400/30 font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}" title="Kelola User &amp; Teknisi">
                        <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                        <span class="hidden sm:inline">Data User</span>
                    </a>

                    <!-- Data Tarif Link -->
                    <a href="{{ route('tarifs.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5 {{ request()->routeIs('tarifs.*') ? 'bg-slate-800 text-amber-400 border border-amber-400/30 font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}" title="Kelola Master Tarif">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        <span class="hidden sm:inline">Data Tarif</span>
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 text-xs font-medium transition-all flex items-center gap-1.5 cursor-pointer" title="Keluar">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-3.5 sm:p-6 md:p-8 space-y-6 relative z-10">
        @yield('content')
    </main>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="no-print fixed bottom-5 right-4 left-4 sm:left-auto z-50 max-w-sm w-auto pointer-events-none space-y-2">
        @if(session('success'))
            <div id="toastSuccess" class="bg-slate-900/95 backdrop-blur-md border border-slate-800 border-l-4 border-l-emerald-400 text-slate-100 rounded-xl p-3.5 shadow-xl flex items-center justify-between gap-3 pointer-events-auto transition-all duration-300">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
                    <span class="font-medium text-xs leading-snug text-slate-200">
                        {{ session('success') }}
                    </span>
                </div>
                <button onclick="dismissToast('toastSuccess')" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider shrink-0 cursor-pointer">
                    Tutup
                </button>
            </div>
        @endif

        @if($errors->any())
            <div id="toastError" class="bg-slate-900/95 backdrop-blur-md border border-slate-800 border-l-4 border-l-rose-500 text-slate-100 rounded-xl p-3.5 shadow-xl pointer-events-auto transition-all duration-300">
                <div class="flex justify-between items-center mb-1.5">
                    <div class="flex items-center gap-1.5 text-rose-400 font-semibold text-xs">
                        <span class="material-symbols-outlined text-base">error</span>
                        <span>Terdapat Kesalahan:</span>
                    </div>
                    <button onclick="dismissToast('toastError')" class="text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider cursor-pointer">
                        Tutup
                    </button>
                </div>
                <ul class="list-disc pl-5 text-xs text-rose-300 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Mobile Bottom Quick Status Footer -->
    <footer class="no-print bg-[#090d15] border-t border-slate-800/60 py-3.5 px-4 text-center text-xs text-slate-500 relative z-10">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 font-mono-num">
            <span>GajianARMN &bull; Fieldwork Utilitarian UI System</span>
            <span>Local Time: {{ now()->translatedFormat('d M Y - H:i') }}</span>
        </div>
    </footer>

    @auth
    <!-- AI ASSISTANT FLOATING CHATBOT WIDGET -->
    <div id="aiChatWidget" class="no-print">
        <!-- Floating Action Button (FAB) -->
        <button 
            id="aiFabBtn" 
            onclick="openAiChat()" 
            class="group fixed bottom-5 right-5 z-50 px-3.5 py-2.5 rounded-full bg-slate-900/95 hover:bg-slate-800 border border-slate-700/80 hover:border-amber-400/50 text-slate-200 hover:text-white shadow-xl shadow-black/50 flex items-center gap-2 transition-all cursor-pointer active:scale-95"
            title="Buka Asisten AI"
        >
            <span class="material-symbols-outlined text-amber-400 text-xl group-hover:rotate-12 transition-transform">auto_awesome</span>
            <span class="text-xs font-semibold hidden sm:inline text-slate-200">Asisten AI</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
        </button>

        <!-- Chat Window Modal -->
        <div 
            id="aiChatWindow" 
            class="hidden fixed bottom-3 left-3 right-3 sm:left-auto sm:right-6 sm:bottom-6 w-auto sm:w-[420px] max-w-[calc(100vw-24px)] h-[84vh] sm:h-[550px] max-h-[650px] bg-slate-900/95 border border-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden backdrop-blur-xl z-50"
        >
            <!-- Header -->
            <div class="px-4 py-3 bg-slate-950/80 border-b border-slate-800/80 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center font-semibold shrink-0">
                        <span class="material-symbols-outlined text-lg">auto_awesome</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-xs font-bold text-white">Asisten Gajian AI</h4>
                            <span class="px-1.5 py-0.2 rounded text-[9px] bg-amber-400/10 text-amber-400 font-medium">Gemini</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Bantu catat job &amp; rekap gajian</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" onclick="clearAiChatHistory()" title="Bersihkan obrolan" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">delete_sweep</span>
                    </button>
                    <button type="button" onclick="closeAiChat()" title="Tutup" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>

            <!-- Quick Action Chips -->
            <div class="p-2.5 bg-slate-950/40 border-b border-slate-800/60 space-y-2 shrink-0">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth py-0.5">
                    <button type="button" onclick="sendQuickPrompt('Berapa total pendapatan dan job order saya bulan ini?')" class="px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-medium text-xs whitespace-nowrap border border-slate-700/60 shrink-0 transition-all shadow-sm">
                        Rekap Bulan Ini
                    </button>
                    <button type="button" onclick="sendQuickPrompt('Buatkan format pesan WhatsApp rekap harian untuk saya kirim ke koordinator')" class="px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-medium text-xs whitespace-nowrap border border-slate-700/60 shrink-0 transition-all shadow-sm">
                        Teks WA Rekap
                    </button>
                    <button type="button" onclick="toggleCategoryPicker()" class="px-3 py-1.5 rounded-lg bg-amber-400/10 hover:bg-amber-400/20 text-amber-300 font-medium text-xs whitespace-nowrap border border-amber-400/30 shrink-0 transition-all shadow-sm flex items-center gap-1">
                        <span>Pilih Job</span>
                        <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                </div>

                <!-- Expandable Category Quick Picker Menu -->
                <div id="categoryPickerMenu" class="hidden mt-2 p-3 rounded-2xl bg-slate-950/95 border border-slate-800 space-y-2.5 max-h-56 overflow-y-auto no-scrollbar shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs text-amber-400">tune</span>
                            <span>Status Pekerjaan:</span>
                        </p>
                        <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800">
                            <button 
                                type="button"
                                id="statusTabBerhasil" 
                                onclick="setQuickStatus('berhasil')" 
                                class="px-2.5 py-1 rounded-lg bg-emerald-500 text-slate-950 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer shadow-sm"
                            >
                                Berhasil
                            </button>
                            <button 
                                type="button"
                                id="statusTabGagal" 
                                onclick="setQuickStatus('gagal')" 
                                class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-rose-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer"
                            >
                                Gagal
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" onclick="quickRecordCategory('Kirim Faktur')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Kirim Faktur
                        </button>
                        <button type="button" onclick="quickRecordCategory('Kunjungan')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Kunjungan
                        </button>
                        <button type="button" onclick="quickRecordCategory('Pasang Baru QRIS')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Pasang Baru QRIS
                        </button>
                        <button type="button" onclick="quickRecordCategory('Pemasangan EDC')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Pemasangan EDC
                        </button>
                        <button type="button" onclick="quickRecordCategory('Penarikan EDC')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Penarikan EDC
                        </button>
                        <button type="button" onclick="quickRecordCategory('Proaktif Maintenance Dalam Mall')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Maintenance Mall
                        </button>
                        <button type="button" onclick="quickRecordCategory('Proaktif Maintenance Luar Mall')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-amber-500/15 hover:border-amber-500/40 text-slate-200 hover:text-amber-300 font-bold text-[11px] text-left border border-slate-800/80 truncate transition-all active:scale-98">
                            Maintenance Luar Mall
                        </button>
                        <button type="button" onclick="quickRecordCategory('Piket Mall (Diluar JO)')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-cyan-500/20 border border-slate-800 text-cyan-300 hover:text-cyan-200 font-bold text-[11px] text-left truncate transition-all active:scale-98">
                            Piket Mall (50k)
                        </button>
                        <button type="button" onclick="quickRecordCategory('Piket Event')" class="p-2.5 rounded-xl bg-slate-900/90 hover:bg-cyan-500/20 border border-slate-800 text-cyan-300 hover:text-cyan-200 font-bold text-[11px] text-left truncate transition-all active:scale-98">
                            Piket Event
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div id="aiMessagesContainer" class="flex-1 p-4 space-y-3.5 overflow-y-auto text-xs no-scrollbar">
                <!-- Welcome AI Message -->
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-400/15 border border-amber-400/30 text-amber-400 flex items-center justify-center font-semibold shrink-0 text-xs">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span>
                    </div>
                    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl rounded-tl-sm p-3 text-slate-200 space-y-1 max-w-[85%] shadow-sm">
                        <p class="font-semibold text-amber-400 text-xs">
                            Halo Mas! 👋
                        </p>
                        <p class="text-slate-300 text-xs leading-relaxed">Saya Asisten AI Gajian ARMN. Ada pekerjaan atau piket yang mau dicatat, atau mau cek rekap gajian?</p>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" class="p-3 bg-slate-950/90 border-t border-slate-800/80 flex items-center gap-2 shrink-0">
                <input 
                    type="text" 
                    id="aiInputText" 
                    placeholder="Tulis pesan atau catat job..." 
                    required
                    autocomplete="off"
                    class="flex-1 px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 placeholder-slate-500 font-normal text-xs focus:border-amber-400/80 focus:ring-1 focus:ring-amber-400/30 focus:outline-none transition-all"
                >
                <button 
                    type="submit" 
                    id="aiSendBtn"
                    class="p-2 rounded-xl bg-amber-400 hover:bg-amber-300 active:scale-95 text-slate-950 font-semibold flex items-center justify-center cursor-pointer transition-all shrink-0 min-h-[38px] min-w-[38px]"
                    title="Kirim Pesan"
                >
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>
        </div>
    </div>
    @endauth

    <script>
        let aiHistory = [];

        function openAiChat() {
            const chatWin = document.getElementById('aiChatWindow');
            const fabBtn = document.getElementById('aiFabBtn');
            if (chatWin) {
                chatWin.classList.remove('hidden');
                if (fabBtn) fabBtn.classList.add('hidden');
                if (window.innerWidth < 640) {
                    document.body.style.overflow = 'hidden';
                }
                document.getElementById('aiInputText')?.focus();
            }
        }

        function closeAiChat() {
            const chatWin = document.getElementById('aiChatWindow');
            const fabBtn = document.getElementById('aiFabBtn');
            if (chatWin) {
                chatWin.classList.add('hidden');
                if (fabBtn) fabBtn.classList.remove('hidden');
                document.body.style.overflow = '';
            }
        }

        let currentQuickStatus = 'berhasil';

        function setQuickStatus(status) {
            currentQuickStatus = status;
            const btnBerhasil = document.getElementById('statusTabBerhasil');
            const btnGagal = document.getElementById('statusTabGagal');

            if (btnBerhasil && btnGagal) {
                if (status === 'berhasil') {
                    btnBerhasil.className = 'px-2.5 py-1 rounded-lg bg-emerald-500 text-slate-950 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer shadow-sm';
                    btnGagal.className = 'px-2.5 py-1 rounded-lg text-slate-400 hover:text-rose-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
                } else {
                    btnGagal.className = 'px-2.5 py-1 rounded-lg bg-rose-500 text-white font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer shadow-sm';
                    btnBerhasil.className = 'px-2.5 py-1 rounded-lg text-slate-400 hover:text-emerald-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
                }
            }
        }

        function toggleCategoryPicker() {
            const menu = document.getElementById('categoryPickerMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        function quickRecordCategory(categoryName) {
            const menu = document.getElementById('categoryPickerMenu');
            if (menu) menu.classList.add('hidden');

            if (categoryName.includes('Piket Mall') || categoryName.includes('Piket Event')) {
                const defaultText = categoryName.includes('Mall') ? '50000' : '100000';
                const customFee = prompt('Masukkan nominal ' + categoryName + ' (kosongkan untuk default Rp ' + (categoryName.includes('Mall') ? '50.000' : '100.000') + '):', defaultText);
                
                if (customFee !== null && customFee.trim() !== '') {
                    sendQuickPrompt('Catat ' + categoryName + ' nominal ' + customFee.trim() + ' ' + currentQuickStatus + ' hari ini');
                    return;
                }
            }

            sendQuickPrompt('Catat ' + categoryName + ' ' + currentQuickStatus + ' hari ini');
        }

        function sendQuickPrompt(promptText) {
            const menu = document.getElementById('categoryPickerMenu');
            if (menu) menu.classList.add('hidden');

            const input = document.getElementById('aiInputText');
            if (input) {
                input.value = promptText;
                document.getElementById('aiChatForm')?.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        }

        const AI_CHAT_STORAGE_KEY = 'gajian_ai_chat_user_{{ auth()->id() ?? "guest" }}';

        function loadSavedAiChatHistory() {
            const container = document.getElementById('aiMessagesContainer');
            if (!container) return;

            try {
                const saved = localStorage.getItem(AI_CHAT_STORAGE_KEY);
                if (saved) {
                    const messages = JSON.parse(saved);
                    if (Array.isArray(messages) && messages.length > 0) {
                        container.innerHTML = '';
                        aiHistory = [];
                        messages.forEach(msg => {
                            renderSingleMessageDOM(msg.role, msg.text, msg.isError || false, msg.jobId || null, msg.undone || false);
                            aiHistory.push({ role: msg.role, text: msg.text });
                        });
                        container.scrollTop = container.scrollHeight;
                        return;
                    }
                }
            } catch (e) {
                console.error("Failed to load saved chat history:", e);
            }
        }

        function saveMessageToStorage(role, text, isError = false, jobId = null, undone = false) {
            try {
                let messages = JSON.parse(localStorage.getItem(AI_CHAT_STORAGE_KEY) || '[]');
                messages.push({ role, text, isError, jobId, undone, timestamp: Date.now() });
                if (messages.length > 40) messages = messages.slice(-40);
                localStorage.setItem(AI_CHAT_STORAGE_KEY, JSON.stringify(messages));
            } catch (e) {
                console.error("Failed to save chat message:", e);
            }
        }

        function clearAiChatHistory() {
            if (!confirm('Apakah Anda yakin ingin membersihkan riwayat obrolan AI?')) {
                return;
            }
            localStorage.removeItem(AI_CHAT_STORAGE_KEY);
            aiHistory = [];
            const container = document.getElementById('aiMessagesContainer');
            if (container) {
                container.innerHTML = `
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black shrink-0 text-sm shadow-md shadow-amber-500/20">
                            <span class="material-symbols-outlined text-base">auto_awesome</span>
                        </div>
                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl rounded-tl-sm p-3.5 text-slate-200 space-y-1.5 max-w-[85%] shadow-md backdrop-blur-sm">
                            <p class="font-extrabold text-amber-400 text-xs flex items-center gap-1">
                                <span>Halo Mas!</span> 👋
                            </p>
                            <p class="text-slate-200 leading-relaxed">Saya Asisten AI Gajian ARMN. Ada pekerjaan atau piket yang mau dicatat, atau mau minta rekap gajian hari ini?</p>
                        </div>
                    </div>
                `;
            }
        }

        function renderSingleMessageDOM(role, text, isError = false, jobId = null, undone = false) {
            const container = document.getElementById('aiMessagesContainer');
            if (!container) return;

            const isUser = role === 'user';
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex items-start gap-2.5 ${isUser ? 'justify-end' : ''}`;

            const formattedText = text.replace(/\n/g, '<br>');

            let undoHtml = '';
            if (jobId && !undone) {
                undoHtml = `
                    <div class="mt-2.5 pt-2 border-t border-slate-700/60 flex items-center justify-between gap-2" id="undoContainer_${jobId}">
                        <span class="text-[11px] text-slate-400 font-medium">Salah input data?</span>
                        <button 
                            type="button"
                            onclick="undoCreatedJob('${jobId}', this)" 
                            class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/40 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 transition-all cursor-pointer shadow-sm active:scale-95"
                        >
                            <span class="material-symbols-outlined text-xs">undo</span>
                            <span>Batalkan (Undo)</span>
                        </button>
                    </div>
                `;
            } else if (undone) {
                undoHtml = `
                    <div class="mt-2.5 pt-2 border-t border-slate-700/60 flex items-center gap-1.5 text-xs text-emerald-400 font-bold">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Pencatatan telah dibatalkan</span>
                    </div>
                `;
            }

            if (isUser) {
                msgDiv.innerHTML = `
                    <div class="bg-gradient-to-r from-amber-400 via-amber-500 to-orange-400 text-slate-950 font-bold rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%] shadow-lg shadow-amber-500/10 text-xs">
                        ${formattedText}
                    </div>
                `;
            } else if (isError) {
                msgDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center font-black shrink-0 text-sm shadow">
                        <span class="material-symbols-outlined text-base">warning</span>
                    </div>
                    <div class="bg-rose-950/60 border border-rose-700/60 text-rose-200 rounded-2xl rounded-tl-sm p-3.5 max-w-[85%] leading-relaxed shadow-lg backdrop-blur-sm text-xs">
                        ${formattedText}
                    </div>
                `;
            } else {
                msgDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black shrink-0 text-sm shadow-md shadow-amber-500/20">
                        <span class="material-symbols-outlined text-base">auto_awesome</span>
                    </div>
                    <div class="bg-slate-800/70 border border-slate-700/60 text-slate-200 rounded-2xl rounded-tl-sm p-3.5 max-w-[85%] leading-relaxed shadow-md backdrop-blur-sm text-xs space-y-1">
                        ${formattedText}
                        ${undoHtml}
                    </div>
                `;
            }

            container.appendChild(msgDiv);
            container.scrollTop = container.scrollHeight;
        }

        function appendMessage(role, text, isError = false, jobId = null) {
            renderSingleMessageDOM(role, text, isError, jobId);
            saveMessageToStorage(role, text, isError, jobId);
        }

        async function refreshDashboardData() {
            try {
                const response = await fetch("{{ route('dashboard.stats') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data && data.success) {
                    const elemPendapatanHariIni = document.getElementById('metricPendapatanHariIni');
                    const elemTotalJobHariIni = document.getElementById('metricTotalJobHariIni');
                    const elemTotalPiketHariIni = document.getElementById('metricTotalPiketHariIni');
                    const elemPendapatanBulanIni = document.getElementById('metricPendapatanBulanIni');
                    const elemTotalJobBulanIni = document.getElementById('metricTotalJobBulanIni');
                    const elemTotalPiketBulanIni = document.getElementById('metricTotalPiketBulanIni');
                    const elemPendapatanPiketBulanIni = document.getElementById('metricPendapatanPiketBulanIni');
                    const elemPendapatanJoBulanIni = document.getElementById('metricPendapatanJoBulanIni');
                    const elemRataRataJoPerHari = document.getElementById('metricRataRataJoPerHari');
                    const elemTargetJoQtyPerHari = document.getElementById('metricTargetJoQtyPerHari');
                    const elemTargetJoRevenuePerHari = document.getElementById('metricTargetJoRevenuePerHari');

                    const elemTercapai = document.getElementById('metricTercapaiPendapatan');
                    const elemTarget = document.getElementById('metricTargetPendapatan');
                    const elemPersen = document.getElementById('metricPersenTarget');
                    const elemProgressBar = document.getElementById('metricProgressBar');
                    const elemSisaTarget = document.getElementById('metricSisaTarget');
                    const elemLabelSisaHari = document.getElementById('labelSisaHari');
                    const elemRataRataHarian = document.getElementById('metricRataRataHarianDibutuhkan');
                    const elemTargetJoVolume = document.getElementById('metricTargetJoVolume');
                    const elemSisaJoVolume = document.getElementById('metricSisaJoVolume');
                    const elemPersenJoVolume = document.getElementById('metricPersenJoVolume');
                    const elemTargetJoQtyCard = document.getElementById('metricTargetJoQtyPerHariCard');
                    const elemTargetJoRevenueCard = document.getElementById('metricTargetJoRevenuePerHariCard');

                    if (elemPendapatanHariIni) elemPendapatanHariIni.textContent = data.pendapatan_hari_ini;
                    if (elemTotalJobHariIni) elemTotalJobHariIni.innerHTML = `${data.total_job_hari_ini} <span class="text-xs text-slate-400 font-bold">JO</span>`;
                    if (elemTotalPiketHariIni) elemTotalPiketHariIni.innerHTML = `${data.total_piket_hari_ini} <span class="text-[11px] text-slate-400 font-bold">Kali</span>`;
                    if (elemPendapatanBulanIni) elemPendapatanBulanIni.textContent = data.pendapatan_bulan_ini;
                    if (elemTotalJobBulanIni) elemTotalJobBulanIni.innerHTML = `${data.total_job_bulan_ini} <span class="text-xs text-slate-400 font-bold">JO</span>`;
                    if (elemTotalPiketBulanIni) elemTotalPiketBulanIni.innerHTML = `${data.total_piket_bulan_ini} <span class="text-[11px] text-slate-400 font-bold">Kali</span>`;
                    if (elemPendapatanPiketBulanIni) elemPendapatanPiketBulanIni.textContent = data.pendapatan_piket_bulan_ini;
                    if (elemPendapatanJoBulanIni) elemPendapatanJoBulanIni.textContent = data.pendapatan_jo_bulan_ini;
                    if (elemRataRataJoPerHari) elemRataRataJoPerHari.textContent = `Rata-rata ${data.rata_rata_jo_per_hari}`;
                    if (elemTargetJoQtyPerHari) elemTargetJoQtyPerHari.textContent = data.target_jo_qty_per_hari;
                    if (elemTargetJoRevenuePerHari) elemTargetJoRevenuePerHari.textContent = `(${data.target_jo_revenue_per_hari}/Hari)`;

                    if (elemTercapai) elemTercapai.textContent = data.tercapai_pendapatan;
                    if (elemTarget) elemTarget.textContent = data.target_pendapatan;
                    if (elemPersen) elemPersen.textContent = data.persen_target;
                    if (elemProgressBar) elemProgressBar.style.width = data.persen_target;
                    if (elemSisaTarget) elemSisaTarget.textContent = data.sisa_target;
                    if (elemLabelSisaHari) elemLabelSisaHari.textContent = data.label_sisa_hari;
                    if (elemRataRataHarian) elemRataRataHarian.textContent = data.rata_rata_harian_dibutuhkan;
                    if (elemTargetJoVolume) elemTargetJoVolume.textContent = data.target_jo_volume;
                    if (elemSisaJoVolume) elemSisaJoVolume.textContent = data.sisa_jo_volume;
                    if (elemPersenJoVolume) elemPersenJoVolume.textContent = data.persen_jo_volume;
                    if (elemTargetJoQtyCard) elemTargetJoQtyCard.textContent = data.target_jo_qty_per_hari;
                    if (elemTargetJoRevenueCard) elemTargetJoRevenueCard.textContent = `${data.target_jo_revenue_per_hari}/Hari`;
                }
            } catch (e) {
                console.error("Failed to refresh stats dynamically:", e);
            }
        }

        async function undoCreatedJob(jobId, btnElement) {
            if (!confirm('Apakah Anda yakin ingin membatalkan & menghapus pencatatan job ini?')) {
                return;
            }

            const cleanJobId = String(jobId);
            const undoContainer = btnElement ? (btnElement.closest('[id^="undoContainer_"]') || document.getElementById('undoContainer_' + cleanJobId)) : document.getElementById('undoContainer_' + cleanJobId);

            if (btnElement) {
                btnElement.disabled = true;
                btnElement.innerHTML = '<span class="material-symbols-outlined text-xs animate-spin">refresh</span> <span>Membatalkan...</span>';
            }

            try {
                const response = await fetch("{{ route('ai.undo.post') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id: cleanJobId })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (undoContainer) {
                        undoContainer.className = 'mt-2.5 pt-2 border-t border-slate-700/60 flex items-center gap-1.5 text-xs text-emerald-400 font-bold';
                        undoContainer.innerHTML = '<span class="material-symbols-outlined text-sm">check_circle</span><span>' + (data.message || 'Pencatatan telah dibatalkan.') + '</span>';
                    }

                    try {
                        let messages = JSON.parse(localStorage.getItem(AI_CHAT_STORAGE_KEY) || '[]');
                        messages = messages.map(m => {
                            if (String(m.jobId) === cleanJobId) {
                                m.undone = true;
                            }
                            return m;
                        });
                        localStorage.setItem(AI_CHAT_STORAGE_KEY, JSON.stringify(messages));
                    } catch (e) {
                        console.error("Failed to update undo storage:", e);
                    }

                    refreshDashboardData();
                } else {
                    alert(data.message || 'Gagal membatalkan pencatatan.');
                    if (btnElement) {
                        btnElement.disabled = false;
                        btnElement.innerHTML = '<span class="material-symbols-outlined text-xs">undo</span> <span>Batalkan (Undo)</span>';
                    }
                }
            } catch (err) {
                console.error("Undo Error:", err);
                alert('Gagal terhubung ke server.');
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<span class="material-symbols-outlined text-xs">undo</span> <span>Batalkan (Undo)</span>';
                }
            }
        }

        function appendTypingIndicator() {
            const container = document.getElementById('aiMessagesContainer');
            if (!container) return null;

            const indicatorDiv = document.createElement('div');
            indicatorDiv.id = 'aiTypingIndicator';
            indicatorDiv.className = 'flex items-start gap-2.5';
            indicatorDiv.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black shrink-0 text-sm shadow-md shadow-amber-500/20">
                    <span class="material-symbols-outlined text-base">auto_awesome</span>
                </div>
                <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl rounded-tl-sm px-4 py-3 text-slate-400 font-bold flex items-center gap-2.5 shadow-md backdrop-blur-sm">
                    <span class="text-xs text-slate-300">Sedang memproses</span>
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-bounce" style="animation-delay: 0ms;"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-bounce" style="animation-delay: 150ms;"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-bounce" style="animation-delay: 300ms;"></span>
                    </div>
                </div>
            `;
            container.appendChild(indicatorDiv);
            container.scrollTop = container.scrollHeight;
            return indicatorDiv;
        }

        async function handleAiChatSubmit(event) {
            event.preventDefault();

            const input = document.getElementById('aiInputText');
            const sendBtn = document.getElementById('aiSendBtn');
            const message = input.value.trim();

            if (!message) return;

            input.value = '';
            appendMessage('user', message);
            const indicator = appendTypingIndicator();

            input.disabled = true;
            sendBtn.disabled = true;

            try {
                const response = await fetch("{{ route('ai.chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        message: message,
                        history: aiHistory
                    })
                });

                let data;
                try {
                    data = await response.json();
                } catch (jsonErr) {
                    data = { success: false, reply: 'Response Error (' + response.status + ')' };
                }

                indicator?.remove();

                if (data && data.success) {
                    const createdJobId = (data.auto_created && data.created_job && data.created_job.id) ? data.created_job.id : null;
                    appendMessage('assistant', data.reply, false, createdJobId);
                    aiHistory.push({ role: 'user', text: message });
                    aiHistory.push({ role: 'assistant', text: data.reply });

                    if (aiHistory.length > 12) {
                        aiHistory = aiHistory.slice(-12);
                    }

                    if (data.auto_created) {
                        refreshDashboardData();
                    }
                } else {
                    appendMessage('assistant', (data && data.reply) ? data.reply : 'Maaf, terjadi kendala pada AI Assistant.', true);
                }
            } catch (err) {
                indicator?.remove();
                console.error("AI Chat JS Exception:", err);
                appendMessage('assistant', 'Gagal terhubung ke server: ' + err.message, true);
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        }

        function dismissToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(16px)';
                setTimeout(() => toast.remove(), 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadSavedAiChatHistory();

            const toastSuccess = document.getElementById('toastSuccess');
            if (toastSuccess) {
                setTimeout(() => {
                    dismissToast('toastSuccess');
                }, 3000);
            }

            const toastError = document.getElementById('toastError');
            if (toastError) {
                setTimeout(() => {
                    dismissToast('toastError');
                }, 4000);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

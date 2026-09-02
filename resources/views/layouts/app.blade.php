<!DOCTYPE html>
<html lang="id" class="dark h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kalkulator Gajian Teknisi') - Fieldwork Utilitarian</title>
    
    <!-- Google Fonts: Plus Jakarta Sans, JetBrains Mono & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700&family=Plus+Jakarta+Sans:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root, html {
            color-scheme: dark;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        input[type="date"],
        input[type="month"] {
            color-scheme: dark;
        }
        ::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.8) sepia(1) saturate(5) hue-rotate(5deg);
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-950 text-slate-100 antialiased selection:bg-amber-400 selection:text-slate-950">

    <!-- Top Navigation Header -->
    <header class="no-print sticky top-0 z-40 bg-slate-900/95 backdrop-blur border-b-2 border-amber-400/40 px-4 py-3 shadow-lg">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-amber-400/25 border border-amber-300 group-active:scale-95 transition-transform">
                    <span class="material-symbols-outlined font-bold text-xl">account_balance_wallet</span>
                </div>
                <div>
                    <h1 class="text-base md:text-lg font-black tracking-tight text-white uppercase flex items-center gap-1">
                        GAJIAN<span class="text-amber-400">ARMN</span>
                    </h1>
                    <p class="text-[10px] uppercase font-extrabold tracking-widest text-slate-400 -mt-1">
                        Fieldwork Calculator
                    </p>
                </div>
            </a>

            <!-- User & Nav Controls -->
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-amber-400 text-slate-950 border border-amber-300 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    <span class="material-symbols-outlined text-base">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('tarifs.index') }}" class="px-3 py-2 rounded-md font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-1.5 {{ request()->routeIs('tarifs.*') ? 'bg-amber-400 text-slate-950 border border-amber-300 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    <span class="material-symbols-outlined text-base">payments</span>
                    <span>Tarif Admin</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-md bg-rose-950/80 hover:bg-rose-900 border border-rose-700/60 text-rose-300 font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">logout</span>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto p-4 md:p-6 space-y-6">
        @yield('content')
    </main>

    <!-- Toast Notifications Container (Fixed Bottom-Right Guaranteed) -->
    <div id="toastContainer" class="no-print" style="position: fixed; bottom: 24px; right: 24px; z-index: 99999; max-width: 380px; width: calc(100% - 48px); pointer-events: none;">
        @if(session('success'))
            <div id="toastSuccess" style="background-color: #0f172a; border: 2px solid #10b981; color: #6ee7b7; border-radius: 16px; padding: 14px 18px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: space-between; gap: 12px; pointer-events: auto; transition: opacity 0.3s ease, transform 0.3s ease; opacity: 1; transform: translateY(0);">
                <div style="font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.4;">
                    {{ session('success') }}
                </div>
                <button onclick="dismissToast('toastSuccess')" style="color: #34d399; background: none; border: none; font-weight: 900; font-size: 11px; text-transform: uppercase; cursor: pointer; letter-spacing: 1px; flex-shrink: 0;">
                    TUTUP
                </button>
            </div>
        @endif

        @if($errors->any())
            <div id="toastError" style="background-color: #0f172a; border: 2px solid #f43f5e; color: #fda4af; border-radius: 16px; padding: 14px 18px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); pointer-events: auto; transition: opacity 0.3s ease, transform 0.3s ease; opacity: 1; transform: translateY(0); margin-top: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-weight: 900; font-size: 12px; text-transform: uppercase;">Terdapat Kesalahan:</span>
                    <button onclick="dismissToast('toastError')" style="color: #fb7185; background: none; border: none; font-weight: 900; font-size: 11px; text-transform: uppercase; cursor: pointer;">
                        TUTUP
                    </button>
                </div>
                <ul style="list-style-type: disc; padding-left: 16px; font-size: 11px; margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Mobile Bottom Quick Status Footer -->
    <footer class="no-print bg-slate-900 border-t border-slate-800 py-4 px-4 text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 font-mono-num">
            <span>Fieldwork Utilitarian UI System &bull; Single-User Edition</span>
            <span>Local Time: {{ now()->translatedFormat('d M Y - H:i') }}</span>
        </div>
    </footer>

    @auth
    <!-- GEMINI AI ASSISTANT FLOATING CHATBOT WIDGET -->
    <div id="aiChatWidget" class="no-print">
        <!-- Floating Action Button (FAB) -->
        <button 
            id="aiFabBtn" 
            onclick="openAiChat()" 
            class="group fixed bottom-6 right-6 z-50 px-4 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 active:scale-95 text-slate-950 font-black shadow-2xl shadow-amber-400/30 flex items-center gap-2 border-2 border-amber-300 transition-all cursor-pointer"
        >
            <span class="material-symbols-outlined text-2xl group-hover:rotate-12 transition-transform">smart_toy</span>
            <span class="text-xs uppercase tracking-wider font-extrabold hidden sm:inline">Asisten AI</span>
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-ping"></span>
        </button>

        <!-- Chat Window Modal -->
        <div 
            id="aiChatWindow" 
            class="hidden fixed bottom-6 right-4 sm:right-6 w-[380px] max-w-[calc(100vw-32px)] h-[520px] max-h-[calc(100vh-48px)] bg-slate-900 border-2 border-amber-400/90 rounded-2xl shadow-2xl flex flex-col overflow-hidden backdrop-blur-lg z-50"
        >
            <!-- Header -->
            <div class="p-3.5 bg-slate-950 border-b border-slate-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center font-black">
                        <span class="material-symbols-outlined text-lg">smart_toy</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-white tracking-wider flex items-center gap-1.5">
                            <span>Asisten Gajian AI</span>
                            <span class="px-1.5 py-0.2 text-[9px] rounded bg-emerald-950 text-emerald-300 border border-emerald-500/50">Gemini 2.5</span>
                        </h4>
                        <p class="text-[10px] text-slate-400 font-medium">Siap bantu catat job &amp; analisis gajian</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="clearAiChatHistory()" title="Bersihkan obrolan" class="text-slate-400 hover:text-rose-400 p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-base">delete_sweep</span>
                    </button>
                    <button type="button" onclick="closeAiChat()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>

            <!-- Quick Action Chips -->
            <div class="p-2 bg-slate-950/80 border-b border-slate-800/80 space-y-2 flex-shrink-0">
                <div class="flex items-center gap-1.5 overflow-x-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <button onclick="sendQuickPrompt('Berapa total pendapatan dan job order saya bulan ini?')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-[10px] uppercase tracking-wider whitespace-nowrap border border-slate-700 flex-shrink-0">
                        Rekap Bulan Ini
                    </button>
                    <button onclick="sendQuickPrompt('Buatkan format pesan WhatsApp rekap harian untuk saya kirim ke koordinator')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-300 font-bold text-[10px] uppercase tracking-wider whitespace-nowrap border border-slate-700 flex-shrink-0">
                        Teks WA Rekap
                    </button>
                    <button onclick="toggleCategoryPicker()" class="px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 font-black text-[10px] uppercase tracking-wider whitespace-nowrap border border-amber-300 flex-shrink-0 flex items-center gap-1">
                        <span>Catat Job Cepat</span>
                        <span class="material-symbols-outlined text-xs">expand_more</span>
                    </button>
                </div>

                <!-- Expandable Category Quick Picker Menu -->
                <div id="categoryPickerMenu" class="hidden p-2.5 rounded-xl bg-slate-900 border border-slate-700 space-y-2 animate-fadeIn">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Pekerjaan:</p>
                        <!-- Status Toggle Buttons -->
                        <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800">
                            <button 
                                type="button"
                                id="statusTabBerhasil" 
                                onclick="setQuickStatus('berhasil')" 
                                class="px-2 py-0.5 rounded bg-emerald-500 text-slate-950 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer"
                            >
                                Berhasil
                            </button>
                            <button 
                                type="button"
                                id="statusTabGagal" 
                                onclick="setQuickStatus('gagal')" 
                                class="px-2 py-0.5 rounded text-slate-400 hover:text-rose-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer"
                            >
                                Gagal
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-1.5">
                        <button onclick="quickRecordCategory('Kirim Faktur')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Kirim Faktur
                        </button>
                        <button onclick="quickRecordCategory('Kunjungan')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Kunjungan
                        </button>
                        <button onclick="quickRecordCategory('Pasang Baru QRIS')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Pasang Baru QRIS
                        </button>
                        <button onclick="quickRecordCategory('Pemasangan EDC')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Pemasangan EDC
                        </button>
                        <button onclick="quickRecordCategory('Penarikan EDC')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Penarikan EDC
                        </button>

                        <button onclick="quickRecordCategory('Proaktif Maintenance Dalam Mall')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Maintenance Dalam Mall
                        </button>
                        <button onclick="quickRecordCategory('Proaktif Maintenance Luar Mall')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-slate-200 hover:text-amber-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Maintenance Luar Mall
                        </button>

                        <button onclick="quickRecordCategory('Piket Mall (Diluar JO)')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-cyan-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Piket Mall (50k)
                        </button>
                        <button onclick="quickRecordCategory('Piket Event')" class="p-1.5 rounded-lg bg-slate-950 hover:bg-amber-400/20 text-cyan-300 font-bold text-[10px] text-left border border-slate-800 truncate">
                            Piket Event
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div id="aiMessagesContainer" class="flex-1 p-3.5 space-y-3 overflow-y-auto text-xs">
                <!-- Welcome AI Message -->
                <div class="flex items-start gap-2">
                    <div class="w-6 h-6 rounded bg-amber-400 text-slate-950 flex items-center justify-center font-black flex-shrink-0 text-xs mt-0.5">
                        <span class="material-symbols-outlined text-sm">smart_toy</span>
                    </div>
                    <div class="bg-slate-800 border border-slate-700/80 rounded-2xl rounded-tl-none p-3 text-slate-200 space-y-1 max-w-[85%]">
                        <p class="font-bold text-amber-400">Halo Mas!</p>
                        <p>Saya Asisten AI Gajian ARMN. Ada pekerjaan atau piket yang mau dicatat, atau mau minta rekap gajian hari ini?</p>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" class="p-3 bg-slate-950 border-t border-slate-800 flex items-center gap-2 flex-shrink-0">
                <input 
                    type="text" 
                    id="aiInputText" 
                    placeholder="Tulis pesan atau catat job..." 
                    required
                    autocomplete="off"
                    class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-900 border-2 border-slate-700 text-white font-medium text-xs focus:border-amber-400 focus:outline-none"
                >
                <button 
                    type="submit" 
                    id="aiSendBtn"
                    class="p-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black flex items-center justify-center cursor-pointer border-2 border-amber-300 transition-all"
                >
                    <span class="material-symbols-outlined text-base">send</span>
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
                document.getElementById('aiInputText')?.focus();
            }
        }

        function closeAiChat() {
            const chatWin = document.getElementById('aiChatWindow');
            const fabBtn = document.getElementById('aiFabBtn');
            if (chatWin) {
                chatWin.classList.add('hidden');
                if (fabBtn) fabBtn.classList.remove('hidden');
            }
        }

        let currentQuickStatus = 'berhasil';

        function setQuickStatus(status) {
            currentQuickStatus = status;
            const btnBerhasil = document.getElementById('statusTabBerhasil');
            const btnGagal = document.getElementById('statusTabGagal');

            if (btnBerhasil && btnGagal) {
                if (status === 'berhasil') {
                    btnBerhasil.className = 'px-2 py-0.5 rounded bg-emerald-500 text-slate-950 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
                    btnGagal.className = 'px-2 py-0.5 rounded text-slate-400 hover:text-rose-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
                } else {
                    btnGagal.className = 'px-2 py-0.5 rounded bg-rose-500 text-white font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
                    btnBerhasil.className = 'px-2 py-0.5 rounded text-slate-400 hover:text-emerald-400 font-black text-[9px] uppercase tracking-wider transition-all cursor-pointer';
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

        const AI_CHAT_STORAGE_KEY = 'gajian_ai_chat_history_v2';

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
                            renderSingleMessageDOM(msg.role, msg.text, msg.isError || false, msg.jobId || null);
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

        function saveMessageToStorage(role, text, isError = false, jobId = null) {
            try {
                let messages = JSON.parse(localStorage.getItem(AI_CHAT_STORAGE_KEY) || '[]');
                messages.push({ role, text, isError, jobId, timestamp: Date.now() });
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
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded bg-amber-400 text-slate-950 flex items-center justify-center font-black flex-shrink-0 text-xs mt-0.5">
                            <span class="material-symbols-outlined text-sm">smart_toy</span>
                        </div>
                        <div class="bg-slate-800 border border-slate-700/80 rounded-2xl rounded-tl-none p-3 text-slate-200 space-y-1 max-w-[85%]">
                            <p class="font-bold text-amber-400">Halo Mas!</p>
                            <p>Saya Asisten AI Gajian ARMN. Ada pekerjaan atau piket yang mau dicatat, atau mau minta rekap gajian hari ini?</p>
                        </div>
                    </div>
                `;
            }
        }

        function renderSingleMessageDOM(role, text, isError = false, jobId = null) {
            const container = document.getElementById('aiMessagesContainer');
            if (!container) return;

            const isUser = role === 'user';
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex items-start gap-2 ${isUser ? 'justify-end' : ''}`;

            const formattedText = text.replace(/\n/g, '<br>');

            let undoHtml = '';
            if (jobId) {
                undoHtml = `
                    <div class="mt-2.5 pt-2 border-t border-slate-700/60 flex items-center justify-between gap-2" id="undoContainer_${jobId}">
                        <span class="text-[11px] text-slate-400 font-medium">Salah input data?</span>
                        <button 
                            type="button"
                            onclick="undoCreatedJob(${jobId}, this)" 
                            class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/40 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 transition-all cursor-pointer shadow-sm active:scale-95"
                        >
                            <span class="material-symbols-outlined text-xs">undo</span>
                            <span>Batalkan (Undo)</span>
                        </button>
                    </div>
                `;
            }

            if (isUser) {
                msgDiv.innerHTML = `
                    <div class="bg-amber-400 text-slate-950 font-bold rounded-2xl rounded-tr-none p-3 max-w-[85%] shadow">
                        ${formattedText}
                    </div>
                `;
            } else if (isError) {
                msgDiv.innerHTML = `
                    <div class="w-6 h-6 rounded bg-rose-500 text-white flex items-center justify-center font-black flex-shrink-0 text-xs mt-0.5">
                        <span class="material-symbols-outlined text-sm">warning</span>
                    </div>
                    <div class="bg-rose-950/80 border border-rose-700/80 text-rose-200 rounded-2xl rounded-tl-none p-3 max-w-[85%] leading-relaxed shadow">
                        ${formattedText}
                    </div>
                `;
            } else {
                msgDiv.innerHTML = `
                    <div class="w-6 h-6 rounded bg-amber-400 text-slate-950 flex items-center justify-center font-black flex-shrink-0 text-xs mt-0.5">
                        <span class="material-symbols-outlined text-sm">smart_toy</span>
                    </div>
                    <div class="bg-slate-800 border border-slate-700/80 text-slate-200 rounded-2xl rounded-tl-none p-3 max-w-[85%] leading-relaxed shadow">
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

                    if (elemPendapatanHariIni) elemPendapatanHariIni.textContent = data.pendapatan_hari_ini;
                    if (elemTotalJobHariIni) elemTotalJobHariIni.innerHTML = `${data.total_job_hari_ini} <span class="text-xs text-slate-400 font-bold">JO</span>`;
                    if (elemTotalPiketHariIni) elemTotalPiketHariIni.innerHTML = `${data.total_piket_hari_ini} <span class="text-[11px] text-slate-400 font-bold">Kali</span>`;
                    if (elemPendapatanBulanIni) elemPendapatanBulanIni.textContent = data.pendapatan_bulan_ini;
                    if (elemTotalJobBulanIni) elemTotalJobBulanIni.innerHTML = `${data.total_job_bulan_ini} <span class="text-xs text-slate-400 font-bold">JO</span>`;
                    if (elemTotalPiketBulanIni) elemTotalPiketBulanIni.innerHTML = `${data.total_piket_bulan_ini} <span class="text-[11px] text-slate-400 font-bold">Kali</span>`;
                    if (elemPendapatanPiketBulanIni) elemPendapatanPiketBulanIni.textContent = data.pendapatan_piket_bulan_ini;
                }
            } catch (e) {
                console.error("Failed to refresh stats dynamically:", e);
            }
        }

        async function undoCreatedJob(jobId, btnElement) {
            if (!confirm('Apakah Anda yakin ingin membatalkan & menghapus pencatatan job ini?')) {
                return;
            }

            const undoContainer = document.getElementById('undoContainer_' + jobId);
            if (btnElement) {
                btnElement.disabled = true;
                btnElement.innerHTML = '<span class="material-symbols-outlined text-xs animate-spin">refresh</span> <span>Membatalkan...</span>';
            }

            try {
                const response = await fetch('/ai/undo/' + jobId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (undoContainer) {
                        undoContainer.innerHTML = '<span class="text-[11px] text-emerald-400 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-xs">check_circle</span> ' + data.message + '</span>';
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
            indicatorDiv.className = 'flex items-start gap-2';
            indicatorDiv.innerHTML = `
                <div class="w-6 h-6 rounded bg-amber-400 text-slate-950 flex items-center justify-center font-black flex-shrink-0 text-xs">
                    <span class="material-symbols-outlined text-sm">smart_toy</span>
                </div>
                <div class="bg-slate-800 border border-slate-700/80 rounded-2xl rounded-tl-none px-4 py-3 text-slate-400 font-bold flex items-center gap-1.5">
                    <span>Sedang memproses</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
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

<!DOCTYPE html>
<html lang="id" class="dark h-full bg-[#0b0f17] text-slate-100 selection:bg-amber-400 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kalkulator Gajian Teknisi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,350,0,0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-[#0b0f17] relative overflow-hidden">

    <div class="w-full max-w-sm space-y-5 relative z-10">
        <!-- Brand Header -->
        <div class="text-center space-y-1.5">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 shadow-sm mb-1">
                <span class="material-symbols-outlined text-2xl">bolt</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">
                Gajian<span class="text-amber-400">ARMN</span>
            </h1>
            <p class="text-xs text-slate-400 font-normal">
                Kalkulator Pendapatan Teknisi Lapangan
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 sm:p-7 shadow-sm space-y-5">
            <div class="border-b border-slate-800/80 pb-3">
                <h2 class="text-sm font-semibold text-white">
                    Masuk ke Akun Anda
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Silakan masukkan email dan password untuk melanjutkan.</p>
            </div>

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-800/60 text-rose-300 text-xs font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-rose-400 shrink-0">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-slate-300">
                        Email User
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                        placeholder="nama@gajianarmn.com"
                    >
                </div>

                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium text-slate-300">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="passwordInput" 
                            value=""
                            required 
                            class="w-full pl-3.5 pr-10 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('passwordInput', 'passwordToggleIcon')" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 p-1 transition-colors cursor-pointer flex items-center justify-center"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span id="passwordToggleIcon" class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-0.5">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="w-3.5 h-3.5 rounded bg-slate-950 border-slate-700 text-amber-400 focus:ring-amber-400">
                        <span class="text-slate-300 font-normal">Ingat saya</span>
                    </label>
                    <button 
                        type="button" 
                        onclick="openForgotPasswordModal()" 
                        class="text-amber-400 hover:text-amber-300 hover:underline transition-colors cursor-pointer font-medium"
                    >
                        Lupa sandi?
                    </button>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 active:scale-[0.99] text-slate-950 font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                >
                    <span>Masuk ke Dashboard</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </button>
            </form>

            <!-- Registration Redirect Link -->
            <div class="pt-3 border-t border-slate-800/80 text-center">
                <p class="text-xs text-slate-400">
                    Belum punya akun teknisi? 
                    <a href="{{ route('register') }}" class="font-medium text-amber-400 hover:text-amber-300 underline transition-colors">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Lupa Sandi -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 sm:p-6 max-w-sm w-full shadow-xl space-y-4 relative">
            <div class="flex items-start justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-base">help</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Reset Password</h3>
                        <p class="text-[11px] text-slate-400">Bantuan Akun Teknisi</p>
                    </div>
                </div>
                <button type="button" onclick="closeForgotPasswordModal()" class="text-slate-400 hover:text-white p-1">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs text-slate-300 leading-relaxed">
                <p>Untuk alasan keamanan, proses reset password dilakukan oleh <strong>Admin / Koordinator Lapangan</strong>.</p>
                <p>Silakan hubungi Admin via WhatsApp dengan menyebutkan <strong>Nama Lengkap</strong> &amp; <strong>Email</strong> Anda:</p>

                <a 
                    href="https://wa.me/6285712424969?text=Halo%20Admin%20GajianARMN,%20saya%20lupa%20password%20akun%20teknisi%20saya.%20Mohon%20bantuan%20reset%20password." 
                    target="_blank" 
                    class="w-full py-2.5 px-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer shadow-sm"
                >
                    <span class="material-symbols-outlined text-base">chat</span>
                    <span>Hubungi Admin (085712424969)</span>
                </a>
            </div>

            <div class="pt-1 text-center">
                <button type="button" onclick="closeForgotPasswordModal()" class="text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        function openForgotPasswordModal() {
            const modal = document.getElementById('forgotPasswordModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeForgotPasswordModal() {
            const modal = document.getElementById('forgotPasswordModal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
</body>
</html>

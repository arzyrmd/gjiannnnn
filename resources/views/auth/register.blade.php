<!DOCTYPE html>
<html lang="id" class="dark h-full bg-[#0b0f17] text-slate-100 selection:bg-amber-400 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Teknisi - Kalkulator Gajian Teknisi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,350,0,0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-[#0b0f17] relative overflow-y-auto py-8">

    <div class="w-full max-w-sm space-y-5 relative z-10 my-auto">
        <!-- Brand Header -->
        <div class="text-center space-y-1.5">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 shadow-sm mb-1">
                <span class="material-symbols-outlined text-2xl">person_add</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">
                Gajian<span class="text-amber-400">ARMN</span>
            </h1>
            <p class="text-xs text-slate-400 font-normal">
                Pendaftaran Akun Teknisi Lapangan
            </p>
        </div>

        <!-- Register Card -->
        <div class="bg-slate-900/80 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 sm:p-7 shadow-sm space-y-5">
            <div class="border-b border-slate-800/80 pb-3">
                <h2 class="text-sm font-semibold text-white">
                    Buat Akun Teknisi
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Isi formulir di bawah ini untuk membuat akun baru.</p>
            </div>

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-950/60 border border-rose-800/60 text-rose-300 text-xs font-medium space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-rose-400 shrink-0">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- Nama Lengkap -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-medium text-slate-300">
                        Nama Lengkap Teknisi
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                        placeholder="Contoh: Budi Prasetyo"
                    >
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-slate-300">
                        Alamat Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                        placeholder="teknisi@gajianarmn.com"
                    >
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium text-slate-300">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="regPassword" 
                            required 
                            class="w-full pl-3.5 pr-10 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                            placeholder="Minimal 6 karakter"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('regPassword', 'regPasswordIcon')" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 p-1 transition-colors cursor-pointer flex items-center justify-center"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span id="regPasswordIcon" class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-medium text-slate-300">
                        Ulangi Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="regPasswordConfirm" 
                            required 
                            class="w-full pl-3.5 pr-10 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400/60 transition-all text-xs"
                            placeholder="Ulangi password di atas"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('regPasswordConfirm', 'regPasswordConfirmIcon')" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 p-1 transition-colors cursor-pointer flex items-center justify-center"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span id="regPasswordConfirmIcon" class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 active:scale-[0.99] text-slate-950 font-semibold text-xs transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm"
                    >
                        <span>Daftar Akun Teknisi</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                </div>
            </form>

            <div class="pt-3 border-t border-slate-800/80 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-medium text-amber-400 hover:text-amber-300 underline transition-colors">
                        Masuk di sini
                    </a>
                </p>
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
    </script>
</body>
</html>

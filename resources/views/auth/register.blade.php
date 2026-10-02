<!DOCTYPE html>
<html lang="id" class="dark h-full bg-[#070a11] text-slate-100 selection:bg-amber-400 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Teknisi - Kalkulator Gajian Teknisi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-[#070a11] relative overflow-y-auto py-8">

    <!-- Ambient Glowing Background Light Blobs -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-[140px]"></div>
    </div>

    <div class="w-full max-w-md space-y-6 relative z-10 my-auto">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 text-slate-950 shadow-2xl shadow-amber-500/25 mb-2 ring-1 ring-amber-300/40">
                <span class="material-symbols-outlined text-3xl font-black">person_add</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight uppercase text-white">
                GAJIAN<span class="text-amber-400">ARMN</span>
            </h1>
            <p class="text-xs uppercase tracking-widest font-extrabold text-slate-400">
                Pendaftaran Akun Teknisi Lapangan
            </p>
        </div>

        <!-- Register Card -->
        <div class="bg-slate-900/80 backdrop-blur-2xl border border-slate-800/80 rounded-3xl p-7 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="border-b border-slate-800/80 pb-4">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400">assignment_ind</span>
                    <span>Buat Akun Teknisi</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Isi formulir di bawah ini untuk membuat akun teknisi baru.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-600/60 text-rose-300 text-xs font-bold space-y-1 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-rose-400">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Nama Lengkap Teknisi
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                        placeholder="Contoh: Budi Prasetyo"
                    >
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Alamat Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        class="w-full px-4 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                        placeholder="teknisi@gajianarmn.com"
                    >
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="regPassword" 
                            required 
                            class="w-full pl-4 pr-12 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                            placeholder="Minimal 6 karakter"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('regPassword', 'regPasswordIcon')" 
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 p-1.5 rounded-xl transition-colors cursor-pointer flex items-center justify-center"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span id="regPasswordIcon" class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Ulangi Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="regPasswordConfirm" 
                            required 
                            class="w-full pl-4 pr-12 py-3 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                            placeholder="Ulangi password di atas"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('regPasswordConfirm', 'regPasswordConfirmIcon')" 
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 p-1.5 rounded-xl transition-colors cursor-pointer flex items-center justify-center"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <span id="regPasswordConfirmIcon" class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-400 hover:from-amber-300 hover:to-orange-300 active:scale-[0.98] text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg shadow-amber-500/25 transition-all cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>DAFTAR TEKNISI SEKARANG</span>
                        <span class="material-symbols-outlined text-xl">how_to_reg</span>
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-800/80 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-amber-400 hover:text-amber-300 underline transition-colors inline-flex items-center gap-1">
                        <span>Masuk disini</span>
                        <span class="material-symbols-outlined text-sm">login</span>
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

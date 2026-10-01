<!DOCTYPE html>
<html lang="id" class="dark h-full bg-[#070a11] text-slate-100 selection:bg-amber-400 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kalkulator Gajian Teknisi</title>
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
<body class="h-full flex items-center justify-center p-4 antialiased bg-[#070a11] relative overflow-hidden">

    <!-- Ambient Glowing Background Light Blobs -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-[140px]"></div>
    </div>

    <div class="w-full max-w-md space-y-6 relative z-10">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 text-slate-950 shadow-2xl shadow-amber-500/25 mb-2 ring-1 ring-amber-300/40">
                <span class="material-symbols-outlined text-3xl font-black">electric_bolt</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight uppercase text-white">
                GAJIAN<span class="text-amber-400">ARMN</span>
            </h1>
            <p class="text-xs uppercase tracking-widest font-extrabold text-slate-400">
                Kalkulator Pendapatan Teknisi Lapangan
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 backdrop-blur-2xl border border-slate-800/80 rounded-3xl p-7 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="border-b border-slate-800/80 pb-4">
                <h2 class="text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400">lock</span>
                    <span>Masuk Akun Teknisi</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masuk untuk akses kalkulator &amp; rekap gajian.</p>
            </div>

            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-600/60 text-rose-300 text-xs font-bold flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-base text-rose-400">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Email Teknisi
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', 'teknisi@gajianarmn.com') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3.5 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                        placeholder="nama@gajianarmn.com"
                    >
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        value="password123"
                        required 
                        class="w-full px-4 py-3.5 rounded-2xl bg-slate-950/90 border border-slate-700/80 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition-all text-sm"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-amber-400 focus:ring-amber-400">
                        <span class="font-bold text-slate-300">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-400 hover:from-amber-300 hover:to-orange-300 active:scale-[0.98] text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg shadow-amber-500/25 transition-all cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>MASUK KABIN TEKNISI</span>
                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                </button>
            </form>

            <!-- Quick Credentials Info Box -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800/80 text-xs space-y-1.5 shadow-inner">
                <span class="font-black text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">info</span>
                    <span>Kredensial Default Seeder:</span>
                </span>
                <div class="font-mono text-[11px] text-slate-300 pl-6 leading-relaxed">
                    Email: <span class="text-white font-bold">teknisi@gajianarmn.com</span><br>
                    Pass: <span class="text-white font-bold">password123</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

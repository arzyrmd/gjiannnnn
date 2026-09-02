<!DOCTYPE html>
<html lang="id" class="dark h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kalkulator Gajian Teknisi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-slate-950">

    <div class="w-full max-w-md space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 shadow-xl shadow-amber-400/10 mb-2 border-2 border-amber-300">
                <span class="material-symbols-outlined text-3xl font-bold">electric_bolt</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight uppercase text-white">
                GAJIAN<span class="text-amber-400">ARMN</span>
            </h1>
            <p class="text-xs uppercase tracking-widest font-extrabold text-slate-400">
                Kalkulator Pendapatan Teknisi Lapangan
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900 border-2 border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="border-b border-slate-800 pb-4">
                <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400">lock</span>
                    <span>Masuk Akun Teknisi</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Silakan masuk untuk akses kalkulator &amp; rekap gajian.</p>
            </div>

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-950/80 border border-rose-600 text-rose-300 text-xs font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
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
                        class="w-full px-4 py-3.5 rounded-xl bg-slate-950 border-2 border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 transition-colors text-base"
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
                        class="w-full px-4 py-3.5 rounded-xl bg-slate-950 border-2 border-slate-800 text-white font-mono placeholder:text-slate-600 focus:outline-none focus:border-amber-400 transition-colors text-base"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-amber-400 focus:ring-amber-400">
                        <span class="font-bold">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 px-6 rounded-xl bg-amber-400 hover:bg-amber-300 active:scale-[0.98] text-slate-950 font-black text-base uppercase tracking-wider shadow-lg shadow-amber-400/20 transition-all cursor-pointer border-2 border-amber-300 flex items-center justify-center gap-2"
                >
                    <span>MASUK KABIN TEKNISI</span>
                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                </button>
            </form>

            <!-- Quick Credentials Info Box -->
            <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800 text-xs space-y-1">
                <span class="font-black text-amber-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">info</span>
                    <span>Kredensial Default Seeder:</span>
                </span>
                <div class="font-mono text-[11px] text-slate-300 pl-5">
                    Email: <span class="text-white font-bold">teknisi@gajianarmn.com</span><br>
                    Pass: <span class="text-white font-bold">password123</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SMA Yumik Kulsum</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased selection:bg-yumik-primary selection:text-white bg-[#E9EEED] dark:bg-[#0A0D14] min-h-screen flex items-center justify-center relative font-sans transition-colors duration-500 text-slate-700 dark:text-slate-300 overflow-x-hidden">

    {{-- Background Blur --}}
    <div class="fixed top-[-10%] left-[-10%] w-[50rem] h-[50rem] bg-yumik-primary/25 dark:bg-yumik-primary/10 blur-[100px] rounded-full pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[45rem] h-[45rem] bg-purple-500/15 dark:bg-purple-900/15 blur-[120px] rounded-full pointer-events-none -z-10"></div>

    {{-- Theme Toggle --}}
    <button id="theme-toggle" class="fixed top-6 right-6 z-30 w-11 h-11 rounded-full bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-xl flex items-center justify-center text-slate-500 dark:text-slate-300 hover:scale-110 active:scale-95 transition-all">
        <i id="theme-toggle-icon" class="ph-bold ph-moon text-lg"></i>
    </button>

    {{-- Container Login --}}
    <div class="w-full max-w-md px-6 py-8">

        {{-- Logo + Branding --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="w-14 h-14 rounded-[1.25rem] bg-gradient-to-br from-yumik-primary to-green-600 flex items-center justify-center text-white shadow-lg shadow-yumik-primary/30">
                    <i class="ph-fill ph-graduation-cap text-3xl"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight leading-none">Yumik</h1>
                    <p class="text-xs font-bold text-yumik-primary uppercase tracking-widest mt-0.5">LMS</p>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-1">Selamat Datang </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Masuk untuk melanjutkan ke sistem</p>
        </div>

        {{-- Form Login Card --}}
        <div class="relative overflow-hidden rounded-[2rem] bg-white/60 dark:bg-[#1A1F29]/60 backdrop-blur-2xl border-2 border-white/90 dark:border-white/5 shadow-[0_15px_40px_-15px_rgba(24,201,93,0.15)] p-8">

            <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-white/10 dark:from-white/5 dark:via-transparent dark:to-transparent pointer-events-none"></div>

            <div class="relative z-10">

                {{-- Error --}}
                @if($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-sm font-medium text-red-600 dark:text-red-400 flex items-start gap-2">
                        <i class="ph-fill ph-warning-circle text-lg shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                        <i class="ph-fill ph-check-circle text-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Username</label>
                        <div class="relative">
                            <i class="ph-bold ph-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            <input type="text" name="username" value="{{ old('username') }}"
                                placeholder="Masukkan username"
                                required autofocus
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <i class="ph-bold ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            <input type="password" name="password" id="password"
                                placeholder="Masukkan password"
                                required
                                class="w-full pl-11 pr-12 py-3.5 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                <i id="password-icon" class="ph-bold ph-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="peer sr-only">
                            <div class="w-5 h-5 rounded-lg bg-white/60 dark:bg-white/5 border border-slate-300 dark:border-white/20 peer-checked:bg-yumik-primary peer-checked:border-yumik-primary transition-all flex items-center justify-center">
                                <i class="ph-bold ph-check text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-yumik-primary transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full py-4 rounded-2xl bg-yumik-primary hover:bg-green-600 text-white font-bold shadow-lg shadow-yumik-primary/30 transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                        <i class="ph-bold ph-sign-in text-lg"></i>
                        Masuk
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-white/10 text-center">
                    <p class="text-[11px] text-slate-400 font-medium">
                        <i class="ph-fill ph-lock-key"></i>
                        Sistem Informasi Akademik Terpadu
                    </p>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <p class="text-center text-xs text-slate-500 dark:text-slate-500 mt-6 font-medium">
            &copy; {{ date('Y') }} <span class="font-bold text-slate-700 dark:text-slate-300">SMA Yumik Kulsum</span>
        </p>
    </div>

    {{-- Script --}}
    <script>
        // Toggle Password
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('password-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        }

        // Theme Toggle
        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-toggle-icon');

        if (document.documentElement.classList.contains('dark')) {
            themeIcon.classList.replace('ph-moon', 'ph-sun');
        }

        themeBtn.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            themeIcon.classList.replace(isDark ? 'ph-moon' : 'ph-sun', isDark ? 'ph-sun' : 'ph-moon');
        });
    </script>
</body>
</html>
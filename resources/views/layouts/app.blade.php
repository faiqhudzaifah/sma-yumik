<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS SMA Yumik Kulsum')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased selection:bg-yumik-primary selection:text-white bg-[#E9EEED] dark:bg-[#0A0D14] min-h-screen flex relative font-sans transition-colors duration-500 text-slate-700 dark:text-slate-300 overflow-x-hidden">
    
    <!-- Latar Belakang Glassmorphism -->
    <div class="fixed top-[-10%] left-[-10%] w-[50rem] h-[50rem] bg-yumik-primary/25 dark:bg-yumik-primary/10 blur-[100px] rounded-full pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[45rem] h-[45rem] bg-purple-500/15 dark:bg-purple-900/15 blur-[120px] rounded-full pointer-events-none -z-10"></div>

    <!-- Overlay untuk Mobile Sidebar (Ada di z-40) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0"></div>

    <!-- Layout Wrapper: HAPUS z-10 di sini agar sidebar bisa tembus ke atas -->
    <div class="w-full max-w-[1400px] mx-auto flex gap-6 px-4 sm:px-8 py-6 relative">
        
        <!-- Sidebar (Sudah kita set z-[100] di dalam komponennya) -->
        <x-sidebar />

        <!-- Area Konten Utama (Berada di z-30 agar tertutup rapi oleh overlay) -->
        <main class="flex-1 min-w-0 flex flex-col pb-12 w-full relative z-30">
            
            <x-header-profile name="{{ auth()->user()->name ?? 'Administrator' }}" avatar="{{ auth()->user()->guru->photo ?? '' }}" />

            <div class="mt-4 w-full">
                @yield('content')
            </div>

            <footer class="mt-auto pt-12 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} SMA Yumik Kulsum. Sistem Informasi Akademik Terpadu.
            </footer>
        </main>
    </div>

    <!-- Floating Theme Toggle -->
    <button id="theme-toggle" class="fixed bottom-6 right-6 z-30 w-12 h-12 rounded-full bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-xl flex items-center justify-center text-slate-500 dark:text-slate-300 hover:scale-110 active:scale-95 transition-all">
        <i id="theme-toggle-icon" class="ph-bold ph-moon text-xl"></i>
    </button>

    <!-- Script Responsivitas & Tema -->
    <script>
        // Logika Tema
        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-toggle-icon');
        if (document.documentElement.classList.contains('dark')) themeIcon.classList.replace('ph-moon', 'ph-sun');

        themeBtn.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            themeIcon.classList.replace(isDark ? 'ph-moon' : 'ph-sun', isDark ? 'ph-sun' : 'ph-moon');
        });

        // Logika Mobile Sidebar
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('mobile-menu-btn');

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
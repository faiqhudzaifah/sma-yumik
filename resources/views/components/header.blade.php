<header class="sticky top-0 z-40 h-20 w-full flex items-center justify-between px-4 sm:px-8 bg-white/40 dark:bg-[#0A0D14]/40 backdrop-blur-xl border-b border-slate-200/50 dark:border-white/5">
    
    <!-- Tombol Toggle Sidebar Mobile -->
    <div class="flex items-center gap-4 lg:hidden">
        <button class="w-10 h-10 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-600 dark:text-slate-300 shadow-sm">
            <i class="ph-bold ph-list text-xl"></i>
        </button>
        <x-logo size="w-8 h-8" />
    </div>

    <!-- Breadcrumb Cepat (Opsional untuk layar besar) -->
    <div class="hidden lg:block">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Portal Administrasi</h2>
        <p class="text-xs text-slate-500">Tahun Ajaran Aktif: <span class="font-bold text-yumik-primary">2026/2027 Ganjil</span></p>
    </div>

    <!-- Menu Kanan -->
    <div class="flex items-center gap-3">
        <!-- Pindah dari dashboard.blade.php -->
        <button id="theme-toggle" class="w-10 h-10 rounded-full bg-white/80 dark:bg-black/40 border border-slate-200 dark:border-white/10 shadow-sm flex items-center justify-center text-slate-500 dark:text-slate-300 hover:scale-105 active:scale-95 transition-all">
            <i id="theme-toggle-icon" class="ph-bold ph-moon text-lg"></i>
        </button>

        <div class="h-6 w-px bg-slate-200 dark:bg-white/10 mx-1"></div>

        <button class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full bg-white/80 dark:bg-black/40 border border-slate-200 dark:border-white/10 shadow-sm hover:border-yumik-primary/50 transition-colors group">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 hidden sm:block">{{ auth()->user()->username ?? 'Admin' }}</span>
            <div class="w-8 h-8 rounded-full bg-yumik-primary text-white flex items-center justify-center">
                <i class="ph-bold ph-user"></i>
            </div>
        </button>
    </div>
</header>
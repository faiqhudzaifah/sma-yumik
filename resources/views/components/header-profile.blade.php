@props(['name' => 'Pengguna', 'avatar' => null])

<div class="flex items-center justify-between py-2 sm:py-6 px-2">
    <div class="flex items-center gap-4">
        <!-- Tombol Menu Mobile -->
        <button id="mobile-menu-btn" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white shadow-sm active:scale-95 transition-all">
            <i class="ph-bold ph-list text-xl"></i>
        </button>
        
        <div class="flex flex-col">
            <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">Selamat datang,</span>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight line-clamp-1">{{ $name }}</h2>
        </div>
    </div>
    
    <button class="relative rounded-full w-10 h-10 sm:w-12 sm:h-12 p-0.5 bg-gradient-to-tr from-yumik-primary to-yumik-accent shadow-lg shrink-0 hover:scale-105 active:scale-95 transition-transform">
        @if($avatar)
            <img src="{{ asset($avatar) }}" alt="Profile" class="w-full h-full rounded-full border-2 border-white dark:border-[#1E1E1E] object-cover">
        @else
            <div class="w-full h-full rounded-full border-2 border-white dark:border-[#1E1E1E] bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                <i class="ph-fill ph-user text-lg sm:text-xl"></i>
            </div>
        @endif
        <span class="absolute top-0 right-0 w-3 h-3 sm:w-3.5 sm:h-3.5 bg-red-500 border-2 border-white dark:border-[#1E1E1E] rounded-full"></span>
    </button>
</div>
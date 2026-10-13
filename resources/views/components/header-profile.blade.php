@props(['name' => 'Pengguna', 'avatar' => null, 'email' => null])

<div class="flex items-center justify-between py-2 sm:py-6 px-2">
    <div class="flex items-center gap-4">
        <button id="mobile-menu-btn" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white/50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white shadow-sm active:scale-95 transition-all">
            <i class="ph-bold ph-list text-xl"></i>
        </button>

        <div class="flex flex-col">
            <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">Selamat datang,</span>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight line-clamp-1">{{ $name }}</h2>
        </div>
    </div>

    <x-profile-dropdown :name="$name" :avatar="$avatar" :email="$email" />
</div>
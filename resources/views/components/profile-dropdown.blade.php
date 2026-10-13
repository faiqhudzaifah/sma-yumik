@props(['name' => 'Pengguna', 'avatar' => null, 'email' => null])

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    {{-- Tombol Avatar --}}
    <button @click="open = !open" type="button"
        class="relative rounded-full w-10 h-10 sm:w-12 sm:h-12 p-0.5 bg-gradient-to-tr from-yumik-primary to-yumik-accent shadow-lg shrink-0 hover:scale-105 active:scale-95 transition-transform">
        @if($avatar)
            <img src="{{ asset('storage/' . $avatar) }}" alt="Profile" class="w-full h-full rounded-full border-2 border-white dark:border-[#1E1E1E] object-cover">
        @else
            <div class="w-full h-full rounded-full border-2 border-white dark:border-[#1E1E1E] bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                <i class="ph-fill ph-user text-lg sm:text-xl"></i>
            </div>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        x-cloak
        class="absolute right-0 top-[calc(100%+12px)] w-64 bg-white dark:bg-[#1A1F29] rounded-2xl shadow-2xl border border-slate-100 dark:border-white/10 overflow-hidden z-50">

        <div class="p-4 border-b border-slate-100 dark:border-white/10 bg-slate-50/50 dark:bg-black/20">
            <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $name }}</p>
            @if($email)
                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $email }}</p>
            @endif
        </div>

        <div class="p-2">
            <a href="{{ route('profile.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                <i class="ph-bold ph-user-circle text-lg text-slate-400"></i>
                Profil Saya
            </a>

            <a href="{{ route('password.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                <i class="ph-bold ph-key text-lg text-slate-400"></i>
                Ubah Password
            </a>
        </div>

        <div class="p-2 border-t border-slate-100 dark:border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                    <i class="ph-bold ph-sign-out text-lg"></i>
                    Keluar Sesi
                </button>
            </form>
        </div>
    </div>
</div>
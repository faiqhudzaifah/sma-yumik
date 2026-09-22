@props(['title', 'subject', 'progress' => 0, 'icon' => 'book-open'])

<x-card glass="true" class="group cursor-pointer hover:shadow-[0_15px_40px_rgba(24,201,93,0.15)] transition-all duration-300 active:scale-95 border-2 border-transparent hover:border-yumik-primary/20">
    <div class="flex justify-between items-start mb-5">
        <div class="w-14 h-14 rounded-[1.5rem] bg-gradient-to-br from-slate-100 to-slate-200 dark:from-[#2A2A2A] dark:to-[#1A1A1A] flex items-center justify-center text-yumik-primary shadow-sm group-hover:scale-110 transition-transform duration-300">
            <i class="ph-fill ph-{{ $icon }} text-3xl drop-shadow-sm"></i>
        </div>
        <x-badge color="accent">{{ $subject }}</x-badge>
    </div>
    
    <h3 class="font-bold text-xl text-slate-800 dark:text-white mb-1.5 leading-tight group-hover:text-yumik-primary transition-colors line-clamp-2">{{ $title }}</h3>
    <p class="text-xs text-slate-400 font-medium mb-6">Lanjutkan materi terakhir</p>
    
    <x-progress-bar :percentage="$progress" />
</x-card>
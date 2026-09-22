@props(['percentage' => 0, 'showLabel' => true])

<div class="w-full">
    @if($showLabel)
        <div class="flex justify-between items-end text-xs font-semibold mb-2">
            <span class="text-slate-500 dark:text-slate-400">Progres Belajar</span>
            <span class="text-yumik-primary text-sm">{{ $percentage }}%</span>
        </div>
    @endif
    
    <!-- Track -->
    <div class="h-3.5 w-full bg-slate-100 dark:bg-[#2A2A2A] rounded-full overflow-hidden shadow-inner">
        <!-- Bar -->
        <div class="h-full bg-gradient-to-r from-yumik-primary to-[#20d868] rounded-full transition-all duration-700 ease-out relative" style="width: {{ $percentage }}%;">
            <!-- Efek Shimmer -->
            <div class="absolute top-0 left-0 bottom-0 right-0 bg-gradient-to-r from-transparent via-white/30 to-transparent translate-x-[-100%] animate-[shimmer_2s_infinite]"></div>
        </div>
    </div>
</div>
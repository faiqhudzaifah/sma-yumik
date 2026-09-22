@props(['icon' => null])

<div class="relative w-full text-slate-500 focus-within:text-yumik-primary transition-colors">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
            <i class="ph-bold ph-{{ $icon }} text-xl"></i>
        </div>
    @endif
    
    <select {{ $attributes->merge(['class' => 'appearance-none w-full py-4 pr-12 rounded-[1.5rem] bg-white/60 dark:bg-[#1A1F29]/60 backdrop-blur-xl border border-white/90 dark:border-white/5 shadow-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all cursor-pointer ' . ($icon ? 'pl-12' : 'pl-6')]) }}>
        {{ $slot }}
    </select>

    <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-400">
        <i class="ph-bold ph-caret-down text-lg"></i>
    </div>
</div>
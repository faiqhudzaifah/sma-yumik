@props(['icon', 'color' => 'blue', 'tooltip' => 'Aksi', 'href' => null, 'type' => 'button'])

@php
    $colorClasses = [
        'blue' => 'text-blue-600 hover:bg-blue-50 hover:border-blue-200 dark:text-blue-400 dark:hover:bg-blue-500/10 dark:hover:border-blue-500/30',
        'amber' => 'text-amber-500 hover:bg-amber-50 hover:border-amber-200 dark:hover:bg-amber-500/10 dark:hover:border-amber-500/30',
        'red' => 'text-red-500 hover:bg-red-50 hover:border-red-200 dark:hover:bg-red-500/10 dark:hover:border-red-500/30',
        // Tambahkan warna emerald (hijau) di sini
        'emerald' => 'text-emerald-500 hover:bg-emerald-50 hover:border-emerald-200 dark:text-emerald-400 dark:hover:bg-emerald-500/10 dark:hover:border-emerald-500/30',
    ];
    
    $baseClass = "relative inline-flex items-center justify-center w-10 h-10 rounded-[1rem] bg-white dark:bg-[#1A1D24] border border-slate-200 dark:border-white/10 shadow-sm transition-all hover:scale-105 group/btn " . ($colorClasses[$color] ?? $colorClasses['blue']);
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $baseClass }}">
        <i class="ph ph-{{ $icon }} text-xl"></i>
        
        <!-- Tooltip -->
        <span class="absolute bottom-[110%] left-1/2 -translate-x-1/2 px-2.5 py-1.5 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-[10px] font-bold tracking-wide rounded-md opacity-0 pointer-events-none group-hover/btn:opacity-100 group-hover/btn:-translate-y-1 transition-all duration-200 shadow-xl whitespace-nowrap z-[100]">
            {{ $tooltip }}
            <!-- Segitiga penunjuk -->
            <svg class="absolute text-slate-800 dark:text-white h-1.5 w-full left-0 top-full" x="0px" y="0px" viewBox="0 0 255 255" xml:space="preserve"><polygon class="fill-current" points="0,0 127.5,127.5 255,0"/></svg>
        </span>
    </a>
@else
    <button type="{{ $type }}" class="{{ $baseClass }}">
        <i class="ph ph-{{ $icon }} text-xl"></i>
        
        <!-- Tooltip -->
        <span class="absolute bottom-[110%] left-1/2 -translate-x-1/2 px-2.5 py-1.5 bg-slate-800 dark:bg-white text-white dark:text-slate-800 text-[10px] font-bold tracking-wide rounded-md opacity-0 pointer-events-none group-hover/btn:opacity-100 group-hover/btn:-translate-y-1 transition-all duration-200 shadow-xl whitespace-nowrap z-[100]">
            {{ $tooltip }}
            <!-- Segitiga penunjuk -->
            <svg class="absolute text-slate-800 dark:text-white h-1.5 w-full left-0 top-full" x="0px" y="0px" viewBox="0 0 255 255" xml:space="preserve"><polygon class="fill-current" points="0,0 127.5,127.5 255,0"/></svg>
        </span>
    </button>
@endif
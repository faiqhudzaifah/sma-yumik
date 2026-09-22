@props(['id', 'type' => 'success', 'title', 'message'])

@php
    $styles = [
        'success' => ['icon' => 'check-circle', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-500/10'],
        'error' => ['icon' => 'x-circle', 'color' => 'text-red-500', 'bg' => 'bg-red-500/10'],
        'info' => ['icon' => 'info', 'color' => 'text-blue-500', 'bg' => 'bg-blue-500/10'],
    ];
    $style = $styles[$type];
@endphp

<!-- Toast Container (Menggunakan class hidden secara default, akan diatur tampilannya via JS/Alpine nantinya) -->
<div id="{{ $id }}" class="fixed bottom-6 right-6 z-[100] flex items-start gap-4 p-5 rounded-[1.5rem] bg-white/90 dark:bg-[#1A1F29]/90 backdrop-blur-2xl border border-white/90 dark:border-white/10 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.1)] w-80 transform transition-all duration-300 translate-y-0 opacity-100">
    
    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $style['bg'] }} {{ $style['color'] }}">
        <i class="ph-fill ph-{{ $style['icon'] }} text-xl drop-shadow-sm"></i>
    </div>
    
    <div class="flex-1 pt-1">
        <h5 class="text-sm font-bold text-slate-800 dark:text-white mb-0.5">{{ $title }}</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $message }}</p>
    </div>
    
    <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="document.getElementById('{{ $id }}').style.display = 'none'">
        <i class="ph-bold ph-x text-sm"></i>
    </button>
</div>
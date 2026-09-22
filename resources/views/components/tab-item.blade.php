@props(['name', 'active' => false, 'icon' => null, 'href' => '#'])

@php
    $activeClass = $active 
        ? 'bg-white dark:bg-[#2A2D35] text-slate-800 dark:text-white shadow-[0_4px_12px_rgba(0,0,0,0.05)]' 
        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-white/20';
@endphp

<a href="{{ $href }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 {{ $activeClass }}">
    @if($icon)
        <i class="ph-bold ph-{{ $icon }} text-lg"></i>
    @endif
    {{ $name }}
</a>
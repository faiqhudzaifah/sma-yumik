@props(['color' => 'primary'])

@php
    $colors = [
        'primary' => 'bg-yumik-primary/10 text-yumik-primary border-yumik-primary/20',
        'accent'  => 'bg-yumik-accent/10 text-yumik-accent border-yumik-accent/20',
        'neutral' => 'bg-slate-200/50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 border-transparent'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold backdrop-blur-sm border ' . $colors[$color]]) }}>
    {{ $slot }}
</div>
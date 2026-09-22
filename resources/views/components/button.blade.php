@props(['variant' => 'primary'])

@php
    $baseStyle = 'inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full font-semibold transition-all duration-200 active:scale-95 focus:outline-none';
    
    $variants = [
        'primary' => 'bg-yumik-primary text-white shadow-[0_8px_20px_rgba(24,201,93,0.3)] hover:shadow-[0_10px_25px_rgba(24,201,93,0.4)]',
        'secondary' => 'bg-white dark:bg-yumik-surface-dark text-slate-800 dark:text-white shadow-[0_4px_15px_rgba(0,0,0,0.05)] border border-slate-100 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-[#252525]',
        'accent' => 'bg-yumik-accent text-white shadow-[0_8px_20px_rgba(139,92,246,0.3)] hover:shadow-[0_10px_25px_rgba(139,92,246,0.4)]',
    ];

    $style = $baseStyle . ' ' . $variants[$variant];
@endphp

<button {{ $attributes->merge(['class' => $style, 'type' => 'button']) }}>
    {{ $slot }}
</button>
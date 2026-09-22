@props(['type' => 'success', 'title', 'message'])

@php
    $typeClasses = [
        'success' => 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400',
        'error' => 'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400',
        'warning' => 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-600 dark:text-amber-400',
        'info' => 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20 text-blue-600 dark:text-blue-400',
    ];

    $icons = [
        'success' => 'check-circle',
        'error' => 'warning-circle',
        'warning' => 'warning',
        'info' => 'info',
    ];

    $class = $typeClasses[$type] ?? $typeClasses['info'];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

<!-- Toast Notification (Floating) -->
<div 
    class="yumik-toast fixed top-6 right-6 z-[100] max-w-sm w-full transform transition-all duration-500 translate-x-[120%] opacity-0"
    role="alert"
>
    <div class="flex items-start gap-4 p-4 rounded-2xl border backdrop-blur-xl shadow-2xl {{ $class }}">
        <i class="ph-fill ph-{{ $icon }} text-2xl shrink-0 mt-0.5"></i>
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-bold tracking-wide uppercase mb-1">{{ $title }}</h4>
            <p class="text-sm opacity-90">{{ $message }}</p>
        </div>
        <button 
            type="button" 
            class="toast-close w-8 h-8 flex items-center justify-center rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors shrink-0 -mt-1 -mr-1"
            aria-label="Tutup"
        >
            <i class="ph-bold ph-x text-lg"></i>
        </button>
    </div>
</div>
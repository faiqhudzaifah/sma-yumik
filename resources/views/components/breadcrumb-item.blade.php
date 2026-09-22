@props(['href' => '#', 'active' => false])

<div class="flex items-center gap-2">
    <a href="{{ $href }}" class="{{ $active ? 'text-yumik-primary' : 'hover:text-slate-700 dark:hover:text-slate-200 transition-colors' }}">
        {{ $slot }}
    </a>
    @if(!$active)
        <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600"></i>
    @endif
</div>
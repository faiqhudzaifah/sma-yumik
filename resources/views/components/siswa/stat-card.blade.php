@props([
    'icon' => 'chart',
    'label' => '',
    'value' => 0,
    'color' => 'primary',  // primary | blue | amber | purple
    'suffix' => '',
])

@php
    $colorMap = [
        'primary' => ['bg' => 'bg-yumik-primary', 'shadow' => 'shadow-yumik-primary/30'],
        'blue'    => ['bg' => 'bg-blue-500',     'shadow' => 'shadow-blue-500/30'],
        'amber'   => ['bg' => 'bg-amber-500',    'shadow' => 'shadow-amber-500/30'],
        'purple'  => ['bg' => 'bg-purple-500',   'shadow' => 'shadow-purple-500/30'],
    ];
    $c = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<x-card class="!p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
    <div class="absolute -right-4 -bottom-4 opacity-[0.07] transform group-hover:scale-110 transition-transform duration-500">
        <i class="ph-fill ph-{{ $icon }} text-8xl"></i>
    </div>

    <div class="relative z-10">
        <div class="w-12 h-12 {{ $c['bg'] }} rounded-2xl flex items-center justify-center text-white mb-4 shadow-lg {{ $c['shadow'] }}">
            <i class="ph-fill ph-{{ $icon }} text-2xl"></i>
        </div>
        <h2 class="text-4xl font-black text-slate-800 dark:text-white leading-none">
            {{ $value }}<span class="text-2xl ml-0.5">{{ $suffix }}</span>
        </h2>
        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-2">{{ $label }}</p>
    </div>
</x-card>
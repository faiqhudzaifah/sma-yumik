@props(['src' => null, 'size' => 'md', 'status' => null, 'alt' => 'User Avatar'])

@php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-12 h-12 text-sm',
        'lg' => 'w-16 h-16 text-lg',
        'xl' => 'w-24 h-24 text-2xl',
    ];
    
    $statusColors = [
        'online' => 'bg-green-500',
        'offline' => 'bg-slate-400',
        'busy' => 'bg-red-500',
    ];
@endphp

<div class="relative inline-block">
    <div class="{{ $sizes[$size] }} rounded-full bg-slate-100 dark:bg-slate-800 border-2 border-white dark:border-[#1E232E] shadow-sm flex items-center justify-center overflow-hidden">
        @if($src)
            <img src="{{ $src }}" alt="{{ $alt }}" class="w-full h-full object-cover">
        @else
            <i class="ph-fill ph-user text-slate-400"></i>
        @endif
    </div>
    
    @if($status)
        <span class="absolute bottom-0 right-0 w-[25%] h-[25%] rounded-full border-2 border-white dark:border-[#1A1F29] {{ $statusColors[$status] }}"></span>
    @endif
</div>
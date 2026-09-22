@props(['size' => 'md'])

@php
    $iconSize = $size === 'sm' ? 'text-xl' : ($size === 'lg' ? 'text-4xl' : 'text-2xl');
    $textSize = $size === 'sm' ? 'text-lg' : ($size === 'lg' ? 'text-3xl' : 'text-xl');
    $circleSize = $size === 'sm' ? 'w-8 h-8' : ($size === 'lg' ? 'w-12 h-12' : 'w-10 h-10');
@endphp

@props(['size' => 'w-10 h-10'])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 select-none']) }}>
    <!-- Gambar Logo Custom dengan batas ukuran -->
    <img src="{{ asset('images/logo.png') }}" alt="Logo Yumik" class="{{ $size }} object-contain drop-shadow-md">
    
    <!-- Teks Logo (Opsional, akan sembunyi di layar sangat kecil) -->
    <div class="hidden sm:flex flex-col justify-center">
        <span class="font-bold text-xl leading-none text-slate-800 dark:text-white tracking-tight">Yumik</span>
    </div>
</div>
@props(['icon' => null, 'type' => 'text'])

<div class="relative w-full text-slate-500 focus-within:text-yumik-primary transition-colors">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="ph-bold ph-{{ $icon }} text-xl"></i>
        </div>
    @endif
    
    <input 
        type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'w-full py-4 pr-5 rounded-full bg-slate-100 dark:bg-[#242424] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all border-none ' . ($icon ? 'pl-12' : 'pl-5')
        ]) }}
    >
</div>
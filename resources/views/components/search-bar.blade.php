@props(['placeholder' => 'Cari data...', 'name' => 'search', 'value' => ''])

<div class="relative w-full z-20 group">
    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
        <i class="ph ph-text-aa text-xl text-slate-400 group-focus-within:text-yumik-primary transition-colors"></i>
    </div>
    
    <input 
        type="text" 
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}" 
        {{ $attributes->merge(['class' => 'w-full py-4.5 pl-14 pr-16 rounded-[2rem] bg-white/80 dark:bg-black/20 backdrop-blur-xl border border-white/60 dark:border-white/10 shadow-[0_8px_30px_rgba(0,0,0,0.04)] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all']) }}
    >
    
    <button type="submit" class="absolute inset-y-0 right-2 my-auto w-11 h-11 rounded-full bg-yumik-primary flex items-center justify-center text-white shadow-lg shadow-yumik-primary/30 hover:bg-[#15b353] active:scale-95 transition-all">
        <i class="ph-bold ph-magnifying-glass text-lg"></i>
    </button>
</div>
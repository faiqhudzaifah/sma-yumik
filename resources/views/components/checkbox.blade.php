@props(['id', 'label' => null, 'checked' => false])

<label for="{{ $id }}" class="flex items-center gap-3 cursor-pointer group">
    <div class="relative flex items-center justify-center w-6 h-6">
        <input type="checkbox" id="{{ $id }}" class="peer sr-only" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
        
        <!-- Wadah Kaca -->
        <div class="w-full h-full rounded-[0.5rem] bg-white/60 dark:bg-white/5 border border-slate-300 dark:border-white/20 peer-checked:bg-yumik-primary peer-checked:border-yumik-primary peer-checked:shadow-lg peer-checked:shadow-yumik-primary/30 transition-all duration-300"></div>
        
        <!-- Ikon Centang -->
        <i class="ph-bold ph-check absolute text-white opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-300 text-sm"></i>
    </div>
    
    @if($label)
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-yumik-primary transition-colors select-none">{{ $label }}</span>
    @endif
</label>
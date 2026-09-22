@props(['id', 'name', 'label' => null, 'checked' => false])

<label for="{{ $id }}" class="flex items-center gap-3 cursor-pointer group">
    <div class="relative flex items-center justify-center w-6 h-6">
        <input type="radio" id="{{ $id }}" name="{{ $name }}" class="peer sr-only" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
        
        <!-- Wadah Bulat -->
        <div class="w-full h-full rounded-full bg-white/60 dark:bg-white/5 border border-slate-300 dark:border-white/20 peer-checked:border-yumik-primary peer-checked:shadow-md peer-checked:shadow-yumik-primary/20 transition-all duration-300"></div>
        
        <!-- Titik Isi -->
        <div class="absolute w-2.5 h-2.5 rounded-full bg-yumik-primary opacity-0 peer-checked:opacity-100 scale-0 peer-checked:scale-100 transition-all duration-300"></div>
    </div>
    
    @if($label)
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-yumik-primary transition-colors select-none">{{ $label }}</span>
    @endif
</label>
@props(['icon' => null, 'type' => 'text', 'label' => null])

<div class="w-full">
    @if($label)
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $label }}</label>
    @endif
    
    <div class="relative group text-slate-500 focus-within:text-yumik-primary transition-colors">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="ph-bold ph-{{ $icon }} text-xl"></i>
            </div>
        @endif
        
        @php
            $inputClasses = 'w-full py-4 pr-5 rounded-3xl bg-slate-100 dark:bg-[#242424] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all border-none ' . ($icon ? 'pl-12' : 'pl-5');
        @endphp

        @if($type === 'textarea')
            <textarea {{ $attributes->merge(['class' => $inputClasses]) }}></textarea>
        @else
            <input type="{{ $type }}" {{ $attributes->merge(['class' => $inputClasses]) }}>
        @endif
    </div>
</div>
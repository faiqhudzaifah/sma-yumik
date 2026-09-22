@props(['checked' => false])

<label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox" class="sr-only peer" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
    <div class="w-14 h-8 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yumik-primary shadow-inner"></div>
</label>
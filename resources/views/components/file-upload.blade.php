@props(['label' => 'Unggah Dokumen', 'help' => 'Tarik file ke sini atau klik untuk mencari. (Maks 5MB)'])

<div class="w-full relative group cursor-pointer">
    <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" {{ $attributes }}>
    
    <div class="w-full rounded-[2rem] border-2 border-dashed border-slate-300 dark:border-slate-700/50 bg-white/40 dark:bg-[#1A1F29]/40 backdrop-blur-md p-10 flex flex-col items-center justify-center text-center transition-all group-hover:bg-white/60 group-hover:border-yumik-primary/50">
        
        <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-white to-slate-100 dark:from-[#2A2A2A] dark:to-[#1A1A1A] shadow-[inset_-4px_-4px_8px_rgba(0,0,0,0.05),inset_4px_4px_8px_rgba(255,255,255,0.8)] dark:shadow-[inset_-4px_-4px_8px_rgba(0,0,0,0.3),inset_4px_4px_8px_rgba(255,255,255,0.05)] flex items-center justify-center mb-4 group-hover:-translate-y-1 transition-transform">
            <i class="ph-fill ph-cloud-arrow-up text-3xl text-yumik-primary drop-shadow-sm"></i>
        </div>
        
        <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1">{{ $label }}</h4>
        <p class="text-xs text-slate-500">{{ $help }}</p>
    </div>
</div>
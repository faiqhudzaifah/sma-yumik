@props(['icon' => 'folder-open', 'title', 'description'])

<div class="w-full p-12 flex flex-col items-center justify-center text-center">
    <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-white to-slate-100 dark:from-[#2A2A2A] dark:to-[#1A1A1A] shadow-[inset_-4px_-4px_8px_rgba(0,0,0,0.05),inset_4px_4px_8px_rgba(255,255,255,0.8)] dark:shadow-[inset_-4px_-4px_8px_rgba(0,0,0,0.3),inset_4px_4px_8px_rgba(255,255,255,0.05)] flex items-center justify-center mb-6">
        <i class="ph-fill ph-{{ $icon }} text-5xl text-slate-300 dark:text-slate-600 drop-shadow-sm"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 tracking-tight">{{ $title }}</h3>
    <p class="text-sm text-slate-500 max-w-sm">{{ $description }}</p>
    <div class="mt-8">
        {{ $slot }}
    </div>
</div>
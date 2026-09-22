@props(['id', 'title' => 'Perhatian'])

<!-- Backdrop Overlay -->
<div id="{{ $id }}" class="fixed inset-0 z-[100] hidden items-center justify-center p-6 bg-slate-900/30 dark:bg-black/60 backdrop-blur-md transition-opacity duration-300 opacity-0 data-[state=open]:opacity-100 data-[state=open]:flex">
    
    <!-- Modal Box -->
    <div class="w-full max-w-sm bg-white/95 dark:bg-yumik-surface-dark/95 backdrop-blur-2xl rounded-[2.5rem] p-6 shadow-2xl border border-white/60 dark:border-white/10 transform scale-95 data-[state=open]:scale-100 transition-transform duration-300 relative">
        
        <!-- Tombol Tutup -->
        <button type="button" onclick="document.getElementById('{{ $id }}').setAttribute('data-state', 'closed'); setTimeout(() => document.getElementById('{{ $id }}').classList.add('hidden'), 300)" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-[#2A2A2A] text-slate-500 hover:text-red-500 active:scale-90 transition-all">
            <i class="ph-bold ph-x"></i>
        </button>

        <!-- Ikon Header -->
        <div class="w-16 h-16 rounded-[1.5rem] bg-red-100 dark:bg-red-500/20 text-red-500 flex items-center justify-center mb-5 shadow-inner">
            <i class="ph-fill ph-warning-circle text-3xl drop-shadow-sm"></i>
        </div>

        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 tracking-tight">{{ $title }}</h3>
        <div class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
            {{ $slot }}
        </div>

        <div class="flex gap-3">
            <x-button variant="secondary" class="flex-1 py-3" onclick="document.getElementById('{{ $id }}').setAttribute('data-state', 'closed'); setTimeout(() => document.getElementById('{{ $id }}').classList.add('hidden'), 300)">Batal</x-button>
            <x-button variant="primary" class="flex-1 py-3">Konfirmasi</x-button>
        </div>
    </div>
</div>
@extends('layouts.app')

@section('title', 'Unggah Materi Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.material.index', $schedule->id) }}">Bank Materi</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Unggah Materi</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-8 border-t-4 border-t-yumik-primary">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Materi Baru</h2>
        <p class="text-sm text-slate-500 mb-8">Tambahkan bahan ajar pendukung untuk kelas ini.</p>

        <form action="{{ route('guru.schedule.material.store', $schedule->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                <x-input name="title" value="{{ old('title') }}" placeholder="Contoh: Modul Bab 1 - Pengantar" icon="text-t" required />
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi / Instruksi (Opsional)</label>
                <textarea name="description" rows="3" class="w-full p-4 rounded-2xl bg-slate-100 dark:bg-black/20 border-none text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all" placeholder="Penjelasan singkat mengenai materi ini...">{{ old('description') }}</textarea>
            </div>

            <!-- TABS untuk memilih jenis unggahan (File vs Link) -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Pilih Salah Satu Sumber Materi</p>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Unggah Berkas (PDF/DOC/PPT)</label>
                    <x-file-upload name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" />
                </div>
                
                <div class="flex items-center gap-4 my-2">
                    <div class="h-px bg-slate-200 dark:bg-white/10 flex-1"></div>
                    <span class="text-xs font-bold text-slate-400">ATAU</span>
                    <div class="h-px bg-slate-200 dark:bg-white/10 flex-1"></div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tautan Luar (Google Drive / YouTube)</label>
                    <x-input name="external_link" value="{{ old('external_link') }}" placeholder="https://..." icon="link" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.material.index', $schedule->id) }}">
                    <x-button variant="secondary" type="button">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="shadow-lg shadow-yumik-primary/30"><i class="ph-bold ph-upload-simple"></i> Simpan & Publikasikan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
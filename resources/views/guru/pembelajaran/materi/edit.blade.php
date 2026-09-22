@extends('layouts.app')

@section('title', 'Edit Materi - ' . $material->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.material.index', $schedule->id) }}">Bank Materi</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Edit Materi</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-8 border-t-4 border-t-blue-500 shadow-xl bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Perbarui Materi</h2>
        <p class="text-sm text-slate-500 mb-8">Memperbarui informasi atau berkas materi ajar.</p>

        <form action="{{ route('guru.schedule.material.update', [$schedule->id, $material->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                <x-input name="title" value="{{ old('title', $material->title) }}" icon="text-t" required />
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi / Instruksi (Opsional)</label>
                <textarea name="description" rows="3" class="w-full p-4 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200/60 dark:border-white/10 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all">{{ old('description', $material->description) }}</textarea>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200/60 dark:border-white/10 space-y-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Sumber Materi Saat Ini</p>
                
                @if($material->file_path)
                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/10 mb-4">
                        <i class="ph-fill ph-file-pdf text-2xl text-red-500"></i>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Berkas Terlampir</span>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas (Opsional)</label>
                    <p class="text-xs text-slate-400 mb-2">Abaikan form ini jika Anda tidak ingin mengganti file yang lama.</p>
                    <x-file-upload name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" />
                </div>
                
                <div class="flex items-center gap-4 my-2">
                    <div class="h-px bg-slate-200 dark:bg-white/10 flex-1"></div>
                    <span class="text-xs font-bold text-slate-400">ATAU</span>
                    <div class="h-px bg-slate-200 dark:bg-white/10 flex-1"></div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Perbarui Tautan Luar</label>
                    <x-input name="external_link" value="{{ old('external_link', $material->external_link) }}" placeholder="https://..." icon="link" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.material.index', $schedule->id) }}">
                    <x-button variant="secondary" type="button" class="!px-6">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="!px-6 shadow-lg shadow-yumik-primary/30"><i class="ph-bold ph-floppy-disk"></i> Simpan Perubahan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
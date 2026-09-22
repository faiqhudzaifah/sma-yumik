@extends('layouts.app')

@section('title', 'Buat Tugas Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assignment.index', $schedule->id) }}">Tugas & Kuis</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Buat Tugas Manual</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-8 border-t-4 border-t-yumik-primary shadow-xl">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Tugas / Evaluasi Baru</h2>
        <p class="text-sm text-slate-500 mb-8">Sistem akan membuatkan form penilaian (grading) kosong untuk seluruh siswa secara otomatis.</p>

        <form action="{{ route('guru.schedule.assignment.store', $schedule->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Tugas <span class="text-red-500">*</span></label>
                <x-input name="title" value="{{ old('title') }}" placeholder="Contoh: Makalah Sejarah Kemerdekaan" icon="text-t" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <x-select name="type" icon="folders">
                        <option value="Tugas">Tugas Harian</option>
                        <option value="Proyek">Proyek / Portofolio</option>
                        <option value="Praktik">Praktik</option>
                        <option value="Kuis">Kuis Manual (Non-CBT)</option>
                        <option value="UTS">UTS Manual</option>
                        <option value="UAS">UAS Manual</option>
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Pengumpulan (Opsional)</label>
                    <x-input type="datetime-local" name="due_date" value="{{ old('due_date') }}" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Instruksi / Detail Tugas</label>
                <textarea name="description" rows="5" class="w-full p-4 rounded-2xl bg-slate-100 dark:bg-black/20 border-none text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all" placeholder="Jelaskan kriteria penilaian, format dokumen yang harus diunggah siswa, dll..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.assignment.index', $schedule->id) }}">
                    <x-button variant="secondary" type="button">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-plus-circle"></i> Buat Tugas
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
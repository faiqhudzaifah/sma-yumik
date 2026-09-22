@extends('layouts.app')

@section('title', 'Edit Ujian - ' . $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.show', [$schedule->id, $assessment->id]) }}">Detail Ujian</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Edit Pengaturan</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-8 border-t-4 border-t-yumik-accent shadow-xl">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Perbarui Pengaturan Ujian</h2>
        <p class="text-sm text-slate-500 mb-8">Memperbarui data ujian: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $assessment->title }}</span></p>

        <form action="{{ route('guru.schedule.assessment.update', [$schedule->id, $assessment->id]) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Evaluasi <span class="text-red-500">*</span></label>
                    <x-input name="title" value="{{ old('title', $assessment->title) }}" icon="text-t" required />
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Evaluasi <span class="text-red-500">*</span></label>
                    <x-select name="type" icon="tag">
                        <option value="QUIZ" {{ $assessment->type == 'QUIZ' ? 'selected' : '' }}>Kuis</option>
                        <option value="EXAM" {{ $assessment->type == 'EXAM' ? 'selected' : '' }}>Ujian (UTS/UAS)</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                    <x-select name="status" icon="broadcast">
                        <option value="DRAFT" {{ $assessment->status == 'DRAFT' ? 'selected' : '' }}>DRAFT (Disembunyikan)</option>
                        <option value="PUBLISHED" {{ $assessment->status == 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED (Terbuka)</option>
                        <option value="CLOSED" {{ $assessment->status == 'CLOSED' ? 'selected' : '' }}>CLOSED (Selesai/Ditutup)</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Durasi (Menit) <span class="text-red-500">*</span></label>
                    <x-input type="number" min="1" name="duration" value="{{ old('duration', $assessment->duration) }}" icon="timer" required />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Percobaan <span class="text-red-500">*</span></label>
                    <x-input type="number" min="1" name="attempt_limit" value="{{ old('attempt_limit', $assessment->attempt_limit) }}" icon="arrows-clockwise" required />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.assessment.show', [$schedule->id, $assessment->id]) }}">
                    <x-button variant="secondary" type="button">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-floppy-disk"></i> Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
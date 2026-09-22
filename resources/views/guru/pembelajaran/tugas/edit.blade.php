@extends('layouts.app')

@section('title', 'Edit Tugas - ' . $assignment->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assignment.index', $schedule->id) }}">Tugas & Kuis</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.assignment.show', [$schedule->id, $assignment->id]) }}">Detail Tugas</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Edit Tugas</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-6 lg:p-8 border-t-4 border-t-yumik-accent shadow-xl bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Edit Tugas / Evaluasi</h2>
        <p class="text-sm text-slate-500 mb-8">Memperbarui data tugas: <span class="font-bold">{{ $assignment->title }}</span></p>

        <form action="{{ route('guru.schedule.assignment.update', [$schedule->id, $assignment->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Tugas <span class="text-red-500">*</span></label>
                <x-input name="title" value="{{ old('title', $assignment->title) }}" icon="text-t" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <x-select name="type" icon="folders">
                        <option value="Tugas" {{ $assignment->type == 'Tugas' ? 'selected' : '' }}>Tugas Harian</option>
                        <option value="Proyek" {{ $assignment->type == 'Proyek' ? 'selected' : '' }}>Proyek / Portofolio</option>
                        <option value="Praktik" {{ $assignment->type == 'Praktik' ? 'selected' : '' }}>Praktik</option>
                        <option value="Kuis" {{ $assignment->type == 'Kuis' ? 'selected' : '' }}>Kuis Manual</option>
                        <option value="UTS" {{ $assignment->type == 'UTS' ? 'selected' : '' }}>UTS Manual</option>
                        <option value="UAS" {{ $assignment->type == 'UAS' ? 'selected' : '' }}>UAS Manual</option>
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Pengumpulan (Opsional)</label>
                    <x-input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i') : '') }}" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Instruksi / Detail Tugas</label>
                <textarea name="description" rows="5" class="w-full p-4 rounded-[1.25rem] bg-slate-50 dark:bg-black/20 border border-slate-200/60 dark:border-white/10 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.assignment.show', [$schedule->id, $assignment->id]) }}">
                    <x-button variant="secondary" type="button" class="!px-6">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="!px-6 shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-floppy-disk"></i> Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
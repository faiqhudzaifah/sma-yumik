@extends('layouts.app')

@section('title', 'Buat Pertemuan Baru - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb disamakan dengan index -->
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Buat Sesi Baru</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- Header Form dengan style Gradien yang sama seperti Index -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-[1rem] bg-white/50 dark:bg-black/20 text-yumik-primary flex items-center justify-center shrink-0 shadow-sm border border-white/50 dark:border-white/10">
                <i class="ph-fill ph-presentation-chart text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                    Pertemuan Baru
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1 font-medium text-sm">
                    Buat sesi jurnal mengajar untuk <span class="text-yumik-primary font-bold">{{ $schedule->classroom->name }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Container Form dengan gaya Glassmorphism -->
    <x-card class="p-6 lg:p-8 border-none bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl shadow-lg shadow-slate-200/50 dark:shadow-none">
        <form action="{{ route('guru.schedule.meeting.store', $schedule->id) }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                    <x-input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required class="!bg-slate-50 dark:!bg-black/20 border-slate-200/60" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Pertemuan Ke- <span class="text-red-500">*</span></label>
                    <x-input type="number" min="1" name="meeting_number" value="{{ old('meeting_number', $nextMeetingNumber ?? 1) }}" icon="list-numbers" required class="!bg-slate-50 dark:!bg-black/20 border-slate-200/60" />
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Topik / Bahasan Utama <span class="text-red-500">*</span></label>
                <x-input name="topic" value="{{ old('topic') }}" placeholder="Contoh: Pengantar Persamaan Linear" icon="text-t" required class="!bg-slate-50 dark:!bg-black/20 border-slate-200/60" />
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Jurnal / Deskripsi Aktivitas (Opsional)</label>
                <textarea name="description" rows="4" class="w-full p-4 rounded-[1.25rem] bg-slate-50 dark:bg-black/20 border border-slate-200/60 dark:border-white/10 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all" placeholder="Catatan kegiatan mengajar hari ini...">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">
                    <x-button variant="secondary" type="button" class="!px-6">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="!px-6 shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-calendar-plus"></i> Simpan Pertemuan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
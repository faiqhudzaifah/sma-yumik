@extends('layouts.app')

@section('title', 'Bank Materi - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Bank Materi</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">Bank Materi Pembelajaran</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Bahan ajar dan referensi untuk kelas <span class="text-yumik-primary font-bold">{{ $schedule->classroom->name }}</span></p>
        </div>
        
        <a href="{{ route('guru.schedule.material.create', $schedule->id) }}">
            <x-button variant="primary" class="shadow-md">
                <i class="ph-bold ph-plus text-lg"></i> Unggah Materi Baru
            </x-button>
        </a>
    </div>

    <!-- TABS NAVIGASI -->
    <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar border-b border-slate-200 dark:border-white/10">
        <a href="{{ route('guru.schedule.meeting.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-calendar-check mr-2"></i> Alur Pertemuan
        </a>
        <a href="{{ route('guru.schedule.assignment.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-exam mr-2"></i> Tugas & Kuis
        </a>
        <a href="{{ route('guru.schedule.gradebook.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-notebook mr-2"></i> Buku Nilai
        </a>
        <a href="{{ route('guru.schedule.material.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-yumik-primary text-yumik-primary font-bold whitespace-nowrap">
            <i class="ph-bold ph-books mr-2"></i> Bank Materi
        </a>
    </div>

    <x-card class="p-6 lg:p-8 border-none bg-slate-50/30 dark:bg-black/10">
        @if($materials->isEmpty())
            <x-empty-state icon="books" title="Belum ada materi ajar" description="Unggah modul, slide presentasi, atau tautan video pembelajaran untuk diakses oleh siswa." />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($materials as $material)
                    <x-card class="p-5 flex flex-col justify-between hover:shadow-lg transition-shadow group border border-slate-100 dark:border-white/5">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-12 h-12 rounded-2xl {{ $material->file_path ? 'bg-red-50 text-red-500 dark:bg-red-500/10' : 'bg-blue-50 text-blue-500 dark:bg-blue-500/10' }} flex items-center justify-center">
                                    <i class="ph-fill {{ $material->file_path ? 'ph-file-pdf' : 'ph-link' }} text-3xl"></i>
                                </div>
                                
                                <!-- Opsi Edit & Hapus -->
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('guru.schedule.material.edit', [$schedule->id, $material->id]) }}" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-yumik-primary flex items-center justify-center transition-colors"><i class="ph-bold ph-pencil"></i></a>
                                    <form action="{{ route('guru.schedule.material.destroy', [$schedule->id, $material->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-red-500 flex items-center justify-center transition-colors"><i class="ph-bold ph-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-2">{{ $material->title }}</h3>
                            <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $material->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                            @if($material->file_path)
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-sm font-bold text-yumik-primary flex items-center gap-2 hover:gap-3 transition-all"><i class="ph-bold ph-download-simple"></i> Unduh Berkas</a>
                            @elseif($material->external_link)
                                <a href="{{ $material->external_link }}" target="_blank" class="text-sm font-bold text-blue-500 flex items-center gap-2 hover:gap-3 transition-all"><i class="ph-bold ph-arrow-square-out"></i> Buka Tautan Luar</a>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>
            
            @if($materials->hasPages())
                <div class="mt-8"><x-pagination :paginator="$materials" /></div>
            @endif
        @endif
    </x-card>
</div>
@endsection
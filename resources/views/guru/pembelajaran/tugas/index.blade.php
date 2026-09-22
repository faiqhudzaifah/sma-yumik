@extends('layouts.app')

@section('title', 'Tugas & Evaluasi - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Tugas & Evaluasi</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                Manajemen Tugas
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                Kelola portofolio, PR, dan proyek untuk kelas <span class="text-yumik-primary font-bold">{{ $schedule->classroom->name }}</span>
            </p>
        </div>
        
        <a href="{{ route('guru.schedule.assignment.create', $schedule->id) }}">
            <x-button variant="primary" class="shadow-md">
                <i class="ph-bold ph-plus text-lg"></i> Buat Tugas Baru
            </x-button>
        </a>
    </div>

    <!-- TABS NAVIGASI -->
    <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar border-b border-slate-200 dark:border-white/10">
        <a href="{{ route('guru.schedule.meeting.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-calendar-check mr-2"></i> Alur Pertemuan
        </a>
        <a href="{{ route('guru.schedule.assignment.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-yumik-primary text-yumik-primary font-bold whitespace-nowrap">
            <i class="ph-bold ph-exam mr-2"></i> Tugas & Kuis
        </a>
        <a href="{{ route('guru.schedule.gradebook.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-notebook mr-2"></i> Buku Nilai
        </a>
    </div>

    <x-card class="p-6 lg:p-8 bg-slate-50/30 dark:bg-black/10 border-none shadow-none">
        @if($assignments->isEmpty())
            <x-empty-state 
                icon="file-dashed" 
                title="Belum ada tugas" 
                description="Anda belum memberikan tugas apapun untuk kelas ini." 
            />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($assignments as $assignment)
                    @php
                        $isOverdue = $assignment->due_date && \Carbon\Carbon::parse($assignment->due_date)->isPast();
                    @endphp
                    <x-card class="p-6 hover:-translate-y-1 transition-transform group cursor-pointer border-2 {{ $isOverdue ? 'border-red-500/20 hover:border-red-500/50' : 'border-transparent hover:border-yumik-primary/30' }}">
                        
                        <div class="flex justify-between items-start mb-4">
                            <x-badge color="{{ $isOverdue ? 'neutral' : 'primary' }}" class="{{ $isOverdue ? '!bg-red-50 !text-red-600 dark:!bg-red-900/30 dark:!text-red-400' : '' }}">
                                {{ $assignment->type }}
                            </x-badge>
                            
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 group-hover:bg-yumik-primary group-hover:text-white transition-colors">
                                <i class="ph-bold ph-caret-right"></i>
                            </div>
                        </div>

                        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-2 line-clamp-2 group-hover:text-yumik-primary transition-colors">
                            {{ $assignment->title }}
                        </h3>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                                <i class="ph-fill ph-clock {{ $isOverdue ? 'text-red-500' : 'text-slate-400' }}"></i>
                                @if($assignment->due_date)
                                    <span class="{{ $isOverdue ? 'text-red-500' : '' }}">
                                        {{ \Carbon\Carbon::parse($assignment->due_date)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                @else
                                    Tanpa Tenggat
                                @endif
                            </div>
                            <!-- Progres pengumpulan statis untuk UI, bisa diganti dengan query count dari relasi grades -->
                            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                                <i class="ph-fill ph-users-three text-slate-400"></i>
                                0 / {{ $schedule->classroom->capacity }} Terkumpul
                            </div>
                        </div>

                        <a href="{{ route('guru.schedule.assignment.show', [$schedule->id, $assignment->id]) }}" class="block">
                            <x-button variant="secondary" class="w-full !py-2 !rounded-xl !text-sm group-hover:!bg-yumik-primary group-hover:!text-white group-hover:!border-yumik-primary transition-colors">
                                Lihat Pengumpulan
                            </x-button>
                        </a>
                    </x-card>
                @endforeach
            </div>

            @if($assignments->hasPages())
                <div class="mt-8">
                    <x-pagination :paginator="$assignments" />
                </div>
            @endif
        @endif
    </x-card>
</div>
@endsection
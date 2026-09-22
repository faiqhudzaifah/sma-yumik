@extends('layouts.app')

@section('title', 'Ujian & CBT - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Ujian & Kuis</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                Computer Based Test (CBT)
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                Kelola Kuis dan Ujian (UTS/UAS) untuk kelas <span class="text-yumik-primary font-bold">{{ $schedule->classroom->name }}</span>
            </p>
        </div>
        
        <a href="{{ route('guru.schedule.assessment.create', $schedule->id) }}">
            <x-button variant="primary" class="shadow-md">
                <i class="ph-bold ph-plus text-lg"></i> Buat Ujian Baru
            </x-button>
        </a>
    </div>

    <x-card class="p-6 lg:p-8 bg-slate-50/30 dark:bg-black/10 border-none shadow-none">
        @if($assessments->isEmpty())
            <x-empty-state 
                icon="exam" 
                title="Belum ada ujian" 
                description="Anda belum membuat kuis atau ujian CBT untuk kelas ini." 
            />
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($assessments as $assessment)
                    <x-card class="p-6 flex flex-col justify-between hover:shadow-lg transition-all group border-2 {{ $assessment->status == 'PUBLISHED' ? 'border-yumik-primary/30' : 'border-transparent' }}">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex gap-2">
                                    <x-badge color="neutral" class="!text-[10px]">{{ $assessment->type }}</x-badge>
                                    <x-badge color="{{ $assessment->status == 'PUBLISHED' ? 'primary' : 'neutral' }}" class="!text-[10px] {{ $assessment->status == 'DRAFT' ? '!bg-amber-100 !text-amber-700' : '' }}">
                                        {{ $assessment->status }}
                                    </x-badge>
                                </div>
                                
                                <form action="{{ route('guru.schedule.assessment.destroy', [$schedule->id, $assessment->id]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" onclick="return confirm('Hapus ujian ini beserta nilainya?')"><i class="ph-bold ph-trash"></i></button>
                                </form>
                            </div>

                            <h3 class="font-bold text-xl text-slate-800 dark:text-white mb-2 line-clamp-1 group-hover:text-yumik-primary transition-colors">
                                {{ $assessment->title }}
                            </h3>
                            <p class="text-sm text-slate-500 line-clamp-2 mb-4">{{ $assessment->description ?? 'Tidak ada deskripsi.' }}</p>

                            <div class="grid grid-cols-2 gap-4 mb-6 pt-4 border-t border-slate-100 dark:border-white/5">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Jadwal Mulai</p>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $assessment->start_at ? \Carbon\Carbon::parse($assessment->start_at)->format('d M Y, H:i') : 'Belum diatur' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Durasi</p>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $assessment->duration }} Menit</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('guru.schedule.assessment.show', [$schedule->id, $assessment->id]) }}" class="flex-1">
                                <x-button variant="secondary" class="w-full !py-2.5 !text-sm"><i class="ph-bold ph-cards"></i> Bank Soal</x-button>
                            </a>
                            
                            @if($assessment->status == 'PUBLISHED' || $assessment->status == 'CLOSED')
                                <a href="{{ route('guru.schedule.assessment.grading.index', [$schedule->id, $assessment->id]) }}" class="flex-1">
                                    <x-button variant="primary" class="w-full !py-2.5 !text-sm"><i class="ph-bold ph-check-square-offset"></i> Hasil Ujian</x-button>
                                </a>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>

            @if($assessments->hasPages())
                <div class="mt-8"><x-pagination :paginator="$assessments" /></div>
            @endif
        @endif
    </x-card>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Hasil Ujian - ' . $assessment->title)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.index', $schedule->id) }}">CBT & Ujian</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.show', [$schedule->id, $assessment->id]) }}">{{ $assessment->title }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Daftar Hasil Siswa</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-6 lg:p-8">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Rekapitulasi Hasil Ujian</h2>
                <p class="text-sm text-slate-500 mt-1">Siswa kelas <span class="font-bold">{{ $schedule->classroom->name }}</span></p>
            </div>
            <div class="flex gap-2">
                <!-- Metrik Ringkas -->
                <div class="px-4 py-2 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-100 dark:border-white/5 text-center">
                    <span class="block text-2xl font-black text-slate-800 dark:text-white">{{ $submissions->count() }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Selesai</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-black/20 rounded-xl border border-slate-100 dark:border-white/5 text-center">
                    <span class="block text-2xl font-black text-slate-800 dark:text-white">{{ $siswas->count() - $submissions->count() }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Belum</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-white dark:bg-transparent border-b border-slate-100 dark:border-white/5 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        <th class="p-4 pl-0">Nama Siswa</th>
                        <th class="p-4 text-center">Waktu Pengerjaan</th>
                        <th class="p-4 text-center">Skor Sementara</th>
                        <th class="p-4 pr-0 text-right">Aksi Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($siswas as $siswa)
                        @php 
                            $submission = $submissions->get($siswa->id); 
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                            <td class="p-4 pl-0">
                                <div class="flex items-center gap-3">
                                    <x-avatar src="{{ $siswa->photo ?? '' }}" size="sm" />
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $siswa->user->name }}</span>
                                        <span class="text-[10px] font-medium text-slate-400">{{ $siswa->nisn }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="p-4 text-center">
                                @if($submission)
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->diffInMinutes(\Carbon\Carbon::parse($submission->started_at)) . ' Menit' : 'Sedang mengerjakan...' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('H:i, d M') : '-' }}</p>
                                @else
                                    <x-badge color="neutral" class="!text-[10px]">Belum Mulai</x-badge>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                @if($submission)
                                    <span class="text-lg font-black text-yumik-primary">{{ number_format($submission->score ?? 0, 1) }}</span>
                                @else
                                    <span class="text-sm font-semibold text-slate-300">-</span>
                                @endif
                            </td>

                            <td class="p-4 pr-0 text-right">
                                @if($submission && $submission->submitted_at)
                                    <a href="{{ route('guru.schedule.assessment.grading.show', [$schedule->id, $assessment->id, $submission->id]) }}">
                                        <x-button variant="secondary" class="!py-2 !px-4 !text-xs !rounded-xl group-hover:!bg-yumik-primary group-hover:!text-white group-hover:!border-yumik-primary transition-colors">
                                            Koreksi Esai <i class="ph-bold ph-arrow-right"></i>
                                        </x-button>
                                    </a>
                                @else
                                    <button disabled class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-white/5 text-slate-400 cursor-not-allowed">
                                        Belum Tersedia
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Tugas Saya - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8" x-data="{ filter: 'semua' }">

    <x-card class="!bg-gradient-to-r from-amber-500/10 to-transparent border-amber-500/20 p-6 lg:p-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 shrink-0">
                <i class="ph-fill ph-clipboard-text text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Tugas Saya</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                    Total {{ $assignments->count() }} tugas
                    @if($activeSemester) · Semester {{ $activeSemester->type }}@endif
                </p>
            </div>
        </div>
    </x-card>

    {{-- FILTER TABS --}}
    <div class="flex flex-wrap gap-2">
        @foreach([
            'semua' => ['label' => 'Semua', 'color' => 'slate'],
            'belum' => ['label' => 'Belum Dikerjakan', 'color' => 'amber'],
            'submitted' => ['label' => 'Sudah Dikumpulkan', 'color' => 'blue'],
            'graded' => ['label' => 'Sudah Dinilai', 'color' => 'green'],
            'late' => ['label' => 'Terlambat', 'color' => 'red'],
        ] as $key => $opt)
            <button @click="filter = '{{ $key }}'"
                    :class="filter === '{{ $key }}' ? 'bg-yumik-primary text-white shadow-lg shadow-yumik-primary/30' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/10'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all">
                {{ $opt['label'] }}
                @if($key !== 'semua')
                    ({{ $assignments->where('status', $key)->count() }})
                @else
                    ({{ $assignments->count() }})
                @endif
            </button>
        @endforeach
    </div>

    @if($assignments->isEmpty())
        <x-card class="!p-0">
            <x-empty-state
                icon="clipboard-text"
                title="Belum Ada Tugas"
                description="Tugas akan muncul di sini setelah gurumu menambahkannya."
            />
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($assignments as $assignment)
                <div x-show="filter === 'semua' || filter === '{{ $assignment->status }}'"
                     class="block">
                    <a href="{{ route('siswa.assignment.show', $assignment->id) }}" class="block group">
                        <x-card class="!p-6 hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.2)] hover:-translate-y-0.5 transition-all">
                            <div class="flex flex-col md:flex-row md:items-center gap-5">
                                @php
                                    $statusConfig = [
                                        'belum' => ['icon' => 'clipboard-text', 'color' => 'amber', 'label' => 'Belum Dikerjakan'],
                                        'submitted' => ['icon' => 'paper-plane-tilt', 'color' => 'blue', 'label' => 'Sudah Dikumpulkan'],
                                        'graded' => ['icon' => 'check-circle', 'color' => 'green', 'label' => 'Sudah Dinilai'],
                                        'late' => ['icon' => 'warning-circle', 'color' => 'red', 'label' => 'Terlambat'],
                                    ];
                                    $sc = $statusConfig[$assignment->status] ?? $statusConfig['belum'];
                                @endphp

                                <div class="w-14 h-14 rounded-2xl bg-{{ $sc['color'] }}-100 dark:bg-{{ $sc['color'] }}-500/20 text-{{ $sc['color'] }}-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-{{ $sc['icon'] }} text-2xl"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <x-badge color="{{ $sc['color'] === 'green' ? 'primary' : 'neutral' }}">
                                            {{ $sc['label'] }}
                                        </x-badge>
                                        <x-badge color="accent">{{ $assignment->type }}</x-badge>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800 dark:text-white truncate group-hover:text-yumik-primary transition-colors">
                                        {{ $assignment->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-medium mt-1">
                                        {{ $assignment->schedule->subject->name ?? '-' }}
                                        · {{ $assignment->schedule->guru->name ?? '-' }}
                                    </p>
                                </div>

                                <div class="text-right shrink-0">
                                    @if($assignment->status === 'graded')
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai</p>
                                        <p class="text-3xl font-black text-yumik-primary">{{ $assignment->grade->score }}</p>
                                    @elseif($assignment->due_date)
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tenggat</p>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">
                                            {{ $assignment->due_date->translatedFormat('d M Y') }}
                                        </p>
                                        <p class="text-xs font-bold text-slate-500">
                                            {{ $assignment->due_date->format('H:i') }} WIB
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>


@endsection
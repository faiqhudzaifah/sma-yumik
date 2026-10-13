@extends('layouts.app')

@section('title', 'Dashboard Siswa - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    {{-- ============================================ --}}
    {{-- HERO: SAPAAN + INFO --}}
    {{-- ============================================ --}}
    <x-card class="!bg-gradient-to-r from-yumik-primary/10 to-transparent border-yumik-primary/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-yumik-primary to-green-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-yumik-primary/30 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                </div>

                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                        Halo, {{ auth()->user()->name ?? 'Siswa' }}! 👋
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                        {{ now()->translatedFormat('l, d F Y') }}
                        @if($classroom)
                            · Kelas <span class="text-yumik-primary font-bold">{{ $classroom->name }}</span>
                        @endif
                        @if($activeSemester)
                            · Semester {{ $activeSemester->type }}
                        @endif
                    </p>
                </div>
            </div>

            @if($pendingAssignmentsCount > 0 || $availableExamsCount > 0)
                <div class="shrink-0 flex flex-wrap gap-2">
                    @if($pendingAssignmentsCount > 0)
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                            <i class="ph-fill ph-clipboard-text text-amber-500"></i>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                {{ $pendingAssignmentsCount }} tugas
                            </span>
                        </div>
                    @endif
                    @if($availableExamsCount > 0)
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/20">
                            <i class="ph-fill ph-exam text-purple-500"></i>
                            <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                                {{ $availableExamsCount }} ujian
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </x-card>
{{-- PENGUMUMAN --}}
@php
    $activeSemester = \App\Models\Semester::where('is_active', true)->first();
    $siswa = auth()->user()->siswa;
    $classroomIds = $siswa
        ? $siswa->classrooms()->where('semester_id', $activeSemester?->id)->pluck('classrooms.id')
        : collect();

    $myScheduleIds = \App\Models\Schedule::whereIn('classroom_id', $classroomIds)
        ->where('semester_id', $activeSemester?->id)
        ->pluck('id');

    $announcements = \App\Models\Announcement::with(['schedule.subject', 'schedule.classroom', 'user'])
        ->whereIn('schedule_id', $myScheduleIds)
        ->orderByDesc('priority')
        ->orderByDesc('start_at')
        ->take(5)
        ->get()
        ->map(function ($ann) {
            $ann->is_read = \App\Models\AnnouncementRead::where('announcement_id', $ann->id)
                ->where('user_id', auth()->id())
                ->exists();
            return $ann;
        });
@endphp

@if($announcements->isNotEmpty())
    <x-card class="p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-megaphone text-yumik-primary text-xl"></i>
                Pengumuman
            </h3>
            <span class="text-[10px] font-black uppercase tracking-widest text-yumik-primary bg-yumik-primary/10 px-2 py-1 rounded-full">
                {{ $announcements->count() }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach($announcements as $announcement)
                <x-announcement-card :announcement="$announcement" :allowMarkRead="true" :isRead="$announcement->is_read" />
            @endforeach
        </div>
    </x-card>
@endif
    {{-- ============================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <x-siswa.stat-card
            icon="books"
            label="Mata Pelajaran"
            :value="$activeCoursesCount"
            color="blue"
        />
        <x-siswa.stat-card
            icon="clipboard-text"
            label="Tugas Menunggu"
            :value="$pendingAssignmentsCount"
            color="amber"
        />
        <x-siswa.stat-card
            icon="exam"
            label="Ujian Tersedia"
            :value="$availableExamsCount"
            color="purple"
        />
    </div>

    {{-- ============================================ --}}
    {{-- MAIN GRID --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- KOLOM KIRI --}}
        <div class="xl:col-span-2 space-y-8">

            {{-- MATA PELAJARAN SAYA --}}
            <div>
                <div class="flex items-center justify-between mb-6 px-2">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[1rem] bg-yumik-primary/10 flex items-center justify-center text-yumik-primary">
                            <i class="ph-fill ph-books text-2xl"></i>
                        </div>
                        Mata Pelajaran Saya
                    </h2>
                    @if($activeSemester)
                        <x-badge color="accent">{{ $activeSemester->type }}</x-badge>
                    @endif
                </div>

                @if($recentCourses->isEmpty())
                    <x-card class="!p-0">
                        <x-empty-state
                            icon="books"
                            title="Belum Ada Mata Pelajaran"
                            description="Mata pelajaran akan muncul setelah kamu dimasukkan ke rombel oleh admin."
                        />
                    </x-card>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($recentCourses as $schedule)
                            <x-siswa.course-card :schedule="$schedule" />
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- JADWAL HARI INI --}}
            @if($todaySchedules->isNotEmpty())
                <x-card class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <div class="w-10 h-10 rounded-[1rem] bg-blue-500/10 flex items-center justify-center text-blue-500">
                                <i class="ph-fill ph-calendar-check text-2xl"></i>
                            </div>
                            Jadwal Hari Ini
                        </h2>
                        <x-badge color="neutral">{{ $todayName }}</x-badge>
                    </div>

                    <div class="space-y-3">
                        @foreach($todaySchedules as $jadwal)
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                                <div class="w-14 h-14 rounded-xl bg-white dark:bg-[#0A0D14] border border-slate-200 dark:border-white/10 flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[9px] font-bold uppercase text-slate-400 leading-none">Jam</span>
                                    <span class="text-lg font-black text-yumik-primary leading-none mt-0.5">{{ $jadwal->jam_ke_mulai }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                        {{ $jadwal->subject->name ?? '-' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        {{ $jadwal->guru->name ?? '-' }}
                                        @if($jadwal->ruangan)
                                            · {{ $jadwal->ruangan }}
                                        @endif
                                    </p>
                                </div>
                                <span class="text-xs font-bold text-slate-400 shrink-0">
                                    s/d {{ $jadwal->jam_ke_selesai }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

        </div>

        {{-- KOLOM KANAN --}}
        <div class="space-y-8">

            {{-- TUGAS MENDATANG --}}
            <x-card class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ph-fill ph-clipboard-text text-amber-500 text-xl"></i>
                        Tugas Mendatang
                    </h3>
                    @if($pendingAssignmentsCount > 0)
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-500 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-full">
                            {{ $pendingAssignmentsCount }}
                        </span>
                    @endif
                </div>

                @if($upcomingAssignments->isEmpty())
                    <div class="text-center py-8">
                        <i class="ph-fill ph-check-circle text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                        <p class="text-sm text-slate-500 font-medium">Tidak ada tugas mendatang</p>
                        <p class="text-xs text-slate-400 mt-1">Semua tugas sudah dikumpulkan 🎉</p>
                    </div>
                @else
                    <div class="space-y-1 -mx-2">
                        @foreach($upcomingAssignments as $assignment)
                            <x-siswa.task-item :assignment="$assignment" />
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- UJIAN TERSEDIA --}}
            <x-card class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ph-fill ph-exam text-purple-500 text-xl"></i>
                        Ujian Tersedia
                    </h3>
                    @if($availableExamsCount > 0)
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-500 bg-purple-50 dark:bg-purple-500/10 px-2 py-1 rounded-full">
                            {{ $availableExamsCount }}
                        </span>
                    @endif
                </div>

                @if($availableExams->isEmpty())
                    <div class="text-center py-8">
                        <i class="ph-fill ph-coffee text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                        <p class="text-sm text-slate-500 font-medium">Belum ada ujian</p>
                        <p class="text-xs text-slate-400 mt-1">Nikmati waktu luangmu</p>
                    </div>
                @else
                    <div class="space-y-1 -mx-2">
                        @foreach($availableExams as $assessment)
                            <x-siswa.exam-item :assessment="$assessment" />
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- QUICK LINKS --}}
            <x-card class="p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="ph-fill ph-lightning text-yumik-primary text-xl"></i>
                    Akses Cepat
                </h3>
                <div class="space-y-2">
                    @if(Route::has('siswa.assignment.index'))
                        <a href="{{ route('siswa.assignment.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-clipboard-text"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-yumik-primary transition-colors">Semua Tugas</span>
                            <i class="ph-bold ph-caret-right ml-auto text-slate-300 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endif

                    @if(Route::has('siswa.cbt.index'))
                        <a href="{{ route('siswa.cbt.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                            <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-exam"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-yumik-primary transition-colors">Semua Ujian</span>
                            <i class="ph-bold ph-caret-right ml-auto text-slate-300 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endif

                    @if(Route::has('siswa.akademik.rapor'))
                        <a href="{{ route('siswa.akademik.rapor') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-notebook"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-yumik-primary transition-colors">Rapor Akademik</span>
                            <i class="ph-bold ph-caret-right ml-auto text-slate-300 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endif

                    @if(Route::has('siswa.akademik.kehadiran'))
                        <a href="{{ route('siswa.akademik.kehadiran') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                            <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-500/20 text-green-500 flex items-center justify-center shrink-0">
                                <i class="ph-bold ph-calendar-check"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-yumik-primary transition-colors">Rekap Presensi</span>
                            <i class="ph-bold ph-caret-right ml-auto text-slate-300 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endif
                </div>
            </x-card>

        </div>
    </div>
</div>
@endsection
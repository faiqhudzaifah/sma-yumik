@extends('layouts.app')

@section('title', 'Mata Pelajaran - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    <x-card class="!bg-gradient-to-r from-yumik-primary/10 to-transparent border-yumik-primary/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-yumik-primary to-green-600 flex items-center justify-center text-white shadow-lg shadow-yumik-primary/30 shrink-0">
                    <i class="ph-fill ph-books text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                        Mata Pelajaran Saya
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                        {{ $courses->count() }} mata pelajaran
                        @if($activeSemester)
                            · Semester {{ $activeSemester->type }}
                        @endif
                        @if($enrollmentAktif)
                            · Kelas {{ $enrollmentAktif->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </x-card>

    @if($courses->isEmpty())
        <x-card class="!p-0">
            <x-empty-state
                icon="books"
                title="Belum Ada Mata Pelajaran"
                description="Kamu belum terdaftar di rombel mana pun, atau admin belum menambahkan jadwal untuk kelasmu."
            />
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $schedule)
                <x-siswa.course-card :schedule="$schedule" />
            @endforeach
        </div>
    @endif
</div>
@endsection
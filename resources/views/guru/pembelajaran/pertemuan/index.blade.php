@extends('layouts.app')

@section('title', 'Ruang Pembelajaran - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <!-- BREADCRUMB -->
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Ruang Kelas</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- HEADER COURSE -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                {{ $schedule->subject->name }} 
                <span class="text-yumik-primary">• {{ $schedule->classroom->name }}</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 flex items-center gap-4 font-medium">
                <span><i class="ph-bold ph-users text-slate-400"></i> Kapasitas: {{ $schedule->classroom->capacity }} Siswa</span>
                <span><i class="ph-bold ph-clock text-slate-400"></i> Setiap {{ $schedule->day_of_week }}, Jam ke-{{ $schedule->jam_ke_mulai }}-{{ $schedule->jam_ke_selesai }}</span>
            </p>
        </div>
        
        <x-button variant="secondary" class="shadow-sm">
            <i class="ph-bold ph-gear text-lg"></i> Pengaturan
        </x-button>
    </div>

    <!-- TABS NAVIGASI (Dinamis dengan Route) -->
    <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar border-b border-slate-200 dark:border-white/10">
        <a href="{{ route('guru.schedule.meeting.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-yumik-primary text-yumik-primary font-bold whitespace-nowrap">
            <i class="ph-bold ph-calendar-check mr-2"></i> Alur Pertemuan
        </a>
        <a href="{{ route('guru.schedule.assignment.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-exam mr-2"></i> Tugas & Kuis
        </a>
        <a href="{{ route('guru.schedule.gradebook.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-notebook mr-2"></i> Buku Nilai
        </a>
        <a href="{{ route('guru.schedule.material.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-books mr-2"></i> Bank Materi
        </a>
    </div>

    <!-- DAFTAR PERTEMUAN -->
    <x-card class="p-6 lg:p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Alur Pembelajaran</h2>
            
            <a href="{{ route('guru.schedule.meeting.create', $schedule->id) }}">
                <x-button variant="primary" class="!px-4 !py-2 text-sm shadow-md">
                    <i class="ph-bold ph-plus"></i> Tambah Pertemuan
                </x-button>
            </a>
        </div>

        @if($meetings->isEmpty())
            <x-empty-state 
                icon="presentation-chart" 
                title="Belum ada pertemuan" 
                description="Buat kerangka pertemuan pembelajaran Anda selama satu semester di sini." 
            />
        @else
            <div class="space-y-6">
                @foreach($meetings as $meeting)
                    @php
                        // Logika visual: Jika tanggal pertemuan lebih kecil dari hari ini = Selesai, Jika sama = Aktif
                        $isPast = \Carbon\Carbon::parse($meeting->date)->isBefore(now()->startOfDay());
                        $isToday = \Carbon\Carbon::parse($meeting->date)->isSameDay(now());
                    @endphp

                    <div class="flex flex-col lg:flex-row gap-6 p-5 rounded-3xl border {{ $isToday ? 'border-2 border-yumik-primary/40 bg-white dark:bg-yumik-surface-dark shadow-lg relative overflow-hidden' : 'border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/20' }} transition-all hover:shadow-md">
                        
                        @if($isToday)
                            <div class="absolute top-0 right-0 px-4 py-1 bg-yumik-primary text-white text-[10px] font-bold tracking-widest uppercase rounded-bl-xl shadow-sm">Aktif Hari Ini</div>
                        @endif

                        <div class="w-24 text-center shrink-0">
                            <div class="text-sm {{ $isToday ? 'text-yumik-primary' : 'text-slate-500' }} font-bold uppercase tracking-widest">Pert</div>
                            <div class="text-4xl font-black {{ $isToday ? 'text-slate-800 dark:text-white' : 'text-slate-300 dark:text-slate-600' }}">
                                {{ str_pad($meeting->meeting_number, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">{{ $meeting->topic }}</h3>
                            <p class="text-sm text-slate-500 mb-4 font-medium flex items-center gap-2">
                                <i class="ph-fill ph-calendar-blank text-slate-400"></i> {{ \Carbon\Carbon::parse($meeting->date)->translatedFormat('l, d F Y') }}
                            </p>
                            
                            <!-- Badges Aktivitas (Statis sementara, bisa di-query relasinya nanti) -->
                            <div class="flex flex-wrap gap-2 mb-5">
                                @if($isPast || $isToday)
                                    <x-badge color="primary">
                                        <i class="ph-bold ph-check"></i> Absensi Terbuka
                                    </x-badge>
                                @endif
                                @if($meeting->description)
                                    <x-badge color="neutral">
                                        <i class="ph-fill ph-note"></i> Ada Catatan Jurnal
                                    </x-badge>
                                @endif
                            </div>

                            <a href="{{ route('guru.schedule.meeting.show', [$schedule->id, $meeting->id]) }}">
                                <x-button variant="{{ $isToday ? 'primary' : 'secondary' }}" class="!px-5 !py-2.5 !rounded-xl text-sm shadow-sm hover:-translate-y-0.5">
                                    Kelola Aktivitas <i class="ph-bold ph-arrow-right"></i>
                                </x-button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Menampilkan paginasi dari MeetingController -->
            @if($meetings->hasPages())
                <div class="mt-8">
                    <x-pagination :paginator="$meetings" />
                </div>
            @endif
        @endif
    </x-card>
</div>
@endsection
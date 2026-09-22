@extends('layouts.app')

@section('title', 'Dashboard Guru - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">
    
    <!-- HEADER PENGUMUMAN -->
    <x-card class="!bg-gradient-to-r from-yumik-primary/10 to-transparent border-yumik-primary/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <x-avatar src="{{ auth()->user()->guru->photo ? asset(auth()->user()->guru->photo) : '' }}" size="lg" status="online" />
                
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                        Siap mengajar hari ini, {{ auth()->user()->name }}? 🎓
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                        Hari ini {{ now()->translatedFormat('l, d F Y') }}. Anda memiliki <span class="text-yumik-primary font-bold">{{ $jadwalHariIni->count() }} jadwal</span> mengajar.
                    </p>
                </div>
            </div>
            
            <x-button variant="primary" class="shrink-0 group">
                <i class="ph-bold ph-megaphone text-lg group-hover:rotate-12 transition-transform"></i> Buat Pengumuman
            </x-button>
        </div>
    </x-card>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI (Jadwal & Menunggu Tindakan) -->
        <div class="xl:col-span-2 space-y-8">
            
            <!-- A. TIMELINE JADWAL MENGAJAR HARI INI -->
            <x-card class="p-6 lg:p-8 overflow-visible">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[1rem] bg-yumik-accent/10 flex items-center justify-center text-yumik-accent shadow-inner">
                            <i class="ph-fill ph-calendar-check text-2xl"></i>
                        </div>
                        Jadwal Mengajar Anda
                    </h2>
                </div>

                @if($jadwalHariIni->isEmpty())
                    <x-empty-state icon="calendar-blank" title="Tidak ada jadwal hari ini" description="Anda tidak memiliki jadwal mengajar pada hari {{ $hariIni }}." />
                @else
                    <div class="relative border-l-[3px] border-slate-100 dark:border-white/10 ml-5 space-y-10 py-2">
                        @foreach($jadwalHariIni as $index => $jadwal)
                            <!-- Asumsi jam pertama adalah yang aktif (SEKARANG) untuk visualisasi sementara -->
                            @php $isSekarang = $index === 0; @endphp 

                            <div class="relative pl-8 {{ !$isSekarang ? 'opacity-70 hover:opacity-100 transition-opacity' : '' }}">
                                <span class="absolute {{ $isSekarang ? '-left-[13.5px] top-1.5 w-6 h-6 animate-[pulse_2s_ease-in-out_infinite] shadow-lg shadow-yumik-primary/40 bg-yumik-primary' : '-left-[11px] top-2 w-5 h-5 bg-slate-300 dark:bg-slate-600' }} rounded-full border-4 border-white dark:border-[#1A1F29]"></span>
                                
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-5 {{ $isSekarang ? 'p-5 rounded-3xl bg-slate-50 dark:bg-black/20 border-2 border-yumik-primary/30 hover:shadow-xl' : 'pt-1' }} transition-all">
                                    <div class="w-full">
                                        <x-badge color="{{ $isSekarang ? 'primary' : 'neutral' }}" class="mb-2">
                                            @if($isSekarang) <i class="ph-bold ph-clock"></i> SEKARANG • @endif
                                            Jam ke {{ $jadwal->jam_ke_mulai }} - {{ $jadwal->jam_ke_selesai }}
                                        </x-badge>
                                        
                                        <h3 class="text-{{ $isSekarang ? 'xl font-extrabold' : 'lg font-bold' }} text-slate-800 dark:text-white mt-1">
                                            {{ $jadwal->subject->name }}
                                        </h3>
                                        
                                        <p class="text-sm text-slate-500 font-medium mt-1 flex items-center gap-2">
                                            <i class="ph-fill ph-users-three text-slate-400"></i> Kelas {{ $jadwal->classroom->name }} 
                                            @if($jadwal->ruangan)
                                                <span class="text-slate-300 dark:text-slate-600">•</span> {{ $jadwal->ruangan }}
                                            @endif
                                        </p>
                                    </div>
                                    
                                    @if($isSekarang)
                                    <a href="{{ route('guru.schedule.meeting.index', $jadwal->id) }}" class="w-full sm:w-auto shrink-0">
                                        <x-button variant="primary" class="w-full !rounded-[1.25rem] shadow-lg shadow-yumik-primary/30">
                                            Buka Kelas <i class="ph-bold ph-arrow-right"></i>
                                        </x-button>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <!-- B. MENUNGGU TINDAKAN -->
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3 mb-6 px-2">
                    <i class="ph-fill ph-bell-ringing text-amber-500 text-2xl"></i> Menunggu Tindakan
                </h2>
                
                @if($actionItems->isEmpty())
                    <x-card class="p-6 text-center text-slate-500">Tidak ada tugas atau kuis yang perlu di-review saat ini.</x-card>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($actionItems as $item)
                            <x-card class="!p-6 border-l-4 !border-l-amber-500 hover:-translate-y-1 group cursor-pointer transition-transform shadow-md">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-12 h-12 rounded-[1rem] bg-amber-100 dark:bg-amber-500/20 text-amber-600 flex items-center justify-center">
                                        <i class="ph-fill ph-exam text-2xl"></i>
                                    </div>
                                    <x-badge color="neutral" class="!bg-amber-50 dark:!bg-amber-900/30 !text-amber-600">{{ $item->status }}</x-badge>
                                </div>
                                <h4 class="font-bold text-lg text-slate-800 dark:text-white group-hover:text-amber-500 transition-colors line-clamp-1">{{ $item->title }}</h4>
                                <p class="text-sm text-slate-500 font-medium mt-1 mb-5">{{ $item->schedule->classroom->name }}</p>
                                
                                <a href="{{ route('guru.schedule.assessment.show', [$item->schedule_id, $item->id]) }}" class="text-sm font-bold text-amber-500 flex items-center gap-2 group-hover:gap-3 transition-all">
                                    Lihat Penilaian <i class="ph-bold ph-arrow-right"></i>
                                </a>
                            </x-card>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- KOLOM KANAN (Daftar Pengajaran & Wali Kelas) -->
        <div class="space-y-8">
            
            <!-- C. RUANG KELAS (COURSES) -->
            <div>
                <div class="flex items-center justify-between px-2 mb-6">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">Ruang Kelas Anda</h2>
                    <x-badge color="accent">{{ $activeSemester->type }}</x-badge>
                </div>
                
                <div class="space-y-5">
                    @forelse($courses as $course)
                        <!-- Link mengarah ke index pertemuan course tersebut -->
                        <a href="{{ route('guru.schedule.meeting.index', $course->id) }}" class="block">
                            <x-course-card 
                                title="{{ $course->classroom->name }}" 
                                subject="{{ $course->subject->name }}" 
                                progress="0" 
                                icon="calculator" 
                            />
                        </a>
                    @empty
                        <div class="text-sm text-slate-500 px-2">Belum ada kelas yang ditugaskan.</div>
                    @endforelse
                </div>
            </div>
            
            <!-- D. WALI KELAS DINAMIS (Hanya muncul jika guru adalah wali kelas) -->
            @if($waliKelas)
            <x-card class="!bg-gradient-to-br from-slate-800 to-[#0A0D14] dark:from-[#111622] dark:to-black border border-slate-700/50 !p-7 shadow-2xl relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 text-white mb-6">
                        <div class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                            <i class="ph-fill ph-star text-2xl text-amber-400 drop-shadow-md"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg tracking-tight">Wali Kelas {{ $waliKelas->name }}</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-widest">Akses Manajemen</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-slate-300 font-medium leading-relaxed mb-6">
                        Pantau rekapitulasi kehadiran, perkembangan nilai rata-rata, dan catatan perilaku siswa wali Anda.
                    </p>
                    
                    <x-button variant="secondary" class="w-full !bg-white/10 !border-white/20 !text-white hover:!bg-white/20 hover:!border-white/30 backdrop-blur-sm group/btn">
                        Buka Panel Wali <i class="ph-bold ph-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                    </x-button>
                </div>
            </x-card>
            @endif

        </div>
    </div>
</div>
@endsection
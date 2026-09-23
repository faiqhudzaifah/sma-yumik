<aside id="app-sidebar" class="fixed lg:sticky top-0 lg:top-6 inset-y-0 left-0 z-[100] flex flex-col w-64 h-screen lg:h-[calc(100vh-3rem)] bg-white/95 dark:bg-[#0A0D14]/95 lg:bg-white/30 lg:dark:bg-[#1A1F29]/30 backdrop-blur-2xl lg:backdrop-blur-xl border-r lg:border border-slate-200/50 dark:border-white/10 rounded-r-[2rem] lg:rounded-[2rem] p-6 shadow-2xl lg:shadow-sm transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <!-- BRANDING LOGO -->
    <div class="mb-8 pl-2 flex items-center gap-2 mt-2 lg:mt-0">
        <x-logo size="w-7 h-7" />
        <span class="text-base font-extrabold text-slate-800 dark:text-white tracking-tight">Yumik LMS</span>
    </div>

    <!-- MAIN NAVIGATION -->
    <nav class="flex flex-col gap-1.5 flex-1 overflow-y-auto custom-scrollbar pr-2 pb-4">
        
        @auth
            {{-- ========================================== --}}
            {{-- MENU ADMIN (ROLE ID: 1)                     --}}
            {{-- ========================================== --}}
            @if(auth()->user()->role_id == 1)
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mb-2">Menu Utama</span>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
                    <i class="{{ request()->routeIs('admin.dashboard') ? 'ph-fill' : 'ph-bold' }} ph-squares-four text-xl group-hover:scale-110 transition-transform"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.pengguna.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium group' }} transition-all">
                    <i class="{{ request()->routeIs('admin.pengguna.*') ? 'ph-fill' : 'ph-bold' }} ph-users text-xl group-hover:scale-110 transition-transform"></i>
                    Data Pengguna
                </a>
                
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 pl-2 mt-4 mb-2">Master Akademik</span>
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 hover:bg-slate-50 font-medium group transition-all">
                    <i class="ph-bold ph-calendar-check text-xl group-hover:scale-110"></i> Tahun Ajaran
                </a>
                <a href="{{ route('admin.rombel.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 hover:bg-slate-50 font-medium group transition-all">
                    <i class="ph-bold ph-door text-xl group-hover:scale-110"></i> Data Rombel
                </a>
                <a href="{{ route('admin.mapel.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 hover:bg-slate-50 font-medium group transition-all">
                    <i class="ph-bold ph-book-bookmark text-xl group-hover:scale-110"></i> Mata Pelajaran
                </a>
                <a href="{{ route('admin.jadwal.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 hover:bg-slate-50 font-medium group transition-all">
                    <i class="ph-bold ph-calendar-plus text-xl group-hover:scale-110"></i> Matriks Jadwal
                </a>

            {{-- ========================================== --}}
            {{-- MENU GURU (ROLE ID: 2)                      --}}
            {{-- ========================================== --}}
            @elseif(auth()->user()->role_id == 2)
                @php
                    /** @var \App\Models\User $user */
                    $user = auth()->user();
                    $guru = $user->guru;
                    $activeSemester = \App\Models\Semester::where('is_active', true)->first();
                    
                    $myCourses = collect();
                    $waliClassroom = null;

                    if ($guru && $activeSemester) {
                        $myCourses = \App\Models\Schedule::with(['classroom', 'subject'])
                            ->where('guru_id', $guru->id)
                            ->where('semester_id', $activeSemester->id)
                            ->get();

                        $waliClassroom = \App\Models\Classroom::where('wali_kelas_id', $guru->id)
                            ->where('semester_id', $activeSemester->id)
                            ->first();
                    }
                @endphp

                <!-- 1. DASHBOARD UTAMA -->
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mb-2">Aktivitas Utama</span>
                
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('guru.dashboard') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
                    <i class="{{ request()->routeIs('guru.dashboard') ? 'ph-fill' : 'ph-bold' }} ph-squares-four text-xl group-hover:scale-110 transition-transform"></i>
                    Dashboard
                </a>

                <!-- 2. DAFTAR RUANG KELAS -->
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mt-4 mb-2">
                    Kelas Saya {{ $activeSemester ? '(' . $activeSemester->type . ')' : '' }}
                </span>
                
                @forelse($myCourses as $course)
                    @php
                        $currentRouteSchedule = request()->route('schedule');
                        $currentScheduleId = is_object($currentRouteSchedule) ? $currentRouteSchedule->id : $currentRouteSchedule;
                        $isActiveCourse = $currentScheduleId == $course->id;
                    @endphp
                    
                    <a href="{{ route('guru.schedule.meeting.index', $course->id) }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ $isActiveCourse ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 font-medium group' }} transition-all">
                        <div class="w-8 h-8 rounded-[1rem] {{ $isActiveCourse ? 'bg-yumik-primary/10 text-yumik-primary' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center shrink-0">
                            <i class="{{ $isActiveCourse ? 'ph-bold' : 'ph-fill' }} ph-chalkboard-teacher text-lg"></i>
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-sm truncate leading-tight">{{ $course->classroom->name }}</span>
                            <span class="text-[10px] font-bold truncate {{ $isActiveCourse ? 'text-yumik-primary/60' : 'text-slate-400' }}">{{ $course->subject->code }}</span>
                        </div>
                        
                        @if($isActiveCourse)
                            <i class="ph-bold ph-caret-down ml-auto text-xs opacity-70"></i>
                        @endif
                    </a>

                    @if($isActiveCourse)
                        <div class="flex flex-col gap-1 ml-9 mt-1 mb-3 border-l-[2px] border-slate-100 dark:border-white/10 pl-2">
                            <a href="{{ route('guru.schedule.meeting.index', $course->id) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('guru.schedule.meeting.*') ? 'text-yumik-primary bg-yumik-primary/10' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                                <i class="ph-bold ph-calendar-check text-sm shrink-0"></i> Pertemuan & Presensi
                            </a>
                            <a href="{{ route('guru.schedule.assignment.index', $course->id) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('guru.schedule.assignment.*') ? 'text-yumik-primary bg-yumik-primary/10' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                                <i class="ph-bold ph-files text-sm shrink-0"></i> Tugas Manual
                            </a>
                            <a href="{{ route('guru.schedule.assessment.index', $course->id) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('guru.schedule.assessment.*') ? 'text-yumik-primary bg-yumik-primary/10' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                                <i class="ph-bold ph-exam text-sm shrink-0"></i> Ujian CBT
                            </a>
                            <a href="{{ route('guru.schedule.material.index', $course->id) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('guru.schedule.material.*') ? 'text-yumik-primary bg-yumik-primary/10' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                                <i class="ph-bold ph-books text-sm shrink-0"></i> Bank Materi
                            </a>
                            <a href="{{ route('guru.schedule.gradebook.index', $course->id) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('guru.schedule.gradebook.*') ? 'text-yumik-primary bg-yumik-primary/10' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50 dark:hover:bg-white/5' }}">
                                <i class="ph-bold ph-notebook text-sm shrink-0"></i> Buku Nilai Akhir
                            </a>
                        </div>
                    @endif
                @empty
                    <div class="px-4 py-2 text-xs text-slate-400">Belum ada tugas mengajar.</div>
                @endforelse
                
                <!-- 3. TUGAS TAMBAHAN WALI KELAS -->
                @if($waliClassroom)
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mt-4 mb-2">Tugas Tambahan</span>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10 font-bold group transition-all">
                        <div class="w-8 h-8 rounded-[1rem] bg-amber-100 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0">
                            <i class="ph-fill ph-star text-lg group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm">Wali Kelas</span>
                            <span class="text-[10px] text-amber-600/70">{{ $waliClassroom->name }}</span>
                        </div>
                    </a>
                @endif
                
            {{-- ========================================== --}}
            {{-- MENU SISWA (ROLE ID: 3)                     --}}
            {{-- ========================================== --}}
            @elseif(auth()->user()->role_id == 3)
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mb-2">Aktivitas Belajar</span>
                
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('siswa.dashboard') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
                    <i class="{{ request()->routeIs('siswa.dashboard') ? 'ph-fill' : 'ph-bold' }} ph-squares-four text-xl group-hover:scale-110 transition-transform"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('siswa.course.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('siswa.course.*') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
                    <i class="{{ request()->routeIs('siswa.course.*') ? 'ph-fill' : 'ph-bold' }} ph-books text-xl group-hover:scale-110 transition-transform"></i>
                    Mata Pelajaran
                </a>

                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 pl-2 mt-4 mb-2">Akademik</span>

                @if(Route::has('siswa.akademik.rapor'))
    <a href="{{ route('siswa.akademik.rapor') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('siswa.akademik.rapor') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
        <i class="{{ request()->routeIs('siswa.akademik.rapor') ? 'ph-fill' : 'ph-bold' }} ph-notebook text-xl group-hover:scale-110 transition-transform"></i>
        Rapor Akademik
    </a>
@endif

@if(Route::has('siswa.akademik.kehadiran'))
    <a href="{{ route('siswa.akademik.kehadiran') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('siswa.akademik.kehadiran') ? 'bg-slate-100 dark:bg-white/10 text-yumik-primary shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium group' }} transition-all">
        <i class="{{ request()->routeIs('siswa.akademik.kehadiran') ? 'ph-fill' : 'ph-bold' }} ph-calendar-check text-xl group-hover:scale-110 transition-transform"></i>
        Rekap Presensi
    </a>
@endif
            @endif
        @endauth
    </nav>

    <!-- LOGOUT BUTTON -->
    <div class="mt-auto pt-4 border-t border-slate-200/50 dark:border-white/10">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-red-50/50 dark:bg-red-500/10 text-red-500 font-semibold hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                <i class="ph-bold ph-sign-out"></i>
                Keluar Sesi
            </button>
        </form>
    </div>
</aside>
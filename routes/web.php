<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\LessonHourController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\PromotionController;

use App\Http\Controllers\Guru\MeetingController;
use App\Http\Controllers\Guru\AssignmentController;
use App\Http\Controllers\Guru\AssessmentController;
use App\Http\Controllers\Guru\AssessmentGradingController;
use App\Http\Controllers\Guru\QuestionController;
use App\Http\Controllers\Guru\GradebookController;
use App\Http\Controllers\Guru\MaterialController;
use App\Http\Controllers\Guru\AnnouncementController;
use App\Http\Controllers\Guru\WaliKelasController;

// Tambahkan alias (as) untuk Controller Siswa
use App\Http\Controllers\Siswa\CourseController as SiswaCourseController;
use App\Http\Controllers\Siswa\AssignmentController as SiswaAssignmentController;
use App\Http\Controllers\Siswa\CbtController as SiswaCbtController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\MaterialController as SiswaMaterialController;
use App\Http\Controllers\Siswa\GradebookController as SiswaGradebookController;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Assessment;

// ==========================================
// ROUTE AUTENTIKASI
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| BYPASS LOGIN (HANYA UNTUK DEVELOPMENT/TESTING)
|--------------------------------------------------------------------------
*/
Route::get('/dev/login/{role_id}', function ($role_id) {
    if (app()->environment('production')) {
        abort(403, 'Akses ditolak. Fitur ini dinonaktifkan di production.');
    }

    $user = User::where('role_id', $role_id)->first();

    if (!$user) {
        return "Gagal Bypass: Tidak ditemukan User dengan role_id {$role_id} di database.";
    }

    Auth::login($user);

    return match ((int) $role_id) {
        1 => redirect()->route('admin.dashboard'),
        2 => redirect()->route('guru.dashboard'),
        3 => redirect()->route('siswa.dashboard'),
        4 => redirect()->route('operator.dashboard'),
        default => redirect('/'),
    };
});


/*
|--------------------------------------------------------------------------
| APLIKASI UTAMA (MEMBUTUHKAN AUTENTIKASI)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ==========================================
    // 1. ROUTE ADMIN (Role ID: 1)
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', function () {
            $totalSiswa = Siswa::where('status', 'Aktif')->count();
            $totalGuru = Guru::count();
            $activeClasses = Classroom::with(['waliKelas.user'])->withCount('siswas')->latest()->take(4)->get();

            return view('admin.dashboard', compact('totalSiswa', 'totalGuru', 'activeClasses'));
        })->name('dashboard');

        Route::resource('pengguna', UserController::class);

        Route::get('tahun-ajaran/{tahun_ajaran}/export', [AcademicYearController::class, 'export'])->name('tahun-ajaran.export');
        Route::resource('tahun-ajaran', AcademicYearController::class)->parameters([
            'tahun-ajaran' => 'tahun_ajaran'
        ]);

        Route::resource('rombel', ClassroomController::class)->parameters([
            'rombel' => 'rombel'
        ]);
        Route::post('rombel/{rombel}/assign', [ClassroomController::class, 'assignStudents'])->name('rombel.assign');
        Route::post('rombel/{rombel}/remove/{siswa}', [ClassroomController::class, 'removeStudent'])->name('rombel.remove');

        Route::resource('mapel', SubjectController::class)->parameters([
            'mapel' => 'mapel'
        ]);

        Route::resource('jam-pelajaran', LessonHourController::class)->except(['create', 'show', 'edit'])->parameters([
            'jam-pelajaran' => 'jam_pelajaran'
        ]);

        Route::resource('jadwal', ScheduleController::class)->parameters([
            'jadwal' => 'jadwal'
        ]);

        Route::resource('kalender', AcademicCalendarController::class)->parameters([
            'kalender' => 'kalender_akademik'
        ]);

        Route::get('promotion', [PromotionController::class, 'index'])->name('promotion.index');
        Route::get('promotion/{rombel}/review', [PromotionController::class, 'review'])->name('promotion.review');
        Route::post('promotion/{rombel}/process', [PromotionController::class, 'process'])->name('promotion.process');
    });

    // ==========================================
    // 2. ROUTE GURU (Role ID: 2)
    // ==========================================
    Route::prefix('guru')->name('guru.')->group(function () {

        // DASHBOARD GURU DINAMIS
        Route::get('/dashboard', function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $guru = $user->guru;

            if (!$guru) {
                abort(403, 'Akses ditolak. Data detail Guru tidak ditemukan.');
            }

            $activeSemester = Semester::where('is_active', true)->first();

            $hariMap = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $hariIni = $hariMap[now()->format('l')];

            $jadwalHariIni = collect();
            $courses = collect();
            $waliKelas = null;
            $actionItems = collect();
            $mySchedules = collect();

            if ($activeSemester) {
                $jadwalHariIni = Schedule::with(['classroom', 'subject'])
                    ->where('guru_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->where('day_of_week', $hariIni)
                    ->orderBy('jam_ke_mulai', 'asc')
                    ->get();

                $courses = Schedule::with(['classroom', 'subject'])
                    ->where('guru_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->get();

                $mySchedules = $courses;

                $waliKelas = Classroom::where('wali_kelas_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->first();

                $actionItems = Assessment::with('schedule.classroom')
                    ->whereHas('schedule', function ($q) use ($guru) {
                        $q->where('guru_id', $guru->id);
                    })
                    ->whereIn('status', ['PUBLISHED', 'CLOSED'])
                    ->latest()
                    ->take(2)
                    ->get();
            }

            return view('guru.dashboard', compact(
                'guru',
                'activeSemester',
                'hariIni',
                'jadwalHariIni',
                'courses',
                'waliKelas',
                'actionItems',
                'mySchedules'
            ));
        })->name('dashboard');

        // RUTING UNTUK WORKSPACE PEMBELAJARAN GURU
        Route::prefix('schedule/{schedule}')->name('schedule.')->group(function () {

            // Pertemuan & Absensi
            Route::resource('meeting', MeetingController::class);
            Route::post('meeting/{meeting}/attendances', [MeetingController::class, 'updateAttendances'])->name('meeting.attendances.update');

            // Tugas (Assignments)
            Route::resource('assignment', AssignmentController::class);
            Route::post('assignment/{assignment}/grades', [AssignmentController::class, 'updateGrades'])->name('assignment.grades.update');

            // Materi (Materials)
            Route::resource('material', MaterialController::class);

            // Ujian & Kuis (CBT)
            Route::resource('assessment', AssessmentController::class);
            Route::post('assessment/{assessment}/questions', [QuestionController::class, 'store'])->name('assessment.question.store');
            Route::delete('assessment/{assessment}/questions/{question}', [QuestionController::class, 'destroy'])->name('assessment.question.destroy');

            // Koreksi Manual Ujian CBT
            Route::get('assessment/{assessment}/grading', [AssessmentGradingController::class, 'index'])->name('assessment.grading.index');
            Route::get('assessment/{assessment}/grading/{submission}', [AssessmentGradingController::class, 'show'])->name('assessment.grading.show');
            Route::post('assessment/{assessment}/grading/{submission}', [AssessmentGradingController::class, 'updateScore'])->name('assessment.grading.update');

            // Gradebook (Buku Nilai)
            Route::get('gradebook', [GradebookController::class, 'index'])->name('gradebook.index');
            Route::get('gradebook/export', [GradebookController::class, 'export'])->name('gradebook.export');
        });

        // ==========================================
        // PANEL WALI KELAS (SEJAJAR DENGAN SCHEDULE)
        // ==========================================
        Route::prefix('wali-kelas')->name('wali-kelas.')->group(function () {
            Route::get('/', [WaliKelasController::class, 'dashboard'])->name('dashboard');
            Route::get('/jadwal', [WaliKelasController::class, 'jadwal'])->name('jadwal');
            Route::get('/siswa', [WaliKelasController::class, 'siswa'])->name('siswa');
            Route::get('/siswa/{siswa}', [WaliKelasController::class, 'siswaDetail'])->name('siswa.detail');
            Route::get('/kehadiran', [WaliKelasController::class, 'kehadiran'])->name('kehadiran');
            Route::get('/kehadiran/matrix', [WaliKelasController::class, 'kehadiranMatrix'])->name('kehadiran.matrix');
        });
    });

    // ==========================================
    // 3. ROUTE SISWA (Role ID: 3)
    // ==========================================
    Route::prefix('siswa')->name('siswa.')->group(function () {

        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('course')->name('course.')->group(function () {
            Route::get('/', [SiswaCourseController::class, 'index'])->name('index');
            Route::get('/{course}', [SiswaCourseController::class, 'show'])->name('show');
        });

        Route::get('/material/{material}', [SiswaMaterialController::class, 'show'])->name('material.show');

        Route::prefix('assignment')->name('assignment.')->group(function () {
            Route::get('/', [SiswaAssignmentController::class, 'index'])->name('index');
            Route::get('/{assignment}', [SiswaAssignmentController::class, 'show'])->name('show');
            Route::post('/{assignment}/submit', [SiswaAssignmentController::class, 'submit'])->name('submit');
        });

        Route::prefix('cbt')->name('cbt.')->group(function () {
            Route::get('/', [SiswaCbtController::class, 'index'])->name('index');
            Route::get('/{cbt}', [SiswaCbtController::class, 'show'])->name('show');
            Route::post('/{cbt}/start', [SiswaCbtController::class, 'start'])->name('start');
            Route::get('/{cbt}/exam', [SiswaCbtController::class, 'exam'])->name('exam');
            Route::get('/{cbt}/attempt/{attempt}', [SiswaCbtController::class, 'attempt'])->name('attempt');
            Route::post('/{cbt}/attempt/{attempt}/store', [SiswaCbtController::class, 'storeAnswer'])->name('store_answer');
            Route::post('/{cbt}/attempt/{attempt}/submit', [SiswaCbtController::class, 'submit'])->name('submit');
        });

        Route::prefix('akademik')->name('akademik.')->group(function () {
            Route::get('/rapor', [SiswaGradebookController::class, 'index'])->name('rapor');
            Route::get('/kehadiran', [SiswaGradebookController::class, 'attendance'])->name('kehadiran');
        });
    });

    // ==========================================
    // 4. ROUTE OPERATOR (Role ID: 4)
    // ==========================================
    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', function () {
            return "Dashboard Operator";
        })->name('dashboard');
    });

    // ==========================================
    // 5. ROUTE PENGUMUMAN (GURU & SISWA)
    // ==========================================
    Route::post('/guru/announcement', [AnnouncementController::class, 'store'])
        ->name('guru.announcement.store');

    Route::delete('/guru/announcement/{announcement}', [AnnouncementController::class, 'destroy'])
        ->name('guru.announcement.destroy');

    Route::post('/announcement/{announcement}/read', [AnnouncementController::class, 'markAsRead'])
        ->name('announcement.read');

    // ==========================================
    // 6. ROUTE PROFIL & PASSWORD (SEMUA ROLE)
    // ==========================================
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])
        ->name('profile.index');

    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/password', [\App\Http\Controllers\PasswordController::class, 'index'])
        ->name('password.index');

    Route::put('/password', [\App\Http\Controllers\PasswordController::class, 'update'])
        ->name('password.update');

    // ==========================================
    // 7. ROUTE GLOBAL
    // ==========================================
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

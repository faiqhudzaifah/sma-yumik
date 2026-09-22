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

// Import Controller Guru
use App\Http\Controllers\Guru\MeetingController;
use App\Http\Controllers\Guru\AssignmentController;
use App\Http\Controllers\Guru\AssessmentController;
use App\Http\Controllers\Guru\AssessmentGradingController;
use App\Http\Controllers\Guru\QuestionController;
use App\Http\Controllers\Guru\GradebookController;
use App\Http\Controllers\Guru\MaterialController;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Assessment;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome'); // Halaman landing page / login asli nantinya
})->name('login');


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

            // Peta Translasi Hari untuk pencocokan DB
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

            if ($activeSemester) {
                // 1. Ambil Jadwal Hari Ini
                $jadwalHariIni = Schedule::with(['classroom', 'subject'])
                    ->where('guru_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->where('day_of_week', $hariIni)
                    ->orderBy('jam_ke_mulai', 'asc')
                    ->get();

                // 2. Ambil Semua Ruang Kelas (Courses)
                $courses = Schedule::with(['classroom', 'subject'])
                    ->where('guru_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->get();

                // 3. Cek Status Wali Kelas
                $waliKelas = Classroom::where('wali_kelas_id', $guru->id)
                    ->where('semester_id', $activeSemester->id)
                    ->first();

                // 4. Ambil Tugas/Kuis yang butuh aksi (Published/Closed)
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
                'actionItems'
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
    });

    // ==========================================
    // 3. ROUTE SISWA & OPERATOR (Placeholder)
    // ==========================================
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', function () {
            return "Dashboard Siswa";
        })->name('dashboard');
    });

    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', function () {
            return "Dashboard Operator";
        })->name('dashboard');
    });

    // ==========================================
    // ROUTE GLOBAL
    // ==========================================
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

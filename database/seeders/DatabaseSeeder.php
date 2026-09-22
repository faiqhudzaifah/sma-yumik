<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Schedule;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA PENGGUNA & ROLE
        // ==========================================
        $roles = [
            ['id' => 1, 'name' => 'admin', 'description' => 'Administrator Utama'],
            ['id' => 2, 'name' => 'guru', 'description' => 'Tenaga Pendidik / Guru'],
            ['id' => 3, 'name' => 'siswa', 'description' => 'Peserta Didik'],
            ['id' => 4, 'name' => 'operator', 'description' => 'Operator Akademik'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        User::updateOrCreate(
            ['username' => 'admin_utama'],
            ['name' => 'Super Admin', 'email' => 'admin@yumik.id', 'password' => Hash::make('password123'), 'role_id' => 1]
        );

        $guruUser = User::updateOrCreate(
            ['username' => '198005122005011003'],
            ['name' => 'Budi Santoso, S.Pd.', 'email' => 'budi@guru.yumik.id', 'password' => Hash::make('password123'), 'role_id' => 2]
        );
        $guru = Guru::updateOrCreate(
            ['user_id' => $guruUser->id],
            ['nip' => '198005122005011003', 'gender' => 'L']
        );

        $siswaUser = User::updateOrCreate(
            ['username' => '0051234567'],
            ['name' => 'Andi Darmawan', 'email' => 'andi@siswa.yumik.id', 'password' => Hash::make('password123'), 'role_id' => 3]
        );
        $siswa = Siswa::updateOrCreate(
            ['user_id' => $siswaUser->id],
            ['nisn' => '0051234567', 'gender' => 'L', 'enrollment_year' => date('Y'), 'status' => 'Aktif']
        );

        User::updateOrCreate(
            ['username' => 'operator_akademik'],
            ['name' => 'Siti Aisyah', 'email' => 'operator@yumik.id', 'password' => Hash::make('password123'), 'role_id' => 4]
        );

        // ==========================================
        // 2. DATA AKADEMIK DASAR
        // ==========================================
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026/2027']
        );

        $semester = Semester::updateOrCreate(
            ['academic_year_id' => $academicYear->id, 'type' => 'Ganjil'],
            ['start_date' => '2026-07-15', 'end_date' => '2026-12-15', 'is_active' => true]
        );

        // Mapel sekarang berelasi dengan semester
        $subject1 = Subject::updateOrCreate(
            ['semester_id' => $semester->id, 'code' => 'MTK-P'],
            ['name' => 'Matematika Peminatan', 'guru_id' => $guru->id]
        );

        // ==========================================
        // 3. OPERASIONAL KELAS & JADWAL
        // ==========================================
        $classroom = Classroom::updateOrCreate(
            ['room_code' => 'X-1', 'semester_id' => $semester->id],
            [
                'name' => 'Kelas X-1 Sains',
                'grade_level' => 10,
                'major' => 'Sains',
                'capacity' => 36,
                'wali_kelas_id' => $guru->id,
            ]
        );

        // Memasukkan siswa ke kelas
        $classroom->siswas()->syncWithoutDetaching([$siswa->id]);

        // Jadwal (Menggunakan jam_ke_mulai dan jam_ke_selesai sesuai struktur baru)
        Schedule::updateOrCreate(
            [
                'semester_id' => $semester->id,
                'classroom_id' => $classroom->id,
                'subject_id' => $subject1->id,
                'guru_id' => $guru->id,
                'day_of_week' => 'Senin',
            ],
            [
                'jam_ke_mulai' => 1,
                'jam_ke_selesai' => 2,
                'ruangan' => null
            ]
        );
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();

            // 1. Relasi Induk (Wajib ada untuk Opsi 2)
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();

            // 2. Identitas Kelas
            $table->string('name')->comment('Nama tampil kelas (cth: X MIPA 1)');
            $table->string('room_code')->nullable()->comment('Kode fisik ruangan (cth: R-01)'); // unique() dihapus
            $table->integer('grade_level')->comment('Tingkatan kelas: 10, 11, 12');
            $table->string('major')->nullable()->comment('Jurusan: MIPA, IPS, Bahasa, atau Umum');

            // 3. Operasional
            $table->integer('capacity')->default(40)->comment('Kapasitas maksimal siswa');

            // 4. Relasi Wali Kelas
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};

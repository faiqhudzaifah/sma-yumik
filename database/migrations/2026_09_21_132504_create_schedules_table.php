<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Relasi Sentral
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();

            // Guru dimasukkan ke jadwal karena 1 mapel bisa diajar guru berbeda di kelas yang beda
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();

            // Logika Waktu (Anti-Bongkar Pasang)
            $table->enum('day_of_week', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->integer('jam_ke_mulai')->comment('Mulai pada Jam Pelajaran ke- (cth: 1)');
            $table->integer('jam_ke_selesai')->comment('Berakhir pada Jam Pelajaran ke- (cth: 3)');

            // Logika Ruangan (Menyelesaikan masalah Lab/Lapangan)
            $table->string('ruangan', 50)->nullable()->comment('Kosongkan jika di kelas utama. Isi jika di Lab/Lapangan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

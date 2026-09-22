<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->timestamps();

            // Mencegah siswa yang sama dimasukkan 2 kali ke kelas yang sama
            $table->unique(['classroom_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_siswa');
    }
};

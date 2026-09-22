<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_hours', function (Blueprint $table) {
            $table->id();

            // Pengelompokan Hari (Karena Jumat biasanya jadwalnya beda)
            $table->enum('day_type', ['Umum', 'Jumat'])->default('Umum')->comment('Umum untuk Senin-Kamis');

            // Urutan Jam
            $table->integer('jam_ke')->nullable()->comment('Isi dengan angka 1, 2, dst. Kosongkan jika ini jam istirahat');

            // Rentang Waktu
            $table->time('start_time');
            $table->time('end_time');

            // Jenis Kegiatan
            $table->enum('type', ['Belajar', 'Istirahat', 'Upacara', 'Lainnya'])->default('Belajar');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_hours');
    }
};

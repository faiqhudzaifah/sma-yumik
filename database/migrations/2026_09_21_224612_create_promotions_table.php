<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('from_classroom_id')->constrained('classrooms')->cascadeOnDelete();

            // Nullable karena jika LULUS, siswa tidak masuk ke rombel mana pun lagi
            $table->foreignId('to_classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();

            $table->enum('status', ['NAIK', 'TINGGAL', 'LULUS']);

            // Mencatat pada Tahun Ajaran kapan proses ini dieksekusi
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};

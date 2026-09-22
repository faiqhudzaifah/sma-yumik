<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();

            $table->string('title')->comment('Contoh: Ulangan Harian 1, Tugas Makalah');
            $table->text('description')->nullable();
            $table->enum('type', ['Tugas', 'Kuis', 'Praktik', 'Proyek', 'UTS', 'UAS']);
            $table->dateTime('due_date')->nullable()->comment('Batas waktu pengumpulan (opsional)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();

            $table->string('title')->comment('Judul materi');
            $table->text('description')->nullable()->comment('Penjelasan singkat atau instruksi');

            // Kolom untuk menampung path file yang diunggah
            $table->string('file_path')->nullable()->comment('Path file di storage (PDF/Doc/PPT)');
            // Kolom untuk menampung link eksternal
            $table->string('external_link')->nullable()->comment('Link YouTube atau Google Drive');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

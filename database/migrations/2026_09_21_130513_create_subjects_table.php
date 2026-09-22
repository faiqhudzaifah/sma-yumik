<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            // Relasi Induk
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();

            // Relasi Guru Pengampu
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();

            // Identitas Mapel
            $table->string('name')->comment('Contoh: Pendidikan Agama Islam');
            $table->string('code', 20)->nullable()->comment('Contoh: PAI');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

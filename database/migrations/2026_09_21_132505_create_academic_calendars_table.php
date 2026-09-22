<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            $table->string('event_name')->comment('Contoh: Ujian Tengah Semester');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['Libur', 'Ujian', 'Kegiatan', 'Lainnya'])->default('Kegiatan');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};

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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['QUIZ', 'EXAM']);

            // Pengaturan Waktu & Akses
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('duration')->comment('Dalam satuan menit');
            $table->integer('attempt_limit')->default(1)->comment('Batas percobaan ujian');
            $table->boolean('shuffle_questions')->default(false);

            $table->enum('status', ['DRAFT', 'PUBLISHED', 'CLOSED'])->default('DRAFT');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};

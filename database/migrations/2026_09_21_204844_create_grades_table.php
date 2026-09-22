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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();

            // Decimal 5,2 memungkinkan nilai seperti 85.50
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable()->comment('Komentar guru untuk siswa');

            $table->timestamps();

            // Mencegah 1 tugas memiliki 2 nilai untuk siswa yang sama
            $table->unique(['assignment_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};

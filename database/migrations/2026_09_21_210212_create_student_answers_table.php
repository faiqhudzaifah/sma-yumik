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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();

            // Jika PG, simpan ID opsi. Jika Esai, simpan teks.
            $table->foreignId('option_id')->nullable()->constrained('options')->nullOnDelete();
            $table->text('answer_text')->nullable();

            $table->boolean('is_correct')->nullable()->comment('Null jika esai belum dikoreksi');
            $table->decimal('points_awarded', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};

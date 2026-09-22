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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();

            $table->date('date')->comment('Tanggal pelaksanaan pertemuan');
            $table->integer('meeting_number')->comment('Pertemuan ke- (1, 2, dst)');
            $table->string('topic')->comment('Materi pokok / Topik bahasan');
            $table->text('description')->nullable()->comment('Catatan aktivitas / Jurnal mengajar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};

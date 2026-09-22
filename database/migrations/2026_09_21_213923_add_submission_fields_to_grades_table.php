<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->text('submitted_text')->nullable()->comment('Jawaban teks langsung dari siswa')->after('siswa_id');
            $table->string('file_path')->nullable()->comment('File lampiran tugas dari siswa')->after('submitted_text');
            $table->dateTime('submitted_at')->nullable()->comment('Waktu pengumpulan')->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['submitted_text', 'file_path', 'submitted_at']);
        });
    }
};

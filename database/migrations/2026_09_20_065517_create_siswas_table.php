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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Identitas Siswa
            $table->string('nisn')->unique()->comment('Nomor Induk Siswa Nasional');
            $table->string('nis')->unique()->nullable()->comment('Nomor Induk Sekolah');

            // Data Demografi
            $table->enum('gender', ['L', 'P']);
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion')->nullable();

            // Kontak Pribadi
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Data Orang Tua / Wali
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('guardian_name')->nullable()->comment('Nama Wali (jika ada)');
            $table->string('parent_phone')->nullable();

            // Status Akademik
            $table->year('enrollment_year')->comment('Tahun masuk sekolah');
            $table->enum('status', ['Aktif', 'Lulus', 'Pindah', 'Keluar', 'Cuti'])->default('Aktif');
            $table->string('photo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};

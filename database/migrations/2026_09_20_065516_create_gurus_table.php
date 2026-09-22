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
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Identitas & Kependidikan
            $table->string('nip')->unique()->nullable()->comment('Nomor Induk Pegawai');
            $table->string('nuptk')->unique()->nullable()->comment('Nomor Unik Pendidik');
            $table->string('front_title')->nullable()->comment('Gelar Depan (cth: Drs., Ir.)');
            $table->string('back_title')->nullable()->comment('Gelar Belakang (cth: S.Pd., M.Kom.)');

            // Data Demografi
            $table->enum('gender', ['L', 'P']);
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion')->nullable();

            // Kontak & Alamat
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Status & Media
            $table->enum('employment_status', ['PNS', 'PPPK', 'GTY', 'Honorer'])->default('Honorer');
            $table->string('photo')->nullable()->comment('Path foto profil spesifik');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};

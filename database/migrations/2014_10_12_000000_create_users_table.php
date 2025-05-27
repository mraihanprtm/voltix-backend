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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment BigInt standar Laravel
            $table->string('firebase_uid')->unique()->comment('UID dari Firebase Authentication, ini akan jadi identifier utama dari klien');
            $table->string('name');
            $table->string('email')->unique()->comment('Email dari Firebase, sebaiknya unik');
            $table->timestamp('email_verified_at')->nullable(); // Standar Laravel, bisa diisi dari status verifikasi Firebase
            
            // Kolom spesifik Voltix
            $table->integer('jenis_listrik')->nullable()->comment('Kapasitas daya listrik pengguna (misal: 900, 1300, 2200 VA)');
            $table->string('foto_profil')->nullable()->comment('URL ke foto profil pengguna');
            
            // $table->string('password'); // Kita tidak perlukan password jika auth utama via Firebase ID Token
            $table->rememberToken(); // Standar Laravel untuk fitur "remember me" (mungkin tidak relevan untuk API murni)
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

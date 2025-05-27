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
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            
            // Foreign key ke tabel users (menggunakan firebase_uid sebagai referensi jika itu yang utama)
            // Atau jika tabel users Laravel punya 'id' sendiri, bisa foreignId ke 'users.id'
            // dan tabel users punya kolom 'firebase_uid'.
            // Pilihan 1: Menggunakan string firebase_uid secara langsung (lebih sederhana jika tidak ada relasi Eloquent yang kompleks)
            $table->string('user_id')->comment('Firebase UID pemilik ruangan'); 
            $table->index('user_id'); // Tambahkan index untuk performa query berdasarkan user_id

            // Pilihan 2: Foreign key ke primary key tabel users Laravel (jika tabel users punya kolom firebase_uid terpisah)
            // $table->foreignId('user_table_id')->constrained('users')->onDelete('cascade');
            // Anda perlu memutuskan bagaimana user_id ini akan direlasikan.
            // Untuk konsistensi dengan apa yang kita bahas sebelumnya (menyimpan firebase_uid di setiap tabel utama),
            // kita akan gunakan Pilihan 1 untuk saat ini.

            $table->string('nama_ruangan');
            $table->float('panjang_ruangan')->comment('Dalam meter');
            $table->float('lebar_ruangan')->comment('Dalam meter');
            $table->string('jenis_ruangan')->comment('Menyimpan nama enum JenisRuangan, misal: KamarTidur, RuangTamu');
            // Di Laravel, enum bisa disimpan sebagai string. Anda akan menangani mappingnya di model atau saat input/output.
            // Atau Anda bisa menggunakan tipe enum PostgreSQL jika database mendukung dan Anda mau.
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};

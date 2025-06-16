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
        Schema::create('perangkat', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment

            // Kolom untuk kepemilikan data oleh pengguna (Firebase UID)
            // Ini penting jika perangkat dibuat atau dimiliki secara spesifik oleh pengguna.
            // Jika perangkat adalah data master/global, kolom ini mungkin tidak diperlukan
            // dan relasi ke pengguna akan melalui tabel lain (misalnya, ruangan_perangkat).
            // Untuk konsistensi dan fleksibilitas, seringkali baik untuk menambahkannya.
            $table->string('user_id')->nullable()->comment('Firebase UID pemilik perangkat, jika relevan. Nullable jika perangkat bisa global.');
            $table->index('user_id');
            $table->string('uuid')->unique()->comment('UUID unik untuk perangkat, digunakan untuk identifikasi global.');
            $table->string('nama');
            $table->integer('jumlah')->default(1);
            $table->integer('daya')->comment('Dalam Watt');
            $table->string('jenis')->comment('Menyimpan nama enum jenis, misal: Lampu, Lainnya');
            // Di Laravel, enum bisa disimpan sebagai string.
            // Anda akan menangani mappingnya di model Eloquent atau saat input/output.

            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkat');
    }
};

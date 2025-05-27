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
        Schema::create('rekomendasi_penghematan_lampu', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment

            // Foreign key ke tabel users (menyimpan Firebase UID)
            // Di RekomendasiPenghematanLampuEntity, userId merujuk ke ID UserEntity lokal.
            // Di backend, kita akan menggunakan Firebase UID.
            $table->string('user_id')->nullable()->comment('Firebase UID pengguna yang mendapat rekomendasi, nullable jika rekomendasi bisa publik');
            $table->index('user_id');

            $table->foreignId('ruangan_id')
                  ->constrained('ruangan') // Merujuk ke tabel 'ruangans'
                  ->onDelete('cascade'); // Jika ruangan dihapus, rekomendasi terkait juga dihapus

            $table->foreignId('lampu_id')
                  ->constrained('lampu') // Merujuk ke tabel 'lampus'
                  ->onDelete('cascade'); // Jika lampu dihapus, rekomendasi terkait juga dihapus
            
            // tanggal rekomendasi akan otomatis ditangani oleh $table->timestamps() sebagai created_at
            // Jika Anda ingin kolom 'tanggal' yang terpisah dan bisa diatur manual:
            // $table->timestamp('tanggal_rekomendasi')->useCurrent();

            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_penghematan_lampu');
    }
};

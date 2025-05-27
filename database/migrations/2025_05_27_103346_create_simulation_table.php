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
        Schema::create('simulation', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment

            // Foreign key ke tabel users (menyimpan Firebase UID)
            // Setiap simulasi harus dimiliki oleh seorang pengguna
            $table->string('user_id')->comment('Firebase UID pemilik simulasi');
            $table->index('user_id');

            $table->string('name');

            // Foreign key ke tabel ruangans (nullable)
            // Jika simulasi bisa dibuat berdasarkan template ruangan
            $table->foreignId('ruangan_id')->nullable()
                  ->constrained('ruangan') // Merujuk ke tabel 'ruangans'
                  ->onDelete('set null'); // Jika ruangan dihapus, ruangan_id di simulasi menjadi null

            // createdAt akan otomatis dibuat oleh $table->timestamps()
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation');
    }
};

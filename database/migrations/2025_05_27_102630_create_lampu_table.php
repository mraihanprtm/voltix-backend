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
        Schema::create('lampu', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment

            // Foreign key ke tabel perangkats
            // Ini menghubungkan setiap entri lampu ke entri perangkat yang sesuai.
            $table->foreignId('perangkat_id')
                  ->constrained('perangkat') // Merujuk ke tabel 'perangkats'
                  ->onDelete('cascade'); // Jika perangkat dihapus, lampu terkait juga akan dihapus

            $table->string('jenis')->comment('Menyimpan nama enum jenisLampu, misal: Neon, LED');
            $table->integer('lumen');
            
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampu');
    }
};

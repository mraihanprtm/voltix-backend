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
        Schema::create('ruangan_perangkat', function (Blueprint $table) {
            // Primary key bisa berupa kombinasi dari kedua foreign key,
            // atau Anda bisa menggunakan $table->id(); jika lebih suka.
            // Menggunakan kombinasi foreign key sebagai primary key adalah umum untuk tabel pivot.
            // $table->id(); // Alternatif jika Anda ingin primary key auto-increment standar

            $table->foreignId('ruangan_id')
                  ->constrained('ruangan') // Merujuk ke tabel 'ruangans'
                  ->onDelete('cascade');   // Jika ruangan dihapus, entri di pivot table ini juga dihapus

            $table->foreignId('perangkat_id')
                  ->constrained('perangkat') // Merujuk ke tabel 'perangkats'
                  ->onDelete('cascade');   // Jika perangkat dihapus, entri di pivot table ini juga dihapus

            // Kolom tambahan untuk tabel pivot
            $table->time('waktu_nyala')->nullable(); // Menyimpan waktu nyala
            $table->time('waktu_mati')->nullable();  // Menyimpan waktu mati

            // Menjadikan kombinasi ruangan_id dan perangkat_id sebagai primary key
            // Ini juga secara otomatis membuat index pada kedua kolom tersebut.
            $table->primary(['ruangan_id', 'perangkat_id']);

            // Tidak perlu timestamps() untuk tabel pivot kecuali Anda benar-benar membutuhkannya
            // untuk melacak kapan relasi dibuat atau diubah.
            // $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan_perangkat');
    }
};

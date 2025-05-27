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
        Schema::create('simulation_devices', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment (menggantikan deviceId)

            $table->foreignId('simulation_id')
                  ->constrained('simulation') // Merujuk ke tabel 'simulations'
                  ->onDelete('cascade'); // Jika simulasi dihapus, perangkat terkait juga dihapus

            $table->string('nama');
            $table->integer('daya')->comment('Dalam Watt');
            $table->integer('jumlah')->default(1);
            $table->string('jenis')->comment('Menyimpan nama enum jenis dari PerangkatEntity, misal: Lampu, Lainnya');
            $table->time('waktu_nyala');
            $table->time('waktu_mati');
            
            // Tidak perlu timestamps() di sini kecuali Anda ingin melacak kapan
            // sebuah perangkat ditambahkan/diubah dalam konteks simulasi.
            // Biasanya, timestamp simulasi utama sudah cukup.
            // $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_devices');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Jika Anda membuat Enum PHP untuk jenis lampu:
// namespace App\Enums;
// enum JenisLampuEnum: string {
//     case Neon = 'Neon';
//     case LED = 'LED';
// }
// Lalu import di sini: use App\Enums\JenisLampuEnum;

class Lampu extends Model
{
    use HasFactory;

    protected $table = 'lampu';

    protected $fillable = [
        'perangkat_id', // Foreign key ke tabel perangkats
        'jenis',        // Akan disimpan sebagai string (nama enum jenisLampu dari Android)
        'lumen',
    ];

    protected $casts = [
        'lumen' => 'integer',
        // Jika menggunakan Enum PHP 8.1+ untuk jenis:
        // 'jenis' => \App\Enums\JenisLampuEnum::class,
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Perangkat.
     * Setiap entri detail lampu dimiliki oleh satu entri perangkat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function perangkat()
    {
        return $this->belongsTo(Perangkat::class, 'perangkat_id');
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model RekomendasiPenghematanLampu.
     * Satu jenis lampu bisa muncul di banyak rekomendasi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rekomendasiPenghematanLampu()
    {
        return $this->hasMany(RekomendasiPenghematanLampu::class); // Laravel akan mengasumsikan foreign key 'lampu_id'
    }
}

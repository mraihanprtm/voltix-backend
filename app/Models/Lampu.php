<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Jika Anda membuat Enum PHP untuk jenis lampu:
// namespace App\Enums;
// enum JenisLampuEnum: string {
//     case Neon = 'Neon';
//     case LED = 'LED';
// }
// Lalu import di sini: use App\Enums\JenisLampuEnum;

class Lampu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lampu';

    protected $fillable = [
        'perangkat_id', // Foreign key ke tabel perangkats
        'jenis',        // Akan disimpan sebagai string (nama enum jenisLampu dari Android)
        'lumen',
        'uuid',
    ];

    protected $casts = [
        'id' => 'integer',
        'perangkat_id' => 'integer',
        'lumen' => 'integer',
        'isDeleted' => 'boolean',
        'lastModified' => 'timestamp',
    ];

    protected $appends = ['isDeleted', 'lastModified'];

    public function getIsDeletedAttribute()
    {
        return $this->deleted_at !== null;
    }

    public function getLastModifiedAttribute()
    {
        return $this->updated_at ? $this->updated_at->getTimestamp() * 1000 : null;
    }

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

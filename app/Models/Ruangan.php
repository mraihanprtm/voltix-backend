<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Import enum JenisRuangan dari aplikasi Android jika Anda ingin mereplikasinya di PHP.
// Atau, Anda bisa menggunakan string biasa dan melakukan validasi di tempat lain.
// Untuk Enum di PHP 8.1+, Anda bisa mendefinisikannya seperti ini:
// enum JenisRuanganEnum: string {
//     case KamarTidur = 'KamarTidur';
//     case RuangTamu = 'RuangTamu';
//     case Dapur = 'Dapur';
//     case KamarMandi = 'KamarMandi';
//     case Lainnya = 'Lainnya';
// }

class Ruangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ruangan'; // Eksplisit jika nama tabel berbeda dari konvensi

    protected $fillable = [
        'user_id',
        'nama_ruangan',    // MUST be snake_case, NOT namaRuangan
        'panjang_ruangan', // MUST be snake_case, NOT panjangRuangan
        'lebar_ruangan',   // MUST be snake_case, NOT lebarRuangan
        'jenis_ruangan',   // MUST be snake_case, NOT jenisRuangan
        'uuid',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'string',
        'panjangRuangan' => 'float',
        'lebarRuangan' => 'float',
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
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Setiap ruangan dimiliki oleh satu pengguna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Asumsi 'user_id' di tabel 'ruangans' adalah Firebase UID
        // dan merujuk ke kolom 'firebase_uid' di tabel 'users'.
        return $this->belongsTo(User::class, 'user_id', 'firebase_uid');
    }

    /**
     * Mendefinisikan relasi many-to-many ke model Perangkat melalui tabel pivot 'ruangan_perangkat'.
     * Juga menyertakan kolom tambahan dari tabel pivot ('waktu_nyala', 'waktu_mati').
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function perangkat()
    {
        return $this->belongsToMany(Perangkat::class, 'ruangan_perangkat', 'ruangan_id', 'perangkat_id')
                    ->withPivot('waktu_nyala', 'waktu_mati'); // Untuk mengakses kolom tambahan di pivot
                    // ->withTimestamps(); // Jika tabel pivot Anda memiliki timestamps (opsional)
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model RekomendasiPenghematanLampu.
     * Satu ruangan bisa memiliki banyak rekomendasi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rekomendasiPenghematanLampu()
    {
        return $this->hasMany(RekomendasiPenghematanLampu::class); // Laravel akan mengasumsikan foreign key 'ruangan_id'
    }

    // Jika Simulation memiliki foreign key ruangan_id
    // public function simulations()
    // {
    //     return $this->hasMany(Simulation::class);
    // }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}

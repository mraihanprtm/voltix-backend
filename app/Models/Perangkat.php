<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Jika Anda membuat Enum PHP untuk jenis perangkat:
// namespace App\Enums;
// enum JenisPerangkatEnum: string {
//     case Lampu = 'Lampu';
//     case Lainnya = 'Lainnya';
// }
// Lalu import di sini: use App\Enums\JenisPerangkatEnum;

class Perangkat extends Model
{
    use HasFactory;

    protected $table = 'perangkat';

    protected $fillable = [
        'user_id', // Firebase UID pemilik (jika perangkat dimiliki langsung oleh user)
        'nama',
        'jumlah',
        'daya',
        'jenis',   // Akan disimpan sebagai string (nama enum dari Android)
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'daya' => 'integer',
        // Jika menggunakan Enum PHP 8.1+ untuk jenis:
        // 'jenis' => JenisPerangkatEnum::class,
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model User (jika perangkat dimiliki user).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function user()
    // {
    //     // Asumsi 'user_id' di tabel 'perangkats' adalah Firebase UID
    //     // dan merujuk ke kolom 'firebase_uid' di tabel 'users'.
    //     return $this->belongsTo(User::class, 'user_id', 'firebase_uid');
    // }

    /**
     * Mendefinisikan relasi many-to-many ke model Ruangan melalui tabel pivot 'ruangan_perangkat'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'ruangan_perangkat', 'perangkat_id', 'ruangan_id')
                    ->withPivot('waktu_nyala', 'waktu_mati'); // Untuk mengakses kolom tambahan di pivot
                    // ->withTimestamps(); // Jika tabel pivot Anda memiliki timestamps
    }

    /**
     * Mendefinisikan relasi "hasOne" ke model Lampu.
     * Satu perangkat (jika berjenis lampu) akan memiliki satu entri detail lampu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lampuDetail()
    {
        // Asumsi foreign key di tabel 'lampus' adalah 'perangkat_id'
        return $this->hasOne(Lampu::class, 'perangkat_id');
    }
}

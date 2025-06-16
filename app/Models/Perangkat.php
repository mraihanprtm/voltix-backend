<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Jika Anda membuat Enum PHP untuk jenis perangkat:
// namespace App\Enums;
// enum JenisPerangkatEnum: string {
//     case Lampu = 'Lampu';
//     case Lainnya = 'Lainnya';
// }
// Lalu import di sini: use App\Enums\JenisPerangkatEnum;

class Perangkat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perangkat';

    protected $fillable = [
        'user_id', // Firebase UID pemilik (jika perangkat dimiliki langsung oleh user)
        'uuid',
        'nama',
        'jumlah',
        'daya',
        'jenis',   // Akan disimpan sebagai string (nama enum dari Android)
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'string', // Assuming user_id in DB is integer
        'jumlah' => 'integer',
        'daya' => 'integer',
        'isDeleted' => 'boolean', // For the accessor
        'lastModified' => 'timestamp', // To cast updated_at to a specific format if needed, usually not necessary for epoch
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
     * The accessors to append to the model's array form.
     */
    protected $appends = ['isDeleted', 'lastModified'];

    /**
     * Accessor for the 'isDeleted' attribute.
     * Matches the Kotlin PerangkatEntity's isDeleted field.
     *
     * @return bool
     */
    public function getIsDeletedAttribute()
    {
        return $this->deleted_at !== null;
    }

    /**
     * Accessor for the 'lastModified' attribute.
     * Matches the Kotlin PerangkatEntity's lastModified field (expects milliseconds).
     * Laravel's updated_at is a Carbon instance.
     *
     * @return int|null
     */
    public function getLastModifiedAttribute()
    {
        return $this->updated_at ? $this->updated_at->getTimestamp() * 1000 : null;
    }

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

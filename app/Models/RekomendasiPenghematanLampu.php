<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiPenghematanLampu extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang berasosiasi dengan model.
     * Laravel biasanya bisa menentukannya secara otomatis dari nama model (plural snake case),
     * tapi untuk nama yang panjang atau spesifik, lebih baik didefinisikan secara eksplisit.
     *
     * @var string
     */
    protected $table = 'rekomendasi_penghematan_lampu';

    /**
     * Atribut-atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',    // Firebase UID pengguna
        'ruangan_id', // Foreign key ke tabel ruangans
        'lampu_id',   // Foreign key ke tabel lampus
        // 'tanggal_rekomendasi', // Jika Anda menambahkan kolom ini di migrasi
    ];

    /**
     * Atribut-atribut yang seharusnya di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'tanggal_rekomendasi' => 'datetime', // Jika Anda menggunakan kolom tanggal_rekomendasi
        // created_at dan updated_at otomatis di-cast ke Carbon instances.
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Asumsi: 'user_id' di tabel ini adalah string Firebase UID, dan model User Anda
     * memiliki kolom 'firebase_uid' sebagai identifier unik atau primary key.
     * Jika model User Anda menggunakan 'id' sebagai primary key dan 'firebase_uid' sebagai kolom lain,
     * Anda mungkin perlu menyesuaikan foreign key dan owner key di sini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Jika User model Anda menggunakan 'id' sebagai PK dan punya kolom 'firebase_uid':
        // return $this->belongsTo(User::class, 'user_id', 'firebase_uid');
        // Jika 'user_id' di tabel ini merujuk ke 'id' di tabel 'users' (dan 'users.id' adalah PK):
        return $this->belongsTo(User::class, 'user_id', 'firebase_uid'); 
        // Catatan: Relasi ini mengasumsikan 'user_id' di tabel ini adalah firebase_uid dan
        // 'firebase_uid' adalah kolom unik di tabel 'users' yang bisa dijadikan referensi.
        // Jika 'user_id' di tabel ini seharusnya merujuk ke 'id' (PK auto-increment) di tabel 'users',
        // maka kolom 'user_id' di migrasi rekomendasi seharusnya foreignId dan relasinya:
        // return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Ruangan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class); // Asumsi foreign key adalah 'ruangan_id'
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Lampu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lampu()
    {
        return $this->belongsTo(Lampu::class); // Asumsi foreign key adalah 'lampu_id'
    }
}

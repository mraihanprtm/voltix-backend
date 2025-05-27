<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Jika Anda menggunakan Sanctum untuk jenis token lain

class User extends Authenticatable // implements MustVerifyEmail (jika Anda ingin fitur verifikasi email Laravel)
{
    use HasFactory, Notifiable; // HasApiTokens (jika menggunakan Sanctum)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firebase_uid',
        'name',
        'email',
        'jenis_listrik',
        'foto_profil',
        'email_verified_at', // Jika Anda menyimpannya dari Firebase
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Ini penting agar password (jika ada) dan remember_token tidak ikut terkirim dalam respons API.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password', // Kita tidak menggunakan password Laravel
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed', // Tidak digunakan
        'jenis_listrik' => 'integer',
    ];

    /**
     * Mendefinisikan relasi "hasMany" ke model Ruangan.
     * Seorang pengguna bisa memiliki banyak ruangan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ruangan()
    {
        // Asumsi: tabel 'ruangans' memiliki kolom 'user_id' yang menyimpan 'firebase_uid' dari tabel 'users'.
        // Jika 'user_id' di 'ruangans' merujuk ke 'id' (PK) di 'users', maka cukup: return $this->hasMany(Ruangan::class);
        return $this->hasMany(Ruangan::class, 'user_id', 'firebase_uid');
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model Item (jika Anda mengadaptasi tabel 'items' untuk Voltix).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function items()
    // {
    //     return $this->hasMany(Item::class, 'user_id', 'firebase_uid');
    // }

    /**
     * Mendefinisikan relasi "hasMany" ke model Simulation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function simulation()
    {
        return $this->hasMany(Simulation::class, 'user_id', 'firebase_uid');
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model RekomendasiPenghematanLampu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rekomendasiPenghematanLampu()
    {
        return $this->hasMany(RekomendasiPenghematanLampu::class, 'user_id', 'firebase_uid');
    }

    // Jika Anda memiliki tabel 'perangkats' yang secara langsung dimiliki oleh user
    // (selain melalui ruangan), Anda bisa menambahkan relasi di sini.
    // public function perangkats()
    // {
    //     return $this->hasMany(Perangkat::class, 'user_id', 'firebase_uid');
    // }
}
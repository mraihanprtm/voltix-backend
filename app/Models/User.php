<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Biarkan ini jika tidak dipakai
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- PENTING: UNCOMMENT BARIS INI

class User extends Authenticatable // implements MustVerifyEmail (jika Anda ingin fitur verifikasi email Laravel)
{
    use HasApiTokens, HasFactory, Notifiable; // <--- PENTING: UNCOMMENT DAN TAMBAHKAN HasApiTokens DI SINI

    protected $fillable = [
        'firebase_uid', // Pastikan nama kolom ini sama dengan di Supabase & migrasi Anda
        'name',
        'email',
        'email_verified_at', // Jika Anda menyimpan ini sebagai timestamp
        // atau 'email_verified' jika boolean (sesuaikan juga $casts)
        'jenis_listrik',
        'foto_profil',
        'is_prabayar', // <-- TAMBAHKAN INI
    ];

    protected $hidden = [
        // 'password', // Tidak ada password Laravel
        'remember_token', // Atau 'remember_tol' jika itu nama kolom Anda
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // Jika email_verified_at adalah timestamp
        // 'email_verified' => 'boolean', // Jika email_verified adalah boolean
        'jenis_listrik' => 'integer',
        'is_prabayar' => 'boolean', // <-- PERBAIKI MENJADI INI
        // 'password' => 'hashed', // Tidak ada password Laravel
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
        // Ini adalah poin KRITIS, kita perlu konfirmasi ini.
        return $this->hasMany(Ruangan::class, 'user_id', 'firebase_uid'); // <--- PERHATIAN DI SINI
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model Simulation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function simulation()
    {
        // Ini juga poin KRITIS
        return $this->hasMany(Simulation::class, 'user_id', 'firebase_uid'); // <--- PERHATIAN DI SINI
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model RekomendasiPenghematanLampu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rekomendasiPenghematanLampu()
    {
        // Dan ini juga poin KRITIS
        return $this->hasMany(RekomendasiPenghematanLampu::class, 'user_id', 'firebase_uid'); // <--- PERHATIAN DI SINI
    }
}
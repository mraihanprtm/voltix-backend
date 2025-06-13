<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'firebase_uid',
        'name',
        'email',
        'password', 
        'role',
        'email_verified_at',
        'jenis_listrik',
        'foto_profil',
        'is_prabayar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'jenis_listrik' => 'integer',
        'is_prabayar' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Mendefinisikan relasi "hasMany" ke model Ruangan.
     * Seorang pengguna bisa memiliki banyak ruangan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ruangan()
    {
        return $this->hasMany(Ruangan::class, 'user_id', 'firebase_uid');
    }

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
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    use HasFactory;

    protected $table = 'simulation';

    protected $fillable = [
        'user_id',    // Firebase UID pemilik simulasi
        'name',
        'ruangan_id', // Foreign key ke tabel ruangans (nullable)
    ];

    /**
     * Atribut yang seharusnya di-cast.
     * created_at dan updated_at otomatis di-cast ke Carbon.
     *
     * @var array
     */
    protected $casts = [
        // Tidak ada cast khusus yang diperlukan dari kolom yang ada saat ini,
        // selain yang otomatis ditangani Eloquent (seperti timestamps).
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Setiap simulasi dimiliki oleh satu pengguna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Asumsi 'user_id' di tabel 'simulations' adalah Firebase UID
        // dan merujuk ke kolom 'firebase_uid' di tabel 'users'.
        return $this->belongsTo(User::class, 'user_id', 'firebase_uid');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Ruangan (jika simulasi berdasarkan template ruangan).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id'); // Foreign key adalah 'ruangan_id'
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model SimulationDevice.
     * Satu simulasi bisa memiliki banyak perangkat simulasi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function simulationDevices()
    {
        // Laravel akan mengasumsikan foreign key 'simulation_id' di tabel 'simulation_devices'
        return $this->hasMany(SimulationDevice::class);
    }
}

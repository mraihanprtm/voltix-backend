<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Jika Anda membuat Enum PHP untuk jenis perangkat (Lampu, Lainnya):
// use App\Enums\JenisPerangkatEnum;

class SimulationDevice extends Model
{
    use HasFactory;

    protected $table = 'simulation_devices';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'simulation_id', // Foreign key ke tabel simulations
        'nama',
        'daya',
        'jumlah',
        'jenis',         // String (Lampu, Lainnya)
        'waktu_nyala',   // Akan disimpan sebagai string 'HH:MM:SS' atau 'HH:MM'
        'waktu_mati',    // Akan disimpan sebagai string 'HH:MM:SS' atau 'HH:MM'
    ];

    /**
     * Atribut yang seharusnya di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'daya' => 'integer',
        'jumlah' => 'integer',
        // 'waktu_nyala' => 'datetime:H:i', // Atau biarkan sebagai string jika formatnya sudah H:i:s
        // 'waktu_mati' => 'datetime:H:i',  // Eloquent akan mencoba mem-parse ke Carbon jika tipenya time di DB
        // Jika menggunakan Enum PHP 8.1+ untuk jenis:
        // 'jenis' => JenisPerangkatEnum::class,
    ];

    /**
     * Menunjukkan apakah model harus memiliki timestamps (created_at, updated_at).
     * Biasanya untuk tabel detail seperti ini, timestamps tidak selalu diperlukan.
     *
     * @var bool
     */
    public $timestamps = false; // Set false jika tabel Anda tidak memiliki kolom created_at dan updated_at

    /**
     * Mendefinisikan relasi "belongsTo" ke model Simulation.
     * Setiap perangkat simulasi dimiliki oleh satu simulasi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function simulation()
    {
        return $this->belongsTo(Simulation::class, 'simulation_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    protected $table = 'sesi';

    protected $fillable = [
        'nama_sesi',
        'jam_mulai',
        'batas_waktu',
        'is_active',
        'peruntukan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Cek status berdasarkan jam masuk
     * Toleransi 3 menit setelah batas waktu
     */
    public function getStatus($jamMasuk)
    {
        $jamMulai = $this->jam_mulai ? strtotime($this->jam_mulai) : strtotime($this->batas_waktu) - 3600;
        $batasToleransi = strtotime($this->batas_waktu) + (3 * 60);
        $jamMasukTime = strtotime($jamMasuk);

        if ($jamMasukTime <= $jamMulai) {
            return 'Tepat Waktu';
        } elseif ($jamMasukTime <= $batasToleransi) {
            return 'Terlambat (Toleransi)';
        } else {
            return 'Terlambat';
        }
    }
}
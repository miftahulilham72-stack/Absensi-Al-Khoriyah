<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'lembaga',
        'gugus',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    // Relasi untuk absensi manual (diambil dari method manual di controller)
    public function absensi_manual()
    {
        return $this->hasMany(Absensi::class)->where('absen_manual', true);
    }
}
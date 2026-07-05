<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'peserta_id',
        'sesi_id',
        'jam_masuk',
        'status',
        'ttd_image',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }
}
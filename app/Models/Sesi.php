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
}
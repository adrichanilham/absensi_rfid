<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfid extends Model
{
    use HasFactory;

    protected $table = 'rfid';

    protected $fillable = [
        'uid',
        'siswa_id',
        'aktif',
        'terdaftar_pada',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'terdaftar_pada' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}

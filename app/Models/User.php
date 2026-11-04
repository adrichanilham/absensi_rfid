<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\PengajuanIzin;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi akun wali murid dengan anak.
    public function anak()
    {
        return $this->hasOne(Siswa::class, 'wali_murid_id');
    }

    // Relasi guru dengan kelas yang diampu.
    public function kelasDiampu()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    // Relasi operator dengan pengajuan izin yang diverifikasi.
    public function pengajuanIzinDiverifikasi()
    {
        return $this->hasMany(
            PengajuanIzin::class,
            'diverifikasi_oleh'
        );
    }
}

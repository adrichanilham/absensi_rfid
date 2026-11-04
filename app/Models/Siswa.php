<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas_id',
        'status',
        'wali_murid_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function rfid()
    {
        return $this->hasOne(Rfid::class, 'siswa_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    public function pengajuanIzin()
    {
        return $this->hasMany(PengajuanIzin::class, 'siswa_id');
    }
    public function waliMurid()
    {
        return $this->belongsTo(User::class, 'wali_murid_id');
    }
}

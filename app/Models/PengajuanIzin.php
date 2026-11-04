<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'siswa_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'alasan',
        'dokumen_path',
        'status',
        'diverifikasi_oleh',
        'catatan_verifikasi',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function verifier()
    {
        return $this->belongsTo(
            User::class,
            'diverifikasi_oleh'
        );
    }
}

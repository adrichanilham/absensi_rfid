<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::where('nama_kelas', 'Kelas 1')->firstOrFail();

        Siswa::firstOrCreate(
            ['nisn' => '0012345678'],
            [
                'nama_lengkap' => 'Siswa Percobaan',
                'jenis_kelamin' => 'L',
                'kelas_id' => $kelas->id,
                'status' => 'aktif',
            ]
        );
    }
}

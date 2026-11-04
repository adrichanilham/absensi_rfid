<?php

namespace Database\Seeders;

use App\Models\Rfid;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class RfidSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = Siswa::where('nisn', '0012345678')->firstOrFail();

        Rfid::firstOrCreate(
            ['uid' => 'UID_UJI_001'],
            [
                'siswa_id' => $siswa->id,
                'aktif' => true,
                'terdaftar_pada' => now(),
            ]
        );
    }
}

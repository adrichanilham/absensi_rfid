<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::firstOrCreate([
            'nama_kelas' => 'Kelas 1',
        ]);
    }
}

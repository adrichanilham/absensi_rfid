<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'operator',
                'password' => 'GantiPassword123!',
                'role' => 'operator',
            ],
            [
                'username' => 'guru1',
                'password' => 'GantiPassword123!',
                'role' => 'guru',
            ],
            [
                'username' => 'guru2',
                'password' => 'GantiPassword123!',
                'role' => 'guru',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'password' => $data['password'],
                    'role' => $data['role'],
                ]
            );
        }
    }
}
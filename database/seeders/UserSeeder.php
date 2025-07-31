<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Koordinator
            [
                'nama' => 'Koordinator',
                'email' => 'koordinator@example.com',
                'nomor_induk' => 'KD001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'role_id' => 2,
                'phone' => '081234567890'
            ],
            // Admin
            [
                'nama' => 'Administrator',
                'email' => 'admin@example.com',
                'nomor_induk' => 'AD001',
                'password' => Hash::make('  '),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 1,
                'phone' => '081234567891'
            ],
            [
                'nama' => 'Administrator 1',
                'email' => 'admin1@example.com',
                'nomor_induk' => 'AD002',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 1,
                'phone' => '081234567891'
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}

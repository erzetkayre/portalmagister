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
                'password' => Hash::make('password'),
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

        $refDosen = [
            [
                'user_id' => 1,
                'kode_dosen' => 'KD001',
                'nip' => '198501012010121001',
                'nama_dosen' => 'Koordinator',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Sistem Informasi',
                'gender' => 'L'
            ],
            [
                'user_id' => 2,
                'kode_dosen' => 'AD001',
                'nip' => '198502022010122002',
                'nama_dosen' => 'Administrator',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Manajemen Sistem',
                'gender' => 'L'
            ],
            [
                'user_id' => 3,
                'kode_dosen' => 'AD002',
                'nip' => '198503032010123003',
                'nama_dosen' => 'Dr. Ahmad Fauzi, M.Kom',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Pemrograman Web',
                'gender' => 'L'
            ],
        ];

        foreach ($refDosen as $dosen) {
            Dosen::create($dosen);
        }
    }
}

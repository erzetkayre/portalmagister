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
            [
                'nama' => 'Koordinator 1',
                'email' => 'koordinator1@example.com',
                'nomor_induk' => 'KD002',
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
            // Dosen
            [
                'nama' => 'Dr. Ahmad Fauzi, M.Kom',
                'email' => 'dsn1@example.com',
                'nomor_induk' => 'DS001',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 3,
                'phone' => '081234567892'
            ],
            [
                'nama' => 'Dr. Siti Nurhaliza, M.T',
                'email' => 'dsn2@example.com',
                'nomor_induk' => 'DS002',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 3,
                'phone' => '081234567893'
            ],
            [
                'nama' => 'Prof. Bambang Sutrisno, Ph.D',
                'email' => 'dsn3@example.com',
                'nomor_induk' => 'DS003',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 3,
                'phone' => '081234567896'
            ],
            [
                'nama' => 'Ir. Rina Anggraeni, M.Eng',
                'email' => 'dsn4@example.com',
                'nomor_induk' => 'DS004',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 3,
                'phone' => '081234567897'
            ],
            // Mahasiswa
            [
                'nama' => 'Budi Santoso',
                'email' => 'mhs1@example.com',
                'nomor_induk' => 'MHS001',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 4,
                'phone' => '081234567894'
            ],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'mhs2@example.com',
                'nomor_induk' => 'MHS002',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 4,
                'phone' => '081234567895'
            ],
            [
                'nama' => 'Andi Wijaya',
                'email' => 'mhs3@example.com',
                'nomor_induk' => 'MHS003',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 4,
                'phone' => '081234567898'
            ],
            [
                'nama' => 'Sari Wulandari',
                'email' => 'mhs4@example.com',
                'nomor_induk' => 'MHS004',
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'role_id' => 4,
                'phone' => '081234567899'
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $refDosen = [
            [
                'user_id' => 1,
                'kode_dosen' => 'DSN001',
                'nip' => '198501012010121001',
                'nama_dosen' => 'Super Administrator',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Sistem Informasi',
                'gender' => 'L'
            ],
            [
                'user_id' => 2,
                'kode_dosen' => 'DSN002',
                'nip' => '198502022010122002',
                'nama_dosen' => 'Administrator',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Manajemen Sistem',
                'gender' => 'L'
            ],
            [
                'user_id' => 3,
                'kode_dosen' => 'DSN003',
                'nip' => '198503032010123003',
                'nama_dosen' => 'Dr. Ahmad Fauzi, M.Kom',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Pemrograman Web',
                'gender' => 'L'
            ],
            [
                'user_id' => 4,
                'kode_dosen' => 'DSN004',
                'nip' => '198504042010124004',
                'nama_dosen' => 'Dr. Siti Nurhaliza, M.T',
                'status_dosen' => 'aktif',
                'bidang_keahlian' => 'Jaringan Komputer',
                'gender' => 'P'
            ]
        ];

        foreach ($refDosen as $dosen) {
            Dosen::create($dosen);
        }

         $refMahasiswa = [
            [
                'user_id' => 5,
                'nim' => '2021001',
                'nama_mhs' => 'Budi Santoso',
                'angkatan' => 2021,
                'status_mhs' => 'aktif',
                'sks' => 120,
                'ipk' => 350, // IPK 3.50 (dalam skala 100)
                'gender' => 'L'
            ],
            [
                'user_id' => 6,
                'nim' => '2021002',
                'nama_mhs' => 'Dewi Lestari',
                'angkatan' => 2021,
                'status_mhs' => 'aktif',
                'sks' => 115,
                'ipk' => 375, // IPK 3.75 (dalam skala 100)
                'gender' => 'P'
            ]
        ];

        foreach ($refMahasiswa as $mahasiswa) {
            Mahasiswa::create($mahasiswa);
        }
    }
}

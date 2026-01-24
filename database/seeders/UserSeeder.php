<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('main')->table('users')->insert([
            // Koordinator
            [
                'name' => 'Koordinator PWK',
                'email' => 'koordinator@pwk.com',
                'nomor_induk' => 'KD6001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Koordinator Elektro',
                'email' => 'koordinator@elektro.com',
                'nomor_induk' => 'KD7001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],

            // Admin
            [
                'name' => 'Admin PWK',
                'email' => 'admin@pwk.com',
                'nomor_induk' => 'ADM6001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Admin Elektro',
                'email' => 'admin@elektro.com',
                'nomor_induk' => 'ADM7001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],

            // Dosen
            [
                'name' => 'Dosen PWK',
                'email' => 'dosen@pwk.com',
                'nomor_induk' => 'DSN6001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Dosen 2 PWK',
                'email' => 'dosen2@pwk.com',
                'nomor_induk' => 'DSN6002',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Dosen Elektro',
                'email' => 'dosen@elektro.com',
                'nomor_induk' => 'DSN7001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],
            [
                'name' => 'Dosen 2 Elektro',
                'email' => 'dosen2@elektro.com',
                'nomor_induk' => 'DSN7002',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],

            // Mahasiswa
            [
                'name' => 'Mahasiswa PWK',
                'email' => 'mahasiswa@pwk.com',
                'nomor_induk' => 'MHS6001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Mahasiswa 2PWK',
                'email' => 'mahasiswa2@pwk.com',
                'nomor_induk' => 'MHS6002',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Mahasiswa 3PWK',
                'email' => 'mahasiswa3@pwk.com',
                'nomor_induk' => 'MHS6003',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Mahasiswa 4PWK',
                'email' => 'mahasiswa4@pwk.com',
                'nomor_induk' => 'MHS6004',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 1
            ],
            [
                'name' => 'Mahasiswa Elektro',
                'email' => 'mahasiswa@elektro.com',
                'nomor_induk' => 'MHS7001',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],
            [
                'name' => 'Mahasiswa 2Elektro',
                'email' => 'mahasiswa2@elektro.com',
                'nomor_induk' => 'MHS7002',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],

            [
                'name' => 'Mahasiswa 3Elektro',
                'email' => 'mahasiswa3@elektro.com',
                'nomor_induk' => 'MHS7003',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],

            [
                'name' => 'Mahasiswa 4Elektro',
                'email' => 'mahasiswa4@elektro.com',
                'nomor_induk' => 'MHS7004',
                'password' => Hash::make('password'),
                'first_login' => true,
                'is_active' => true,
                'study_program_id' => 2
            ],
        ]);

    }
}

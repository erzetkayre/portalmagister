<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'nama_role' => 'admin', 'deskripsi' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_role' => 'koordinator', 'deskripsi' => 'Koordinator', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_role' => 'dosen', 'deskripsi' => 'Dosen', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_role' => 'mahasiswa', 'deskripsi' => 'Mahasiswa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

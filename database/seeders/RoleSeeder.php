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
        DB::connection('main')->table('roles')->insert([
            ['id' => 1, 'role_name' => 'admin', 'description' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_name' => 'koordinator', 'description' => 'Koordinator', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_name' => 'dosen', 'description' => 'Dosen', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'role_name' => 'mahasiswa', 'description' => 'Mahasiswa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

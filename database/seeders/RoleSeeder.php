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
            ['id' => 1, 'role_name' => 'elektro_admin', 'description' => 'Administrator Magister Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_name' => 'elektro_koordinator', 'description' => 'Koordinator Magister Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_name' => 'elektro_dosen', 'description' => 'Dosen Magister Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'role_name' => 'elektro_mahasiswa', 'description' => 'Mahasiswa Magister Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'role_name' => 'pwk_admin', 'description' => 'Administrator Magister PWK', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'role_name' => 'pwk_koordinator', 'description' => 'Koordinator Magister PWK', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'role_name' => 'pwk_dosen', 'description' => 'Dosen Magister PWK', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'role_name' => 'pwk_mahasiswa', 'description' => 'Mahasiswa Magister PWK', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

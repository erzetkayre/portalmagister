<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::connection('main')->table('roles')->where('role_name', 'admin')->value('id');

        $admins = [
            [
                'name' => 'Admin PWK',
                'email' => 'admin@pwk.test',
                'nomor_induk' => 'admpwk001',
                'study_program_id' => 1,
            ],
            [
                'name' => 'Admin Elektro',
                'email' => 'admin@elektro.test',
                'nomor_induk' => 'admelektro001',
                'study_program_id' => 2,
            ],
        ];

        foreach ($admins as $data) {
            $userId = DB::connection('main')->table('users')->insertGetId([
                'name' => $data['name'],
                'email' => $data['email'],
                'nomor_induk' => $data['nomor_induk'],
                'study_program_id' => $data['study_program_id'],
                'password' => Hash::make('password'),
                'first_login' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::connection('main')->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $adminRole,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

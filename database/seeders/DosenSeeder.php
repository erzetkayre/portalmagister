<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('pwk')->table('ref_dosen')->insert([
            [ 'nama_dsn' => 'Dosen 1 PWK', 'user_id' => 5, 'kode_dsn' => 'KD6001', 'nip' => 'KD6001000' ],
            [ 'nama_dsn' => 'Dosen 2 PWK', 'user_id' => 6, 'kode_dsn' => 'KD6002', 'nip' => 'KD6002000' ],
        ]);
        DB::connection('elektro')->table('ref_dosen')->insert([
            [ 'nama_dsn' => 'Dosen 1 Elektro', 'user_id' => 7, 'kode_dsn' => 'KD7001', 'nip' => 'KD7001000' ],
            [ 'nama_dsn' => 'Dosen 2 Elektro', 'user_id' => 8, 'kode_dsn' => 'KD7002', 'nip' => 'KD7002000' ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('pwk')->table('ref_mahasiswa')->insert([
            [ 'nama_mhs' => 'Mahasiswa 1 PWK', 'user_id' => 9, 'pem_akademik' => 1, 'nim' => 'IO6001000' ],
            [ 'nama_mhs' => 'Mahasiswa 2 PWK', 'user_id' => 10, 'pem_akademik' => 2, 'nim' => 'IO6002000' ],
            [ 'nama_mhs' => 'Mahasiswa 3 PWK', 'user_id' => 11, 'pem_akademik' => 1, 'nim' => 'IO6003000' ],
            [ 'nama_mhs' => 'Mahasiswa 4 PWK', 'user_id' => 12, 'pem_akademik' => 2, 'nim' => 'IO6004000' ],
        ]);
        DB::connection('elektro')->table('ref_mahasiswa')->insert([
            [ 'nama_mhs' => 'Mahasiswa 1 Elektro', 'user_id' => 13, 'pem_akademik' => 1, 'nim' => 'IO7001000' ],
            [ 'nama_mhs' => 'Mahasiswa 2 Elektro', 'user_id' => 14, 'pem_akademik' => 2, 'nim' => 'IO7002000' ],
            [ 'nama_mhs' => 'Mahasiswa 3 Elektro', 'user_id' => 15, 'pem_akademik' => 1, 'nim' => 'IO7003000' ],
            [ 'nama_mhs' => 'Mahasiswa 4 Elektro', 'user_id' => 16, 'pem_akademik' => 2, 'nim' => 'IO7004000' ],
        ]);
    }
}

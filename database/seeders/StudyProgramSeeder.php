<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('main')->table('study_programs')->insert([
            ['program_name' => 'pwk', 'description' => 'Magister PWK UNS', 'db_connection' => 'pwk'],
            ['program_name' => 'elektro', 'description' => 'Magister Teknik Elektro UNS', 'db_connection' => 'elektro'],
        ]);
    }
}

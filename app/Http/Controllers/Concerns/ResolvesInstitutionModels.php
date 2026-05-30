<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Auth;
use App\Models\Elektro;
use App\Models\PWK;

trait ResolvesInstitutionModels
{
    protected function institutionModel(string $key): string
    {
        $map = [
            'elektro' => [
                'mahasiswa' => Elektro\Mahasiswa::class,
                'dosen'     => Elektro\Dosen::class,
                'jabatan'   => Elektro\Jabatan::class,
                'mk'        => Elektro\MataKuliah::class,
                'ruang'     => Elektro\Ruang::class,
            ],
            'pwk' => [
                'mahasiswa' => PWK\Mahasiswa::class,
                'dosen'     => PWK\Dosen::class,
                'jabatan'   => PWK\Jabatan::class,
                'mk'        => PWK\MataKuliah::class,
                'ruang'     => PWK\Ruang::class,
            ],
        ];

        $conn = Auth::user()->getDbConnection();

        return $map[$conn][$key] ?? abort(403);
    }
}

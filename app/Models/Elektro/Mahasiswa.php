<?php

namespace App\Models\Elektro;

use App\Helpers\Sortable;
use App\Helpers\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory, Filterable, Sortable;
    protected $connection = 'elektro';
    protected $table = 'ref_mahasiswa';

    protected $fillable = [
        'id',
        'nim',
        'nama_mhs',
        'angkatan',
        'status_mhs',
        'ipk',
        'sks',
        'gender',
    ];
    protected array $filterableColumns = [
        'search' => [
            'nama_mhs',
            'nim',
            'angkatan',
            'status',
        ],
    ];

    protected array $sortableColumns = [
        'nama_mhs',
        'status_mhs',
        'nim',
        'angkatan',
        'created_at',
    ];

    public function toSearchResult(): array
    {
        return [
            'id' => $this->id,
            'nama_mhs' => $this->nama_mhs,
            'nim' => $this->nim,
            'status' => $this->status,
            'angkatan' => $this->angkatan,
            'created_at' => $this->created_at->format('j F Y'),
            'created_at_raw' => $this->created_at
        ];
    }
}

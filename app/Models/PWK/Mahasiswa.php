<?php

namespace App\Models\PWK;

use App\Helpers\Filterable;
use App\Helpers\Sortable;
use App\Models\PWK\Dosen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory, Filterable, Sortable;
    protected $connection = 'pwk';
    protected $table = 'ref_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_mhs',
        'angkatan',
        'status_mhs',
        'ipk',
        'sks',
        'gender',
        'pem_akademik',
    ];
    protected array $filterableColumns = [
        'search' => [
            'nama_mhs',
            'nim',
        ],
        'status_mhs' => 'status_mhs',
        'angkatan'   => 'angkatan',
    ];

    protected array $sortableColumns = [
        'nim',
        'angkatan',
        'status_mhs',
        'created_at',
    ];

    public function pemAkademik()
    {
        return $this->belongsTo(Dosen::class, 'pem_akademik');
    }

    public function toSearchResult(): array
    {
        return [
            'id'           => $this->id,
            'nama_mhs'     => $this->nama_mhs,
            'nim'          => $this->nim,
            'angkatan'     => $this->angkatan,
            'status_mhs'   => $this->status_mhs,
            'pem_akademik' => $this->pem_akademik,
            'created_at'   => $this->created_at->translatedFormat('j F Y'),
        ];
    }
}

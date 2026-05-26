<?php

namespace App\Models\Elektro;

use App\Helpers\Sortable;
use App\Helpers\Filterable;
use App\Models\Elektro\Dosen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory, Filterable, Sortable;
    protected $connection = 'elektro';
    protected $table = 'ref_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_mhs',
        'angkatan',
        'status_mhs',
        'thesis_path',
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

    public function thesis()
    {
        return $this->hasMany(Thesis::class, 'mahasiswa_id');
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
            'created_at'   => $this->created_at->format('j F Y'),
        ];
    }
}

<?php

namespace App\Models\PWK;

use App\Helpers\Filterable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use Filterable, Sortable, SoftDeletes;

    protected $connection = 'pwk';
    protected $table = 'ref_dosen';

    protected $fillable = [
        'user_id', 'kode_dsn', 'nip', 'nama_dsn',
        'bidang_keahlian', 'gender', 'status_dsn',
    ];

    protected array $filterableColumns = [
        'search'     => ['nama_dsn', 'nip', 'kode_dsn'],
        'status_dsn' => 'status_dsn',
    ];

    protected array $sortableColumns = [
        'kode_dsn', 'nip', 'status_dsn', 'created_at',
    ];

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'dosen_id');
    }

    public function toSearchResult(): array
    {
        return [
            'id'              => $this->id,
            'nama_dsn'        => $this->nama_dsn,
            'nip'             => $this->nip,
            'kode_dsn'        => $this->kode_dsn,
            'bidang_keahlian' => $this->bidang_keahlian,
            'gender'          => $this->gender,
            'status_dsn'      => $this->status_dsn,
            'created_at'      => $this->created_at->format('j F Y'),
        ];
    }
}

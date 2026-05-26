<?php

namespace App\Models\Elektro;

use App\Helpers\Filterable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use Filterable, Sortable, SoftDeletes;

    protected $connection = 'elektro';
    protected $table = 'ref_jabatan';

    protected $fillable = ['dosen_id', 'nama_jabatan'];

    protected array $filterableColumns = [
        'search' => ['nama_jabatan'],
    ];

    protected array $sortableColumns = ['nama_jabatan', 'created_at'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function toSearchResult(): array
    {
        return [
            'id'           => $this->id,
            'nama_jabatan' => $this->nama_jabatan,
            'dosen_id'     => $this->dosen_id,
            'nama_dsn'     => $this->dosen?->nama_dsn,
            'created_at'   => $this->created_at->format('j F Y'),
        ];
    }
}

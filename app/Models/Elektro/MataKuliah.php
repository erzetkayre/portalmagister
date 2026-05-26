<?php

namespace App\Models\Elektro;

use App\Helpers\Filterable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataKuliah extends Model
{
    use Filterable, Sortable, SoftDeletes;

    protected $connection = 'elektro';
    protected $table = 'ref_mata_kuliah';

    protected $fillable = ['nama_mk', 'kode_mk', 'sks', 'status_mk'];

    protected array $filterableColumns = [
        'search'    => ['nama_mk', 'kode_mk'],
        'status_mk' => 'status_mk',
    ];

    protected array $sortableColumns = ['nama_mk', 'kode_mk', 'sks', 'created_at'];

    public function toSearchResult(): array
    {
        return [
            'id'        => $this->id,
            'nama_mk'   => $this->nama_mk,
            'kode_mk'   => $this->kode_mk,
            'sks'       => $this->sks,
            'status_mk' => $this->status_mk,
            'created_at' => $this->created_at->format('j F Y'),
        ];
    }
}

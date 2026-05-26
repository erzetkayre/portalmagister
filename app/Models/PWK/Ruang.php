<?php

namespace App\Models\PWK;

use App\Helpers\Filterable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruang extends Model
{
    use Filterable, Sortable, SoftDeletes;

    protected $connection = 'pwk';
    protected $table = 'ref_ruang';

    protected $fillable = ['nama_ruang'];

    protected array $filterableColumns = [
        'search' => ['nama_ruang'],
    ];

    protected array $sortableColumns = ['nama_ruang', 'created_at'];

    public function toSearchResult(): array
    {
        return [
            'id'         => $this->id,
            'nama_ruang' => $this->nama_ruang,
            'created_at' => $this->created_at->format('j F Y'),
        ];
    }
}

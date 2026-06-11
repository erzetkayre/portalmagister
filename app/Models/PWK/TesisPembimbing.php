<?php

namespace App\Models\PWK;

use Illuminate\Database\Eloquent\Model;

class TesisPembimbing extends Model
{
    protected $connection = 'pwk';
    protected $table = 'tesis_pembimbing';

    protected $fillable = ['tesis_id', 'dosen_id', 'slot'];

    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}

<?php

namespace App\Models\PWK;

use Illuminate\Database\Eloquent\Model;

class TesisPengajuan extends Model
{
    protected $connection = 'pwk';
    protected $table = 'tesis_pengajuan';

    protected $fillable = ['tesis_id', 'dosen_id', 'slot', 'alasan'];

    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}

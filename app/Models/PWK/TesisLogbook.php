<?php

namespace App\Models\PWK;

use Illuminate\Database\Eloquent\Model;

class TesisLogbook extends Model
{
    protected $connection = 'pwk';
    protected $table = 'tesis_logbook';

    protected $fillable = [
        'tesis_id', 'proses_tesis', 'nomor_konsultasi',
        'tgl_konsultasi', 'ringkasan', 'catatan', 'file_konsultasi',
    ];

    protected $casts = [
        'tgl_konsultasi' => 'date',
    ];

    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }
}

<?php

namespace App\Models\PWK;

use Illuminate\Database\Eloquent\Model;

class TesisDokumen extends Model
{
    protected $connection = 'pwk';
    protected $table = 'tesis_dokumen';

    protected $fillable = [
        'tesis_id',
        'file_khs', 'file_krs', 'file_surat_permohonan', 'file_sk_pembimbing',
        'file_surat_kelayakan_pratesis', 'file_revisi_pratesis', 'file_laporan_pratesis',
        'file_surat_kelayakan_tesis_1',  'file_revisi_tesis_1',  'file_laporan_tesis_1',
        'file_surat_kelayakan_tesis_2',  'file_revisi_tesis_2',  'file_laporan_tesis_2',
    ];

    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }

    public function getFileUrl(string $field): ?string
    {
        return $this->$field ? asset('storage/' . $this->$field) : null;
    }
}

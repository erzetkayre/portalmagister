<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TesisDraft extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tesis_draft';

    protected $fillable = [
        'mhs_id',
        'us_judul',
        'us_abstrak',
        'us_dospem_satu',
        'us_dospem_dua',
        'ket_dospem_satu',
        'ket_dospem_dua',
        'tgl_pengajuan',
        'status',
        'file_khs',
        'file_krs',
        'file_sk_pembimbing',
        'file_tesis',
    ];

    protected $casts = [
        'tgl_pengajuan' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mhs_id');
    }

    public function dosenPembimbingSatu()
    {
        return $this->belongsTo(Dosen::class, 'us_dospem_satu');
    }

    public function dosenPembimbingDua()
    {
        return $this->belongsTo(Dosen::class, 'us_dospem_dua');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['text' => 'WAITING', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
            'approved' => ['text' => 'SETUJU', 'class' => 'bg-green-100 text-green-800 border-green-300'],
            'rejected' => ['text' => 'TOLAK', 'class' => 'bg-red-100 text-red-800 border-red-300'],
            default => ['text' => 'DRAFT', 'class' => 'bg-gray-100 text-gray-800 border-gray-300'],
        };
    }
}
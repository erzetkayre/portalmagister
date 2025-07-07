<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;
    protected $table = 'ref_mahasiswa';

    protected $fillable = [
        'nim',
        'nama_mhs',
        'angkatan',
        'status_mhs',
        'ipk',
        'sks',
        'gender',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;
    protected $connection = 'pwk';
    protected $table = 'ref_mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_mhs',
        'angkatan',
        'status_mhs',
        'ipk',
        'sks',
        'gender',
    ];

}

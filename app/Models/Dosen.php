<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dosen extends Model
{
    use HasFactory;
    protected $table = 'ref_dosen';

    protected $fillable = [
        'nip',
        'kode_dosen',
        'nama_dosen',
        'status_dosen',
        'bidang_keahlian',
        'gender',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

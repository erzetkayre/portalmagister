<?php

namespace App\Models\PWK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tesis extends Model
{
    use SoftDeletes;

    protected $connection = 'pwk';
    protected $table = 'tesis';

    protected $fillable = [
        'mhs_id', 'judul', 'abstrak', 'status', 'proses_tesis',
        'tgl_pengajuan', 'tgl_setuju',
    ];

    protected $casts = [
        'tgl_pengajuan' => 'datetime',
        'tgl_setuju'    => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mhs_id');
    }

    public function pengajuan()
    {
        return $this->hasMany(TesisPengajuan::class)->orderBy('slot');
    }

    public function pembimbing()
    {
        return $this->hasMany(TesisPembimbing::class)->orderBy('slot');
    }

    public function logbook()
    {
        return $this->hasMany(TesisLogbook::class);
    }

    public function dokumen()
    {
        return $this->hasOne(TesisDokumen::class);
    }

    public function ujian()
    {
        return $this->hasMany(UjianTesis::class);
    }

    public function ujianByJenis(string $jenis): ?UjianTesis
    {
        return $this->ujian()->where('jenis_ujian', $jenis)->first();
    }

    public function logbookCountByProses(string $proses): int
    {
        return $this->logbook()->where('proses_tesis', $proses)->count();
    }

    public function toSearchResult(): array
    {
        return [
            'id'            => $this->id,
            'judul'         => $this->judul,
            'abstrak'       => $this->abstrak,
            'status'        => $this->status,
            'proses_tesis'  => $this->proses_tesis,
            'tgl_pengajuan' => $this->tgl_pengajuan?->translatedFormat('j F Y'),
            'tgl_setuju'    => $this->tgl_setuju?->translatedFormat('j F Y'),
        ];
    }
}

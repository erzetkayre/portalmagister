<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sempro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tesis_ujian_proposal';

    protected $fillable = [
        'tesis_id',
        'tanggal',
        'tempat',
        'jam_mulai',
        'jam_selesai',
        'status_seminar',
        'kartu_bimbingan',
        'draft_semhas',
        'surat_kelayakan',
        'tgl_upload_surat',
        'summary',
        'catatan',
        'berita_acara',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'tgl_upload_surat' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship with TesisDraft
     */
    public function tesis()
    {
        return $this->belongsTo(TesisDraft::class, 'tesis_id');
    }

    /**
     * Get mahasiswa through tesis relationship
     */
    public function mahasiswa()
    {
        return $this->hasOneThrough(Mahasiswa::class, TesisDraft::class, 'id', 'id', 'tesis_id', 'mhs_id');
    }

    /**
     * Get dosen pembimbing satu through tesis
     */
    public function dosenPembimbingSatu()
    {
        return $this->hasOneThrough(
            Dosen::class,
            TesisDraft::class,
            'id', // Foreign key on tesis table
            'id', // Foreign key on dosen table
            'tesis_id', // Local key on tesis_ujian table
            'us_dospem_satu' // Local key on tesis table
        );
    }

    /**
     * Get dosen pembimbing dua through tesis
     */
    public function dosenPembimbingDua()
    {
        return $this->hasOneThrough(
            Dosen::class,
            TesisDraft::class,
            'id', // Foreign key on tesis table
            'id', // Foreign key on dosen table
            'tesis_id', // Local key on tesis_ujian table
            'us_dospem_dua' // Local key on tesis table
        );
    }

    /**
     * Scope for status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_seminar', $status);
    }

    /**
     * Scope for kartu uploaded status
     */
    public function scopeKartuUploaded($query)
    {
        return $query->where('status_seminar', 'kartu_uploaded');
    }

    /**
     * Scope for waiting status
     */
    public function scopeWaiting($query)
    {
        return $query->where('status_seminar', 'waiting');
    }

    /**
     * Scope for approved status
     */
    public function scopeApproved($query)
    {
        return $query->where('status_seminar', 'approved');
    }

    /**
     * Scope for scheduled status
     */
    public function scopeScheduled($query)
    {
        return $query->where('status_seminar', 'scheduled');
    }

    /**
     * Scope for done status
     */
    public function scopeDone($query)
    {
        return $query->where('status_seminar', 'done');
    }

    /**
     * Check if ujian can be scheduled
     */
    public function canBeScheduled()
    {
        return $this->status_seminar === 'approved';
    }

    /**
     * Check if kartu bimbingan is uploaded
     */
    public function hasKartuBimbingan()
    {
        return !empty($this->kartu_bimbingan);
    }

    /**
     * Check if surat kelayakan is uploaded
     */
    public function hasSuratKelayakan()
    {
        return !empty($this->surat_kelayakan);
    }

    /**
     * Check if schedule is set
     */
    public function hasSchedule()
    {
        return !empty($this->tanggal) && !empty($this->tempat) && !empty($this->jam_mulai) && !empty($this->jam_selesai);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status_seminar) {
            case 'kartu_uploaded':
                return 'Kartu Bimbingan Diupload';
            case 'waiting':
                return 'Menunggu Persetujuan Admin';
            case 'approved':
                return 'Disetujui - Atur Jadwal';
            case 'scheduled':
                return 'Jadwal Telah Diatur';
            case 'done':
                return 'Selesai';
            case 'rejected':
                return 'Ditolak';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status_seminar) {
            'kartu_uploaded' => ['text' => 'Kartu Uploaded', 'variant' => 'secondary'],
            'waiting' => ['text' => 'Menunggu Review', 'variant' => 'secondary'],
            'approved' => ['text' => 'Disetujui - Atur Jadwal', 'variant' => 'default'],
            'scheduled' => ['text' => 'Jadwal Diatur', 'variant' => 'default'],
            'done' => ['text' => 'Selesai', 'variant' => 'default'],
            'rejected' => ['text' => 'Ditolak', 'variant' => 'destructive'],
            default => ['text' => 'Draft', 'variant' => 'outline'],
        };
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d F Y') : null;
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeAttribute()
    {
        if ($this->jam_mulai && $this->jam_selesai) {
            return $this->jam_mulai->format('H:i') . ' - ' . $this->jam_selesai->format('H:i');
        }
        return null;
    }
}

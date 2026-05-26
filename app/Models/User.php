<?php

namespace App\Models;

use App\Helpers\Sortable;
use App\Helpers\Filterable;
use App\Models\StudyProgram;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Sortable, Filterable;

    protected $connection = 'main';
    protected $fillable = [
        'name',
        'email',
        'nomor_induk',
        'email_verified_at',
        'password',
        'first_login',
        'is_active',
        'study_program_id',
        'photo',
        'signature',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'first_login' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected array $filterableColumns = [
        'search' => [
            'name',
            'email',
            'nomor_induk',
        ],
        'is_active' => 'is_active',
    ];

    protected array $sortableColumns = [
        'name',
        'is_active',
        'nomor_induk',
        'created_at',
    ];

    public function toSearchResult(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'nomor_induk' => $this->nomor_induk,
            'roles' => $this->roles->map(fn ($r) => [
                'id' => $r->id,
                'role_name' => $r->role_name,
            ]),
            'created_at' => $this->created_at->format('j F Y'),
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole(string $role)
    {
        return $this->roles()
            ->where('role_name', $role)
            ->exists();
    }

    public function getDbConnection(): string
    {
        return $this->studyProgram->db_connection;
    }

    public function mahasiswaProfile(): ?\Illuminate\Database\Eloquent\Model
    {
        $class = match ($this->getDbConnection()) {
            'elektro' => \App\Models\Elektro\Mahasiswa::class,
            'pwk' => \App\Models\PWK\Mahasiswa::class,
            default => null,
        };
        return $class ? $class::where('user_id', $this->id)->first() : null;
    }

    public function dosenProfile(): ?\Illuminate\Database\Eloquent\Model
    {
        $class = match ($this->getDbConnection()) {
            'elektro' => \App\Models\Elektro\Dosen::class,
            'pwk' => \App\Models\PWK\Dosen::class,
            default => null,
        };
        return $class ? $class::where('user_id', $this->id)->first() : null;
    }

    public function scopeByStudyProgram($query, $program)
    {
        return $query->where('study_program_id', $program);
    }

    public function scopeFilterRole($query, $role)
    {
        if (!$role) return $query;

        return $query->whereHas('roles', fn ($q) =>
            $q->where('role_name', $role)
        );
    }

}

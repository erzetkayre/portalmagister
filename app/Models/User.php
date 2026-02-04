<?php

namespace App\Models;

use App\Helpers\Sortable;
use App\Helpers\Filterable;
use App\Models\StudyProgram;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, Sortable, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

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
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
        return $this->roles->contains('role_name', $role);
    }

    public function scopeByStudyProgram($query, $program)
    {
        return $query->where('study_program_id', $program);
    }

}

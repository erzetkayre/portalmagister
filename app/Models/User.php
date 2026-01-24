<?php

namespace App\Models;

use App\Models\StudyProgram;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nama_role',
        'deskripsi',
    ];

    /**
     * Get all users that belong to this role.
     */
    public function users()
    {
        return $this->hasOne(User::class, 'role_id');
    }
}

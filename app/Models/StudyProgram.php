<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    protected $connection = 'main';
    protected $table = 'study_programs';


    protected $fillable = [
        'id',
        'program_name',
        'description',
        'db_connection'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}

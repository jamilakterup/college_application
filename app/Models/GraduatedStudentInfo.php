<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraduatedStudentInfo extends Model
{
    protected $table = 'graduated_student_info';

    protected $fillable = [
        'user_id',
        'name',
        'father_name',
        'mother_name',
        'class_roll',
        'hsc_roll',
        'session',
        'institution_name',
        'mobile',
        'photo'
    ];
}

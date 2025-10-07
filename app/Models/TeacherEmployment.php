<?php

namespace App\Models;

use App\Models\TeacherPersonal;
use Illuminate\Database\Eloquent\Model;

class TeacherEmployment extends Model
{
    protected $table = 'teacher_employment';

    public function teacherPersonal(){
        return $this->hasOne(TeacherPersonal::class, 'id', 'id');
    }
}

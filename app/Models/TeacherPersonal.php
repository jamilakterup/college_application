<?php

namespace App\Models;

use App\Models\TeacherEducation;
use App\Models\TeacherEmployment;
use Illuminate\Database\Eloquent\Model;

class TeacherPersonal extends Model
{
    protected $table = 'teacher_personal';
    public $timestamps = false;

    public function teacherEmployment(){
        return $this->hasOne(TeacherEmployment::class, 'id', 'id');
    }

    public function teacherEducation(){
        return $this->hasOne(TeacherEducation::class, 'id', 'id');
    }

    public function teacherAppointment(){
        return $this->hasOne(TeacherAppointment::class, 'id', 'id');
    }

    public function teacherCareer(){
        return $this->hasOne(TeacherCareer::class, 'id', 'id');
    }
}

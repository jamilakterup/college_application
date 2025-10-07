<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfoDegree extends Model
{
    protected $table = 'student_info_degree';
	public $timestamps = false;	

    public function admitted_student(){
        return $this->belongsTo('App\Models\DegreeAdmittedStudent', 'refference_id', 'auto_id')->withDefault(function(){
            return new DegreeAdmittedStudent();
        });
    }
}

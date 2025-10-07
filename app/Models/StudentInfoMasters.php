<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfoMasters extends Model
{
    protected $table = 'student_info_masters';
	public $timestamps = false;	

    public function admitted_student(){
        return $this->belongsTo('App\Models\MastersAdmittedStudent', 'refference_id', 'auto_id')->withDefault(function(){
            return new MastersAdmittedStudent();
        });
    }
}

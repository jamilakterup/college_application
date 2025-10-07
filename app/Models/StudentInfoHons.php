<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfoHons extends Model
{
    protected $table = 'student_info_hons';
	public $timestamps = false;	

    public function admitted_student(){
        return $this->belongsTo('App\Models\HonsAdmittedStudent', 'refference_id', 'auto_id')->withDefault(function(){
            return new HonsAdmittedStudent();
        });
    }
}

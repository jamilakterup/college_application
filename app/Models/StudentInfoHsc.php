<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfoHsc extends Model
{
    protected $table = 'student_info_hsc';
	public $timestamps = false;

    public function admitted_student(){
        return $this->belongsTo('App\Models\HscAdmittedStudent', 'refference_id', 'auto_id')->withDefault(function(){
            return new HscAdmittedStudent();
        });
    }
}

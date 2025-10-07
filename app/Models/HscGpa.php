<?php

namespace App\Models;

use App\Models\GradeScale;
use Illuminate\Database\Eloquent\Model;

class HscGpa extends Model
{
    protected $table = 'hsc_cgpa';	

	public function student()
	{
		return $this->belongsTo('App\Models\StudentInfoHsc','student_id');
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group','group_id');
	}

	public function exam()
	{
		return $this->belongsTo('App\Models\Exam','exam_id');
	}

	public function subMark() {
        return $this->hasMany(StudentSubMarkGp::class, 'student_id', 'student_id');
    }

    public function gadeScale(){
    	return $this->belongsTo(GradeScale::class, 'grade', 'letter_grade');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubMarkGp extends Model
{
    protected $table = 'student_sub_mark_gp';

    protected $guarded = [];
    
	public function student()
	{
		return $this->belongsTo('App\Models\StudentInfoHsc','student_id')->withDefault();
	}

	public function subject()
	{
		return $this->belongsTo('App\Models\Subject','subject_id')->withDefault();
	}

	public function exam()
	{
		return $this->belongsTo('App\Models\Exam','exam_id')->withDefault();
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group','group_id')->withDefault();
	}
}

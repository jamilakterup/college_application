<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTestMark extends Model
{
    protected $table = 'class_test_marks';	

	public function student()
	{
		return $this->belongsTo('App\Models\StudentInfoHsc','student_id');
	}

	public function subject()
	{
		return $this->belongsTo('App\Models\Subject','subject_id');
	}

	public function exam()
	{
		return $this->belongsTo('App\Models\Exam','exam_id');
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group','group_id');
	}

	public function particle()
	{
		return $this->belongsTo('App\Models\Xmparticle','particle_id');
	}
}

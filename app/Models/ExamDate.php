<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamDate extends Model
{
    protected $table = 'exam_date';
	public $timestamps = false;
	protected $fillable = ['class_id', 'group_id','exam_id','session','exam_year', 'subject_id','date'];



	//ELoquent Relationship
	public function classe() {
		return $this->belongsTo('Classe','classe_id');
	}

	public function group() {
		return $this->belongsTo('Group','group_id');
	}

	public function exam() {
		return $this->belongsTo('Exam','exam_id');
	}

	public function subject() {
		return $this->belongsTo('Subject','subject_id');
	}
}

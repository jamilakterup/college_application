<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassExam extends Model
{
    protected $table = 'class_exam';
	public $timestamps = false;
	protected $fillable = ['classe_id', 'exam_id'];



	//Create ClasseExam Rules & Validation
	public static $rules = ['classe_id'	=> 'required|integer',
							'exam_id'	=> 'required|integer'];

	public static function validate($data) {

			return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function classe() {
		return $this->belongsTo('App\Models\Classe');		
	}	

	public function exam() {
		return $this->belongsTo('App\Models\Exam');
	}
}

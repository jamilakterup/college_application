<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTestExam extends Model
{
    protected $table = 'class_test_assign';
	public $timestamps = false;
	protected $fillable = ['class_test_id', 'exam_id'];



	//Create ClasseExam Rules & Validation
	public static $rules = ['class_test_id'	=> 'required|integer',
							'exam_id'	=> 'required|integer'];

	public static function validate($data) {

			return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function classe() {
		return $this->belongsTo('App\Models\Classe');		
	}	

	public function exam() {
		return $this->belongsTo('App\Models\ClassTest','class_test_id');
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Exam extends Model
{
    protected $table = 'exams';
	public $timestamps = false;
	protected $fillable = ['name'];

	//Create Exam Rules & Validation
	public static $rules = ['name' => 'required|unique:exams'];

	public static function validate($data) {
	
		return Validator::make($data, self::$rules);

	}

	//Update Exam Rules & Validation
	public static function updateValidate($data) {
		
		$id = $data['id'];
		$rules = ["name" => "required|unique:exams,name,$id"];

		return Validator::make($data, $rules);
	}
	
	//Eloquent Relationship
	public function classeexams() {
		return $this->hasMany('App\Models\ClasseExam');
	}	

	public function configmarks() {
		return $this->hasMany('App\Models\ConfigMark');
	}	

	public function configmerits() {
		return $this->hasMany('App\Models\ConfigMerit');
	}

	public function publishresults() {
		return $this->hasMany('App\Models\PublishResult');
	}
}

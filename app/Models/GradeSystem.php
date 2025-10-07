<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSystem extends Model
{
    protected $table = 'grade_systems';
	public $timestamps = false;
	protected $fillable = ['name', 'status'];



	//Create GradeSystem Rules & Validation
	public static $rules = ['name' => 'required|unique:grade_systems', 'status' => 'boolean'];

	



	//Update GradeSystem Rules & Validation
	public static function updateValidate($data) {

		$verifier = App::make('validation.presence');
		$verifier->setConnection('school');

		$id = $data['id'];

		$rules = ["name" => "required|unique:grade_systems,name,$id", "status" => "boolean"];

		return Validator::make($data, $rules);

	}



	//Eloquent Relationship
	public function gradescales() {
		return $this->hasMany('App\Models\GradeScale');
	}
}

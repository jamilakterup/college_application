<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Subject extends Model
{
    protected $table = 'subjects';
	protected $fillable = ['name','code'];



	//Create Subject Rules and Validation
	public static $rules = ['name' => 'required|min:2',
							'code'=>'required|unique:subjects'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Update Subject Rules and Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["name" => "required|min:2",
				  "code"=>"required|unique:subjects,code,$id"];

		return Validator::make($data, $rules);

	}



	//Eloquent Relationship
	public function msubjects() {

		return $this->hasMany('App\Models\Msubject');

	}
}

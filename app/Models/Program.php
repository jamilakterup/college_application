<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Program extends Model
{
    protected $table = 'programs';
	protected $fillable = ['name', 'short_name', 'timeline'];



	//Create Program Ruels and Validation
	public static $rules = ['code'		=> 'required|unique:programs',
							'name' 		=> 'required|min:2|unique:programs', 
							'short_name'=> 'required|min:2', 
							'timeline' 	=> 'required|numeric'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);
		
	}



	//Update Program Rules and Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["code"		=> "required|unique:programs,code,$id",
				  "name" 		=> "required|min:2|unique:programs,name,$id", 
				  "short_name" 	=> "required|min:2",				  
				  "timeline" 	=> "required|numeric"];

		return Validator::make($data, $rules);
		
	}


	//Eloquent Relationship
	public function deptprograms() {

		return $this->hasMany('App\Models\DeptProgram');

	}

	public function courses() {

		return $this->hasMany('App\Models\Course');

	}

	public function admissions() {

		return $this->hasMany('App\Models\Admission');

	}
}

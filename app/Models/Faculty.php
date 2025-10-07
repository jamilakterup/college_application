<?php

namespace App\Models;

use App\Models\Department;
use App\Models\FacultyHead;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Faculty extends Model
{
    protected $table = 'faculties';
	protected $fillable = ['faculty_code', 'faculty_name', 'short_name'];



	//Create Faculty Rules and Validation
	public static $rules = ['faculty_code' 	=> 'required|unique:faculties',
							'faculty_name'	=> 'required|unique:faculties',
							'short_name'	=> 'required|min:2'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Update Faculty Rules and validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["faculty_code" 	=> "required|unique:faculties,faculty_code,$id",
				  "faculty_name" 	=> "required|unique:faculties,faculty_name,$id",
				  "short_name"		=> "required|min:2"];

		return Validator::make($data, $rules);

	}	



	//Eloquent Relationship
	public function facultyheads() {

		return $this->hasMany(FacultyHead::class);

	}

	public function departments() {

		return $this->hasMany(Department::class);
		
	}
}

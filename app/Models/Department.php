<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Department extends Model
{
    protected $table = 'departments';
	protected $fillable = ['faculty_id', 'dept_code', 'dept_name', 'short_name', 'seat'];



	//Create Department Rules and Validation
	public static $rules = ['faculty_id' 	=> 'required|integer',
							'dept_code'		=> 'required|unique:departments',
							'dept_name'		=> 'required|min:2',
							'short_name'	=> 'required',
							'seat'			=> 'required|numeric|min:1'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Update department rules and validation	
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["faculty_id" 	=> "required|integer",
				  "dept_code"	=> "required|unique:departments,dept_code,$id",
				  "dept_name"	=> "required|min:2",
				  "short_name"	=> "required",
				  "seat"		=> "required|numeric|min:1"];

		return Validator::make($data, $rules);	

	}
	


	//Eloquent Relationship
	public function faculty() {

		return $this->belongsTo('App\Models\Faculty');

	}

	public function deptheads() {

		return $this->hasMany('App\Models\DeptHead');

	}

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

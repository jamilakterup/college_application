<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Course extends Model
{
    protected $table = 'courses';
	protected $fillable = ['department_id', 'program_id', 'code', 'name', 'mark', 'type', 'level', 'session'];



	//Create Course Rules and Validation
	public static $rules = ['department_id'	=> 'required|integer',
							'program_id'	=> 'required|integer',
							'code'			=> 'required',
							'name'			=> 'required',
							'mark'			=> 'required|integer',
							'type'			=> 'required|boolean',
							'level'			=> 'required|integer|min:1|max:10',
							'session'		=> 'required'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Eloquent relationship
	public function department() {

		return $this->belongsTo('App\Models\Department');

	}

	public function program() {

		return $this->belongsTo('App\Models\Program');

	}

	public function courseteachers() {

		return $this->hasMany('App\Models\CourseTeacher');

	}
}

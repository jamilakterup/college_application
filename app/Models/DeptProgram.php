<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class DeptProgram extends Model
{
    protected $table = 'deptprograms';
	protected $fillable = ['department_id', 'program_id'];


	//Create Department Program Rules and Validation
	public static $rules = ['department_id' => 'required|integer', 
							'program_id' 	=> 'required|integer',
							'status'		=> 'boolean'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Eloquent Relationship
	public function department() {

		return $this->belongsTo('App\Models\Department');

	}

	public function program() {

		return $this->belongsTo('App\Models\Program');
		
	}
}

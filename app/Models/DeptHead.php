<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class DeptHead extends Model
{
    protected $table = 'deptheads';
	protected $fillable = ['department_id', 'name', 'starting_date', 'end_date', 'status'];



	//Create Department Head Rules and Validation
	public static $rules = ['department_id'		=> 'required|integer',
							'name'				=> 'required',
							'starting_date'		=> 'required|date',
							'end_date'			=> 'required|date',
							'status'			=> 'required|boolean'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	
	


	//Eloquent Relationship
	public function department() {

		return $this->belongsTo('App\Models\Department');
		
	}
}

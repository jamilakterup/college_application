<?php

namespace App\Models;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Model;
use Validator;

class FacultyHead extends Model
{
    protected $table = 'facultyheads';
	protected $fillable = ['faculty_id', 'name', 'status', 'starting_date', 'end_date'];



	//Create Faculty Head Rules and Validation
	public static $rules = ['faculty_id' 	=> 'required|integer', 
							'name'			=> 'required',
							'status' 		=> 'boolean', 
							'starting_date'	=> 'required|date',
							'end_date'		=> 'required|date'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function faculty() {

		return $this->belongsTo(Faculty::class);

	}
}

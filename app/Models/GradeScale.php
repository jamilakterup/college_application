<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $table = 'grade_scales';
	public $timestamps = false;
	protected $fillable = ['gradesystem_id', 'letter_grade', 'grade_number', 'point', 'range_low', 'range_high', 'gpa_low', 'gpa_high'];



	//Create GradeScale Rules & Validation
	public static $rules = ['gradesystem_id'	=> 'required|integer',
							'letter_grade'		=> 'required',
							'grade_number'		=> 'required|numeric',
							'point'				=> 'required|numeric',
							'range_low'			=> 'required|numeric',
							'range_high'		=> 'required|numeric',
							'gpa_low'			=> 'required|numeric',
							'gpa_high'			=> 'required|numeric'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}		



	//Eloquent Relationship
	public function gradesystem() {
		return $this->belongsTo('App\Models\GradeSystem');
	}
}

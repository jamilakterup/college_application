<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $table = 'admissions';
	protected $fillable = ['department-id', 'program_id', 'paysliptitle_id','slip_type', 'session', 'open_date', 'close_date', 'status'];



	//Create Admission Rules and Validation
	public static $rules = ['department_id'		=> 'required',
							'program_id'		=> 'required|integer',
							'paysliptitle_id'	=> 'required|integer',							
							'session'			=> 'required',
							'slip_type'			=> 'required',
							'open_date'			=> 'required|date',
							'close_date'		=> 'required|date',
							'status'			=> 'boolean'];

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

	public function paysliptitle() {

		return $this->belongsTo('App\Models\PayslipTitle');

	}

	public function admissionRequirements() {

		return $this->hasMany('App\Models\AdmissionRequirement');

	}
}

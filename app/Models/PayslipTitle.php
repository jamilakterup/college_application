<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class PayslipTitle extends Model
{
    protected $table = 'paysliptitles';
	protected $fillable = ['title', 'status'];



	//Create PayslipTitle Rules and Validation
	public static $rules = ['title'		=> 'required|unique:paysliptitles|max:255',
							'status'	=> 'boolean'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Update PayslipTitle Rules and Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["title"	=> "required|unique:paysliptitles,title,$id|max:255",
				  "status"	=> "boolean"];

		return Validator::make($data, $rules);

	}	



	//Eloquent Relationship
	public function admissions() {

		return $this->hasMany('App\Models\Admission');

	}

	public function payslipgenerators() {

		return $this->hasMany('App\Models\PayslipGenerator');

	}

	public function payslipheader() {

		return $this->belongsTo('App\Models\PayslipHeader', 'payslipheader_id');

	}
}

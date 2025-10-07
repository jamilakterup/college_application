<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class PayslipHeader extends Model
{
    protected $table = 'payslipheaders';
	protected $fillable = ['title'];


	//Create PayslipHeader Rules and Validation
	// public static $rules = ['title'	=> 'required|unique:payslipheaders'];

	public static function validateRules($data) {
		$rules = [
			'title'	=> 'required|unique:payslipheaders',
			'start_date'=>'required|before:'.$data['end_date'],
			'end_date'=>'required|after:'.$data['start_date'],
			'type' => 'required',
			'level' => 'required'
		];
	}

	public static function validate($data) {
		$rules = [
			'title'	=> 'required|unique:payslipheaders',
			'start_date'=>'required|before:'.$data['end_date'],
			'end_date'=>'required|after:'.$data['start_date'],
			'type' => 'required',
			'level' => 'required',
			// 'exam_year' => 'required'

		];
		return Validator::make($data, $rules);
	}



	//Update PayslipHeader Rules and Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = [
				"title"	=> "required|unique:payslipheaders,title,$id",
				'start_date'=>'required|before:'.$data['end_date'],
				'end_date'=>'required|after:'.$data['start_date'],
				'type' => 'required',
				'level' => 'required',
				// 'exam_year' => 'required'
				];

		return Validator::make($data, $rules);

	}

	public static function updateValidateRules($data) {

		$id = $data['id'];

		$rules = [
				"title"	=> "required|unique:payslipheaders,title,$id",
				'start_date'=>'required|before:'.$data['end_date'],
				'end_date'=>'required|after:'.$data['start_date'],
				'type' => 'required',
				'level' => 'required',
				// 'exam_year' => 'required'
				];
	}



	//Eloquent Relationship
	public function payslipitems() {

		return $this->hasMany('App\Models\PayslipItem', 'payslipheader_id');

	}

	public function payslipgenerators() {

		return $this->hasMany('App\Models\PayslipGenerator', 'payslipheader_id');

	}
}

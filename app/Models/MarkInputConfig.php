<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MarkInputConfig extends Model
{
    protected $table = 'mark_input_config';
	public $timestamps = false;
	protected $fillable = ['exam_id', 'session', 'exp_date'];
	

	//Create Course Rules and Validation
	public static $rules = ['session'	=> 'required',
							'exam_year'	=> 'required',
							'exam_id'	=> 'required',
							'exp_date'	=> 'required'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}


	//Update Department Rules & Validation
	public static function updateValidate($data) {

		$exam_id = $data['exam_id'];
		$rules = [
			"exam_id" => "required|unique:mark_input_config,exam_id,$exam_id",
			'session'	=> 'required',
			'exam_year'	=> 'required',
			'exp_date'	=> 'required'];

		return Validator::make($data, $rules);
		
	}
}

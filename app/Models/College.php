<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class College extends Model
{

    protected $table = 'colleges';
	protected $fillable = ['college_name', 'college_name_bengali', 'area_name', 'area_name_bengali', 'college_code', 'phone', 'establish_date', 'status', 'website', 'biller_id'];



	//Create College Rules and Validation
	public static $rules = ['college_name'			=> 'required|min:3',
							'college_name_bengali'	=> 'required|min:3',
							'logo'					=> 'image|required|mimes:jpg,jpeg,bmp,png|max:150',
							'website'				=> 'required|url',
							'biller_id'				=> 'alpha_dash',
							'area_name'				=> 'required',
							'area_name_bengali'		=> 'required',
							'college_code'			=> 'required',
							'phone'					=> 'required',
							'establish_date'		=> 'required|date',
							'status'				=> 'boolean'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Update College Rules and Validation					
	public static $update_rules =  ['college_name'			=> 'required|min:3',
									'college_name_bengali'	=> 'required|min:3',
									'logo'					=> 'image|mimes:jpg,jpeg,bmp,png|max:150',
									'website'				=> 'required|url',
									'biller_id'				=> 'alpha_dash',								
									'area_name'				=> 'required',
									'area_name_bengali'		=> 'required',
									'college_code'			=> 'required',
									'phone'					=> 'required',
									'establish_date'		=> 'required|date',
									'status'				=> 'boolean'];

	public static function updateValidate($data) {

		return Validator::make($data, self::$update_rules);

	}
}

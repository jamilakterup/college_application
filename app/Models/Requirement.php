<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Requirement extends Model
{
    protected $table = 'requirements';
	protected $fillable = ['certificate_full_name', 'certificate_short_name'];



	//Create Requirement Rules and Validation
	public static $rules = ['certificate_full_name'		=> 'required|unique:requirements',
							'certificate_short_name'	=> 'required|unique:requirements'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Update Requirement Rules and Validation
	public static function updateValidate($data) {

		$id = $data['id'];	
		
		$rules = ["certificate_full_name"		=> "required|unique:requirements,certificate_full_name,$id",
				  "certificate_short_name"		=> "required|unique:requirements,certificate_short_name,$id"];			

		return Validator::make($data, $rules);

	}


	
	//Eloquent Relationship
	public function admissionRequirements() {

		return $this->hasMany('App\Models\AdmissionRequirement');

	}
}

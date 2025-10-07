<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Xmparticle extends Model
{
    protected $table = 'xmparticles';
	public $timestamps = false;
	protected $fillable = ['name', 'short_name', 'total', 'pass'];



	//Create Xmparticle Rules & Validation
	public static $rules = ['name'		=> 'required|unique:xmparticles',
							'short_name'=> 'required|unique:xmparticles',
							'total'		=> 'required|integer|min:1',
							'pass'		=> 'required|integer'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Update Xmparticles Rules & Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["name"		=> "required|unique:xmparticles,name,$id",
				  "short_name"	=> "required|unique:xmparticles,short_name,$id",
				  "total"		=> "required|integer",
				  "pass"		=> "required|integer"];

		return Validator::make($data, $rules);		  

	}	



	//Eloquent Relationship
	public function configexamparticles() {
		return $this->hasMany('ConfigExamParticle');
	}	

	public function marks() {
		return $this->hasMany('Mark');
	}
}

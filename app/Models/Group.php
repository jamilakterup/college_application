<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Group extends Model
{
    protected $table = 'groups';
	public $timestamps = false;
	protected $fillable = ['name'];



	//Create Department Rules & Validation
	public static $rules = ['name' => 'required|unique:groups'];

	public static function validate($data) {

		    	return Validator::make($data, self::$rules);

	}



	//Update Department Rules & Validation
	public static function updateValidate($data) {

			$id = $data['id'];

		$rules = ["name" => "required|unique:groups,name,$id"];

		return Validator::make($data, $rules);
		
	}



	//Eloquent Relationship
	public function classedepartments() {
		return $this->hasMany('App\Models\ClasseDepartment');
	}

	public function academicinfos() {
		return $this->hasMany('App\Models\AcedemicInfo');
	}	

	public function classesections() {
		return $this->hasMany('App\Models\ClasseSection');
	}

	public function classeshifts() {
		return $this->hasMany('App\Models\ClasseShift');
	}

	public function classesubjects() {
		return $this->hasMany('App\Models\ClasseSubject');
	}		

	public function configexamparticles() {
		return $this->hasMany('App\Models\ConfigExamParticle');
	}		

	public function configmarks() {
		return $this->hasMany('App\Models\ConfigMark');
	}	

	public function configmerits() {
		return $this->hasMany('App\Models\ConfigMerit');
	}
}

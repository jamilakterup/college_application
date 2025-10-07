<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Classe extends Model
{
    protected $table = 'classes';
	public $timestamps = false;
	protected $fillable = ['name'];



	//Create Class Rules & Validation
	public static $rules = ['name'					=> 'required|unique:classes',
							];

	public static function validate($data) {

		
	

		return Validator::make($data, self::$rules);

	}		



	//Update Class Rules & Validation
	public static function updateValidate($data) {

	
		$id = $data['id'];

		$rules = ["name"					=> "required|unique:classes,name,$id",
				  ];

		return Validator::make($data, $rules);

	}



	public function classedepartments() {
		return $this->hasMany('App\Models\ClassGroup');
	}	

		//Eloquent Relationship
/*public function classesections() {
		return $this->hasMany('ClasseSection');
	}	

	public function classeshifts() {
		return $this->hasMany('ClasseShift');
	}

	public function academicinfos() {
		return $this->hasMany('AcedemicInfo');
	}		

	public function classesubjects() {
		return $this->hasMany('ClasseSubject');
	}	

	public function classeexams() {
		return $this->hasMany('ClasseExam');
	}	

	public function configexamparticles() {
		return $this->hasMany('ConfigExamParticle');
	}

	public function configmarks() {
		return $this->hasMany('ConfigMark');
	}

	public function configmerits() {
		return $this->hasMany('ConfigMerit');
	}	

	public function passpercentagevalue() {
		return $this->hasOne('PassPercentageValue');
	}

	public function publishresults() {
		return $this->hasMany('PublishResult');
	}
*/
}

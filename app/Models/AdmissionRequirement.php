<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionRequirement extends Model
{
    protected $table = 'admissionrequirements';
	protected $fillable = ['admission_id', 'requirement_id'];



	//Create AdmissionRequirement Rules And validation
	public static $rules = ['admission_id'		=> 'integer',
							'requirement_id'	=> 'integer'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}						



	//Eloquent Relationship
	public function admission() {

		return $this->belongsTo('App\Models\Admission');

	}

	public function requirement() {

		return $this->belongsTo('App\Models\Requirement');

	}
}

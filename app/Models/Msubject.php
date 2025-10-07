<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Msubject extends Model
{
    protected $table = 'msubjects';
	protected $fillable = ['material_id', 'subject_id'];



	//Create Msubject Rules & Validation
	public static $rules = ['material_id'	=> 'required|integer',
							'subject_id'	=> 'required|integer'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Eloquent Relationship
	public function material() {

		return $this->belongsTo('App\Models\Material');

	}

	public function subject() {

		return $this->belongsTo('App\Models\Department','subject_id');

	}
}

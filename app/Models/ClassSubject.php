<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    protected $table = 'class_subject';
	public $timestamps = false;
	protected $fillable = ['classe_id', 'group_id', 'subject_id'];


	//Create ClasseSubject Rules & Validation
	public static $rules = ['classe_id'		=> 'required|integer',
							'group_id'	=> 'required|integer',
							'subject_id'	=> 'required|integer'];

	public static function validate($data) {

		

		return Validator::make($data, self::$rules);

	}

	//ELoquent Relationship
	public function classe() {
		return $this->belongsTo('App\Models\Classe');
	}

	public function department() {
		return $this->belongsTo('App\Models\Group');
	}

	public function subject() {
		return $this->belongsTo('App\Models\Subject','subject_id');
	}
}

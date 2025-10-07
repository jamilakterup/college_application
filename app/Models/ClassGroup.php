<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    protected $table = 'class_group';
	public $timestamps = false;
	protected $fillable = ['classe_id','group_id'];



	//Create ClasseDepartment Rules & Validation
	public static $rules = ['classe_id'		=> 'required|integer',
							'group_id'	=> 'required|integer'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function classe() {
		return $this->belongsTo('App\Models\Classe');
	}

	public function group() {
		return $this->belongsTo('App\Models\Group','group_id');
	}
}

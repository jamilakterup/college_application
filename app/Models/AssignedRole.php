<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedRole extends Model
{
    protected $table = 'assigned_roles';
	public $timestamps = false;
	protected $fillable = ['user_id', 'role_id'];



	//Create AssignRole Rules & Validation
	public static $rules = ['user_id'	=> 'required|integer',
							'role_id'	=> 'required|integer'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Eloquent Relationship
	public function user() {

		return $this->belongsTo('App\Models\User');

	}

	public function role() {

		return $this->belongsTo('App\Models\Role');

	}
}

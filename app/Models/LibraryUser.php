<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class LibraryUser extends Model
{
    protected $table = 'libraryusers';
	protected $fillable = ['user_type'];



	//Create Library User Rules & Validation
	public static $rules = ['user_type' => 'required|min:4|unique:libraryusers'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Update Library User Rules & Validation
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["user_type" => "required|min:4|unique:libraryusers,user_type,$id"];

		return Validator::make($data, $rules);

	}



	//Eloquent Relationship
	public function circulation() {

		return $this->hasOne('App\Models\Circulation');

	}

	public function libmembers() {
		return $this->hasMany('App\Models\Libmember');
	}
}

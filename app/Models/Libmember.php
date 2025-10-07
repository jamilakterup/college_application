<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Libmember extends Model
{
    protected $table = 'libmembers';
	protected $fillable = ['libraryuser_id', 'full_name', 'date_of_birth', 'contact_no', 'gender', 'photo', 'permanent_address', 'mailing_address'];



	//Create Library Member Rules and Validation
	public static $rules = ['libraryuser_id'	=> 'required|integer',
							'full_name'			=> 'required|min:3',
							'date_of_birth'		=> 'required|date',
							'contact_no'		=> 'required|min:11|unique:libmembers',
							'gender'			=> 'required|in:1,2']; // 1 = male, 2 = female

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Update Library Member Rules and Validation					
	public static function updateValidate($data) {

		$id = $data['id'];

		$rules = ["libraryuser_id"	=> "required|integer",
			      "full_name"		=> "required|min:3",
				  "date_of_birth"	=> "required|date",
				  "contact_no"		=> "required|min:11|unique:libmembers,contact_no,$id",
				  "gender"			=> "required|in:1,2"]; // 1 = male, 2 = female		

		return Validator::make($data, $rules);

	}



	//Eloquent Relationship
	public function libraryuser() {
		return $this->belongsTo('App\Models\LibraryUser');
	}

	public function libcirculations() {
		$this->hasMany('App\Models\Libcirculation');
	}
}

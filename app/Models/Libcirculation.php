<?php

namespace App\Models;
use Validator;
use Session;

use Illuminate\Database\Eloquent\Model;

class Libcirculation extends Model
{
    protected $table = 'libcirculations';
	protected $fillable = ['libmember_id', 'maccession_id', 'issue_date', 'return_date', 'member_return_date', 'status'];


	//Create Library Circulation Rules & Validation
	public static $rules = ['libmember_id'		=> 'required|integer',
							'maccession_id'		=> 'required|integer',
							'issue_date'		=> 'date',
							'return_date'		=> 'date',
							'member_return_date'=> 'date',
							'status'			=> 'integer']; // 1 = issued, 2 = returned

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Check Library Circulation Rules & Validation
	public static $check = ['libraryuser_id'	=> 'required|integer',
							'libmember_id'		=> 'required|integer'];

	public static function checkValidate($data) {

		return Validator::make($data, self::$check);

	}				



	//Custom Create Library Circulation Rules & Validation
	public static function customValidate($data) {

		$libraryuser_id = Session::has('libraryuser_id') ? Session::get('libraryuser_id') : NULL;
		$libmember_id = Session::has('libmember_id') ? Session::get('libmember_id') : NULL;

		$rules = ["libraryuser_id"		=> "integer|size:$libraryuser_id",
				  "libmember_id"		=> "required|integer|size:$libmember_id",
				  "accession_no"		=> "required|integer",
				  "issued_days"			=> "required|integer",
				  "overdue_amount"		=> "required|numeric"];		

		return Validator::make($data, $rules);

	}	



	//Update Library Circulation Rules & Validation
	public static $uRule = ['libmember_id'		=> 'required|integer',
							'accession_no'		=> 'required|integer',
							'issue_date'		=> 'required|date',
							'return_date'		=> 'required|date',
							'status'			=> 'integer']; // 1 = issued, 2 = returned

	public static function updateValidate($data) {

		return Validator::make($data, self::$uRule);

	}			



	//Eloquent Relationship
	public function libmember() {
		return $this->belongsTo('App\Models\Libmember');
	}	

	public function maccession() {
		return $this->belongsTo('App\Models\Maccession');
	}
}

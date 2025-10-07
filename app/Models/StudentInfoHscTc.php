<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfoHscTc extends Model
{
    protected $table = 'student_info_hsc_tc';
	public $timestamps = false;	
	//protected $fillable = ['permission_id', 'role_id'];



	//Create Permission Role Rules & Validation
	// public static $rules = ['permission_id'	=> 'required|integer',
	// 						'role_id'		=> 'required|integer'];

	// public static function validate($data) {

	// 	return Validator::make($data, self::$rules);

	// }	



	//Eloquent Relationship
	/*public function permission() {

		return $this->belongsTo('Permission');

	}

	public function role() {

		return $this->belongsTo('Role');

	}
*/
}

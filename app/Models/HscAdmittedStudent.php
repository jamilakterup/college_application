<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HscAdmittedStudent extends Model
{
    protected $table = 'hsc_admitted_students';
	public $timestamps = false;	
	protected $fillable = [

   'entry_time',
    'photo',
    'name',
    'compulsory',
    'selective',
    'optional',
    'hsc_group',
    'blood_group',
    'exam_name',
    'bangla_name',
    'PIN_number',
    'fathers_name',
    'mothers_name',
    'date_of_birth',
    'religion',
    'password',
    'sex',
    'guardian_name',
    'guardian_phone',
    'relation',
    'village',
    'post_office',
    'district',
    'upozilla',
    'mobile',
    'email',
    'permanent_village',
    'permanent_post_office',
    'permanent_thana',
    'permanent_district',
    'permanent_email',
    'ssc_roll',
    'ssc_passing_year',
    'ssc_board',
    'permanent_mobile',
    'income',
    'occupation',
    'admission_session',
    'ssc_gpa',

    'ssc_reg_no',
    'ssc_group',
    'ssc_institution',

	];



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

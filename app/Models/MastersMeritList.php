<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MastersMeritList extends Model
{
    protected $table = 'masters_merit_list';
    public $timestamps = false; 
    //protected $fillable = ['permission_id', 'role_id'];

    public static function validateRules($data) {
		$rules = [
			'admission_roll' => 'required|numeric',
			'name'=>'required',
			'current_level'=>'required',
			'session'=>'required',
			'admission_status' => 'required',
			'faculty' => 'required',
			'subject' => 'required',
			'hons_roll' => 'required',
			'major_degree' => 'required',

		];
        return $rules;
	}



    //Create Permission Role Rules & Validation
    // public static $rules = ['permission_id'  => 'required|integer',
    //                      'role_id'       => 'required|integer'];

    // public static function validate($data) {

    //  return Validator::make($data, self::$rules);

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

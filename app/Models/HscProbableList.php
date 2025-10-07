<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HscProbableList extends Model
{
    protected $table = 'student_info_hsc_formfillup';
    public $timestamps = false;

    public static function validateRules($data) {
		$rules = [
			'student_id' => 'required|numeric',
			'name'=>'required',
			'session'=>'required',
			'current_level' => 'required',
			'groups' => 'required',
			'student_type' => 'required',
			'registration_type' => 'required',
			'status' => 'required'

		];
        return $rules;
	}
}

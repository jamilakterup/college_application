<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MscProbableList extends Model
{
    protected $table = 'student_info_masters_formfillup';
    public $timestamps = false;

    public static function validateRules($data) {
		$rules = [
			'student_id' => 'required|numeric',
			'name'=>'required',
			'session'=>'required',
			'current_level' => 'required',
			'faculty_name' => 'required',
			'dept_name' => 'required',
			'student_type' => 'required',
			'registration_type' => 'required',
			'status' => 'required'

		];
        return $rules;
	}
}

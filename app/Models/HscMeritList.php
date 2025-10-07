<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HscMeritList extends Model
{
	protected $table = 'hsc_merit_list';
	public $timestamps = false;

	public static function validateRules($data)
	{
		$rules = [
			'ssc_roll' => 'required|numeric',
			'ssc_board' => 'required',
			'session' => 'required',
			'admission_status' => 'required',
			'passing_year' => 'required',
			'ssc_group' => 'required',
			'current_level' => 'required',
		];
		return $rules;
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeMeritList extends Model
{

    protected $table = 'deg_merit_list';
    public $timestamps = false;
    
    public static function validateRules($data) {
		$rules = [
			'admission_roll' => 'required|numeric',
			'name'=>'required',
			'session'=>'required',
			'admission_status' => 'required',
			'groups' => 'required',
		];
        return $rules;
	}
}

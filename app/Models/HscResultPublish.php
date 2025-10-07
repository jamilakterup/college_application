<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class HscResultPublish extends Model
{
    protected $table = 'result_publish';

	
	public static $rules = ['level' => 'required',
							'session'=>'required',
                      'open'=>'required|integer'];

	public static function validate($data) {

		    	return Validator::make($data, self::$rules);

	}



	//Update Department Rules & Validation
	public static function updateValidate($data) {

			$id = $data['id'];

		$rules = [  "level" => "required",
		            "session"=>"required",
				    "open"=>"required|integer"	];

		return Validator::make($data, $rules);
		
	}

	public function exam()
	{
		return $this->belongsTo('App\Models\Exam','exam_id');
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group','group_id');
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class StudentSubInfo extends Model
{
    protected $table = 'student_subject_info';

    protected $guarded = [];

	//Create Class Rules & Validation
/*	public static $rules = ['name'					=> 'required|unique:classes',
							];*/
/*
	public static function validate($data) {	

		return Validator::make($data, self::$rules);

	}*/		



	//Update Class Rules & Validation
	public static function updateValidate($data) {
	
		$id = $data['id'];
		$rules = ["sel_subject1"		=> "required|digits:3|numeric",
				  "sel_subject2"		=> "required|digits:3|numeric",
				  "sel_subject3"		=> "required|digits:3|numeric",
				  "fourth_subject"		=> "required|digits:3|numeric",
				  ];

		return Validator::make($data, $rules);

	}

	public function student()
	{
		return $this->belongsTo('App\Models\StudentInfoHsc','student_id');
	}

	public function sub1()
	{
		return $this->belongsTo('App\Models\Subject','sub1_id');
	}

	public function sub2()
	{
		return $this->belongsTo('App\Models\Subject','sub2_id');
	}

	public function sub3()
	{
		return $this->belongsTo('App\Models\Subject','sub3_id');
	}

	public function sub4()
	{
		return $this->belongsTo('App\Models\Subject','sub4_id');
	}
	public function sub5()
	{
		return $this->belongsTo('App\Models\Subject','sub5_id');
	}
	public function sub6()
	{
		return $this->belongsTo('App\Models\Subject','sub6_id');
	}
	public function sub7()
	{
		return $this->belongsTo('App\Models\Subject','sub21_id');
	}
	public function sub8()
	{
		return $this->belongsTo('App\Models\Subject','sub22_id');
	}
	public function sub9()
	{
		return $this->belongsTo('App\Models\Subject','sub23_id');
	}
	public function sub10()
	{
		return $this->belongsTo('App\Models\Subject','sub24_id');
	}
	public function sub11()
	{
		return $this->belongsTo('App\Models\Subject','sub25_id');
	}
	public function sub12()
	{
		return $this->belongsTo('App\Models\Subject','sub26_id');
	}	
	public function fourth()
	{
		return $this->belongsTo('App\Models\Subject','fourth_id');
	}
	public function fourth2()
	{
		return $this->belongsTo('App\Models\Subject','fourth2_id');
	}
}

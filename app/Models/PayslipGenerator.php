<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class PayslipGenerator extends Model
{
    protected $table = 'payslipgenerators';
	protected $fillable = ['paysliptitle_id', 'payslipheader_id', 'payslipitem_id', 'fees'];
	protected $guarded = [];



	//Create PayslipGenerator Rules and Validation
	public static $rules = ['paysliptitle_id'	=> 'required|integer',
							'payslipheader_id'	=> 'required|integer',
							'payslipitem_id'	=> 'required|integer',
							'fees'				=> 'required|numeric'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}



	//Eloquent Relationship
	public function paysliptitle() {

		return $this->belongsTo('App\Models\PayslipTitle');

	}	

	public function payslipheader() {

		return $this->belongsTo('App\Models\PayslipHeader');

	}	

	public function payslipitem() {

		return $this->belongsTo('App\Models\PayslipItem');

	}
}

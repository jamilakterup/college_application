<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class PayslipItem extends Model
{
    protected $table = 'payslipitems';
	protected $fillable = ['payslipheader_id', 'item'];
	protected $guarded = [];



	//Create PayslipItem Rules And Validation
	public static $rules = ['payslipheader_id'	=> 'required|integer',
							'item'				=> 'required',
							'item_type' => 'required'
							];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function payslipheader() {

		return $this->belongsTo('App\Models\PayslipHeader');

	}	

	public function payslipgenerators() {

		return $this->hasMany('App\Models\PayslipGenerator', 'payslipitem_id');

	}

	public function itemtype() {

		return $this->belongsTo('App\Models\PayslipItemType', 'type_id');

	}
}

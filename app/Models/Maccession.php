<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Maccession extends Model
{
    protected $table = 'maccessions';
	protected $fillable = ['material_id', 'accession_no'];



	//Create Maccession Rules and Validation
	public static $rules = ['material_id'	=> 'required|integer',
							'accession_no'	=> 'required|integer',
							'condition'		=> 'integer|in:0,1,2']; // 0 = damage, 1 = usable, 2 = repairable

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship					
	public function material() {
		return $this->belongsTo('App\Models\Material');
	}

	public function libcirculations() {
		$this->hasMany('App\Models\Libcirculation');
	}
}

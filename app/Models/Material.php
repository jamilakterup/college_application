<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Material extends Model
{
    protected $table = 'materials';
	protected $fillable = ['physical_form','call_no','isbn','title','subtitle','author','editor','location','size','no_of_pages','edition','edition_year','publisher','publishing_year','place_of_publication','clue_page','price','dues','source_details','series','available','condition','copy_no'];



	//Create Material Rules & Validation
	public static $rules = ['accession_no'			=> 'required|integer',
							'physical_form'			=> 'required',
							'call_no'				=> 'required',
							'isbn'					=> 'required',
							'title'					=> 'required',
							// 'subtitle'				=> 'required',
							'author'				=> 'required',
							// 'editor'				=> 'required',
							'location'				=> 'required',
							'size'					=> 'required',
							// 'no_of_pages'			=> 'required',
							// 'edition'				=> 'required',
							'edition_year'			=> 'required',
							'publisher'				=> 'required',
							'publishing_year'		=> 'required',
							// 'place_of_publication'	=> 'required',
							'clue_page'				=> 'required',
							'price'					=> 'required',
							'source_details'		=> 'required',
							// 'series'				=> 'required',
							'available'				=> 'boolean',
							'no_of_books'			=> 'required|integer|min:1'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}	



	//Update Material Rules & Validation
	public static $update_rules = ['physical_form'			=> 'required',
								   'call_no'				=> 'required',
								   'isbn'					=> 'required',
								   'title'					=> 'required',
								   // 'subtitle'				=> 'required',
								   'author'					=> 'required',
								   // 'editor'					=> 'required',
								   'location'				=> 'required',
								   'size'					=> 'required',
								   // 'no_of_pages'			=> 'required',
								   // 'edition'				=> 'required',
								   'edition_year'			=> 'required',
								   'publisher'				=> 'required',
								   'publishing_year'		=> 'required',
								   // 'place_of_publication'	=> 'required',
								   'clue_page'				=> 'required',
								   'price'					=> 'required',
								   'source_details'			=> 'required',
								   // 'series'					=> 'required',
								   'available'				=> 'boolean'];

	public static function updateValidate($data) {

		return Validator::make($data, self::$update_rules);

	}	



	//Upload Material Rules & Validation	
	public static $upload_rules = ['material_csv' => 'required'];

	public static function uploadValidate($data) {

		return Validator::make($data, self::$upload_rules);

	}



	//Eloquent Relationship	
	public function msubjects() {

		return $this->hasMany('App\Models\Msubject');

	}		

	public function maccessions() {

		return $this->hasMany('App\Models\Maccession');

	}
}

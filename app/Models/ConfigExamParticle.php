<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigExamParticle extends Model
{
    protected $table = 'config_exam_particles';
	public $timestamps = false;
	protected $fillable = ['classe_id', 'group_id', 'subject_id', 'xmparticle_id', 'total','per_centage', 'pass','particle_convert','pass_particle_convert'];



	//Create ConfigExamParticle Rules & Validation
	public static $rules = ['classe_id'		=> 'required|integer',
							'group_id'	=> 'required|integer',
							'subject_id'	=> 'required|integer',
							'xmparticle_id'	=> 'required|integer',
							'total'			=> 'required|integer',
							'pass'			=> 'required|integer',
							'per_centage'	=> 'required|integer',
							'particle_convert' => 'required|integer',
							'pass_particle_convert' => 'required|integer'];
						

	public static function validate($data) {

		

		return Validator::make($data, self::$rules);

	}	



	//Eloquent Relationship
	public function classe() {
		return $this->belongsTo('App\Models\Classe');
	}

	public function department() {
		return $this->belongsTo('App\Models\Group');
	}

	public function subject() {
		return $this->belongsTo('App\Models\Subject');
	}

	public function xmparticle() {
		return $this->belongsTo('App\Models\Xmparticle');
	}
}

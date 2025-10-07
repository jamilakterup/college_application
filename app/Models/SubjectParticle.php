<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectParticle extends Model
{
    protected $table = 'config_exam_subject';
	public $timestamps = false;
	protected $fillable = ['classe_id', 'group_id', 'subject_id', 'total','total_pass','total_converted'];	



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

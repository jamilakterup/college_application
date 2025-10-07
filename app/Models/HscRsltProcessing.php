<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HscRsltProcessing extends Model
{
    protected $table = 'hsc_result_processing';


	public function exam()
	{
		return $this->belongsTo('App\Models\Exam','exam_id');
	}

	public function group()
	{
		return $this->belongsTo('App\Models\Group','group_id');
	}

	public function classe()
	{
		return $this->belongsTo('App\Models\Classe','classe_id');
	}
}

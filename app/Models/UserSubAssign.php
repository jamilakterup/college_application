<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubAssign extends Model
{
    protected $table = 'user_sub_assign';
	public $timestamps = false;
	protected $fillable = ['user_id', 'subject_id'];
}

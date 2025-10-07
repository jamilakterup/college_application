<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTest extends Model
{
    protected $table = 'class_test';
	public $timestamps = false;
	protected $fillable = ['name'];

	//Create Exam Rules & Validation
	public static $rules = ['name' => 'required|unique:exams'];
}

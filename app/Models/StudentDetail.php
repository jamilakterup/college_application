<?php

namespace App\Models;

use Validator;
use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $table = 'student_details';
    protected $guarded = [];
    
    public function student(){
        return $this->belongsTo(Student::class);
    }
}

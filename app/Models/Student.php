<?php

namespace App\Models;

use Validator;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class);
    }
    
    public function studentPayments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function details(){
        return $this->hasOne(StudentDetail::class, 'student_id');
    }
}

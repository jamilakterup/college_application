<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubjectSelection extends Model
{
    protected $fillable = [
        'student_id',
        'student_type',
        'admission_roll',
        'combination_id',
        'session',
        'level',
        'group'
    ];

    /**
     * Get the subject combination
     */
    public function combination()
    {
        return $this->belongsTo(SubjectCombination::class, 'combination_id');
    }

    /**
     * Get the student (polymorphic relation)
     */
    public function student()
    {
        return $this->morphTo();
    }

    /**
     * Get all subjects for this selection through combination
     */
    public function subjects()
    {
        return $this->combination ? $this->combination->subjects : collect();
    }
}

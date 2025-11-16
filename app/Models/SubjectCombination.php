<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectCombination extends Model
{
    protected $fillable = [
        'combination_code',
        'combination_name',
        'group',
        'level',
        'session',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all course subjects for this combination (simple system)
     */
    public function subjects()
    {
        return $this->belongsToMany(CourseSubject::class, 'combination_subject', 'combination_id', 'subject_id')
            ->withPivot('subject_code', 'subject_type', 'order')
            ->withTimestamps()
            ->orderBy('combination_subject.order');
    }

    /**
     * Get students who selected this combination
     */
    public function studentSelections()
    {
        return $this->hasMany(StudentSubjectSelection::class, 'combination_id');
    }

    /**
     * Scope for active combinations only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope for specific level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Get formatted combination display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->combination_code . ' - ' . $this->combination_name;
    }

    /**
     * Get subject list as comma separated string
     */
    public function getSubjectListAttribute()
    {
        return $this->subjects->map(function ($subject) {
            return $subject->subject_name_bn ?: $subject->subject_name;
        })->implode(', ');
    }
}

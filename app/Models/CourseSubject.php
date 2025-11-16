<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    protected $table = 'course_subjects';
    
    protected $fillable = [
        'subject_name',
        'subject_name_bn',
        'subject_code',
        'group',
        'credit',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit' => 'decimal:2'
    ];

    /**
     * Get combinations that include this course subject
     */
    public function combinations()
    {
        return $this->belongsToMany(SubjectCombination::class, 'combination_subject', 'subject_id', 'combination_id')
                    ->withPivot('subject_code', 'subject_type', 'order')
                    ->withTimestamps()
                    ->orderBy('combination_subject.order');
    }

    /**
     * Scope for active subjects only
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
     * Get display name (Bangla or English)
     */
    public function getDisplayNameAttribute()
    {
        return $this->subject_name_bn ?: $this->subject_name;
    }

    /**
     * Get full display with code
     */
    public function getFullNameAttribute()
    {
        $name = $this->subject_name_bn ?: $this->subject_name;
        return $name . ' (' . $this->subject_code . ')';
    }
}

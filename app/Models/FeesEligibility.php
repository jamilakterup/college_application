<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesEligibility extends Model
{
    protected $table = 'fees_eligibility';
    
    protected $fillable = [
        'student_id',
        'student_type', // Full class name: App\Models\StudentInfoHsc, etc.
        'session',
        'academic_year',
        'level',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Polymorphic relationship to student info tables
     * Uses morphTo() for clean, efficient polymorphic relation
     * morphMap is configured in AppServiceProvider
     */
    public function student()
    {
        return $this->morphTo(__FUNCTION__, 'student_type', 'student_id');
    }

    /**
     * Helper method to check if student is eligible for fees payment
     * Supports both: 
     *   - isEligible($id, StudentInfoHsc::class, $session)
     *   - isEligible($studentModel, null, $session)
     * 
     * @param int|\Illuminate\Database\Eloquent\Model $studentIdOrModel
     * @param string|null $studentType Full class name (e.g., App\Models\StudentInfoHsc)
     * @param string|null $session
     * @return bool
     */
    public static function isEligible($studentIdOrModel, $studentType = null, $session = null)
    {
        // Support passing student model directly
        if (is_object($studentIdOrModel)) {
            $studentId = $studentIdOrModel->id;
            $studentType = $studentType ?? get_class($studentIdOrModel);
        } else {
            $studentId = $studentIdOrModel;
        }

        $query = self::where('student_id', $studentId)
                    ->where('student_type', $studentType)
                    ->where('is_active', true);
        
        if ($session) {
            $query->where('session', $session);
        }

        return $query->exists();
    }

    // Scope for active eligibilities
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific student type
    public function scopeStudentType($query, $type)
    {
        return $query->where('student_type', $type);
    }

    // Scope for specific session
    public function scopeSession($query, $session)
    {
        return $query->where('session', $session);
    }

    // Relationship with fees applications
    public function feesApplications()
    {
        return $this->hasMany(FeesApplication::class, 'reference_id', 'student_id')
                    ->where(function($query) {
                        $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.\"student_type\"')) = ?", [$this->student_type])
                              ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.\"academic_session\"')) = ?", [$this->session]);
                    });
    }

    // Get the latest invoice through fees application
    public function getLatestInvoiceAttribute()
    {
        $application = $this->feesApplications()
                           ->with('invoice')
                           ->where('status', 'Paid')
                           ->latest()
                           ->first();
        
        return $application ? $application->invoice : null;
    }

    // Check if student has paid
    public function hasPaid()
    {
        return $this->feesApplications()
                    ->where('status', 'Paid')
                    ->exists();
    }

    /**
     * Get student information based on student_type
     * Now uses polymorphic relation - much cleaner!
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getStudentInfo()
    {
        // Simply return the polymorphic relation
        return $this->student;
    }

    // Convert to FeesEligible format for compatibility with existing fees payment system
    public function toFeesEligible()
    {
        $studentInfo = $this->getStudentInfo();
        
        if (!$studentInfo) {
            return null;
        }

        return (object) [
            'reference_id' => $this->student_id,
            'student_type' => $this->student_type,
            'academic_session' => $this->session,
            'current_level' => $this->level,
            'student_info' => $studentInfo,
            'status' => $this->is_active ? 'active' : 'inactive',
        ];
    }
}

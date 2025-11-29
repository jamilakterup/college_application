<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesConfiguration extends Model
{
    protected $table = 'fees_configurations';

    // Payment Type Constants
    const TYPE_GENERAL = 'general';
    const TYPE_PROMOTION = 'promotion';
    const TYPE_ADMISSION = 'admission';
    const TYPE_FORM_FILLUP = 'form_fillup';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_EXAM = 'exam';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_FEES_PAYMENT = 'fees_payment';
    const TYPE_OTHER = 'other';

    protected $fillable = [
        'title',
        'type',
        'course', // Full class name: App\Models\StudentInfoHsc, etc.
        'level',
        'academic_session',
        'opening_date',
        'clossing_date',
        'status',
        'check_eligible_list',
        'form_fields',
        'required_fields',
    ];


    protected $casts = [
        'status' => 'boolean',
        'check_eligible_list' => 'boolean',
        'form_fields' => 'array',
        'required_fields' => 'array',
        'opening_date' => 'date',
        'clossing_date' => 'date',
    ];

    /**
     * Polymorphic relationship to course (student type)
     * course stores full class name like 'App\Models\StudentInfoHsc'
     */
    public function courseModel()
    {
        // This returns the class that this configuration is for
        // But we don't actually need a relation since course is just a class name
        // We'll use it for filtering and validation
        return $this->course;
    }

    /**
     * Scope to get only active/open configurations
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->whereDate('opening_date', '<=', now())
            ->whereDate('clossing_date', '>=', now());
    }

    /**
     * Scope to filter by course
     */
    public function scopeByCourse($query, $courseClass)
    {
        return $query->where('course', $courseClass);
    }

    /**
     * Check if configuration is currently open
     */
    public function isOpen()
    {
        return $this->status &&
            now()->between($this->opening_date, $this->clossing_date);
    }

    /**
     * Check if current date is within the configuration date range
     */
    public function isWithinDateRange()
    {
        return now()->between($this->opening_date, $this->clossing_date);
    }

    /**
     * Check if configuration is active (status = 1)
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * Get all payment types
     */
    public static function getPaymentTypes()
    {
        return [
            self::TYPE_GENERAL => 'General Payment',
            self::TYPE_PROMOTION => 'Student Promotion',
            self::TYPE_ADMISSION => 'Admission Fees',
            self::TYPE_FORM_FILLUP => 'Form Fillup Fees',
            self::TYPE_REGISTRATION => 'Registration Fees',
            self::TYPE_EXAM => 'Exam Fees',
            self::TYPE_CERTIFICATE => 'Certificate Fees',
            self::TYPE_FEES_PAYMENT => 'Fees Payment',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get human-readable type name
     */
    public function getTypeName()
    {
        $types = self::getPaymentTypes();
        return $types[$this->type] ?? 'Unknown';
    }

    /**
     * Check if this configuration requires post-payment action
     */
    public function requiresPostPaymentAction()
    {
        return in_array($this->type, [
            self::TYPE_PROMOTION,
            self::TYPE_ADMISSION,
        ]);
    }

    /**
     * Get human-readable course name
     */
    public function getCourseName()
    {
        // Full class name mapping
        $courseMap = [
            'App\Models\StudentInfoHsc' => 'HSC',
            'App\\Models\\StudentInfoHsc' => 'HSC',
            'App\Models\StudentInfoHons' => 'Honours',
            'App\\Models\\StudentInfoHons' => 'Honours',
            'App\Models\StudentInfoDegree' => 'Degree',
            'App\\Models\\StudentInfoDegree' => 'Degree',
            'App\Models\StudentInfoMasters' => 'Masters',
            'App\\Models\\StudentInfoMasters' => 'Masters',
            // Short names
            'hsc' => 'HSC',
            'honours' => 'Honours',
            'degree' => 'Degree',
            'masters' => 'Masters',
        ];

        return $courseMap[$this->course] ?? strtoupper($this->course ?? 'Unknown');
    }
}

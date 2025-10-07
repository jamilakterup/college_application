<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $guarded = [];

    /**
     * Get the payments for this document type.
     */
    public function payments()
    {
        return $this->hasMany(DocumentPayment::class);
    }

    /**
     * Get the student documents for this document type.
     */
    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class);
    }
    
    /**
     * Check if a specific student has paid for this document
     */
    public function isPaidByStudent($studentId)
    {
        return $this->payments()
            ->where('student_id', $studentId)
            ->where('status', 'paid')
            ->exists();
    }
    
    /**
     * Get the payment record for a specific student
     */
    public function getPaymentForStudent($studentId)
    {
        return $this->payments()
            ->where('student_id', $studentId)
            ->where('status', 'paid')
            ->first();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPayment extends Model
{
    protected $guarded = [];

    protected $dates = [
        'paid_at'
    ];

    /**
     * Get the document type associated with this payment.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the student associated with this payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the student document associated with this payment.
     */
    public function studentDocument()
    {
        return $this->belongsTo(StudentDocument::class);
    }
}
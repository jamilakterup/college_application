<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $guarded = [];

    /**
     * Get the student that owns the document.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the document type.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the payment associated with this document.
     */
    public function payment()
    {
        return $this->hasOne(DocumentPayment::class);
    }
}
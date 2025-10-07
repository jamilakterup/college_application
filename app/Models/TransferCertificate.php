<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransferCertificate extends Model
{
    protected $table = 'transfer_certificates';

    protected $fillable = [
        'student_id',
        'student_name',
        'father_name',
        'mother_name',
        'tc_no',
        'roll_no',
        'issue_date',
        'admission_date',
        'leaving_date',
        'reason_for_leaving',
        'leaving_fees_upto',
        'issued_by',
        'status'
    ];

    protected $dates = [
        'issue_date',
        'admission_date',
        'leaving_date',
        'date_of_birth'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'admission_date' => 'date',
        'leaving_date' => 'date',
        'date_of_birth' => 'date',
        'dues_amount' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(StudentInfoHsc::class, 'student_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(\App\User::class, 'issued_by');
    }

    public function generateTcNo()
    {
        $year = Carbon::now()->format('Y');
        $lastRecord = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $serial = $lastRecord ? (int)substr($lastRecord->tc_no, -4) + 1 : 1;
        return 'TC/' . $year . '/' . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySession($query, $session)
    {
        return $query->where('session', $session);
    }

    public function getAttendancePercentageAttribute()
    {
        if ($this->working_days && $this->present_days) {
            return round(($this->present_days / $this->working_days) * 100, 2);
        }
        return null;
    }
}

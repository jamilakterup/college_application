<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CharacterCertificate extends Model
{
    protected $table = 'character_certificates';

    protected $fillable = [
        'student_id',
        'student_name',
        'father_name',
        'mother_name',
        'certificate_no',
        'groups',
        'issue_date',
        'academic_year',
        'class_name',
        'roll_no',
        'registration_no',
        'study_period_from',
        'study_period_to',
        'issued_by',
        'status'
    ];

    protected $dates = [
        'issue_date',
        'study_period_from',
        'study_period_to'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'study_period_from' => 'date',
        'study_period_to' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(StudentInfoHsc::class, 'student_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(\App\User::class, 'issued_by');
    }

    public function generateCertificateNo()
    {
        $year = Carbon::now()->format('Y');
        $lastRecord = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $serial = $lastRecord ? (int)substr($lastRecord->certificate_no, -4) + 1 : 1;
        return 'CC/' . $year . '/' . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function getCharacterRatingTextAttribute()
    {
        $ratings = [
            'excellent' => 'অত্যন্ত উত্তম',
            'very_good' => 'অতি উত্তম',
            'good' => 'উত্তম',
            'satisfactory' => 'সন্তোষজনক'
        ];

        return $ratings[$this->character_rating] ?? 'উত্তম';
    }
}

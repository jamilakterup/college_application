<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    public function student_info_hsc(){
    	return $this->belongsTo('App\Models\StudentInfoHsc', 'roll', 'id');
    }

    public static function validateRules($data) {
        $rules = [
            'name' => 'required',
            'roll' => 'required|numeric',
            'type' => 'required',
            'level' => 'required',
            'session' => 'required',
            'exam_year' => 'required',
            'slip_name' => 'required',
            'start_date'=>'required|before:'.$data['end_date'],
            'end_date'=>'required|after:'.$data['start_date'],
            'total_amount' => 'required|numeric'
        ];
        return $rules;
    }

    public function header(){
        return $this->belongsTo(PayslipHeader::class, 'header_id');
    }
}

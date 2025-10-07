<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MastersAdmittedStudent extends Model
{
    protected $table = 'masters_admitted_student';
    public $timestamps = false; 
    protected $fillable = [

        'auto_id'   ,
        'entry_time',
        'name',
        'name_bangla',
        'father_name',
        'father_income',
        'mother_name',
        'birth_date ',
        'blood_group',
        'gender',
        'permanent_email',
        'email',
        'password',
        'permanent_mobile',
        'contact_no',
        'photo',
        'religion',
        'permanent_village',
        'present_village ',
        'permanent_po',
        'present_po',
        'permanent_ps',
        'present_ps',
        'permanent_dist',
        'present_dist',
        'guardian_name',
        'guardian_contact',
        'guardian_relation',
        'guardian_income',
        'guardian_occupation',
        'ssc_roll',
        'ssc_institute',
        'ssc_board',
        'ssc_gpa',
        'ssc_pass_year',
        'hsc_roll',
        'hsc_institute',
        'hsc_board',
        'hsc_gpa',
        'hsc_pass_year',
        'payment_status',
        'paid_date',
        'complete_sms',
        'sent_time',
        'status ',
        'honrs_passing_institute',
        'honrs_passing_year',
        'honrs_passing_cgpa',
        'honrs_session',
        'honrs_roll',
        'from_faculty',
        'to_faculty',
        'from_subject',
        'to_subject',
        'masters_session',
        'admission_roll'
    ];



    //Create Permission Role Rules & Validation
    // public static $rules = ['permission_id'  => 'required|integer',
    //                      'role_id'       => 'required|integer'];

    // public static function validate($data) {

    //  return Validator::make($data, self::$rules);

    // }    



    //Eloquent Relationship
    /*public function permission() {

        return $this->belongsTo('Permission');

    }

    public function role() {

        return $this->belongsTo('Role');

    }
*/
}

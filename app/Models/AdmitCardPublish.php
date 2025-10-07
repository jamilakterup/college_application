<?php

namespace App\Models;

use Mpdf\Mpdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AdmitCardPublish extends Model
{
    protected $table = 'admit_card_publish';

    public static $rules = ['level' => 'required',
							'session'=>'required',
                      'open'=>'required|integer'];

	public static function validate($data) {
		return Validator::make($data, self::$rules);

	}

    public static function updateValidate($data) {
        $id = $data['id'];
        $rules = [  "level" => "required",
                    "session"=>"required",
                    "open"=>"required|integer"	];

        return Validator::make($data, $rules);
    
}

    public static function downloadAdmitCard(){
        $session = request()->get('session');
        $student_id = request()->get('student_id');
        $current_level = request()->get('current_level');
        $exam_id = request()->get('exam_id');
        $group =  request()->get('group');
        $curr_level=Classe::find($current_level);

        $query_student = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name);
        if($student_id)
            $query_student->where('id', $student_id);
        if($group)
            $query_student->whereGroups($group);

        $student_infos = $query_student->get();
        $student_info_ids = [];

        foreach($student_infos as $student_info) :
            $student_info_ids[] = $student_info->id;
        endforeach;
        $cnt=count($student_info_ids);
        $exam_name=Exam::find($exam_id);
        $student_info_hsc=StudentInfoHsc::whereIn('id', $student_info_ids)->get();
        if($cnt > 1)
            $f_name=$student_info_ids[0].'-'.$student_info_ids[$cnt-1].'.pdf';
        else
            $f_name=$student_info_ids[0].'.pdf';
        $mpdf = new Mpdf();
        $mpdf->allow_charset_conversion=true;
        $mpdf->charset_in='UTF-8';	
        $mpdf->SetHTMLFooter('<p style="vertical-align: bottom; font-family: serif; 
            font-size: 7.5pt; color: #000000; font-weight: bold; font-style: italic; text-align:center">Developed & Maintained by <img style="width:75px; margin-bottom:-5px;" src="'.asset('img/company.png').'"></p>');		    
        $mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.admit_card', compact('student_info_hsc', 'exam_name')));
        return $mpdf->Output($f_name, 'D');
            
    }

    public function exam(){
        return $this->belongsTo('App\Models\Exam');
    }

    public function classe(){
        return $this->belongsTo('App\Models\Classe', 'level');
    }
}

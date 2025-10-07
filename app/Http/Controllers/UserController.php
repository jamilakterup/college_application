<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Exam;
use App\Models\Group;
use App\Models\HscResultPublish;
use App\Models\HscRsltProcessing;
use App\Models\StudentInfoHsc;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use Mpdf\Mpdf;
use DB;

class UserController extends Controller
{
    public function hscResult(Request $request) {

        $title = 'Easy CollegeMate - HSC Result';
        Session::forget('student_id');
        Session::forget('exam_id');
        Session::forget('group_id');

        $student_id = $request->get('student_id');
        $session = $request->get('session');       
        $group_id =  $request->get('group_id');
        $current_level = $request->get('level');
        $exam_id = $request->get('exam_id');
        $exam_year = $request->get('exam_year');
        $show_transcript = false;
        

        $classes = Classe::orderBy('id')->paginate(Ecm::paginate());
        $current_yr_lists = create_option_array('classes', 'id', 'name', 'Level');
        $group_lists = create_option_array('groups', 'id', 'name', 'Group');
        $exam_lists = create_option_array('exams', 'id', 'name', 'Exam');
        return view('hsc_result', compact('title', 'current_yr_lists', 'group_lists', 'exam_lists','student_id', 'session', 'exam_id', 'group_id', 'current_level', 'exam_year','show_transcript'));
    }

    public function hscResultSearch(Request $request){
        $this->validate($request, [
            'student_id' => 'required',
            'level' => 'required',
            'exam_id' => 'required'
        ]);

        $title = 'Easy CollegeMate - HSC Result';
        $classes = Classe::orderBy('id')->paginate(Ecm::paginate());
        $current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Level');
        $group_lists = create_option_array('groups', 'id', 'name', 'Group');
        $exam_lists = create_option_array('exams', 'id', 'name', 'Exam');


        $student_id = $request->get('student_id');
        
        $current_level = $request->get('level');
        $exam_id = $request->get('exam_id');
        $students = DB::table('student_info_hsc')->where('id', $student_id)->get();

        if(count($students) < 1){
            return Redirect::back()->withInput()->with('error','No Student Found!');
        }
        $student = $students->first();
        $curr_level=Classe::find($current_level);

        $publish = HscResultPublish::where('level', $curr_level->name)->where('exam_id', $exam_id)->where('session', $student->session)->where('open', 1)->get();

        if(count($publish) < 1 ){
            $error_message = 'Result is not Published Yet';
            return Redirect::back()->withInput()->with('error',$error_message);
        }

        $publish = $publish[0];
        $session = $publish->session;       
        $exam_year = $publish->exam_year;
        $group_id =  Group::where('name',$student->groups)->first()->id;

        $show_transcript = false;

        if ($request->isMethod('post')):

            $show_transcript = true;

            $group=Group::find($group_id);
            
            $publish_id = $publish->id;

            $student_infos = StudentInfoHsc::whereId($student_id)->whereSession($session)->whereGroups($group->name)->get();
            if($student_infos->count()==0):
                $error_message = 'Class Roll Could not Find. Check again!';
                return Redirect::back()->withInput()->with('error',$error_message);
            endif;

            $check_result=HscRsltProcessing::whereSession($session)->whereExam_id($exam_id)->whereGroup_id($group_id)->count();

            if($check_result==0):
                $error_message = 'Result not Published yet. Check again!';
                return Redirect::back()->withInput()->with('error',$error_message);
            endif;

            return view('hsc_result', compact('title', 'student_id', 'session', 'exam_id', 'group_id', 'current_level', 'exam_year','current_yr_lists', 'group_lists', 'exam_lists', 'show_transcript','publish_id'));
        else:

            $student_id = $request->get('student_id');
            $session = $request->get('session');       
            $group_id =  $request->get('group_id');
            $current_level = $request->get('current_level');
            $exam_id = $request->get('exam_id');

            return view('hsc_result', compact('title', 'student_id', 'session','exam_id', 'group_id', 'current_level', 'exam_year','current_yr_lists', 'group_lists', 'exam_lists', 'show_transcript'));
            
        endif;
    }


    public function hscResultPdf(Request $request) {

        $student_id = $request->get('student_id');          
        $exam_id = $request->get('exam_id');
        $group_id =  $request->get('group_id');
        $publish_id =  $request->get('publish_id');
        $publish = HscResultPublish::find($publish_id);

        if($student_id ==''):
            $error_message='Search Heare';
            return Redirect::to('result')->with('error', $error_message);
        endif;
        $student_info_hsc=StudentInfoHsc::whereId($student_id)->get();        
        $f_name=$student_id.'.pdf';
        $exam_name=Exam::find($exam_id);
     
        // $mpdf = new mPDF(); 
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
        $mpdf->allow_charset_conversion=true;
        $mpdf->charset_in='UTF-8';  
        foreach ($student_info_hsc as  $value) :                        
        $mpdf->AddPage();        
        $mpdf->WriteHTML(view('pdf.transcript', compact('value','exam_name', 'group_id', 'exam_id', 'publish')));
        endforeach;
    
        $mpdf->Output($f_name, 'D');                       

    }
}

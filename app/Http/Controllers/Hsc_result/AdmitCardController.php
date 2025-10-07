<?php

namespace App\Http\Controllers\Hsc_result;

use Ecm;
use Mpdf\Mpdf;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Classe;
use App\Models\ClassExam;
use Illuminate\Http\Request;
use App\Models\StudentInfoHsc;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AdmitCardController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.admit_card.index:Admit Card|Dashboard';		
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = ['' => 'Select']+Classe::orderBy('id')->pluck('name', 'id')->toArray();
		$group_lists = ['' => 'Select group'] + Group::orderBy('id')->pluck('name', 'name')->toArray();
		$exam_lists = ['' => 'Select exam'] + Exam::orderBy('id')->pluck('name', 'id')->toArray();
		return view('BackEnd.hsc_result.admit_card.index', compact('title','current_yr_lists','group_lists','exam_lists','breadcrumb'));					

	}



	public function create() {

		

	}

	public function store(Request $request) {

		if ($request->isMethod('post'))
		{
		    $session = $request->get('session');		
			$group =  $request->get('group');
			$current_level = $request->get('current_level');
			$exam_id = $request->get('exam_id');
			$curr_level=Classe::find($current_level);
			$student_infos = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->get();

			$student_info_ids = [];

			foreach($student_infos as $student_info) :			
			$field_name = 'studentinfo-' . $student_info->id;
			if($student_info->id == $request->get($field_name)) :
				$student_info_ids[] = $student_info->id;
			endif;
		    endforeach;
		   $cnt=count($student_info_ids);
		   $exam_name=Exam::find($exam_id);
		   $student_info_hsc=StudentInfoHsc::whereIn('id', $student_info_ids)->get();

		    $f_name=$student_info_ids[0].'-'.$student_info_ids[$cnt-1].'.pdf';
		    
		    //return public_path().'\img\skcr2.jpg.';
		    $mpdf = new Mpdf();
		    $mpdf->allow_charset_conversion=true;
			$mpdf->charset_in='UTF-8';			    
		    $mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.admit_card', compact('student_info_hsc', 'exam_name')));
		     //return view('pdf.admit_card')->withStudent_info_ids($student_info_ids);
			$mpdf->Output($f_name, 'D');

		    

		}

	}



	public function admitlist(Request $request) {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.admit_card.index:Admit Card|Dashboard';
			
	
		$session = Ecm::filterInput('session', $request->get('session'));		
		$group = Ecm::filterInput('group', $request->get('group'));
		$current_level = Ecm::filterInput('current_year', $request->get('current_year'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));
			

		if($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	
		if($group == '') :
			$error_message = 'Select Group';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		if($current_level == '') :
			$error_message = 'Select Current Year';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		if($exam_id == '') :
			$error_message = 'Select Exam';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;

		$chk_exam=ClassExam::whereExam_id($exam_id)->whereClasse_id($current_level)->count();
		if($chk_exam == 0) :
			$error_message = 'Exam Not Assign';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;

		$curr_level=Classe::find($current_level);

		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->get();
		if(count($student_info) == 0) :
			$error_message = 'No Student Found';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->orderBy('id')->paginate(50);

		return view('BackEnd.hsc_result.admit_card.list', compact('title', 'group','session','current_level','curr_level','student_info','exam_id','breadcrumb'));		
	}



	public function show($id) {



	}



	public function edit($id) {


	}



	public function update($id) {

		
		
	}



	public function destroy($id) {

		
		
	}
}

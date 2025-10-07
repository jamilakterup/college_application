<?php

namespace App\Http\Controllers\Hsc_result;

use Ecm;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Classe;
use App\Models\ExamDate;
use App\Models\ClassExam;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use App\Models\StudentInfoHsc;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class ExamDateController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Exam Date';
		$breadcrumb = 'hsc_result.exam_date.index:Class Subject|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = ['' => 'Select']+Classe::orderBy('id')->pluck('name', 'id')->toArray();
		$group_lists = ['' => 'Select group'] + Group::orderBy('id')->pluck('name', 'name')->toArray();
		$exam_lists = ['' => 'Select exam'] + Exam::orderBy('id')->pluck('name', 'id')->toArray();

		return view('BackEnd.hsc_result.exam_date.index',compact('title','current_yr_lists','group_lists', 'exam_lists','breadcrumb'));

	}

	public function exam_date_list(Request $request) {

		$title = 'Easy CollegeMate - Exam Date';
		$breadcrumb = 'hsc_result.exam_date.index:Exam Date|Dashboard';
			
	
		$session = Ecm::filterInput('session', $request->get('session'));		
		$group = Ecm::filterInput('group', $request->get('group'));
		$current_level = Ecm::filterInput('current_level', $request->get('current_level'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));
		$exam_year = Ecm::filterInput('exam_year', $request->get('exam_year'));

			

		if($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	
		if($exam_year == '') :
			$error_message = 'Select Exam Year';
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
		$group_id=Group::whereName($group)->pluck('id');

		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->orderBy('class_roll')->get();
		if(count($student_info) == 0) :
			$error_message = 'No Student Found';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		$class_sub=ClassSubject::whereClasse_id($current_level)->whereGroup_id($group_id)->orderBy('id')->get();
	

		return view('BackEnd.hsc_result.exam_date.edit', compact('title', 'session', 'group','current_level', 'curr_level','class_sub', 'student_info','group_id','exam_id','exam_year','breadcrumb'));		
	}



	public function edit($class_id,$department_id) {

		$title = 'Easy CollegeMate - Exam Date';
		$breadcrumb = 'hsc_result.exam_date.index:Exam Date|Assign Exam Date';
		$class_sub=ClasseSubject::whereClasse_id($class_id)->whereGroup_id($department_id)->orderBy('id')->get();
		
		$subjects = Subject::orderBy('id')->get();		

		return view('hsc_result.exam_date.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClass_id($class_id)
					->withDepartment_id($department_id)
					->withClass_sub($class_sub)					
					->withSubjects($subjects);

	}


	public function store(Request $request)
	{

		$session = Ecm::filterInput('session', $request->get('session'));		
		$group_id = Ecm::filterInput('group_id', $request->get('group_id'));
		$class_id = Ecm::filterInput('class_id', $request->get('class_id'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));
		$exam_year = Ecm::filterInput('exam_year', $request->get('exam_year'));

		

		if($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	
		if($group_id == '') :
			$error_message = 'Select Group';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		if($class_id == '') :
			$error_message = 'Select Current Year';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		if($exam_id == '') :
			$error_message = 'Select Exam';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;
		$class_has_department = ClassGroup::whereClasse_id($class_id)->whereGroup_id($group_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		//Delete & Insert = Update ClasseSubject
		$class_sub=ClassSubject::whereClasse_id($class_id)->whereGroup_id($group_id)->get();
		ExamDate::whereClass_id($class_id)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSession($session)->whereExam_year($exam_year)->delete();
		$subjects_id = [];
		if($class_sub->count() > 0) :
			foreach($class_sub as $subject) :
				$sub_id = $subject->subject_id;
				if($request->get($sub_id) == '') :
					 $error_message = 'Enter Exam Date of all Subject';
			      return Redirect::back()->with('error',$error_message);
				else:	
					$subjects_id[] = $sub_id;
				endif;	
			endforeach;
			
			if(count($subjects_id) > 0) :
				foreach($subjects_id as $subject_id) :
					$data_array = ['class_id' => $class_id, 'group_id' => $group_id, 'exam_id'=>$exam_id,'session'=>$session,'exam_year'=>$exam_year,'subject_id' => $subject_id,'date'=>$request->get($subject_id)];
					ExamDate::create($data_array);
				endforeach;	
			endif;	
		endif;

				

		$message = 'You have successfully create the Exam Date';		
		return Redirect::route('hsc_result.exam_date.index')
						->withMessage($message);
						
						
	}



	public function update($class_id,$department_id,$exam_id) {

		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_department = ClassGroup::whereClasse_id($class_id)->whereGroup_id($department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		//Delete & Insert = Update ClasseSubject
		$class_sub=ClasseSubject::whereClasse_id($class_id)->whereGroup_id($department_id)->get();

		$subjects_id = [];
		if($class_sub->count() > 0) :
			foreach($class_sub as $subject) :
				$sub_id = $subject->subject_id;
				if($request->get($sub_id) == '') :
					 $error_message = 'Enter Exam Date of all Subject';
			      return Redirect::back()->with('error',$error_message);
				else:	
					$subjects_id[] = $sub_id;
				endif;	
			endforeach;
			$exam_year=date('Y');
			if(count($subjects_id) > 0) :
				foreach($subjects_id as $subject_id) :
					$data_array = ['classe_id' => $class_id, 'group_id' => $department_id, 'exam_id'=>$exam_id,'exam_year','subject_id' => $subject_id,];
					ClasseSubject::create($data_array);
				endforeach;	
			endif;	
		endif;

		$count = Classe::where('id', '<=', $class_id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated the class subject';		
		return Redirect::route('hsc_result.exam_date.index', ['page' => $page])
						->withMessage($message)
						->withId($class_id)
						->withDepartment_id($department_id);			

	}



	public function destroy($class_id,$department_id) {

		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_department = ClasseDepartment::whereClasse_id($class_id)->whereDepartment_id($department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		ClasseSubject::whereClasse_id($class_id)->whereDepartment_id($department_id)->delete();

		$error_message = 'You have unassigned all subject';
		return Redirect::back()->with('error',$error_message);				

	}
}

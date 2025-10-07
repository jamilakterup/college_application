<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassExam;
use App\Models\Classe;
use App\Models\Exam;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingAssignExamController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Exam Assign';
		$breadcrumb = 'hsc_result.assign_exam.index:Class Exam|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.assign_exam.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClasses($classes);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Assign Exam';
		$breadcrumb = 'hsc_result.assign_exam.index:Class Exam|Assign Class Exam';
		$exams = Exam::orderBy('id')->get();

		return view('BackEnd.hsc_result.assign_exam.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExams($exams)
					->with('class_id',$id);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		//Delete & Insert = Update ClassExam
		ClassExam::where('classe_id',$id)->delete();

		$exams_id = [];
		$exams = Exam::get();
		if($exams->count() > 0) :
			foreach($exams as $exam) :
				$exam_id = $exam->id;

				if($request->get('exam-' . $exam_id) == $exam_id) :
					$exams_id[] = $exam_id;
				endif;	
			endforeach;

			if(count($exams_id) > 0) :
				foreach($exams_id as $exam_id) :
					$data_array = ['classe_id' => $id, 'exam_id' => $exam_id];
					ClassExam::create($data_array);
				endforeach;	
			endif;	
		endif;

		$count = Classe::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated the class exam';		
		return Redirect::route('hsc_result.assign_exam.index', ['page' => $page])
						->with('info',$message)
						->withId($id);		

	}



	public function destroy(Request $request, $id) {
		
		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		$class_name = Classe::whereId($id)->pluck('name');

		ClassExam::where('classe_id',$id)->delete();

		$error_message = 'You have unassigned all exam from class ' . $class_name;
		return Redirect::back()->with('warning',$error_message);		

	}
}

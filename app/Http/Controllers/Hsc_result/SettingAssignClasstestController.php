<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassTest;
use App\Models\ClassTestExam;
use App\Models\Exam;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingAssignClasstestController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Class Test Assign';
		//$breadcrumb = 'hsc_result.assign_classtest:Class Exam|Dashboard';
		$classes = Exam::orderBy('id')->where('have_class_test',1)->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.assign_classtest.index')
					->withTitle($title)
					->withClasses($classes);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Assign Test Assign';

		$exams = ClassTest::orderBy('id')->get();

		return view('BackEnd.hsc_result.assign_classtest.edit')
					->withTitle($title)
					->withExams($exams)
					->with('class_id',$id);

	}



	public function update(Request $request, $id) {
		if($id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		//Delete & Insert = Update ClasseExam
		ClassTestExam::whereExam_id($id)->delete();

		$exams_id = [];
		$exams = ClassTest::get();
		if($exams->count() > 0) :
			foreach($exams as $exam) :
				$exam_id = $exam->id;

				if($request->get('exam-' . $exam_id) == $exam_id) :
					$exams_id[] = $exam_id;
				endif;	
			endforeach;

			if(count($exams_id) > 0) :
				foreach($exams_id as $exam_id) :
					$data_array = ['exam_id' => $id, 'class_test_id' => $exam_id];
					ClassTestExam::create($data_array);
				endforeach;	
			endif;	
		endif;

		$count = Exam::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated the class exam';		
		return Redirect::route('hsc_result.assign_class_test.index', ['page' => $page])
						->with('info',$message)
						->withId($id);		

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		$class_name = Exam::whereId($id)->pluck('name');

		ClassTestExam::where('exam_id',$id)->delete();

		$error_message = 'You have unassigned all class test from exam ' . $class_name;
		return Redirect::back()->with('warning',$error_message);		

	}
}

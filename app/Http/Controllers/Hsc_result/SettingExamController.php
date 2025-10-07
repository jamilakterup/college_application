<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassExam;
use App\Models\Exam;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingExamController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Exam List';
		$breadcrumb = 'hsc_result.exam.index:Exam List|Dashboard';
		$exams = Exam::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.exam.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExams($exams);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Exam';
		$breadcrumb = 'hsc_result.exam.index:Exam List|Add Exam';

		return view('BackEnd.hsc_result.exam.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);		

	}



	public function store(Request $request) {

		$data = $request->all();

		$validation = Exam::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	
          $have_classtest = $request->get('have_class_test');
		  if (!isset($have_classtest)) {
			$have_classtest =0;
			}
		//Insert Exam
		$exam = new Exam;
		$exam->name = $request->get('name');
		$exam->have_class_test = $have_classtest;
		$exam->save();

		//Page
		$page = ceil(Exam::count()/Ecm::paginate());
		$id = $exam->id;

		$message = 'You have successfully created a new exam';
		return Redirect::route('hsc_result.exam.index', ['page' => $page])
						->with('success',$message)
						->withId($id);		

	}



	public function show($id) {

		$exist = Exam::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no exam with this id';
			return Redirect::route('hsc_result.exam.index')->with('error',$error_message);
		endif;	

		$exam = Exam::find($id);

		$title = 'Easy CollegeMate - Exam - ' . $exam->name;
		$breadcrumb = 'hsc_result.exam.index:Exam List|Exam - ' . $exam->name;

		return view('hsc_result.exam.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExam($exam);		

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Exam';
		$breadcrumb = 'hsc_result.exam.index:Exam|Edit Exam';
		$exam = Exam::find($id);

		return view('BackEnd.hsc_result.exam.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExam($exam);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;	
		$data = $request->all();
		$validation = Exam::updateValidate($data);
		

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//Update Exam
		$exam = Exam::find($id);
		$exam->name = $request->get('name');
		$exam->have_class_test = $request->get('have_class_test');
		$exam->update();

		//Page
		$count = Exam::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated the exam';
		return Redirect::route('hsc_result.exam.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$exam = Exam::find($id);
		$exam->delete();

		ClassExam::where('exam_id',$id)->delete();

		$error_message = 'You have deleted the exam';
		return Redirect::back()->with('warning',$error_message);	

	}
}

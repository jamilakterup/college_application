<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassTest;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ClassTestController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Class Test List';
		$breadcrumb = 'hsc_result.class_test.index:Class Test List|Dashboard';
		$class_tests = ClassTest::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.classtest.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->with('class_tests',$class_tests);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Class Test';
		$breadcrumb = 'hsc_result.class_test.index:Class Test List|Add Class Test';

		return view('BackEnd.hsc_result.classtest.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);		

	}



	public function store(Request $request) {

		$data = $request->get('name');

		if($data == '') :
			$error_message = 'Please Enter Class Test name';			
			return Redirect::back()->with('error',$error_message);
		endif;	

		//Insert Exam
		$exam = new ClassTest;
		$exam->name = $request->get('name');
		$exam->save();

		//Page
		$page = ceil(ClassTest::count()/Ecm::paginate());
		$id = $exam->id;

		$message = 'You have successfully created a new class test';
		return Redirect::route('hsc_result.class_test.index', ['page' => $page])
						->with('success',$message)
						->withId($id);		

	}



	public function show($id) {

		$exist = ClassTest::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no class test with this id';
			return Redirect::route('hsc_result.class_test.index')->with('error',$error_message);
		endif;	

		$exam = ClassTest::find($id);

		$title = 'Easy CollegeMate - Exam - ' . $exam->name;
		$breadcrumb = 'hsc_result.class_test.index:Class Test List|Class Test - ' . $exam->name;

		return view('hsc_result.classtest.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExam($exam);		

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Class Test';
		$breadcrumb = 'hsc_result.class_test.index:Class Test|Edit Class Test';
		$exam = ClassTest::find($id);

		return view('BackEnd.hsc_result.classtest.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withExam($exam);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;		

		//Update Exam
		$exam = ClassTest::find($id);
		$exam->name = $request->get('name');
		$exam->update();

		//Page
		$count = ClassTest::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated the class test';
		return Redirect::route('hsc_result.class_test.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$exam = ClassTest::find($id);
		$exam->delete();

		//ClasseExam::whereExam_id($id)->delete();

		$error_message = 'You have deleted the class test';
		return Redirect::back()->with('warning',$error_message);	

	}
}

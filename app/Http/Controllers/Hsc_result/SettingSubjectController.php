<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingSubjectController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Subject';
		$breadcrumb = 'hsc_result.subject.index:Subject|Dashboard';
		
		$subjects = Subject::orderBy('id')->paginate(Ecm::paginate());
	
		$subject_type_lists = ['' => 'Select type', 0 => 'Compulsory', 1 => 'Optional'];

		return view('BackEnd.hsc_result.subject.index', compact('title', 'breadcrumb', 'subjects', 'subject_type_lists'));

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Subject';
		$breadcrumb = 'hsc_result.subject.index:Subject|Add Subject';
		

		return view('BackEnd.hsc_result.subject.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);				

	}



	public function store(Request $request) {

		$data = $request->all();

		$validation = Subject::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;		

		//Insert subject
		$subject = new Subject;
		$subject->name = $request->get('name');
		$subject->code = $request->get('code');		
		$subject->optional = $request->get('optional');
		$subject->save();

		
		//Page
		$page = ceil(Subject::count()/Ecm::paginate());
		$id = $subject->id;

		$message = 'You have successfully created a new subject';
		return Redirect::route('hsc_result.subject.index', ['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$exist = Subject::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no subject with this id';
			return Redirect::route('hsc_result.subject.index')->with('error',$error_message);
		endif;	

		$subject = Subject::find($id);

		$title = 'Easy CollegeMate - subject - ' . $subject->name;
		$breadcrumb = 'hsc_result.subject.index:Subject|Subject - ' . $subject->name;

		return view('hsc_result.subject.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withSubject($subject);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Subject';
		$breadcrumb = 'hsc_result.subject.index:Subject|Edit Subject';
		$subject = Subject::find($id);
	

		return view('BackEnd.hsc_result.subject.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withSubject($subject);				

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;	

		$data = $request->all();
		$validation = Subject::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

			

		//Update subject
		$subject = Subject::find($id);
		$subject->name = $request->get('name');
		$subject->code = $request->get('code');		
		$subject->optional = $request->get('optional');		
		$subject->update();			

				

		//Page
		$count = Subject::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated the subject';
		return Redirect::route('hsc_result.subject.index', ['page' => $page])
						->with('info',$message)
						->withId($id);		

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$subject = Subject::find($id);
		$subject->delete();

		$error_message = 'You have deleted the subject';
		return Redirect::back()->with('warning',$error_message);		

	}



	public function search() {

		$title = 'Easy CollegeMate - Subject';
		$breadcrumb = 'hsc_result.subject.index:Subject|Dashboard';

		//Search Subject
		$subject_name = Ecm::filterInput('subject_name', $request->get('subject_name'));		
		$optional = Ecm::filterInput('optional', $request->get('optional'));
	
		$subjects = Ecm::searchSubject($subject_name, $parent_subject, $optional);

		
		$subject_type_lists = ['' => 'Select type', 0 => 'Compulsory', 1 => 'Optional'];

		return view('hsc_result.subject.search', compact('title', 'breadcrumb', 'subjects', 'subject_type_lists', 'subject_name','optional'));		

	}
}

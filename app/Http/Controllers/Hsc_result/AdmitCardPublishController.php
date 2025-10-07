<?php

namespace App\Http\Controllers\Hsc_result;

use Ecm;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\AdmitCardPublish;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AdmitCardPublishController extends Controller
{
    public function index()
	{
		$title = 'Easy CollegeMate - HSC Admit Card Publish';
		$breadcrumb = 'hsc_result.admit_card_publish.index:Admit Card Publish|Dashboard';		
		$all_rslt = AdmitCardPublish::orderBy('id')->paginate(Ecm::paginate());
		
		
		return view('BackEnd.hsc_result.admit_card_publish.index', compact('title', 'breadcrumb', 'all_rslt'));
	}



	public function create()
	{
		$title = 'Easy CollegeMate - Add New';
		$breadcrumb = 'hsc_result.admit_card_publish.index:HSC Admit Card Publish|Add New';
		$level_list = ['' => 'Select level']+Classe::orderBy('id')->pluck('name','id')->toArray();
		$status_list = ['' => 'Select status'] + ['0'=>'Close']+['1'=>'Open'];
		$group_lists = create_option_array('groups', 'id', 'name', 'Group');
		$exam_lists = ['' => 'Select exam'] /*+ Exam::orderBy('id')->pluck('name', 'id')*/;
		$exam_tests = ['' => 'Select class test'];
		return view('BackEnd.hsc_result.admit_card_publish.create', compact('title', 'breadcrumb','status_list','level_list', 'group_lists', 'exam_lists', 'exam_tests'));
	}



	public function store(Request $request)
	{

		$this->validate($request, [
			'session' => 'required',
			'level' => 'required',
			'exam_id' => 'required',
			'exam_year' => 'required',
		]);
		
		$data = $request->all();
		$validation = AdmitCardPublish::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$session=$request->get('session');
		$level=$request->get('level');
		$group_id=$request->get('group_id');
		$exam_id=$request->get('exam_id');
		$exam_year=$request->get('exam_year');

		$check=AdmitCardPublish::whereSession($session)->whereLevel($level)->count();
		if($check!=0):
			$error_message = 'This Session & Level already Taken';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;

		$class = new AdmitCardPublish;
		$class->session = $request->get('session');
		$class->level = $request->get('level');
		$class->exam_id = $request->get('exam_id');		
		$class->exam_year = $request->get('exam_year');		
		$class->open = $request->get('open');
		$class->save();
		
		
		$page = ceil(AdmitCardPublish::count()/Ecm::paginate());
		$message = 'You have successfully created';
		return Redirect::route('hsc_result.admit_card_publish.index', ['page' => $page])
						->with('success',$message)
						->withId($class->id);
	}


	
	public function show($id)
	{
		//
	}


	public function edit($id)
	{
		$title = 'Easy CollegeMate - HSC Admit Card Publish';
		$breadcrumb = 'hsc_result.admit_card_publish.index:HSC Admit Card Publish|Edit Admit Card Publish';
		
		$status_list = ['' => 'Select status'] + ['0'=>'Close']+['1'=>'Open'];
		$class = AdmitCardPublish::find($id);
		$level_list = create_option_array('classes', 'id', 'name', 'Class');
		$group_lists = create_option_array('groups', 'id', 'name', 'Group');
		$exam_lists = ['' => 'Select exam'] /*+ Exam::orderBy('id')->pluck('name', 'id')*/;
		$exam_tests = ['' => 'Select class test'];
		
		return view('BackEnd.hsc_result.admit_card_publish.edit', compact('title', 'breadcrumb','status_list','level_list','class', 'group_lists', 'exam_lists', 'exam_tests'));	
	}


	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'session' => 'required',
			'level' => 'required',
			'exam_id' => 'required',
			'exam_year' => 'required',
		]);
		
		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;
		$data = $request->all();
		$validation = AdmitCardPublish::updateValidate($data);


		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$session=$request->get('session');
		$level=$request->get('level');
		$exam_id=$request->get('exam_id');
		$exam_year=$request->get('exam_year');

		$find_rslt=AdmitCardPublish::find($id);
		if($find_rslt->session!=$session && $find_rslt->level!=$level):
			$check=AdmitCardPublish::whereSession($session)->whereLevel($level)->count();			if($check!=0):
				$error_message = 'This Session & Level already Taken';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
		endif;	

		$class = AdmitCardPublish::find($id);
		$class->session = $request->get('session');
		$class->level = $request->get('level');
		$class->exam_id = $request->get('exam_id');		
		$class->exam_year = $request->get('exam_year');	
		$class->open = $request->get('open');
		$class->update();	

		$classe_id = $id;	
		
		$count = AdmitCardPublish::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated';		
		return Redirect::route('hsc_result.admit_card_publish.index', ['page' => $page])
						->with('info',$message)
						->withId($id);	
	}


	
	public function destroy($id)
	{
		//
	}
}

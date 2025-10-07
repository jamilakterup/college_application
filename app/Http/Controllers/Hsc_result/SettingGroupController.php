<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingGroupController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Group';
		$breadcrumb = 'hsc_result.group.index:Group|Dashboard';
		$departments = Group::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.group.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withDepartments($departments);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Group';
		$breadcrumb = 'hsc_result.group.index:Group|Add Group';

		return view('BackEnd.hsc_result.group.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);

	}



	public function store(Request $request) {

		$data = $request->all();

		$validation = Group::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	


		//Insert Department
		$department = new Group;
		$department->name = $request->get('name');
		$department->save();

		//Page
		$page = ceil(Group::count()/Ecm::paginate());
		$id = $department->id;

		$message = 'You have successfully created a new group';
		return Redirect::route('hsc_result.group.index', ['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$exist = Group::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no department with this id';
			return Redirect::route('hsc_result.group.index')->with('error',$error_message);
		endif;	

		$department = Group::find($id);

		$title = 'Easy CollegeMate - Department - ' . $department->name;
		$breadcrumb = 'setting.group.index:group|group - ' . $department->name;

		return view('setting.group.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withDepartment($department);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Group';
		$breadcrumb = 'hsc_result.group.index:Group|Edit Group';
		$department = Group::find($id);

		return view('BackEnd.hsc_result.group.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withDepartment($department);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;	

		$data = $request->all();
		$validation = Group::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//Update Department
		$department = Group::find($id);
		$department->name = $request->get('name');
		$department->update();

		//Page
		$count = Group::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated the group';
		return Redirect::route('hsc_result.group.index', ['page' => $page])
						->with('info',$message)
						->withId($id);		

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$department = Group::find($id);
		$department->delete();

		$error_message = 'You have deleted the group';
		return Redirect::back()->with('warning',$error_message);		

	}
}

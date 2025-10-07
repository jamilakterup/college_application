<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\AdmissionRequirement;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminRequirementController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Certificate Requirement Setup';
		$breadcrumb = 'admin.admission.index:Admission Management|admin.requirement.index:Certificate Requirement Setup|Dashboard';
		$requirements = Requirement::paginate(Study::paginate());

		return view('BackEnd.admin.requirement.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withRequirements($requirements);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Certificate Requirement';
		$breadcrumb = 'admin.admission.index:Admission Management|admin.requirement.index:Certificate Requirement Setup|requirement.create:Add Certificate Requirement';

		return view('BackEnd.admin.requirement.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Requirement::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$requirement = new Requirement;
		$requirement->certificate_full_name = $request->get('certificate_full_name');
		$requirement->certificate_short_name = $request->get('certificate_short_name');
		$requirement->save();

		$id = $requirement->id;

		$page = ceil(Requirement::count()/Study::paginate());

		$message = 'You have successfully created a new requirement';
		return Redirect::route('admin.requirement.index', ['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$requirement = Requirement::find($id);
		$title = 'Easy CollegeMate - Certificate Requirement Setup';
		$breadcrumb = 'admin.admission.index:Admission Management|Certificate Requirement - ' . $requirement->certificate_short_name;

		return view('admin.requirement.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withRequirement($requirement);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Certificate Requirement';
		$breadcrumb = 'admin.admission.index:Admission Management|admin.requirement.index:Certificate Requirement Setup|Edit Requirement';
		$requirement = Requirement::find($id);

		return view('BackEnd.admin.requirement.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withRequirement($requirement);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;				

		$data = $request->all();
		$validation = Requirement::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$requirement = Requirement::find($id);
		$requirement->certificate_full_name = $request->get('certificate_full_name');
		$requirement->certificate_short_name = $request->get('certificate_short_name');
		$requirement->update();

		$count = Requirement::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());

		$message = 'You have successfully updated the certificate requirement';
		return Redirect::route('admin.requirement.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$requirement = Requirement::find($id);
		$requirement->delete();

		AdmissionRequirement::where('requirement_id',$id)->delete();

		$error_message = 'You have deleted the certificate requirement';
		return Redirect::back()->withInput()->with('warning',$error_message);		

	}
}

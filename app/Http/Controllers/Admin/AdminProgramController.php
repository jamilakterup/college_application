<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Admission;
use App\Models\Course;
use App\Models\DeptProgram;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminProgramController extends Controller
{

	function __construct()
    {
         $this->middleware(
            'permission:program.index|program.create|program.edit|program.delete', ['only' => ['index','show']]);
         $this->middleware('permission:program.create', ['only' => ['create','store']]);
         $this->middleware('permission:program.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:program.delete', ['only' => ['destroy']]);
    }

    public function index() {

		$title = 'Easy CollegeMate - Program Management';
		$breadcrumb = 'admin.program.index:Program Management|Dashboard';
		$programs = Program::paginate(Study::paginate());

		return view('BackEnd.admin.program.index', compact('title', 'breadcrumb', 'programs'));

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Program';
		$breadcrumb = 'admin.program.index:Program Management|Add New Program';

		return view('BackEnd.admin.program.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Program::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//trim timeline
		$timeline = ltrim($request->get('timeline'), '0');
		$timeline = rtrim($timeline, '0');

		$program = new Program;
		$program->code = $request->get('code');
		$program->name = $request->get('name');
		$program->short_name = $request->get('short_name');
		$program->timeline = $timeline;
		$program->save();

		$id = $program->id;		

		$page = ceil(Program::count()/Study::paginate());		
		
		$message = 'You have successfully created a new program';
		return Redirect::route('admin.program.index', ['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$program = Program::find($id);
		$title = 'Easy CollegeMate - ' . $program->name;
		$breadcrumb = 'admin.program.index:Program Management|Program - ' . $program->name;

		return view('admin.program.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withProgram($program); 

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Program';
		$breadcrumb = 'admin.program.index:Program Management|Edit Program';
		$program = Program::find($id);

		return view('BackEnd.admin.program.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withProgram($program);

	}



	public function update(Request $request , $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		$data = $request->all();
		$validation = Program::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//trim timeline
		$timeline = ltrim($request->get('timeline'), '0');
		$timeline = rtrim($timeline, '0');		

		$program = Program::find($id);
		$program->code = $request->get('code');
		$program->name = $request->get('name');
		$program->short_name = $request->get('short_name');
		$program->timeline = $timeline;
		$program->update();

		$count = Program::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());			

		$message = 'You have successfully updated the program';
		return Redirect::route('admin.program.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$program = Program::find($id);
		$program->delete();

		DeptProgram::where('program_id', $id)->delete();
		Course::where('program_id', $id)->delete();
		Admission::where('program_id', $id)->delete();

		$error_message = 'You have deleted the program';
		return Redirect::back()->with('error',$error_message);

	}
}

<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use App\Models\Classe;
use App\Models\Group;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingClassController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Class';
		$breadcrumb = 'hsc_result.class.index:Class|Dashboard';		
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		
		return view('BackEnd.hsc_result.class.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClasses($classes);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Class';
		$breadcrumb = 'hsc_result.class.index:Class|Add Class';
		$departments = Group::orderBy('id')->get();

		return view('BackEnd.hsc_result.class.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withDepartments($departments);

	}



	public function store(Request $request) {

		$data = $request->all();

		$validation = Classe::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$class = new Classe;
		$class->name = $request->get('name');		
		$class->save();

		$classe_id = $class->id;

		$departments_id = [];
		$departments = Group::get();
		if($departments->count() > 0) :
			foreach($departments as $department) :
				$dept_id = $department->id;

				if($request->get('department-' . $dept_id) == $dept_id) :
					$departments_id[] = $dept_id;
				endif;	
			endforeach;

			if(count($departments_id) > 0) :
				foreach($departments_id as $department_id) :
					$data_array = ['classe_id' => $classe_id, 'group_id' => $department_id];
					ClassGroup::create($data_array);
				endforeach;	
			endif;	
		endif;

		//Insert ClasseDepartment
		
		$page = ceil(classe::count()/Ecm::paginate());	

		$message = 'You have successfully created a new class';
		return Redirect::route('hsc_result.class.index', ['page' => $page])
						->with('success',$message)
						->withId($classe_id);						

	}



	public function show($id) {

		$exist = Classe::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no class with this id';
			return Redirect::route('hsc_result.class.index')->with('error',$error_message);
		endif;	

		$class = Classe::find($id);
		$title = 'Easy CollegeMate - Class - ' . $class->name;
		$breadcrumb = 'hsc_result.class.index:Class|Class - ' . $class->name;

		return view('hsc_result.class.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClass($class);		

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Class';
		$breadcrumb = 'hsc_result.class.index:Class|Edit Class';
		$departments = Group::orderBy('id')->get();
		$class = Classe::find($id);

		return view('BackEnd.hsc_result.class.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withDepartments($departments)
					->withClass($class);		

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;
		$data = $request->all();
		$validation = Classe::updateValidate($data);


		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$class = Classe::find($id);
		$class->name = $request->get('name');						
		$class->update();	

		$classe_id = $id;

		 ClassGroup::whereClasse_id($id)->delete();
		
		$departments_id = [];
		$departments = group::get();
		if($departments->count() > 0) :
			foreach($departments as $department) :
				$dept_id = $department->id;

				if($request->get('department-' . $dept_id) == $dept_id) :
					$departments_id[] = $dept_id;
				endif;	
			endforeach;

			if(count($departments_id) > 0) :
				foreach($departments_id as $department_id) :
					$data_array = ['classe_id' => $classe_id, 'group_id' => $department_id];
					ClassGroup::create($data_array);
				endforeach;	
			endif;	
		endif;

		//Delete & Insert = Update ClasseDepartment
		
		$count = Classe::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated the class';		
		return Redirect::route('hsc_result.class.index', ['page' => $page])
						->with('info',$message)
						->withId($id);					

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$class_sub = ClassSubject::where('classe_id', $id)->get();
		if (count($class_sub) > 0) {
			$error_message = 'Subject already assign for this class!';
			return Redirect::back()->with('error',$error_message);
		}

		$class = classe::find($id);
		$class->delete();					

		$error_message = 'You have deleted the class';
		return Redirect::back()->with('warning',$error_message);		

	}
}

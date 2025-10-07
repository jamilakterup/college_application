<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Department;
use App\Models\DeptHead;
use App\Models\DeptProgram;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AdminDeptController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:department.create', ['only' => ['create','store']]);
         $this->middleware('permission:department.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:department.delete', ['only' => ['destroy']]);
         $this->middleware(
            'permission:department.index|department.create|department.edit|department.delete', ['only' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $title = 'Easy CollegeMate - Department Management';
        $breadcrumb = 'admin.dept.index:Department Management|Dashboard';
        $depts = Department::paginate(Study::paginate());

        return view('BackEnd.admin.dept.index')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withDepts($depts);

    }



    public function create() {

        $title = 'Easy CollegeMate - Add New Department';
        $breadcrumb = 'admin.dept.index:Department Management|Add New Department';
        $programs = Program::get();

        return view('BackEnd.admin.dept.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withPrograms($programs);

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = Department::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;

        $department = new Department;
        $department->faculty_id = $request->get('faculty_id');
        $department->dept_code = $request->get('dept_code');
        $department->dept_name = $request->get('dept_name');
        $department->short_name = $request->get('short_name'); 
        $department->seat = $request->get('seat');
        $department->save();

        $id = $department->id;                          

        //Insert value into DeptProgram
        $dept_code = $request->get('dept_code');
        $department_id = Department::where('dept_code',$dept_code)->pluck('id');

        $programs_id = [];

        $programs = Program::get();

        if($programs->count() > 0) :

            foreach($programs as $program) :
                $p_id = $program->id;

                if($request->get($p_id) == $p_id) :
                    $programs_id[] = $p_id;
                endif;  
            endforeach;

            if(count($programs_id) > 0) :
                foreach($programs_id as $program_id) :
                    $data_array = ['department_id' => $department_id, 'program_id' => $program_id];
                    DeptProgram::create($data_array);
                endforeach; 
            endif;  

        endif;

        $page = ceil(Department::count()/Study::paginate());    

        $message = 'You have successfully created a new department';
        return Redirect::route('admin.dept.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);

    }

    

    public function show($id) {

        $dept = Department::find($id);
        $title = 'Easy CollegeMate - Department - ' . $dept->dept_name;
        $breadcrumb = 'admin.dept.index:Department Management|Department - ' . $dept->dept_name;

        return view('admin.dept.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withDept($dept);

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit Department';
        $breadcrumb = 'admin.dept.index:Department Management|Edit Department';
        $programs = Program::get();     
        $dept = Department::find($id);

        return view('BackEnd.admin.dept.edit')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withPrograms($programs)
                    ->withDept($dept);

    }



    public function update(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $data = $request->all();
        $validation = Department::updateValidate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $dept = Department::find($id);
        $dept->faculty_id = $request->get('faculty_id');
        $dept->dept_code = $request->get('dept_code');
        $dept->dept_name = $request->get('dept_name');
        $dept->short_name = $request->get('short_name');
        $dept->seat = $request->get('seat');   
        $dept->update();

        //Update value into DeptProgram
        $department_id = $id;
        $dept_programs = DeptProgram::where('department_id', $department_id)->get();

        //make all programs status of the department zero
        if($dept_programs->count() > 0) :
            foreach($dept_programs as $dept_program) :
                $dept_program->status = 0;
                $dept_program->update();
            endforeach; 
        endif;  

        //get all checked programs id
        $programs_id = [];

        $programs = Program::get();

        if($programs->count() > 0) :

            foreach($programs as $program) :
                $p_id = $program->id;

                if($request->get($p_id) == $p_id) :
                    $programs_id[] = $p_id;
                endif;  
            endforeach;

            if(count($programs_id) > 0) :
                foreach($programs_id as $program_id) :

                    //if exists then update, else insert
                    $exists = DeptProgram::where('department_id', $department_id)->where('program_id', $program_id)->get();

                    if($exists->count() > 0) :
                        $dept_program_id = DeptProgram::where('department_id', $department_id)->where('program_id', $program_id)->pluck('id');
                        $dept_program = DeptProgram::find($dept_program_id);
                        $dept_program->status = 1;
                        $dept_program->update();    
                    else :
                        $data_array = ['department_id' => $department_id, 'program_id' => $program_id];
                        DeptProgram::create($data_array);
                    endif;

                endforeach; 
            endif;  

        endif;      

        $count = Department::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate()); 

        $message = 'You have successfully updated the department';
        return Redirect::route('admin.dept.index', ['page' => $page])
                        ->with('info',$message)
                        ->withId($id);                  

    }



    public function destroy(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $dept = Department::find($id);
        $dept->delete();

        DeptHead::where('department_id', $id)->delete();
        DeptProgram::where('department_id', $id)->delete(); 
        Course::where('department_id', $id)->delete();  
        Admission::where('department_id', $id)->delete();

        $error_message = 'You have deleted the department';
        return Redirect::back()->with('warning',$error_message);     

    }
}

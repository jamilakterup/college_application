<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FacultyHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Auth;

class AdminFacultyController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:faculty.index|faculty.create|faculty.edit|faculty.delete', ['only' => ['index','show']]);
         $this->middleware('permission:faculty.create', ['only' => ['create']]);
         $this->middleware('permission:faculty.edit', ['only' => ['edit']]);
         $this->middleware('permission:faculty.delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $title = 'Easy CollegeMate - Faculty';
        $breadcrumb = 'admin.faculty.index:Faculty Management|Dashboard';
        $faculties = Faculty::paginate(Study::paginate());
        return view('BackEnd.admin.faculty.index')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withFaculties($faculties);

    }



    public function create() {

        $title = 'Easy CollegeMate - Add Faculty';
        $breadcrumb = 'admin.faculty.index:Faculty Management|Add New Faculty';
        
        return view('BackEnd.admin.faculty.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = Faculty::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;

        $faculty = new Faculty;
        $faculty->faculty_code = $request->get('faculty_code');
        $faculty->faculty_name = $request->get('faculty_name');
        $faculty->short_name = $request->get('short_name');
        $faculty->save();

        $id = $faculty->id;

        $page = ceil(Faculty::count()/Study::paginate());       
        
        $message = 'You have successfully created a new faculty';
        return Redirect::route('admin.faculty.index', ['page' => $page])
                        ->with('success', $message)
                        ->withId($id);

    }



    public function show($id) {

        $faculty = Faculty::find($id);
        $title = 'Easy CollegeMate - ' . $faculty->faculty_name;
        $breadcrumb = 'admin.faculty.index:Faculty Management|Faculty - ' . $faculty->faculty_name;

        return View::make('admin.faculty.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withFaculty($faculty);

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit Faculty';
        $breadcrumb = 'admin.faculty.index:Faculty Management|Edit Faculty';        
        $faculty = Faculty::find($id);
        
        return view('BackEnd.admin.faculty.edit')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withFaculty($faculty);

    }



    public function update(Request $request, $id) {
        
        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $data = $request->all();
        $validation = Faculty::updateValidate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;

        $faculty = Faculty::find($id);
        $faculty->faculty_code = $request->get('faculty_code');
        $faculty->faculty_name = $request->get('faculty_name');
        $faculty->short_name = $request->get('short_name');
        $faculty->update();

        $count = Faculty::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());         

        $message = 'You have successfully updated the faculty';
        return Redirect::route('admin.faculty.index', ['page' => $page])
                        ->with('info', $message)
                        ->withId($id);
        
    }



    public function destroy(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $id = $request->get('id');
        $faculty = Faculty::find($id);
        $faculty->delete();

        //delete faculty head
        FacultyHead::where('faculty_id', $id)->delete();

        //delete departments, departments head and its program that are in the faculty 
        $departments = Department::where('faculty_id', $id)->get();
        foreach($departments as $department) :
            $department_id = $department->id;
            DeptProgram::where('department_id', $department_id)->delete();
            DeptHead::where('department_id', $department_id)->delete();
            $department->delete(); 
        endforeach;

        $error_message = 'You have deleted the faculty';
        return Redirect::back()->with('warning', $error_message);
        
    }
}

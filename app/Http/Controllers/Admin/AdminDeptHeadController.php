<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\DeptHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminDeptHeadController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:department.head');
    }

    public function index() {

        $title = 'Easy CollegeMate - Department Head';
        $breadcrumb = 'admin.dept.index:Department Management|admin.dept_head.index:Department Head|Dashboard';
        $dept_heads = DeptHead::paginate(Study::paginate());

        return view('BackEnd.admin.dept_head.index', compact('title', 'breadcrumb', 'dept_heads'));

    }



    public function create() {

        $title = 'Easy CollegeMate - Add Department Head';
        $breadcrumb = 'admin.dept.index:Department Management|admin.dept_head.index:Department Head|Add New Department Head';

        return view('BackEnd.admin.dept_head.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = DeptHead::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $dept_head = new DeptHead;
        $dept_head->department_id = $request->get('department_id');
        $dept_head->name = $request->get('name');
        $dept_head->starting_date = $request->get('starting_date');
        $dept_head->end_date = $request->get('end_date');
        $dept_head->status = $request->get('status');
        $dept_head->save();                                             

        $id = $dept_head->id;

        $page = ceil(DeptHead::count()/Study::paginate());      
        
        $message = 'You have successfully created a new department head';
        return Redirect::route('admin.dept_head.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);              

    }



    public function show($id) {

        $dept_head = DeptHead::find($id);
        $title = 'Easy CollegeMate - Department Head - ' . $dept_head->name;
        $breadcrumb = 'admin.dept.index:Department Management|admin.dept_head.index:Department Head|' . $dept_head->name;

        return view('BackEnd.admin.dept_head.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withDept_head($dept_head);

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit Department Head';
        $breadcrumb = 'admin.dept.index:Department Management|admin.dept_head.index:Department Head|Edit Department Head';
        $dept_head = DeptHead::find($id);

        return view('BackEnd.admin.dept_head.edit', compact('title', 'breadcrumb', 'dept_head'));

    }



    public function update(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $data = $request->all();
        $validation = DeptHead::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $dept_head = DeptHead::find($id);
        $dept_head->department_id = $request->get('department_id');
        $dept_head->name = $request->get('name');
        $dept_head->starting_date = $request->get('starting_date');
        $dept_head->end_date = $request->get('end_date');
        $dept_head->status = $request->get('status');
        $dept_head->update();

        $count = DeptHead::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());         

        $message = 'You have successfully updated the department head';
        return Redirect::route('admin.dept_head.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);                  

    }



    public function destroy(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $id = $request->get('id');
        $dept_head = DeptHead::find($id);
        $dept_head->delete();

        $error_message = 'You have deleted the department head';
        return Redirect::back()->with('warning',$error_message);

    }
}

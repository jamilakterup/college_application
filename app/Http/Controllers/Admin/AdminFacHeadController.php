<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\FacultyHead;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminFacHeadController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:faculty.head');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $title = 'Easy CollegeMate - Faculty Head';
        $breadcrumb = 'admin.faculty.index:Faculty Management|admin.fac_head.index:Faculty Head|Dashboard';
        $faculty_heads = FacultyHead::paginate(Study::paginate());

        return view('BackEnd.admin.fac_head.index', compact('faculty_heads'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }



    public function create() {

        $title = 'Easy CollegeMate - Add Faculty Head';
        $breadcrumb = 'admin.faculty.index:Faculty management|admin.fac_head.index:Faculty Head|Add New Faculty Head';

        return view('BackEnd.admin.fac_head.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = FacultyHead::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $faculty_head = new FacultyHead;
        $faculty_head->faculty_id = $request->get('faculty_id');
        $faculty_head->name = $request->get('name');
        $faculty_head->status = $request->get('status');
        $faculty_head->starting_date = $request->get('starting_date');
        $faculty_head->end_date = $request->get('end_date');
        $faculty_head->save();

        $id = $faculty_head->id;                                

        $page = ceil(FacultyHead::count()/Study::paginate());       
        
        $message = 'You have successfully created a new faculty head';  
        return Redirect::route('admin.fac_head.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);

    }



    public function show($id) {

        $faculty_head = FacultyHead::find($id);
        $title = 'Easy CollegeMate - Faculty Head - ' . $faculty_head->name;
        $breadcrumb = 'admin.faculty.index:Faculty Management|admin.fac_head.index:Faculty Head|' . $faculty_head->name;

        return view('BackEnd.admin.fac_head.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withFaculty_head($faculty_head);  

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit Faculty Head';
        $breadcrumb = 'admin.faculty.index:Faculty Management|admin.fac_head.index:Faculty Head|Edit Faculty Head';
        $faculty_head = FacultyHead::find($id);

        return view('BackEnd.admin.fac_head.edit', compact('faculty_head'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }



    public function update(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;
            
        $data = $request->all();
        $validation = FacultyHead::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $faculty_head = FacultyHead::find($id);
        $faculty_head->faculty_id = $request->get('faculty_id');
        $faculty_head->name = $request->get('name');   
        $faculty_head->status = $request->get('status');
        $faculty_head->starting_date = $request->get('starting_date');
        $faculty_head->end_date = $request->get('end_date');   
        $faculty_head->update();

        $count = FacultyHead::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());         

        $message = 'You have successfully updated the faculty head';
        return Redirect::route('admin.fac_head.index', ['page' => $page])
                        ->with('info', $message)
                        ->withId($id);

    }



    public function destroy(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $id = $request->get('id');
        $faculty_head = FacultyHead::find($id);
        $faculty_head->delete();

        $error_message = 'You have deleted the faculty head';
        return Redirect::back()->with('warning',$error_message);

    }
}

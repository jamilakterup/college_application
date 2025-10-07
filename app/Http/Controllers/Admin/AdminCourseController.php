<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminCourseController extends Controller
{
    public function index() {

        $title = 'Easy CollegeMate - Course Management';
        $breadcrumb = 'admin.course.index:Course Management|Dashboard';
        $courses = Course::paginate(Study::paginate());

        //search filter form data
        $levels = Course::groupBy('level')->distinct()->get();
        $sessions = Course::groupBy('session')->distinct()->get();

        return view('BackEnd.admin.course.index', compact('title', 'breadcrumb', 'courses', 'levels', 'sessions'));

    }



    public function create() {

        $title = 'Easy CollegeMate - Add Course';
        $breadcrumb = 'admin.course.index:Course Management|Add New Course';

        //sessions dropdown list
        $sessions = [];
        $current_year = date('Y');
        $initial_year = $current_year - 5;
        $final_year = $current_year + 5;

        foreach(range($initial_year, $final_year) as $index) :
            $this_year = $index;
            $next_year = $index+1;
            $sessions[] = $this_year . '-' . $next_year;
        endforeach;

        return view('BackEnd.admin.course.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withSessions($sessions);

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = Course::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        //department has the program?
        $department_id = $request->get('department_id');
        $program_id = $request->get('program_id');
        $checker_a = Study::deptHasProgram($department_id, $program_id);

        if($checker_a == false) :
            $error_message = 'The department is not associated with the program! Please try again';
            return Redirect::back()->withInput()->with('error', $error_message);
        endif;

        //course code already has in this session?
        $code = $request->get('code');
        $session = $request->get('session');
        $checker_b = Study::courseCode($code, $session);

        if($checker_b == false) :
            $error_message = 'The course code has already taken in the session! Please try again';
            return Redirect::back()->withInput()->with('error', $error_message);
        endif;  

        //insert course
        $course = new Course;
        $course->department_id = $department_id;
        $course->program_id = $program_id;
        $course->code = $request->get('code');
        $course->name = $request->get('name');
        $course->mark = $request->get('mark');
        $course->type = $request->get('type');
        $course->level = $request->get('level');
        $course->session = $request->get('session');
        $course->save();

        $id = $course->id;

        $page = ceil(Course::count()/Study::paginate());

        $message = 'You have successfully created a new course';
        return Redirect::route('admin.course.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);

    }



    public function show($id) {

        $course = Course::find($id);
        $title = 'Easy CollegeMate - Course - ' . $course->name;
        $breadcrumb = 'admin.course.index:Course Management|Course - ' . $course->name;

        return view('admin.course.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withCourse($course);

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit Course';
        $breadcrumb = 'admin.course.index:Course Management|Edit Course';
        $course = Course::find($id);

        //sessions dropdown list
        $this_course_session = $course->session;
        $this_session_apart = explode('-', $this_course_session);

        $sessions = [];
        $this_course_year = $this_session_apart[0];
        $initial_year = $this_course_year - 5;
        $final_year = $this_course_year + 5;

        foreach(range($initial_year, $final_year) as $index) :
            $this_year = $index;
            $next_year = $index+1;
            $sessions[] = $this_year . '-' . $next_year;
        endforeach;

        return view('BackEnd.admin.course.edit')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withSessions($sessions)
                    ->withCourse($course);      

    }



    public function update(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error', $error_message);
        endif;      

        $data = $request->all();
        $validation = Course::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        //department has the program?
        $department_id = $request->get('department_id');
        $program_id = $request->get('program_id');
        $checker_a = Study::deptHasProgram($department_id, $program_id);

        if($checker_a == false) :
            $error_message = 'The department is not associated with the program! Please try again';
            return Redirect::back()->withInput()->with('error', $error_message);
        endif;  

        //course code already has in this session?
        $code = $request->get('code');
        $session = $request->get('session');
        $checker_b = Study::courseCodeUpdate($id, $code, $session);

        if($checker_b == false) :
            $error_message = 'The course code has already taken in the session! Please try again';
            return Redirect::back()->withInput()->with('error', $error_message);
        endif;          

        //update course
        $course = Course::find($id);
        $course->department_id = $department_id;    
        $course->program_id = $program_id;
        $course->code = $request->get('code');
        $course->name = $request->get('name');
        $course->mark = $request->get('mark');
        $course->type = $request->get('type');
        $course->level = $request->get('level');
        $course->session = $request->get('session');
        $course->update();

        $count = Course::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());         

        $message = 'You have successfully updated the course';      
        return Redirect::route('admin.course.index', ['page' => $page])
                        ->with('info',$message)
                        ->withId($id);


    }



    public function destroy($id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error', $error_message);
        endif;

        $course = Course::find($id);
        $course->delete();
        CourseTeacher::whereCourse_id($id)->delete();

        $error_message = 'You have deleted the course';
        return Redirect::back()->with('error', $error_message); 

    }



    public function search(Request $request) {

        //title and breadcrumb
        $title = 'Easy CollegeMate - Course Management';
        $breadcrumb = 'admin.course.index:Course Management|Dashboard';     

        //search courses outcomes
        $code = Study::filterInput('code', $request->get('code'));
        $department_id = Study::filterInput('department_id', $request->get('department_id'));
        $level = Study::filterInput('level', $request->get('level'));
        $session = Study::filterInput('session', $request->get('session'));

        $courses = Study::searchCourse($code, $department_id, $level, $session);

        //search filter form data
        $levels = Course::groupBy('level')->distinct()->get();
        $sessions = Course::groupBy('session')->distinct()->get();      
        $course_code_lists = ['' => 'Course Code'] + Course::lists('code', 'code');
        $department_lists = ['' => 'Select Department'] + Department::lists('dept_name', 'id');

        return view('BackEnd.admin.course.index', compact('title', 'breadcrumb', 'courses', 'levels', 'sessions'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withCourses($courses)
                    ->withLevels($levels)
                    ->withSessions($sessions)
                    ->withCourse_code_lists($course_code_lists)
                    ->withDepartment_lists($department_lists)
                    ->withCode($code)
                    ->withDepartment_id($department_id)
                    ->withPre_session($session)
                    ->withPre_level($level);

    }
}

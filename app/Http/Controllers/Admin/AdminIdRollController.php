<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\IdRoll;
use Illuminate\Http\Request;
use DB;

class AdminIdRollController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:id_roll.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $title = 'Easy CollegeMate - Id Roll Management';
        $breadcrumb = 'admin.id_roll.create:Id Roll Management|Dashboard';
        $departments = Department::all();
        $session = 0;
        $id_rolls = [];

        if ($request->isMethod('post')) {

            if ($request->search != '') {
                $this->validate($request, [
                    'session' => 'required|min:0|not_in:0'
                ], [
                     'session.not_in' => 'The :attribute field is required!'
                ]);

            }

            // return $request->all();

            $session = $request->session;

            $id_rolls = IdRoll::where('session', $request->session)->get();

            if ($request->submit != '') {

                $dept_name = $request->dept_name;
                $start_digit_dept = $request->start_digit;
                $end_digit_dept = $request->end_digit;

                $hsc_group =$request->hsc_group;
                $start_digit_hsc =$request->start_digit_hsc;
                $end_digit_hsc =$request->end_digit_hsc;

                $degree_group =$request->degree_group;
                $start_digit_degree =$request->start_digit_degree;
                $end_digit_degree = $request->end_digit_degree;

                $count_subject = count($request->dept_name);
                $count_hsc = count($request->hsc_group);
                $count_degree = count($request->degree_group);

                // starting dept_name
                for ($i=0; $i < $count_subject; $i++) {
                    $last_digit_used=$start_digit_dept[$i]-1;
                    $start_digit =$start_digit_dept[$i];
                    $end_digit =$end_digit_dept[$i];

                    $name = 'honours_'.$dept_name[$i];

                    $exists_id_roll = IdRoll::where('session', $session)->where('dept_name', $name)->get();
                    if (count($exists_id_roll) < 1) {
                        $id_roll = new IdRoll;
                        $id_roll->session = $session;
                        $id_roll->dept_name = $name;
                        $id_roll->last_digit_used = default_zero($last_digit_used);
                        $id_roll->start_digit = default_zero($start_digit);
                        $id_roll->end_digit = default_zero($end_digit);
                        $id_roll->save();
                    }
                }

                // starting masters_1_

                for ($i=0; $i < $count_subject; $i++) {
                    $last_digit_used=$start_digit_dept[$i]-1;
                    $start_digit =$start_digit_dept[$i];
                    $end_digit =$end_digit_dept[$i];

                    $name = 'masters_1_'.$dept_name[$i];

                    $exists_id_roll = IdRoll::where('session', $session)->where('dept_name', $name)->get();
                    if (count($exists_id_roll) < 1) {
                        $id_roll = new IdRoll;
                        $id_roll->session = $session;
                        $id_roll->dept_name = $name;
                        $id_roll->last_digit_used = default_zero($last_digit_used);
                        $id_roll->start_digit = default_zero($start_digit);
                        $id_roll->end_digit = default_zero($end_digit);
                        $id_roll->save();
                    }
                }

                // starting masters_2_

                for ($i=0; $i < $count_subject; $i++) {
                    $last_digit_used=$start_digit_dept[$i]-1;
                    $start_digit =$start_digit_dept[$i];
                    $end_digit =$end_digit_dept[$i];

                    $name = 'masters_2_'.$dept_name[$i];

                    $exists_id_roll = IdRoll::where('session', $session)->where('dept_name', $name)->get();
                    if (count($exists_id_roll) < 1) {
                        $id_roll = new IdRoll;
                        $id_roll->session = $session;
                        $id_roll->dept_name = $name;
                        $id_roll->last_digit_used = default_zero($last_digit_used);
                        $id_roll->start_digit = default_zero($start_digit);
                        $id_roll->end_digit = default_zero($end_digit);
                        $id_roll->save();
                    }
                }



                // starting hsc_group
                for ($i=0; $i < $count_hsc; $i++) {
                    $last_digit_used=$start_digit_hsc[$i]-1;
                    $start_digit =$start_digit_hsc[$i];
                    $end_digit =$end_digit_hsc[$i];

                    $hsc_group_name = $hsc_group[$i];

                    $exists_id_roll = IdRoll::where('session', $session)->where('dept_name', $hsc_group_name)->get();
                    if (count($exists_id_roll) < 1) {
                        $id_roll = new IdRoll;
                        $id_roll->session = $session;
                        $id_roll->dept_name = $hsc_group_name;
                        $id_roll->last_digit_used = default_zero($last_digit_used);
                        $id_roll->start_digit = default_zero($start_digit);
                        $id_roll->end_digit = default_zero($end_digit);
                        $id_roll->save();
                    }
                }

                // starting degree_group
                for ($i=0; $i < $count_degree; $i++) {
                    $last_digit_used=$start_digit_degree[$i]-1;
                    $start_digit =$start_digit_degree[$i];
                    $end_digit =$end_digit_degree[$i];

                    $degree_group_name = $degree_group[$i];

                    $exists_id_roll = IdRoll::where('session', $session)->where('dept_name', $degree_group_name)->get();
                    if (count($exists_id_roll) < 1) {
                        $id_roll = new IdRoll;
                        $id_roll->session = $session;
                        $id_roll->dept_name = $degree_group_name;
                        $id_roll->last_digit_used = default_zero($last_digit_used);
                        $id_roll->start_digit = default_zero($start_digit);
                        $id_roll->end_digit = default_zero($end_digit);
                        $id_roll->save();
                    }
                }


            }

            
        }
        
        return view('BackEnd.admin.id_roll.create', compact('id_rolls'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withDepartments($departments)
                    ->withSession($session);
    }

    public function edit(Request $request, $id){
        $title = 'Easy CollegeMate - Id Roll Management';
        $id_roll = IdRoll::find($id);

        $breadcrumb = "admin.id_roll.create:Id Roll Management|$id_roll->dept_name";
        if($request->isMethod('post')){
            $this->validate($request, [
                'last_digit_used' => 'required|numeric',
                'start_digit' => 'required|numeric',
                'end_digit' => 'required|numeric',

            ]);

            $id_roll->last_digit_used = $request->last_digit_used;
            $id_roll->start_digit = $request->start_digit;
            $id_roll->end_digit = $request->end_digit;
            $id_roll->save();
            return redirect()->back()->with('info', 'Id Roll is Successfully Updated');
        }else{
            return view('BackEnd.admin.id_roll.edit', compact('id_roll'))
                        ->withTitle($title)
                        ->withBreadcrumb($breadcrumb);
        }
    }

    public function new(){
        return view('BackEnd.admin.id_roll.new');
    }

    public function new_store(Request $request){
        $this->validate($request, [
            'course' => 'required',
            'start_digit' => 'required|numeric',
            'end_digit' => 'required|numeric',
            'last_digit_used' => 'required|numeric'
        ]);

        if (($request->faculty != '' && $request->dept_name !='') || ($request->faculty == '' && $request->dept_name =='')) {
            return redirect()->back()->with('warning', 'Please Select Facult or Department One')->withInput();
        }

        if ($request->faculty != '' && $request->course !='hsc') {
            return redirect()->back()->with('warning', 'Please Select Course with Department')->withInput();
        }

        if($request->faculty != ''){
            $dept_name = 'hsc_'.$request->faculty;
        }

        if ($request->dept_name != '') {
            switch ($request->course) {
                case 'honours':
                    $dept_name = 'honours_'.$request->dept_name;
                    break;
                case 'masters_2':
                    $dept_name = 'masters_2_'.$request->dept_name;
                    break;
                case 'masters_1':
                    $dept_name = 'masters_1_'.$request->dept_name;
                    break;
                case 'degree':
                    $dept_name = 'degree_'.$request->dept_name;
                    break;
            }
        }

        $id_rolls = IdRoll::where('dept_name', $dept_name)->where('session', $request->session)->get();
        if (count($id_rolls) > 0) {
            return redirect()->back()->with('error', " ID Roll Already Exists for $dept_name")->withInput();
        }else{
            $id_roll = new IdRoll;
            $id_roll->dept_name = $dept_name;
            $id_roll->session = $request->session;
            $id_roll->last_digit_used = $request->last_digit_used;
            $id_roll->start_digit = $request->start_digit;
            $id_roll->end_digit = $request->end_digit;
            $id_roll->save();

            return redirect()->back()->with('success', " ID Roll Created Successfully for $dept_name")->withInput();
        }
    }
}

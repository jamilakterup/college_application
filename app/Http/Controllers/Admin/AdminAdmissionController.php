<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Admission;
use App\Models\AdmissionConfig;
use App\Models\Department;
use App\Models\Program;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use DataTables;

class AdminAdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.admission.index:Admission Management|Dashboard';
        $admissions = Admission::orderBy('open_date', 'desc')->paginate(Study::paginate());
        //return $admissions;
        //search filter form data
        $sessions = Admission::groupBy('session')->distinct()->get();   
        // return $sessions;
        $department_lists = ['' => 'Select Department'] + Department::pluck('dept_name', 'id')->all();
        $program_lists = ['' => 'Select Program'] + Program::pluck('short_name', 'id')->all();
        //return $program_lists;
        $status_lists = ['' => 'Select Status', 1 => 'Open', 0 => 'Closed'];
        $slip_type_lists = slip_type_lists();

        
        /*foreach ($admissions as  $value) {
         echo $value->session;
         echo $value->department['dept_name'];
        }*/

        return view('BackEnd.admin.admission.index')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withAdmissions($admissions)
                    ->withSessions($sessions)
                    ->withSlip_type_lists($slip_type_lists)
                    
                    ->withDepartment_lists($department_lists)
                    ->withProgram_lists($program_lists)
                    ->withStatus_lists($status_lists);
    }

    public function level()
   {

       if(Request::ajax())
        {
            echo json_encode("hello");
            /*$faculty_name=$request->get('faculty');
            
          
            $results=DB::table('faculties')->where('faculty_name', $faculty_name )->first();
            $faculty_id= $results->id;

            $result = DB::table('departments')
                    ->select('dept_name')
                    ->Where('faculty_id', $faculty_id)
                    ->get();

           foreach ($result as  $value)
            {                          

              echo  "<option value='{$value->dept_name}'>{$value->dept_name}</option>";               
            }            */

        }                    
        
    }


    public function create() {

        $title = 'Easy CollegeMate - Open Admission';
        $breadcrumb = 'admin.admission.index:Admission Management|Open New Admission';

        $requirements = Requirement::all();
        $payslip_lists = ['' => 'Select PaySlip'] + PayslipTitle::whereStatus(1)->lists('title', 'id');

        //sessions dropdown list
        $sessions = [];
        $current_year = date('Y');
        $initial_year = $current_year - 10;
        $final_year = $current_year + 10;

        foreach(range($initial_year, $final_year) as $index) :
            $this_year = $index;
            $next_year = $index+1;
            $sessions[] = $this_year . '-' . $next_year;
        endforeach; 
        $slip_type_lists = ['' => 'Select Type', 'Admission' => 'Admission', 'Formfillup' => 'Formfillup'];

        $current_level_lists = ['' => 'Select Level', 'Honours 1st year' => 'Honours 1st year', 'Honours 2nd year' => 'Honours 2nd year', 'Honours 3rd year' => 'Honours 3rd year', 'Honours 4th year' => 'Honours 4th year'];  

        return view('admin.admission.create')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withSessions($sessions)
                    ->withCurrent_level_lists($current_level_lists)
                    ->withSlip_type_lists($slip_type_lists)
                    ->withRequirements($requirements)
                    ->withPayslip_lists($payslip_lists);

    }



    public function store() {

        $data = $request->all();

        $validation = Admission::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        //The department open the program or not
        /*$the_program_exists = DeptProgram::whereDepartment_id($request->get('department_id'))->whereProgram_id($request->get('program_id'))->count();
        if($the_program_exists == 0) :
            $error_message = 'The department does not open the program';
            return Redirect::back()->withInput()->with('error',$error_message);
        endif;*/    

        //Insert admission
        $admission = new Admission;
        $admission->department_id = $request->get('department_id');
        $admission->program_id = $request->get('program_id');
        $admission->paysliptitle_id = $request->get('paysliptitle_id');    
        $admission->session = $request->get('session');
        $admission->current_level = $request->get('current_level');                
        $admission->slip_type = $request->get('slip_type');
        $admission->open_date = $request->get('open_date');
        $admission->close_date = $request->get('close_date');
        $admission->save();

        $id = $admission->id;

        //Insert value into AdmissionRequirement
        $admission_id = $id;

        $requirements_id = [];

        $requirements = Requirement::get();

        if($requirements->count() > 0) :

            foreach($requirements as $requirement) :
                $r_id = $requirement->id;

                if($request->get($r_id) == $r_id) :
                    $requirements_id[] = $r_id;
                endif;  
            endforeach;

            if(count($requirements_id) > 0) :
                foreach($requirements_id as $requirement_id) :
                    $data_array = ['admission_id' => $admission_id, 'requirement_id' => $requirement_id];
                    AdmissionRequirement::create($data_array);
                endforeach; 
            endif;  

        endif;      

        //Pagination Page
        $open_date = $request->get('open_date');
        $count = Admission::where('open_date', '>=', $open_date)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully opened a new admission';
        return Redirect::route('admin.admission.index', ['page' => $page])
                        ->with('success', $message)
                        ->withId($id);

    }



    public function show($id) {

        $admission = Admission::find($id);
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.admission.index:Admission Management|' . $admission->program->short_name . ' in ' . $admission->department->dept_name;
        $total_fees = PayslipGenerator::wherePaysliptitle_id($id)->sum('fees');

        return view('admin.admission.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withAdmission($admission)
                    ->withTotal_fees($total_fees);

    }



    public function edit($id) {

        $title = 'Admission Management - Edit Admission';
        $breadcrumb = 'admin.admission.index:Admission Management|Edit Admission';
        $admission = Admission::find($id);

        $requirements = Requirement::all(); 

        $payslip_lists = ['' => 'Select PaySlip'] + PayslipTitle::whereStatus(1)->lists('title', 'id');         

        //sessions dropdown list
        $this_admission_session = $admission->session;
        $this_session_apart = explode('-', $this_admission_session);

        $sessions = [];
        $this_admission_year = $this_session_apart[0];
        $initial_year = $this_admission_year - 10;
        $final_year = $this_admission_year + 10;

        foreach(range($initial_year, $final_year) as $index) :
            $this_year = $index;
            $next_year = $index+1;
            $sessions[] = $this_year . '-' . $next_year;
        endforeach;

        $slip_type_lists = ['' => 'Select Type', 'Admission' => 'Admission', 'Formfillup' => 'Formfillup'];

        $current_level_lists = ['' => 'Select Level', 'Honours 1st year' => 'Honours 1st year', 'Honours 2nd year' => 'Honours 2nd year', 'Honours 3rd year' => 'Honours 3rd year', 'Honours 4th year' => 'Honours 4th year'];

        return view('admin.admission.edit')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withAdmission($admission)
                    ->withSessions($sessions)
                    ->withSlip_type_lists($slip_type_lists)
                    ->withCurrent_level_lists($current_level_lists)
                    ->withRequirements($requirements)
                    ->withPayslip_lists($payslip_lists);

    }



    public function update($id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;  

        $data = $request->all();
        $validation = Admission::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;

        $admission = Admission::find($id);
        $admission->department_id = $request->get('department_id');
        $admission->program_id = $request->get('program_id');
        $admission->paysliptitle_id = $request->get('paysliptitle_id');
        $admission->current_level = $request->get('current_level');    

        $admission->slip_type = $request->get('slip_type');    
        $admission->session = $request->get('session');
        $admission->open_date = $request->get('open_date');
        $admission->close_date = $request->get('close_date');
        $admission->update();

        //Update value into AdmissionRequirement
        $admission_id = $id;

        AdmissionRequirement::whereAdmission_id($admission_id)->delete();

        $requirements_id = [];

        $requirements = Requirement::get();

        if($requirements->count() > 0) :

            foreach($requirements as $requirement) :
                $r_id = $requirement->id;

                if($request->get($r_id) == $r_id) :
                    $requirements_id[] = $r_id;
                endif;  
            endforeach;

            if(count($requirements_id) > 0) :
                foreach($requirements_id as $requirement_id) :
                    $data_array = ['admission_id' => $admission_id, 'requirement_id' => $requirement_id];
                    AdmissionRequirement::create($data_array);
                endforeach; 
            endif;  

        endif;  

        //Pagination Page
        $open_date = $request->get('open_date');
        $count = Admission::where('open_date', '>=', $open_date)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully updated the admission';
        return Redirect::route('admin.admission.index', ['page' => $page])
                        ->with('success', $message)
                        ->withId($id);

    }



    public function destroy($id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $admission = Admission::find($id);
        $admission->delete();

        AdmissionRequirement::whereAdmission_id($id)->delete();

        $error_message = 'You have deleted the admission';
        return Redirect::back()->with('error',$error_message);

    }



    public function status($id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $status = $request->get('status');

        if($status != 1 && $status != 0) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;      
        
        $admission = Admission::find($id);
        $admission->status = $status;
        $admission->update();

        if($status == 1) :
            $message = 'You have opened the admission';
            return Redirect::back()->with('success', $message);
        else :
            $error_message = 'You have closed the admission';
            return Redirect::back()->with('error',$error_message);     
        endif;              

    }



    public function search() {

        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.admission.index:Admission Management|Dashboard';

        //search admission outcomes
        $session = Study::filterInput('session', $request->get('session'));
        $department_id = Study::filterInput('department_id', $request->get('department_id'));
        $program_id = Study::filterInput('program_id', $request->get('program_id'));
        $status = Study::filterInput('status', $request->get('status'));

        $admissions = Study::searchAdmission($session, $department_id, $program_id, $status);

        //search filter form data
        $sessions = Admission::groupBy('session')->distinct()->get();       
        $department_lists = ['' => 'Select Department'] + Department::lists('dept_name', 'id');
        $program_lists = ['' => 'Select Program'] + Program::lists('short_name', 'id');
        $status_lists = ['' => 'Select Status', 1 => 'Open', 0 => 'Closed'];

        return view('admin.admission.search')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withAdmissions($admissions)
                    ->withSessions($sessions)
                    ->withDepartment_lists($department_lists)
                    ->withProgram_lists($program_lists)
                    ->withStatus_lists($status_lists)
                    ->withPre_session($session)
                    ->withDepartment_id($department_id)
                    ->withProgram_id($program_id)
                    ->withStatus($status);

    }

    public function admission_config(){
        $course = request()->get('course');
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.admission.index:Admission Management|Dashboard';
        $query_config = DB::table('admission_config');

        if($course != '') $query_config->where('course', $course);

        $configs = $query_config->get();

        return view('BackEnd.admin.admission.config_index', compact('course'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withConfigs($configs);
    }

    public function admission_config_datasource(Request $request){
        $configs = AdmissionConfig::query();

        return Datatables::of($configs)
                ->addColumn('actions', function ($config) {
                    $html = "<a href=".route('admin.admission.config.edit', $config->id)." class='btn btn-primary type-b duplicate_data'><i class='fa fa-copy'></i></a>";
                    $html .= " <a href=".route('admin.admission.config.edit', $config->id)." class='btn btn-primary type-b edit_data' data-id=".$config->id."><i class='fa fa-pencil'></i></a>";
                    $html .= " <a href=".route('admin.admission.config.destroy', $config->id)." class='btn btn-danger type-b delete_data' data-id=".$config->id."><i class='fa fa-trash'></i></a>";
                    return $html;
                })
                ->filter(function ($query) use ($request) {

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('open') && ! is_null($request->get('open'))) {
                        $query->where('open', $request->get('open'));
                    }

                    if ($request->has('type') && ! is_null($request->get('type'))) {
                        $query->where('type', $request->get('type'));
                    }

                    if ($request->has('course') && ! is_null($request->get('course')) ) {
                        $query->where('course', $request->get('course'));
                    }
                })

                ->editColumn('type', function ($config) {
                    return get_badge_status('admission_config_type', $config->type);
                })
                ->editColumn('open', function ($config) {
                    return get_badge_status('open', $config->open);
                })

                ->setRowAttr([
                    'data-row-id' => function($config) {
                        return $config->id;
                    },
                    'class'=> function($config) {
                        return 'text-center ' . Study::updatedRow('id', $config->id);
                    }
                ])
                 // ->orderColumn('id', true)
                ->rawColumns(['open','type','actions'])
                // ->escapeColumns()
                ->make(true);
                // ->toJson();
    }

    public function admission_config_edit($id){
        $config = AdmissionConfig::find($id);
        $data = [
            'current_level' => $config->current_level,
            'session' => $config->session,
            'exam_year' => $config->exam_year,
            'opening_date' => $config->opening_date,
            'clossing_date' => $config->clossing_date,
            'course' => $config->course,
            'open' => $config->open,
            'type' => $config->type
        ];

        $html = view('BackEnd.admin.admission.particles.form', $data)->render();
        return response()->json([
                'status' => 200,
                'html' => $html

            ],Response::HTTP_OK);

    }

    public function admission_config_store(Request $request){
        $this->validate($request, [
            'current_level' => 'required',
            'session' => 'required',
            'open' => 'required',
            'exam_year' => 'required',
            'opening_date' => 'required',
            'clossing_date' => 'required',
            'type' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $config = AdmissionConfig::find($id);
                $msg = 'You have successfully updated the admission Settings';
                $status = 'info';
            }else{
                $config = new AdmissionConfig;
                $msg = 'You have successfully created the admission Settings';
                $status = 'success';
            }

            $config->current_level= $request->get('current_level');
            $config->session= $request->get('session');
            $config->course= $request->get('course');
            $config->open= $request->get('open');
            $config->exam_year= $request->get('exam_year');
            $config->opening_date= $request->get('opening_date');
            $config->clossing_date= $request->get('clossing_date');
            $config->type= $request->get('type');
            $config->save();

            DB::commit();

            $array = AdmissionConfig::where('id',$config->id)->get(['id', 'current_level', 'session', 'open', 'exam_year','opening_date', 'clossing_date','type'])->first()->toArray();
            foreach ($array as $key => $val) {
                $value = $val;
                if($key == 'open'){
                    $value = get_badge_status('open', $val);
                }
                if($key == 'type'){
                    $value = get_badge_status('admission_config_type', $val);
                }

                $values[$key] = $value;
            }

            $row_values = array_values($values);

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $config->id,
                'table' => 'datatable',
                'row_values' => $row_values

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function admission_config_destroy(Request $request, $id){
        try {
            AdmissionConfig::find($id)->delete();
            return response()->json([
                'status' => 'warning',
                'message' => 'Config Deleted Successfully',
                'id' => $id,
                'table' => 'datatable',

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}

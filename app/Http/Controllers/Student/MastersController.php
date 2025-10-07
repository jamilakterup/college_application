<?php

namespace App\Http\Controllers\Student;

use DB;
use Image;
use Session;
use Mpdf\Mpdf;
use DataTables;
use App\Libs\Study;
use IdRollGenerate;
use App\Models\Invoice;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StudentInfoMasters;
use App\Models\MastersAdmittedStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class MastersController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:student.masters.create', ['only' => ['create','store']]);
         $this->middleware('permission:student.masters.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:student.masters.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        try {
            return view('BackEnd.student.admission.masters.index');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', $e->errorInfo[2]);
        }
    }

    public function datasource(Request $request){
        $records = StudentInfoMasters::query();

        return DataTables::of($records)
            ->addColumn('actions','BackEnd.student.admission.masters.particles.action_buttons')

            ->editColumn('address', function($records){
                $html = "<strong>Present:</strong> $records->present_village, $records->present_po, $records->present_ps, $records->present_dist,<br\> <strong>Permanent:</strong> $records->permanent_po, $records->permanent_ps, $records->permanent_dist";
                return $html;
            })
            ->editColumn('image', function($records){
                $html = "<img style='height:50px;' src='".asset("upload/college/masters/".$records->session."/".$records->image)."' class='img-thumbnail'>";
                return $html;
            })
            ->addColumn('checkbox', function($records){
                return '<input type="checkbox" name="item_checkbox" data-id="'.$records->id.'"><label></label>';
            })
            ->filter(function ($query) use ($request) {
                
                if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                    $query->where('id','LIKE',"%".$request->get('student_id')."%");
                }

                if ($request->has('admission_roll') && ! is_null($request->get('admission_roll'))) {
                    $query->where('admission_roll','LIKE',"%".$request->get('admission_roll')."%");
                }

                if ($request->has('faculty') && ! is_null($request->get('faculty'))) {
                    $query->where('faculty_name',$request->get('faculty'));
                }

                if ($request->has('department') && ! is_null($request->get('department'))) {
                    $query->where('dept_name', $request->get('department'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }

                if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                    $query->where('current_level', $request->get('current_level'));
                }
                
                query_has_permissions($query, ['current_level','faculty', 'dept_name']);

                $query->orderBy('id', 'desc');

            })
            ->setRowAttr([
                'data-row-id' => function($records) {
                    return $records->id;
                },
                'class'=> function($records) {
                    return 'text-center record-' .$records->id;
                }
            ])
            ->rawColumns(['actions', 'active_status','address','checkbox','image'])
            ->escapeColumns(['address','checkbox','image'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $html = view('BackEnd.student.admission.masters.particles.form')->render();
        return response()->json([
                'modal' => 'modal-lg',
                'html' => $html
            ],Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $rules[] = [
            'student_name' => 'required',
            'session' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
            'gender' => 'required',
            'faculty' => 'required',
            'department' => 'required',
            'current_level' => 'required',
            'gender' => 'required',
            'religion' => 'required',
            'faculty' => 'required',
            'admission_roll' => 'required|numeric'
        ];

        
        $action_type = $request->action_type;
        if($action_type != 'update'){
            $rules[] = ['photo' => 'required|mimes:jpeg,jpg,png'];
        }
        $this->validate($request, Arr::collapse($rules));
        
        DB::beginTransaction();
        
        try {
            
            if($action_type == 'update'){
                return $this->update($request);
            }
    
            $temp_entry_time = date('Y-m-d G:i:s');
            $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
            $admission_roll = $request->get('admission_roll');

            $session = $request->get('session');
            $insCode = INS_CODE;

            $dist = $request->same_as_present ? $request->get('present_dist') : $request->get('permanent_dist');
            $thana = $request->same_as_present ? $request->get('present_ps') : $request->get('permanent_ps');
            DB::table('masters_admitted_student')->insert(
               array(
                'entry_time'=>$entry_time,
                'name'=>$request->get('student_name'),
                'father_name'=>$request->get('father_name'),
                'mother_name'=>$request->get('mother_name'),
                'birth_date'=>$request->get('birth_date'),
                'blood_group'=>$request->get('blood_group'),
                'gender'=>$request->get('gender'),
                'ssc_reg'=>$request->get('ssc_reg'),
                'ssc_institute'=>$request->get('ssc_institution'),
                'ssc_board'=>$request->get('ssc_board'),
                'ssc_gpa'=>$request->get('ssc_gpa'),
                'hsc_reg'=>$request->get('hsc_reg'),
                'hsc_institute'=>$request->get('hsc_institution'),
                'hsc_board'=> $request->get('hsc_board'),
                'hsc_gpa'=>$request->get('hsc_gpa'),
                'session'=> $session ,
                'permanent_mobile'=>$request->get('student_mobile'),
                'current_level'=> $request->get('current_level'),
                'ssc_pass_year'=>$request->get('ssc_passing_year'),
                'hsc_pass_year'=>$request->get('hsc_passing_year'),
                'from_faculty'=>$request->get('faculty'),
                'to_subject'=>$request->get('department'),
                'admission_roll'=>$request->get('admission_roll'),
                'religion'=>$request->get('religion'),
                'honrs_passing_institute'=> @$request->get('honrs_passing_institute'),
                'honrs_passing_year'=> @$request->get('honrs_passing_year'),
                'honrs_passing_cgpa'=> @$request->get('honrs_passing_cgpa'),
                'honrs_session'=>@$request->get('honrs_session'),
                'honrs_roll'=>@$request->get('honrs_roll'),
                'present_village'=>$request->get('present_village'),
                'present_po'=>$request->get('present_po'),
                'present_dist'=>$request->get('present_dist'),
                'present_ps'=>$request->get('present_ps'),
                'permanent_village'=>$request->get('permanent_village'),
                'permanent_po'=>$request->get('permanent_po'),
                'permanent_dist'=>$dist,
                'permanent_ps'=>$thana,
                )
            );
            
          $faculty = $request->get('faculty');
          $prefix='masters_2_';
          $catagory="4"; // for honours
          $subject = $request->get('department');

          $id=IdRollGenerate::id_generate_msc($session,$subject,$prefix);
          $class_roll = IdRollGenerate::roll_generate_msc($id); //

          $admitted_student = MastersAdmittedStudent::where('admission_roll', $admission_roll,)->where('session', $session)->orderBy('auto_id', 'desc')->first();
          
          DB::table('student_info_masters')->insert(
            array('id'=>$id, 'name'=>$request->get('student_name'), 'class_roll'=>$class_roll, 'faculty_name'=>$request->get('faculty'), 'dept_name'=>$request->get('department'), 'current_level'=>$request->get('current_level'), 'father_name'=>$request->get('father_name'), 'mother_name'=>$request->get('mother_name'), 'birth_date'=>$request->get('birth_date'), 'gender'=>$request->get('gender'), 'contact_no'=>$request->get('student_mobile'), 'religion'=>$request->get('religion'),'refference_id'=>$admitted_student->auto_id, 'admission_roll'=>$admission_roll , 'session'=>$session,'present_village'=>$request->get('present_village'),'present_po'=>$request->get('present_po'),'present_dist'=>$request->get('present_dist'),'present_ps'=>$request->get('present_ps'),'permanent_village'=>$request->get('permanent_village'),'permanent_po'=>$request->get('permanent_po'),'permanent_dist'=>$dist,'permanent_ps'=>$thana, 'blood_group' => $request->get('blood_group'), 'hons_roll'=> $request->honrs_roll)
        );
            $filename='';
            $logo = $request->file('photo');
            $student = DB::table("student_info_masters")->where('id',$id)->first();

            if($logo!=''){
                $folder = public_path('upload/college/masters/'.$request->session);
                create_dir($folder);
                $filename = rand(1, 99999999999) .'.jpg';
                $upload_path = $folder.'/'.$filename;
                $db_path = 'upload/college/masters/' . $filename;
                Image::make($logo->getRealPath())->save($upload_path);

                DB::table('masters_admitted_student')->where('auto_id', $admitted_student->auto_id)->update([
                   'photo' => $filename
                ]);

                DB::table('student_info_masters')->where('id', $id)->update([
                    'image' => $filename
                ]);
            }
             	
            $date=date('Y-m-d');   
            DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='honours_{$subject}'");

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => "Student Added Successfully for *<b>$student->id :$student->name</b>",
                'id' => $student->id,
                'table' => 'datatable'
            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function edit(Request $request, $id)
    {

        try {
            $student = StudentInfoMasters::find($id);
            $data = [
                'student' => $student
            ];
            $html = view('BackEnd.student.admission.masters.particles.form', $data)->render();

            return response()->json([
                'modal'=> 'modal-lg',
                'html' => $html
            ],Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function update($request){
        try {
            $id = $request->id;
            $action_type = $request->action_type;

            $admission_roll = $request->get('admission_roll');

            $session = $request->get('session');
            $insCode = INS_CODE;

            $dist = $request->same_as_present ? $request->get('present_dist') : $request->get('permanent_dist');
            $thana = $request->same_as_present ? $request->get('present_ps') : $request->get('permanent_ps');
          DB::table('student_info_masters')->where('id', $id)->update(
            array( 'name'=>$request->get('student_name'), 'faculty_name'=>$request->get('faculty'), 'dept_name'=>$request->get('department'), 'current_level'=>$request->get('current_level'), 'father_name'=>$request->get('father_name'), 'mother_name'=>$request->get('mother_name'), 'birth_date'=>$request->get('birth_date'), 'gender'=>$request->get('gender'), 'contact_no'=>$request->get('student_mobile'), 'religion'=>$request->get('religion'), 'admission_roll'=>$admission_roll , 'session'=>$session,'present_village'=>$request->get('present_village'),'present_po'=>$request->get('present_po'),'present_dist'=>$request->get('present_dist'),'present_ps'=>$request->get('present_ps'),'permanent_village'=>$request->get('permanent_village'),'permanent_po'=>$request->get('permanent_po'),'permanent_dist'=>$dist,'permanent_ps'=>$thana, 'blood_group' => $request->get('blood_group'), 'hons_roll'=> $request->honrs_roll)
        );
            $filename='';
            $logo = $request->file('photo');
            $student = DB::table("student_info_masters")->where('id',$id)->first();

            DB::table('masters_admitted_student')->where('auto_id', $student->refference_id)->update(
                array(
                    'name'=>$request->get('student_name'),
                    'father_name'=>$request->get('father_name'),
                    'mother_name'=>$request->get('mother_name'),
                    'birth_date'=>$request->get('birth_date'),
                    'blood_group'=>$request->get('blood_group'),
                    'gender'=>$request->get('gender'),
                    'ssc_reg'=>$request->get('ssc_reg'),
                    'ssc_institute'=>$request->get('ssc_institution'),
                    'ssc_board'=>$request->get('ssc_board'),
                    'ssc_gpa'=>$request->get('ssc_gpa'),
                    'hsc_reg'=>$request->get('hsc_reg'),
                    'hsc_institute'=>$request->get('hsc_institution'),
                    'hsc_board'=> $request->get('hsc_board'),
                    'hsc_gpa'=>$request->get('hsc_gpa'),
                    'session'=> $session ,
                    'permanent_mobile'=>$request->get('student_mobile'),
                    'current_level'=> $request->get('current_level'),
                    'ssc_pass_year'=>$request->get('ssc_passing_year'),
                    'hsc_pass_year'=>$request->get('hsc_passing_year'),
                    'from_faculty'=>$request->get('faculty'),
                    'to_subject'=>$request->get('department'),
                    'admission_roll'=>$request->get('admission_roll'),
                    'religion'=>$request->get('religion'),
                    'honrs_passing_institute'=> @$request->get('honrs_passing_institute'),
                    'honrs_passing_year'=> @$request->get('honrs_passing_year'),
                    'honrs_passing_cgpa'=> @$request->get('honrs_passing_cgpa'),
                    'honrs_session'=>@$request->get('honrs_session'),
                    'honrs_roll'=>@$request->get('honrs_roll'),
                    'present_village'=>$request->get('present_village'),
                    'present_po'=>$request->get('present_po'),
                    'present_dist'=>$request->get('present_dist'),
                    'present_ps'=>$request->get('present_ps'),
                    'permanent_village'=>$request->get('permanent_village'),
                    'permanent_po'=>$request->get('permanent_po'),
                    'permanent_dist'=>$dist,
                    'permanent_ps'=>$thana,
                    )
             );

            if($logo!=''){
                $folder = public_path('upload/college/honours/'.$student->session);
                create_dir($folder);
                if(\File::exists($folder.'/'.$student->image)){
                    \File::delete($folder.'/'.$student->image);
                }
                $filename = rand(1, 99999999999) .'.jpg';
                $upload_path = $folder.'/'.$filename;
                $db_path = 'upload/college/honours/' . $filename;
                Image::make($logo->getRealPath())->save($upload_path);

                DB::table('masters_admitted_student')->where('auto_id', $student->refference_id)->update([
                   'photo' => $filename
                ]);

                DB::table('student_info_masters')->where('id', $id)->update([
                    'image' => $filename
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'info',
                'message' => "Student Updated Successfully for *<b>$student->name</b>",
                'id' => $student->id,
                'table' => 'datatable'
            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $student = StudentInfoMasters::find($id);
            MastersAdmittedStudent::where('auto_id', $student->refference_id)->delete();
            $student->delete();

            DB::commit();
            return response()->json([
                'status' => 'warning',
                'message' => 'Student has been deleted successfully',
                'id' => $id,
                'table' => 'datatable',
            ],Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function force_promotion(Request $request){
        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $student =  StudentInfoMasters::find($id);

                if($student->current_level=='Masters 1st Year')
                {
                    $student->current_level='Masters 2nd Year';
                }

                $student->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'info',
                'message' => 'Selected Student Promotion Successful',
                'table' => 'datatable',
            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }


    public function regStudent(Request $request)
    {
        
        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'students.honours:Honours|Registered Student';
        
        $id = $request->get('id');
        if($id!='')
        {
            $id=str_split($id);
            $id=$id[2].$id[3].$id[4].$id[5];
        }
        $adm_roll = $request->get('adm_roll');
        $session = $request->get('session');
        
        $query = Study::regSearchHonoursStudent($id, $adm_roll, $session);
        $num_rows = $query->count();
        $students = $query->paginate(Study::paginate());
        return view('BackEnd.student.admission.masters.regStudent', compact('title', 'id','students', 'breadcrumb', 'num_rows', 'session', 'adm_roll'));
    }

    public function regSearch(Request $request) {
        
        $title = 'Easy CollegeMate - Students Honours';
        $breadcrumb = 'students.honours:Honours|Search';
        $id = Study::filterInput('id', $request->get('id'));
        if($id!='')
        {
            $id=str_split($id);
            $id=$id[2].$id[3].$id[4].$id[5];
        }
        $adm_roll = $request->get('adm_roll');
        $session = $request->get('session');
        $students = Study::regSearchHonoursStudent($id, $adm_roll, $session);
        
        return view('BackEnd.student.admission.masters.regStudent', \compact('title', 'breadcrumb', 'students'));
        
    }

    public function printDetails($id){

        $student = DB::table('student_info_masters')->where('id', $id)->first();
        $admitted_student = DB::table('masters_admitted_student')->where('auto_id', $student->refference_id)->where('session', $student->session)->first();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;

        $html = view('admission.masters.form_id', compact('admitted_student', 'student'));

        $mpdf->writeHTML($html);
        $filename = $student->id.'_'.$student->session."_admission.pdf";
        $mpdf->Output($filename, 'I');
      }
}

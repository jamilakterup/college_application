<?php

namespace App\Http\Controllers\Student;

use DB;
use Image;
use Mpdf\Mpdf;
use DataTables;
use App\Libs\Study;
use IdRollGenerate;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StudentInfoDegree;
use App\Http\Controllers\Controller;
use App\Models\DegreeAdmittedStudent;

class DegreeController extends Controller
{
    function __construct()
    {
            $this->middleware('permission:student.degree.create', ['only' => ['create','store']]);
            $this->middleware('permission:student.degree.edit', ['only' => ['edit','update']]);
            $this->middleware('permission:student.degree.delete', ['only' => ['destroy']]);
    }
    
  public function index(Request $request)
  {

      try {
          return view('BackEnd.student.admission.degree.index');
      } catch (\Illuminate\Database\QueryException $e) {
          session()->flash('error', $e->errorInfo[2]);
      }
  }

  public function datasource(Request $request){
      $records = StudentInfoDegree::query();

      return DataTables::of($records)
          ->addColumn('actions','BackEnd.student.admission.degree.particles.action_buttons')

          ->editColumn('address', function($records){
              $html = "<strong>Present:</strong> $records->present_village, $records->present_po, $records->present_ps, $records->present_dist,<br\> <strong>Permanent:</strong> $records->permanent_po, $records->permanent_ps, $records->permanent_dist";
              return $html;
          })
          ->editColumn('image', function($records){
              $html = "<img style='height:50px;' src='".asset("upload/college/degree/".$records->session."/".$records->image)."' class='img-thumbnail'>";
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

              if ($request->has('groups') && ! is_null($request->get('groups'))) {
                  $query->where('groups',$request->get('groups'));
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
          ->rawColumns(['actions', 'active_status','address','checkbox', 'image'])
          ->escapeColumns(['address','checkbox', 'image'])
          ->make(true);
  }

  public function create(Request $request)
  {
      $html = view('BackEnd.student.admission.degree.particles.form')->render();
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
          'ssc_reg' => 'required|numeric',
          'groups' => 'required',
          'current_level' => 'required',
          'gender' => 'required',
          'religion' => 'required',
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
          DB::table('deg_admitted_student')->insert(
             array(
              'entry_time'=>$entry_time,
              'name'=>$request->get('student_name'),
              'father_name'=>$request->get('father_name'),
              'mother_name'=>$request->get('mother_name'),
              'faculty'=>$request->get('groups'),
              'subject'=>$request->get('groups'),
              'birth_date'=>$request->get('birth_date'),
              'blood_group'=>$request->get('blood_group'),
              'gender'=>$request->get('gender'),
              'ssc_reg'=>$request->get('ssc_reg'),
              'ssc_institute'=>$request->get('ssc_institution'),
              'ssc_board'=>$request->get('ssc_board'),
              'ssc_gpa'=>$request->get('ssc_gpa'),
              'hsc_reg'=>$request->get('hsc_reg'),
              'hsc_roll'=>$request->get('hsc_roll'),
              'ssc_roll'=>$request->get('ssc_roll'),
              'hsc_institute'=>$request->get('hsc_institution'),
              'hsc_board'=> $request->get('hsc_board'),
              'hsc_gpa'=>$request->get('hsc_gpa'),
              'session'=> $session ,
              'contact_no'=>$request->get('student_mobile'),
              'permanent_mobile'=>$request->get('student_mobile'),
              'ssc_pass_year'=>$request->get('ssc_passing_year'),
              'hsc_pass_year'=>$request->get('hsc_passing_year'),
              'admission_roll'=>$request->get('admission_roll'),
              'religion'=>$request->get('religion'),
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
          
        $groups = $request->get('groups');
        $prefix='degree_';
        $catagory="3"; // for degree
        $subject = $request->get('groups');

        $id=IdRollGenerate::id_generate_deg($session,$subject,$prefix);
        $class_roll = IdRollGenerate::roll_generate_deg($id); //

        $admitted_student = DegreeAdmittedStudent::where('admission_roll', $admission_roll,)->where('session', $session)->orderBy('auto_id', 'desc')->first();
        
        DB::table('student_info_degree')->insert(
          array('id'=>$id, 'name'=>$request->get('student_name'), 'class_roll'=>$class_roll, 'groups'=>$request->get('groups'), 'current_level'=>$request->get('current_level'), 'father_name'=>$request->get('father_name'), 'mother_name'=>$request->get('mother_name'), 'birth_date'=>$request->get('birth_date'), 'gender'=>$request->get('gender'), 'contact_no'=>$request->get('student_mobile'), 'religion'=>$request->get('religion'),'refference_id'=>$admitted_student->auto_id, 'admission_roll'=>$admission_roll , 'session'=>$session,'present_village'=>$request->get('present_village'),'present_po'=>$request->get('present_po'),'present_dist'=>$request->get('present_dist'),'present_ps'=>$request->get('present_ps'),'permanent_village'=>$request->get('permanent_village'),'permanent_po'=>$request->get('permanent_po'),'permanent_dist'=>$dist,'permanent_ps'=>$thana, 'blood_group' => $request->get('blood_group'))
      );
          $filename='';
          $logo = $request->file('photo');
          $student = DB::table("student_info_degree")->where('id',$id)->first();

          if($logo!=''){
              $folder = public_path('upload/college/degree/'.$request->session);
              create_dir($folder);
              $filename = rand(1, 99999999999) .'.jpg';
              $upload_path = $folder.'/'.$filename;
              $db_path = 'upload/college/degree/' . $filename;
              Image::make($logo->getRealPath())->save($upload_path);

              DB::table('deg_admitted_student')->where('auto_id', $admitted_student->auto_id)->update([
                 'photo' => $filename
              ]);

              DB::table('student_info_degree')->where('id', $id)->update([
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
          $student = StudentInfoDegree::find($id);
          $data = [
              'student' => $student
          ];
          $html = view('BackEnd.student.admission.degree.particles.form', $data)->render();

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
        DB::table('student_info_degree')->where('id', $id)->update(
            array('name'=>$request->get('student_name'), 'groups'=>$request->get('groups'), 'current_level'=>$request->get('current_level'), 'father_name'=>$request->get('father_name'), 'mother_name'=>$request->get('mother_name'), 'birth_date'=>$request->get('birth_date'), 'gender'=>$request->get('gender'), 'contact_no'=>$request->get('student_mobile'), 'religion'=>$request->get('religion'), 'admission_roll'=>$admission_roll , 'session'=>$session,'present_village'=>$request->get('present_village'),'present_po'=>$request->get('present_po'),'present_dist'=>$request->get('present_dist'),'present_ps'=>$request->get('present_ps'),'permanent_village'=>$request->get('permanent_village'),'permanent_po'=>$request->get('permanent_po'),'permanent_dist'=>$dist,'permanent_ps'=>$thana, 'blood_group' => $request->get('blood_group'))
      );
          $filename='';
          $logo = $request->file('photo');
          $student = DB::table("student_info_degree")->where('id',$id)->first();

          DB::table('deg_admitted_student')->where('auto_id', $student->refference_id)->update(
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
                'hsc_roll'=>$request->get('hsc_roll'),
                'ssc_roll'=>$request->get('ssc_roll'),
                'hsc_institute'=>$request->get('hsc_institution'),
                'hsc_board'=> $request->get('hsc_board'),
                'hsc_gpa'=>$request->get('hsc_gpa'),
                'session'=> $session ,
                'contact_no'=>$request->get('student_mobile'),
                'permanent_mobile'=>$request->get('student_mobile'),
                'ssc_pass_year'=>$request->get('ssc_passing_year'),
                'hsc_pass_year'=>$request->get('hsc_passing_year'),
                'admission_roll'=>$request->get('admission_roll'),
                'religion'=>$request->get('religion'),
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
              $folder = public_path('upload/college/degree/'.$student->session);
              create_dir($folder);
              if(\File::exists($folder.'/'.$student->image)){
                  \File::delete($folder.'/'.$student->image);
              }
              $filename = rand(1, 99999999999) .'.jpg';
              $upload_path = $folder.'/'.$filename;
              $db_path = 'upload/college/degree/' . $filename;
              Image::make($logo->getRealPath())->save($upload_path);

              DB::table('deg_admitted_student')->where('auto_id', $student->refference_id)->update([
                 'photo' => $filename
              ]);

              DB::table('student_info_degree')->where('id', $id)->update([
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
          $student = StudentInfoDegree::find($id);
          DegreeAdmittedStudent::where('auto_id', $student->refference_id)->delete();
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
              $student =  StudentInfoDegree::find($id);

              if($student->current_level!='Degree 3rd Year')
                {
                    if($student->current_level=='Degree 1st Year')
                    {
                        $student->current_level='Degree 2nd Year'; 
                        $year='2nd'; 
                    }
                    else
                    {
                        $student->current_level='Degree 3rd Year';
                        $year='3rd';
                    }
                    $student->save();
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
      $breadcrumb = 'students.degree:Degree|Registered Student';
      
      $ref_id = $request->ref_id;
        $admission_roll = $request->admission_roll;
        $session = $request->session;

        $ref_id = deg_tracking_auto_id($ref_id);
      
      $students = Study::regSearchDegreeStudent($ref_id, $admission_roll, $session)->paginate(Study::paginate());
      return view('BackEnd.student.admission.degree.regStudent', compact('students','ref_id', 'admission_roll','session', 'breadcrumb'));
  }

  public function printDetails($id){

      $student = DB::table('student_info_degree')->where('id', $id)->first();
      $admitted_student = DB::table('deg_admitted_student')->where('auto_id', $student->refference_id)->where('session', $student->session)->first();

      $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
      $mpdf->ignore_invalid_utf8 = true;
      $mpdf->autoScriptToLang = true;
      $mpdf->autoVietnamese = true;
      $mpdf->autoArabic = true;
      $mpdf->autoLangToFont = true;

      $html = view('admission.degree.form_id', compact('admitted_student', 'student'));

      $mpdf->writeHTML($html);
      $filename = $student->id.'_'.$student->session."_admission.pdf";
      $mpdf->Output($filename, 'I');
    }
}

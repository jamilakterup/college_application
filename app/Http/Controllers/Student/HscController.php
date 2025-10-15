<?php

namespace App\Http\Controllers\Student;

use DB;
use H2H;
use Image;
use Session;
use Mpdf\Mpdf;
use DataTables;
use App\Libs\Study;
use App\Models\Department;
use Illuminate\Support\Arr;
use App\Models\HscPromotion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StudentInfoHsc;
use App\Models\StudentInfoHscTc;
use App\Models\HscAdmittedStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use IdRollGenerate;

class HscController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:student.hsc.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:student.hsc.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:student.hsc.delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        try {
            return view('BackEnd.student.admission.hsc.index');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', $e->errorInfo[2]);
        }
    }

    public function datasource(Request $request)
    {
        $records = StudentInfoHsc::query();

        return DataTables::of($records)
            ->addColumn('actions', 'BackEnd.student.admission.hsc.particles.action_buttons')

            ->editColumn('address', function ($records) {
                $html = "<strong>Present:</strong> $records->present_village, $records->present_po, $records->present_ps, $records->present_dist,<br\> <strong>Permanent:</strong> $records->permanent_po, $records->permanent_ps, $records->permanent_dist";
                return $html;
            })
            ->editColumn('image', function ($records) {
                $html = "<img style='height:50px;' src='" . asset("upload/college/hsc/" . $records->session . "/" . $records->image) . "' class='img-thumbnail'>";
                return $html;
            })
            ->addColumn('checkbox', function ($records) {
                return '<input type="checkbox" name="item_checkbox" data-id="' . $records->id . '"><label></label>';
            })
            ->editColumn('blood_group', function ($records) {
                return @$records->admitted_student->blood_group;
            })
            ->editColumn('guardian_name', function ($records) {
                return @$records->admitted_student->guardian_name;
            })
            ->editColumn('guardian_phone', function ($records) {
                return @$records->admitted_student->guardian_phone;
            })
            ->filter(function ($query) use ($request) {

                if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                    $query->where('id', 'LIKE', "%" . $request->get('student_id') . "%");
                }

                if ($request->has('ssc_roll') && ! is_null($request->get('ssc_roll'))) {
                    $query->where('ssc_roll', 'LIKE', "%" . $request->get('ssc_roll') . "%");
                }

                if ($request->has('groups') && ! is_null($request->get('groups'))) {
                    $query->where('groups', $request->get('groups'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }

                if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                    $query->where('current_level', $request->get('current_level'));
                }

                query_has_permissions($query, ['current_level', 'hsc_group']);

                $query->orderBy('id', 'desc');
            })
            ->setRowAttr([
                'data-row-id' => function ($records) {
                    return $records->id;
                },
                'class' => function ($records) {
                    return 'text-center record-' . $records->id;
                }
            ])
            ->rawColumns(['actions', 'active_status', 'address', 'checkbox', 'image'])
            ->escapeColumns(['address', 'checkbox', 'blood_group', 'image', 'guardian_phone', 'guardian_name'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $html = view('BackEnd.student.admission.hsc.particles.form')->render();
        return response()->json([
            'modal' => 'modal-lg',
            'html' => $html
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 9600);
        ini_set('request_terminate_timeout', 9600);
        ini_set('fastcgi_read_timeout', 9600);
        ini_set("pcre.backtrack_limit", "5000000");
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $rules[] = [
            'student_name' => 'required',
            'session' => 'required',
            // 'father_name' => 'required',
            // 'mother_name' => 'required',
            // 'gender' => 'required',
            // 'ssc_registration' => 'required|numeric',
            // 'ssc_gpa' => 'required',
            // 'ssc_institute' => 'required',
            // 'ssc_group' => 'required',
            // 'ssc_session' => 'required',
            // 'gender' => 'required',
            // 'religion' => 'required',
            'form_current_level' => 'required'
        ];


        $action_type = $request->action_type;
        if ($action_type != 'update') {
            $rules[] = [
                'photo' => 'required|mimes:jpeg,jpg,png|max:3000',
                'hsc_group' => 'required'
            ];
        }
        $this->validate($request, Arr::collapse($rules));

        DB::beginTransaction();

        if ($action_type == 'update') {
            return $this->update($request);
        }

        try {

            $temp_entry_time = date('Y-m-d G:i:s');
            $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
            $ssc_roll = $request->get('ssc_roll');

            $session = $request->get('session');
            $insCode = INS_CODE;

            $dist = $request->same_as_present ? $request->get('present_dist') : $request->get('permanent_dist');
            $thana = $request->same_as_present ? $request->get('present_ps') : $request->get('permanent_ps');

            $hsc_group = $request->get('hsc_group');
            $compulsorycourse = $request->get('compulsorycourse');

            $compulsorycourse =  implode(",", $compulsorycourse);

            $selectivecourse = $request->get('selectivecourse');
            $selectivecourse =  implode(",", $selectivecourse);

            DB::table('hsc_admitted_students')->insert(
                array(
                    'entry_time' => $entry_time,
                    'name' => $request->get('student_name'),
                    'compulsory' => $compulsorycourse,
                    'selective' => $selectivecourse,
                    'optional' => $request->get('selecting'),
                    'blood_group' => $request->get('blood_group'),
                    'hsc_group' => $request->get('hsc_group'),
                    'fathers_name' => $request->get('father_name'),
                    'mothers_name' => $request->get('mother_name'),
                    'date_of_birth' => $request->get('birth_date'),
                    'ssc_roll' => $request->get('ssc_roll'),
                    'mobile' => $request->get('student_mobile'),
                    'ssc_reg_no' => $request->get('ssc_registration'),
                    'ssc_board' => $request->get('ssc_board'),
                    'ssc_group' => $request->get('ssc_group'),
                    'ssc_institution' => $request->get('ssc_institute'),
                    'ssc_session' => $request->get('ssc_session'),
                    'ssc_gpa' => $request->get('ssc_gpa'),
                    'ssc_passing_year' => $request->ssc_passing_year,
                    'sex' => $request->get('gender'),
                    'religion' => $request->get('religion'),
                    'admission_session' => $session,
                    'village' => $request->get('present_village'),
                    'post_office' => $request->get('present_po'),
                    'district' => $request->get('present_dist'),
                    'upozilla' => $request->get('present_ps'),
                    'permanent_village' => $request->get('permanent_village'),
                    'permanent_post_office' => $request->get('permanent_po'),
                    'permanent_district' => $dist,
                    'permanent_thana' => $thana,
                )
            );
            $courses =  DB::select("SELECT * FROM course_hsc_new WHERE `groups` = '" . strtolower($hsc_group) . "'");

            $cods = array();

            foreach ($courses as $course) {
                if (strpos($course->subjects, ',') !== FALSE) {
                    $subjects = explode(",", $course->subjects);
                    $codes = explode(",", $course->codes);
                    foreach ($subjects as $key => $subject) {
                        $cods[$codes[$key]] = $subject;
                    }
                } else {
                    $cods[$course->codes] = $course->subjects;
                }
            }

            $compulsory = explode(",", $compulsorycourse);
            $selective = explode(",", $selectivecourse);
            $optional = explode(",", $request->get('selecting'));

            $compulsory_string = '';
            $selective_string = '';
            $optional_string = '';

            foreach ($compulsory as $value) {
                $compulsory_string .= $cods[$value] . "(" . $value . "),";
            }
            $compulsory_string = rtrim($compulsory_string, ",");

            foreach ($selective as $value) {
                $selective_string .= $cods[$value] . "(" . $value . "),";
            }
            $selective_string = rtrim($selective_string, ",");

            foreach ($optional as $value) {
                $optional_string .= $cods[$value] . "(" . $value . "),";
            }
            $optional_string = rtrim($optional_string, ",");

            $compulsory_string = str_replace("-", ",", $compulsory_string);
            $selective_string = str_replace("-", ",", $selective_string);
            $optional_string = str_replace("-", ",", $optional_string);

            $all_string = $compulsory_string . "," . $selective_string . "," . $optional_string;

            $prefix = 'hsc_';
            $catagory = "0"; // for hsc
            $id = IdRollGenerate::id_generate_hsc_store($session, $hsc_group);
            $class_roll = IdRollGenerate::roll_generate_hsc_store($id);

            $admitted_student = HscAdmittedStudent::where('ssc_roll', $ssc_roll)->where('admission_session', $session)->where('hsc_group', $request->hsc_group)->orderBy('auto_id', 'desc')->first();

            $current_level = $request->get('form_current_level');

            DB::table('student_info_hsc')->insert(
                array('id' => $id, 'name' => $request->get('student_name'), 'class_roll' => $class_roll, 'session' => $session, 'groups' => $hsc_group, 'current_level' => $current_level, 'father_name' => $request->get('father_name'), 'mother_name' => $request->get('mother_name'), 'birth_date' => $request->get('birth_date'), 'gender' => $request->get('gender'), 'contact_no' => $request->get('student_mobile'), 'religion' => $request->get('religion'), 'refference_id' => $admitted_student->auto_id, 'ssc_roll' => $ssc_roll, 'hsc_subjects_info' => $all_string, 'registration_id' => $request->get('registration_id'), 'present_village' => $request->get('present_village'), 'present_po' => $request->get('present_po'), 'present_dist' => $request->get('present_dist'), 'present_ps' => $request->get('present_ps'), 'permanent_village' => $request->get('permanent_village'), 'permanent_po' => $request->get('permanent_po'), 'permanent_dist' => $dist, 'permanent_ps' => $thana, 'ssc_session' => $request->get('ssc_session'), 'ssc_reg_no' => $request->get('ssc_registration'), 'ssc_roll' => $request->get('ssc_roll'), 'ssc_passing_year' => $request->ssc_passing_year, 'ssc_board' => $request->ssc_board, 'ssc_group' => $request->ssc_group, 'gpa' => $request->ssc_gpa)
            );

            $filename = '';
            $logo = $request->file('photo');
            $student = DB::table("student_info_hsc")->where('id', $id)->first();

            if ($logo != '') {
                $folder = public_path('upload/college/hsc/' . $request->session);
                create_dir($folder);
                $filename = rand(1, 99999999999) . '.jpg';
                $upload_path = $folder . '/' . $filename;
                $db_path = 'upload/college/hsc/' . $filename;
                Image::make($logo->getRealPath())->save($upload_path);

                DB::table('hsc_admitted_students')->where('auto_id', $admitted_student->auto_id)->update([
                    'photo' => $filename
                ]);

                DB::table('student_info_hsc')->where('id', $id)->update([
                    'image' => $filename
                ]);
            }

            DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='hsc_{$hsc_group}'");

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => "Student Added Successfully for *<b>$student->id :$student->name</b>",
                'id' => $student->id,
                'table' => 'datatable'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'danger' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $student = StudentInfoHsc::find($id);
            $data = [
                'student' => $student
            ];
            $html = view('BackEnd.student.admission.hsc.particles.form-edit', $data)->render();

            return response()->json([
                'modal' => 'modal-lg',
                'html' => $html
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }
    // todo
    public function update($request)
    {
        try {
            $id = $request->id;
            $action_type = $request->action_type;

            $ssc_roll = $request->get('ssc_roll');

            $session = $request->get('session');
            $insCode = INS_CODE;

            $dist = $request->same_as_present ? $request->get('present_dist') : $request->get('permanent_dist');
            $thana = $request->same_as_present ? $request->get('present_ps') : $request->get('permanent_ps');

            $hsc_group = $request->get('hsc_group');
            $sub_4 = $request->get('sub_4');
            $sub_5 = $request->get('sub_5');
            $sub_6 = $request->get('sub_6');
            $sub_4th = $request->get('sub_4th');
            $groups = $request->get('groups');
            $auto_id = $request->get('auto_id');
            $compulsory = $request->get('compulsory');
            $selective_sub = $sub_4 . ',' . $sub_5 . ',' . $sub_6;
            $optional_sub = $sub_4th;

            $sub_4_have =  DB::table('course_hsc_new')->where('groups', $groups)->where('codes', $sub_4)->count();
            $sub_5_have =  DB::table('course_hsc_new')->where('groups', $groups)->where('codes', $sub_5)->count();
            $sub_6_have =  DB::table('course_hsc_new')->where('groups', $groups)->where('codes', $sub_6)->count();
            $sub_4th_have =  DB::table('course_hsc_new')->where('groups', $groups)->where('codes', $sub_4th)->count();

            if (($sub_4_have < 1) || ($sub_5_have < 1) || ($sub_6_have < 1) || ($sub_4th_have < 1)) {
                return response()->json([
                    'error' => 'Groups and Subject Codes Not Match'
                ], Response::HTTP_BAD_REQUEST); // 400

            }

            $courses =  DB::select("SELECT * FROM course_hsc_new WHERE `groups` = '$groups'");

            $cods = array();
            foreach ($courses as $course) {
                if (strpos($course->subjects, ',') !== FALSE) {
                    $subjects = explode(",", $course->subjects);
                    $codes = explode(",", $course->codes);
                    foreach ($subjects as $key => $subject) {
                        $cods[$codes[$key]] = $subject;
                    }
                } else {
                    $cods[$course->codes] = $course->subjects;
                }
            }

            $compulsory = explode(",", $compulsory);
            $selective = explode(",", $selective_sub);
            $optional = explode(",", $optional_sub);

            $compulsory_string = '';
            $selective_string = '';
            $optional_string = '';
            foreach ($compulsory as $value) {
                $compulsory_string .= $cods[$value] ?? '' . "(" . $value . "),";
            }
            $compulsory_string = rtrim($compulsory_string, ",");

            foreach ($selective as $value) {
                $selective_string .= $cods[$value] ?? '' . "(" . $value . "),";
            }
            $selective_string = rtrim($selective_string, ",");

            foreach ($optional as $value) {
                $optional_string .= $cods[$value] ?? '' . "(" . $value . "),";
            }
            $optional_string = rtrim($optional_string, ",");

            $compulsory_string = str_replace("-", ",", $compulsory_string);
            $selective_string = str_replace("-", ",", $selective_string);
            $optional_string = str_replace("-", ",", $optional_string);

            $all_string = $compulsory_string . "," . $selective_string . "," . $optional_string;

            $current_level = $request->get('form_current_level');

            DB::table('student_info_hsc')->where('id', $id)->update(
                array(
                    'name' => $request->get('student_name'),
                    'session' => $session,
                    'current_level' => $current_level,
                    'father_name' => $request->get('father_name'),
                    'mother_name' => $request->get('mother_name'),
                    'class_roll' => $request->get('class_roll'),
                    'birth_date' => $request->get('birth_date'),
                    'gender' => $request->get('gender'),
                    'contact_no' => $request->get('student_mobile'),
                    'religion' => $request->get('religion'),
                    'hsc_subjects_info' => $all_string,
                    'registration_id' => $request->get('registration_id'),
                    'guardian' => $request->get('guardian_name'),
                    'guardian_income' => $request->get('income'),
                    'present_village' => $request->get('present_village'),
                    'present_po' => $request->get('present_po'),
                    'present_dist' => $request->get('present_dist'),
                    'present_ps' => $request->get('present_ps'),
                    'permanent_village' => $request->get('permanent_village'),
                    'permanent_po' => $request->get('permanent_po'),
                    'permanent_dist' => $dist,
                    'permanent_ps' => $thana,
                    'ssc_session' => $request->get('ssc_session'),
                    'ssc_reg_no' => $request->get('ssc_registration'),
                    'ssc_roll' => $request->get('ssc_roll'),
                    'ssc_passing_year' => $request->ssc_passing_year,
                    'ssc_board' => $request->ssc_board,
                    'ssc_group' => $request->ssc_group,
                    'gpa' => $request->ssc_gpa
                )
            );

            $filename = '';
            $logo = $request->file('photo');
            $student = DB::table("student_info_hsc")->where('id', $id)->first();

            DB::table('hsc_admitted_students')->where('auto_id', $student->refference_id)->update(
                array(
                    'name' => $request->get('student_name'),
                    'bangla_name' => $request->get('student_name_bn'),
                    'selective' => $selective_sub,
                    'optional' => $optional_sub,
                    'blood_group' => $request->get('blood_group'),
                    'fathers_name' => $request->get('father_name'),
                    'fathers_nid' => $request->get('father_nid'),
                    'mothers_name' => $request->get('mother_name'),
                    'mothers_nid' => $request->get('mother_nid'),
                    'date_of_birth' => $request->get('birth_date'),
                    'birth_reg_no' => $request->get('birth_reg_no'),
                    'password' => $request->get('password'),
                    'ssc_roll' => $request->get('ssc_roll'),
                    'guardian_name' => $request->get('guardian_name'),
                    'guardian_phone' => $request->get('guardian_phone'),
                    'relation' => $request->get('relation'),
                    'quota' => $request->get('quota'),
                    'occupation' => $request->get('occupation'),
                    'income' => $request->get('income'),
                    'mobile' => $request->get('student_mobile'),
                    'ssc_reg_no' => $request->get('ssc_registration'),
                    'ssc_board' => $request->get('ssc_board'),
                    'ssc_group' => $request->get('ssc_group'),
                    'ssc_institution' => $request->get('ssc_institute'),
                    'ssc_session' => $request->get('ssc_session'),
                    'ssc_gpa' => $request->get('ssc_gpa'),
                    'ssc_passing_year' => $request->ssc_passing_year,
                    'sex' => $request->get('gender'),
                    'religion' => $request->get('religion'),
                    'admission_session' => $session,
                    'village' => $request->get('present_village'),
                    'post_office' => $request->get('present_po'),
                    'district' => $request->get('present_dist'),
                    'upozilla' => $request->get('present_ps'),
                    'permanent_village' => $request->get('permanent_village'),
                    'permanent_post_office' => $request->get('permanent_po'),
                    'permanent_district' => $dist,
                    'permanent_thana' => $thana,
                )
            );

            if ($logo != '') {
                $folder = public_path('upload/college/hsc/' . $student->session);
                create_dir($folder);
                if (\File::exists($folder . '/' . $student->image)) {
                    \File::delete($folder . '/' . $student->image);
                }
                $filename = rand(1, 99999999999) . '.jpg';
                $upload_path = $folder . '/' . $filename;
                $db_path = 'upload/college/hsc/' . $filename;
                Image::make($logo->getRealPath())->save($upload_path);

                DB::table('hsc_admitted_students')->where('auto_id', $student->refference_id)->update([
                    'photo' => $filename
                ]);

                DB::table('student_info_hsc')->where('id', $id)->update([
                    'image' => $filename
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'info',
                'message' => "Student Updated Successfully for *<b>$student->name</b>",
                'id' => $student->id,
                'table' => 'datatable'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'danger' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $student = StudentInfoHsc::find($id);
            HscAdmittedStudent::where('auto_id', $student->refference_id)->delete();
            $student->delete();

            DB::commit();
            return response()->json([
                'status' => 'warning',
                'message' => 'Student has been deleted successfully',
                'id' => $id,
                'table' => 'datatable',
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function force_promotion(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $student =  StudentInfoHsc::find($id);

                if ($student->current_level != 'HSC 2nd Year') {
                    $student->current_level = 'HSC 2nd Year';
                    $student->save();
                }
                $student->save();
            }
            $message = 'You have successfully Promoted Student To 2nd Year';
            $status = 'success';
            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $message,
                'table' => 'datatable',
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function regStudent(Request $request)
    {

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student.hsc:HSC|Registered Student';
        $ref_id = $request->ref_id;
        $ssc_roll = $request->ssc_roll;
        $session = $request->session;

        $ref_id = hsc_tracking_auto_id($ref_id);

        $hscstudents = Study::regSearchHscStudent($ref_id, $ssc_roll, $session)->paginate(Study::paginate());

        return    view('BackEnd.student.admission.hsc.regStudent', compact('title', 'hscstudents', 'breadcrumb', 'ref_id', 'ssc_roll', 'session'));
    }

    public function printDetails($id)
    {
        error_reporting(0);
        $student = DB::table('student_info_hsc')->where('id', $id)->first();
        $admitted_student = DB::table('hsc_admitted_students')->where('auto_id', $student->refference_id)->where('admission_session', $student->session)->first();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'times']);
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;
        // $mpdf->SetWatermarkImage(asset('upload/sites/' . config('settings.site_logo')), .09, array(110, 110));
        // $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter('<p style="vertical-align: bottom; font-family: serif; 
        font-size: 8pt; color: #000000; font-weight: bold; font-style: italic; text-align:center">Developed & Maintained by <img style="width:75px; margin-bottom:-5px;" src="' . asset('img/company.png') . '"></p>');

        $html = view('admission.hsc.form_id', compact('admitted_student', 'student'));

        $mpdf->writeHTML($html);
        $filename = $student->id . '_' . $student->session . "_admission.pdf";
        $mpdf->Output($filename, 'I');
    }

    public function totlistSelect()
    {

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student.hsc:HSC|Tot List';

        return view('BackEnd.student.admission.hsc.totlist')
            ->withTitle($title)
            ->withBreadcrumb($breadcrumb);
    }

    public function totlistGenerate(Request $request)
    {

        if ($request->ajax()) {

            $groups = $request->get('groups');
            $session = $request->get('session');

            $results = DB::table('student_info_hsc')
                ->join('hsc_admitted_students', 'student_info_hsc.refference_id', '=', 'hsc_admitted_students.auto_id')
                ->select(
                    'student_info_hsc.name',
                    'student_info_hsc.father_name',
                    'student_info_hsc.mother_name',
                    'student_info_hsc.admission_date',
                    'student_info_hsc.class_roll',
                    'hsc_admitted_students.ssc_reg_no',
                    'hsc_admitted_students.ssc_gpa',
                    'hsc_admitted_students.ssc_total_mark',
                    'student_info_hsc.permanent_village',
                    'student_info_hsc.permanent_po',
                    'student_info_hsc.permanent_ps',
                    'student_info_hsc.permanent_dist',
                    'student_info_hsc.ssc_roll',
                    'student_info_hsc.contact_no',
                    'hsc_admitted_students.ssc_board',
                    'hsc_admitted_students.optional',
                    'student_info_hsc.merit_rank',
                    'student_info_hsc.hsc_subjects_info',
                    'student_info_hsc.gender',
                )
                ->where('student_info_hsc.session', '=', $session)
                ->Where('student_info_hsc.groups', '=', $groups)

                ->get();

            $header_csv = array(
                'SI',
                'Student\'s Name',
                'Father\'s Name',
                'Mother\'s Name',
                'Date Of Admission',
                'Class Roll',
                'SSC/Equ. Registration No',
                'SSC/Equ. Gpa',
                'SSC/Equ. Roll No',
                'Contact No',
                'Gender',
                'Board',
                'Rank',
                'Course',
                'Fourth Subject',
                'SSC Total Mark',
                'Permanent Address'
            );
            H2H::make_head($header_csv);

            echo "<center><h3>TOT List Of Group:{$groups}  Session: {$session}</h3><center>";


            echo "<div style='overflow:scroll; height:400px'>";
            echo "<table border='0' class='table table-bordered' width='1000px'>";
            echo "<tr>";
            echo "<th>Si</th>";
            echo "<th>Student's Name</th>";
            echo "<th>Father's Name</th>";
            echo "<th>Mother's Name</th>";
            echo "<th>Date Of Admission</th>";
            echo "<th>Class Roll</th>";
            echo "<th>SSC/Equ. Reg. No</th>";
            echo "<th>SSC/Equ. Gpa </th>";
            echo "<th>SSC/Equ. Roll</th>";
            echo "<th>Contact No</th>";
            echo "<th>Gender</th>";
            echo "<th>Board</th>";
            echo "<th>Rank</th>";
            echo "<th>Course</th>";
            echo "<th>Fourth Subject</th>";
            echo "<th>SSC Total Mark</th>";

            echo "<tr>";
            $i = 0;
            foreach ($results as $result) {
                $i++;
                H2H::$comma = "";

                $permanent_villege = $result->permanent_village;
                $permanent_po = $result->permanent_po;
                $permanent_thana = $result->permanent_ps;
                $permanent_dist = $result->permanent_dist;
                $permanent_address = "Vill- {$permanent_villege},Post Office- {$permanent_po}, Thana- {$permanent_thana},Dist-{$permanent_dist}";

                $fourth = $result->optional;

                $res = DB::table('course_hsc_new')
                    ->select('subjects')
                    ->where('codes', $fourth)
                    ->get();

                $fth = '';
                foreach ($res as $rest) {
                    $fth = $rest->subjects;
                }

                $fth = $fth . '(' . $fourth . ')';
                echo "<tr>";

                echo "<td>{$i}</td>";
                H2H::make_line($i);

                echo "<td>{$result->name}</td>";
                H2H::make_line($result->name);

                echo "<td>{$result->father_name}</td>";
                H2H::make_line($result->father_name);


                echo "<td>{$result->mother_name}</td>";
                H2H::make_line($result->mother_name);


                $ad = explode(" ", $result->admission_date);
                echo "<td>{$ad[0]}</td>";
                H2H::make_line($ad[0]);

                echo "<td>{$result->class_roll}</td>";
                H2H::make_line($result->class_roll);

                echo "<td>{$result->ssc_reg_no}</td>";
                H2H::make_line($result->ssc_reg_no);

                echo "<td>{$result->ssc_gpa}</td>";
                H2H::make_line($result->ssc_gpa);

                echo "<td>{$result->ssc_roll}</td>";
                H2H::make_line($result->ssc_roll);

                echo "<td>{$result->contact_no}</td>";
                H2H::make_line($result->contact_no);

                echo "<td>{$result->gender}</td>";
                H2H::make_line($result->gender);


                echo "<td>{$result->ssc_board}</td>";
                H2H::make_line($result->ssc_board);

                echo "<td>{$result->merit_rank}</td>";
                H2H::make_line($result->merit_rank);

                echo "<td>{$result->hsc_subjects_info}</td>";
                H2H::make_line($result->hsc_subjects_info);

                echo "<td>{$fth}</td>";
                H2H::make_line($fth);

                echo "<td>{$result->ssc_total_mark}</td>";
                H2H::make_line($result->ssc_total_mark);

                echo "<tr>";

                H2H::make_line($permanent_address);
                H2H::end_line();
            }
            echo "</table>";
            echo "</div>";


            $file_directory = public_path() . '/' . "download/";

            $original_name = "tot_list_{$groups}_{$session}.csv";
            $file_name = $file_directory . "tot_list_{$groups}_{$session}.csv";
            $download_directory = "download/tot_list_{$groups}_{$session}.csv";
            H2H::make_csv($file_name);

            echo "<div style='margin-top:-460px; margin-left:570px;font-size:15px;color:#800000'><a href='" . url('/download') . "/{$original_name}'>Click to Download <img src='../../img/doc_excel_csv.png' /></a></div>";
        }
    }
}

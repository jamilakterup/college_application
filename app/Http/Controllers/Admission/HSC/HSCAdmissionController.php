<?php

namespace App\Http\Controllers\Admission\HSC;

use DB;
use Auth;
use Image;
use Session;
use Mpdf\Mpdf;
use IdRollGenerate;
use App\Libs\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\HscAdmittedStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class HSCAdmissionController extends Controller
{
  public function index()
  {
    Auth::logout();
    Session::flush();
    return view('admission.hsc.index');
  }

  public function admissionForm()
  {

    $ssc_roll    = Session::get('ssc_roll');
    $ssc_board   = Session::get('ssc_board');
    $hsc_group       = Session::get('hsc_group');
    $passing_year = Session::get('passing_year');
    $session = Session::get('session');
    $name = Session::get('name');
    $admission_step = Session::get('admission_step');

    if ($admission_step != 1)
      return Redirect::route('student.hsc.admission');

    $blood_lists = ['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'O+' => 'O+', 'O-' => 'O-', 'AB+' => 'AB+', 'AB-' => 'AB-'];

    $dist = DB::table('district_thana')->distinct()->get(['district']);
    return view('admission.hsc.form', compact('blood_lists'))
      ->withName($name);
  }
  public function districtChange(Request $request)
  {

    if ($request->ajax()) {
      $dist = $request->get('dist');
      $result = DB::table('district_thana')
        ->select('thana')
        ->Where('district', $dist)
        ->get();

      foreach ($result as  $value) {

        echo  "<option value='{$value->thana}'>{$value->thana}</option>";
      }
    }
  }

  public function checkMerit(Request $request)
  {

    // return $request->all();

    if ($request->ajax()) {

      $configs = DB::table('admission_config')->where('course', 'hsc')->where('current_level', 'HSC 1st Year')->where('open', 1)->get();
      if (count($configs) < 1) {
        // admission is not open
        return $status = 4;
      } else {
        $config = $configs->first();
        if ($config->clossing_date < date('Y-m-d')) {
          // admission date is expired
          return $status = 6;
        }
      }

      $ssc_roll = $request->get('ssc_roll');
      $session = $config->session;
      $opening_date = $config->opening_date;
      $ssc_board = $request->get('ssc_board');
      $ssc_passing_year = $request->get('ssc_passing_year');
      $quota_pass = $request->get('quota_pass');
      Session::put('session', $session);
      Session::put('admission_session', $session);


      $merit_result = DB::table('hsc_merit_list')->where('ssc_roll', $ssc_roll)
        ->where('ssc_board', $ssc_board)
        ->where('passing_year', $ssc_passing_year)
        ->get();

      if (count($merit_result) > 0) {
        $invoices = Invoice::where('roll', $ssc_roll)->where('passing_year', $merit_result[0]->passing_year)->where('admission_session', $config->session)->where('ssc_board', $merit_result[0]->ssc_board)->where('date_start', '>=', $opening_date)->where('type', 'hsc_admission')->orderByRaw("CASE WHEN status = 'Paid' THEN 1 ELSE 0 END DESC")->orderBy('id', 'desc')->get();

        if (count($invoices) > 0) {
          $invoice = $invoices->first();
          Session::put('invoice_id', $invoice->id);
          if ($invoice->status == 'Paid') {
            $admitted_student = DB::table('hsc_admitted_students')->where('ssc_roll', $ssc_roll)->where('admission_session', $config->session)->where('ssc_board', $merit_result[0]->ssc_board)->get();

            if (count($admitted_student) > 0) {
              $student_info = DB::table('student_info_hsc')->where('refference_id', $admitted_student[0]->auto_id)->where('session', $config->session)->get();
              if (count($student_info) > 0) {
                // redirect to download slip
                Session::put('hsc_con', 1);
                $auto_id = auto_id_hsc($admitted_student[0]->auto_id);
                Session::put('tracking_id', HSC_PREF . $auto_id);
                return $status = 7;
              }
            }
          }
        } else {
          // invoice not generated
          return $status = 2;
        }

        $check_quota = $merit_result[0]->quota;
        if ($check_quota == 1) {
          $quota_pass_check = $merit_result[0]->password;
          if ($quota_pass != $quota_pass_check) {
            return $status = 5;
          }
        }

        foreach ($merit_result as  $value) {
          $ssc_roll = $value->ssc_roll;
          $ssc_board = $value->ssc_board;
          $name = $value->name;
          $passing_year = $value->passing_year;
          $ssc_group = $value->ssc_group;
          $session = $value->session;
        }

        Session::put('ssc_roll', $ssc_roll);
        Session::put('ssc_board', $ssc_board);
        Session::put('name', $name);
        Session::put('passing_year', $passing_year);
        Session::put('ssc_group', $ssc_group);
        Session::put('hsc_group', $ssc_group);
        Session::put('exam_name', 'ssc');
        Session::put('admission_step', 1);
        Session::put('opening_date', $opening_date);
        // continue
        return $status = 1;
      } else
        // not in merit list
        return $status = 3;
    }
  }

  public function hscGroupChange()
  {

    $group = $_POST['group'];
    $course = $_POST['course'];

    return view('admission.hsc.hsc_group_change')
      ->withGroup($group)
      ->withCourse($course);
  }

  public function retrievepass()
  {

    $ssc_roll = $_POST['ssc_roll'];
    $PIN_number = $_POST['PIN_no'];

    $results = DB::table('hsc_admitted_students')->where('ssc_roll', $ssc_roll)->where('ssc_reg_no', $PIN_number)->get();

    $auto_id = '';
    $password = '';
    foreach ($results as $result) {
      $password = $result->password;
      $auto_id = $result->auto_id;
    }

    $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
    $auto_id = HSC_PREF . $auto_id;
    echo json_encode(array($password, $auto_id));
  }


  public function hscInformationSubmit(Request $request)
  {

    $this->validate($request, [
      'ssc_roll' => 'required|numeric',
      'ssc_gpa' => 'required|numeric',
      'photo' => 'required|mimes:jpeg,jpg,png|max:15|dimensions:width=120,height=150',
      'fathers_nid' => 'required|numeric',
      'mothers_nid' => 'required|numeric',
      'birth_reg_no' => 'required|numeric'
    ]);
    $temp_entry_time = date('Y-m-d G:i:s');
    $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));

    $logo = $request->file('photo');

    $filename = rand(1, 99999999999) . '.jpg';
    $upload_path = public_path('upload/college/hsc/draft/' . $filename);
    $db_path = 'upload/college/hsc/' . $filename;

    Image::make($logo->getRealPath())->save($upload_path);

    $compulsorycourse = $request->get('compulsorycourse');

    $compulsorycourse =  implode(",", $compulsorycourse);

    $selectivecourse = $request->get('selectivecourse');
    $selectivecourse =  implode(",", $selectivecourse);
    $ssc_roll = $request->get('ssc_roll');

    if ($ssc_roll == '') {
      return Redirect::back()->withInput()->with('error', 'Something Went Wrong. Please Try Again.');
    }

    $ssc_passing_year = $request->get('ssc_passing_year');
    $ssc_board = trim($request->get('ssc_board'));
    $PIN_number =  $request->get('ssc_registration');
    $faculty = $request->get('hsc_group');
    $gender = $request->get('gender');
    $ssc_group = $request->get('ssc_group');
    $merit_ssc_group = Session::get('ssc_group');
    $opening_date = Session::get('opening_date');
    $slepHeaders = DB::table('payslipheaders')->where('pro_group', 'hsc')->where('level', 'HSC 1st Year')->where('group_dept', 'LIKE', '%' . $faculty . '%')->where('session', $request->get('admission_session'))->get();

    if (count($slepHeaders) > 1) {
      foreach ($slepHeaders as $header) {
        if (strpos($header->title, $gender) !== false) {
          $pay_header = $header;
        }
      }
    } else {
      $pay_header = $slepHeaders->first();
    }
    if (!isset($pay_header)) {
      $pay_header = $slepHeaders->first();
    }

    if (is_null($pay_header)) {
      $message = 'No Payment Information Found. Please contact to college';
      return Redirect::back()->withInput()->with('warning', $message);
    }

    $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $pay_header->id");
    $total_amount = 0;
    foreach ($amounts as $amount) {
      $total_amount = $total_amount + $amount->fees;
    }
    if ($total_amount == 0) {
      $message = 'No Payment Information Found. Please contact to college';
      return Redirect::back()->withInput()->with('warning', $message);
    }

    $admission_session = $request->get('admission_session');
    $invoices = Invoice::where('roll', $ssc_roll)->where('passing_year', $ssc_passing_year)->where('date_start', '>=', $opening_date)->where('admission_session', $admission_session)->where('ssc_board', $ssc_board)->where('type', 'hsc_admission')->orderBy('id', 'desc')->get();

    if (count($invoices) < 1) {
      $message = 'Invoice Not Generated. Please Contact to College';
      return Redirect::back()->with('warning', $message);
    }

    $invoice = $invoices->first();
    DB::table('invoices')->where('id', $invoice->id)->update([
      'pro_group' => $faculty,
      'slip_type' => $pay_header->code,
      'slip_name' => $pay_header->title
      // 'total_amount' => $total_amount
    ]);

    $submitted_data = array(
      'entry_time' => $entry_time,
      'invoice_id' => $invoice->id,
      'photo' => $filename,
      'ssc_roll' => $ssc_roll,
      'ssc_passing_year' => $ssc_passing_year,
      'entry_time' => $entry_time,
      'photo' => $filename,
      'name' => $request->get('student_name'),
      'compulsory' => $compulsorycourse,
      'selective' => $selectivecourse,
      'optional' => $request->get('selecting'),
      'hsc_group' => $request->get('hsc_group'),
      'blood_group' => $request->get('blood_group'),
      'exam_name' => $request->get('exam_name'),
      'bangla_name' => $request->get('name_bangla'),
      'PIN_number' => $request->get('PIN_number'),
      'fathers_name' => $request->get('father_name'),
      'mothers_name' => $request->get('mother_name'),
      'fathers_nid' => $request->get('fathers_nid'),
      'mothers_nid' => $request->get('mothers_nid'),
      'date_of_birth' => date('Y-m-d', strtotime($request->get('birth_date'))),
      'religion' => $request->get('religion'),
      'quota' => $request->get('quota'),
      'password' => $request->get('password'),
      'sex' => $request->get('gender'),
      'relation' => $request->get('relation'),
      'village' => $request->get('present_village'),
      'post_office' => $request->get('present_po'),
      'district' => $request->get('present_dist'),
      'upozilla' => $request->get('present_thana'),
      'mobile' => $request->get('student_mobile'),
      'permanent_village' => $request->get('permanent_village'),
      'permanent_post_office' => $request->get('permanent_post_office'),
      'permanent_thana' => $request->get('permanent_thana'),
      'permanent_district' => $request->get('permanent_district'),
      'ssc_roll' => $request->get('ssc_roll'),
      'ssc_passing_year' => $request->get('ssc_passing_year'),
      'ssc_board' => $request->get('ssc_board'),
      'income' => $request->get('income'),
      'occupation' => $request->get('occupation'),
      'admission_session' => $request->get('admission_session'),
      'ssc_gpa' => $request->get('ssc_gpa'),
      'guardian_name' => $request->get('guardian_name'),
      'guardian_phone' => $request->get('guardian_phone'),
      'emergency_contact_no' => $request->get('emergency_contact_no'),
      'relation' => $request->get('guardian_relation'),
      'ssc_reg_no' => $request->get('ssc_registration'),
      'ssc_group' => $request->get('ssc_group'),
      'ssc_institution' => $request->get('ssc_institute'),
      'ssc_session' => $request->get('ssc_session'),
      'ssc_gpa' => $request->get('ssc_gpa'),
      'birth_reg_no' => $request->get('birth_reg_no'),
    );

    $admitted_student = DB::table('hsc_admitted_students')->where('admission_session', $admission_session)->where('ssc_board', $ssc_board)->where('ssc_roll', $ssc_roll)->get();

    if (count($admitted_student) > 0) {
      DB::table('hsc_admitted_students')->where('auto_id', $admitted_student->first()->auto_id)
        ->update($submitted_data);
      $admitted_id = $admitted_student->first()->auto_id;
    } else {
      $admitted_id = DB::table('hsc_admitted_students')
        ->insertGetId($submitted_data);
    }

    $admitted_student = DB::table('hsc_admitted_students')->where('auto_id', $admitted_id)->where('admission_session', $admission_session)->where('ssc_roll', $ssc_roll)->get();

    foreach ($admitted_student as $result) {
      $auto_id = auto_id_hsc($result->auto_id);
      $tracking_id = HSC_PREF . $auto_id;
      $password = $result->password;
      $refId = $result->auto_id;
    }

    Session::put('tracking_id', $tracking_id);
    Session::put('password', $password);



    return Redirect::route('student.hsc.admission.form');
  }

  public function hscSignin()
  {

    return view('admission.hsc.sign_in_form');
  }

  public function hscStudentSignin()
  {
    $status = 0;
    $tracking_id = trim($_POST['tracking_id']);
    $password = trim($_POST['password']);
    //$password = $database->passwod_encode($password);
    $number = 0;
    $query = HSC_PREF;

    if ($auto_id = hsc_tracking_auto_id($tracking_id)) {
      $results = DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->where('password', $password)->get();
      $number = count($results);
    }
    if ($number > 0) {
      $hsc_admitted = $results->first();

      Session::put('tracking_id', $tracking_id);
      Session::put('admission_session', $hsc_admitted->admission_session);
      Session::put('hsc_con', 1);


      if ($hsc_admitted->invoice_id != 0) {
        $invoices = Invoice::where('roll', $hsc_admitted->ssc_roll)->where('ssc_board', $hsc_admitted->ssc_board)->where('admission_session', $hsc_admitted->admission_session)->where('type', 'hsc_admission')->orderByRaw("CASE WHEN status = 'Paid' THEN 1 ELSE 0 END DESC")->orderBy('id', 'desc')->get();
        if (count($invoices) > 0) {
          $invoice = $invoices->first();
          Session::put('invoice_id', $invoice->id);
          Session::put('auto_id', $hsc_admitted->auto_id);
        } else {
          // invoice not generated
          return $status = 5;
        }
      }

      // redirect to login
      return $status = 1;
    } else {
      // admitted student not found
      return $status = 2;
    }
  }

  public function HscConfirmation()
  {
    if (!Session::has('hsc_con')) {
      return view('admission.hsc.index');
    }

    $tracking_id = Session::get('tracking_id');
    $auto_id = hsc_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    $result = DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->first();

    $invoice = Invoice::where('id', $invoice_id)->where('admission_session', $result->admission_session)->where('type', 'hsc_admission')->orderBy('id', 'desc')->first();

    $payment_status = 'Paid';


    if ($invoice->status == 'Pending') {
      $payment_status = 'Pending';
    } else {
      $student_infos = DB::table('student_info_hsc')->where('session', $result->admission_session)->where('refference_id', $result->auto_id)->get();

      if (count($student_infos) < 1) {
        return $this->dutchbangla($result->auto_id);
      }
    }

    return view('admission.hsc.confirmation', compact('payment_status'));
  }

  public function SubjectCodeSequence()
  {

    $status = $_POST['status'];
    $id = $_POST['id'];

    return view('admission.hsc.desired_subject_code_sequence')
      ->withStatus($status)
      ->withId($id);
  }

  public function downloadHscForm()
  {
    $tracking_id = Session::get('tracking_id');

    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $auto_id = hsc_tracking_auto_id($tracking_id);

    $admitted_student = DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->get();

    if (count($admitted_student) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $admitted_student = $admitted_student->first();
    $session = $admitted_student->admission_session;

    $student = DB::table('student_info_hsc')->where('refference_id', $auto_id)->where('session', $session)->first();

    error_reporting(0);

    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'times']);
    addMpdfPageSetup($mpdf);

    $html = view('admission.hsc.form_id', compact('admitted_student', 'student'));

    $mpdf->writeHTML($html);
    $filename = $student->id . "_admission_form.pdf";
    $file_path = public_path() . "/download/hsc/";
    $mpdf->Output($file_path . '/' . $filename);
    echo "<center><a href='" . url('/') . "/download/hsc/" . $filename . "' target='_blank'>Click to Download</a></center>";
  }

  public function downloadHscFormtest()
  {
    return view('admission.hsc.form_idtest');
  }


  public function downloadHscIdCard()
  {
    return view('admission.hsc.tid_id');
  }

  public function downloadSlipId()
  {
    $tracking_id = Session::get('tracking_id');

    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $auto_id = hsc_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    $invoice = DB::table('invoices')->where('id', $invoice_id)->first();

    $admitted_student = DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->get();

    if (count($admitted_student) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $admitted_student = $admitted_student->first();
    $session = $admitted_student->admission_session;

    $student = DB::table('student_info_hsc')->where('refference_id', $auto_id)->where('session', $session)->first();

    error_reporting(0);
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'times']);
    addMpdfPageSetup($mpdf);

    $html = view('admission.hsc.slip_id', compact('student', 'invoice', 'tracking_id'));

    $mpdf->writeHTML($html);
    $filename = $student->id . "_admission_confirmation.pdf";
    $file_path = public_path() . "/download/hsc/";
    $mpdf->Output($file_path . '/' . $filename);
    echo "<center><a href='" . url('/') . "/download/hsc/" . $filename . "' target='_blank'>Click to Download</a></center>";
    // return view('admission.hsc.slip_id');
  }

  public function admisionLogout()
  {
    Auth::logout();
    Session::flush();
    return Redirect::route('student.hsc.admission.signin');
  }

  public function roll_generate_hsc($session, $groups)
  {

    if ($groups == 'Humanities')  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
      $cat = "2";
    else if ($groups == 'Science')
      $cat = "1";
    else if ($groups == 'Business Studies')
      $cat = "3";
    $cat = '000';
    $groups = 'hsc_' . $groups;

    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$groups'");
    //convert 1 as 001 for 3 digit roll
    foreach ($results as $result) {
      $digit = str_pad($result->last_digit_used + 1, '3', '0', STR_PAD_LEFT);
      break;
    }
    $session = substr($session, 0, 4);
    $class_roll = $session . $cat . $digit;

    return $class_roll;
  }

  public function randomPassword()
  {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }

  // public function id_generate_hsc($session,$class_roll,$catagory){

  //   $session=substr($session,0,4);  // take session as first year of the session(ex: 2012-2013 , session is 2012)
  //   return $id=$session.$catagory.$class_roll;


  // }


  public function dutchbangla($auto_id)
  {

    $admission_session = Session::get('admission_session');
    $invoice_id = Session::get('invoice_id');
    if ($admission_session == '') {
      return redirect()->route('student.hsc.admission')->with('warning', 'Something Went Wrong Please Try Again!');
    }
    $autoId = $auto_id;

    $student = DB::table('hsc_admitted_students')->where('admission_session', $admission_session)->where('auto_id', $auto_id)->first();
    $invoice = Invoice::where('id', $invoice_id)->where('roll', $student->ssc_roll)->where('admission_session', $admission_session)->where('type', 'hsc_admission')->first();

    $ssc_roll = $student->ssc_roll;
    $hsc_group = $student->hsc_group;
    $slip_type = $invoice->slip_type;
    $pay_am_floor = $invoice->total_amount;

    $trxid = $invoice->trx_id;
    $pay_da = date('Y-m-d h:i:s', strtotime($invoice->txndate));
    $billid = $invoice->roll;

    $results =  DB::table('hsc_admitted_students')->where('admission_session', $admission_session)->where('auto_id', $auto_id)->get();

    if (count($results) < 1) {

      return Redirect::route('student.hsc.admission.HscConfirmation')->with('res', 'Sorry, Student not found');
    }

    foreach ($results as $result) {
      $name = $result->name;
      $PIN_number = $result->PIN_number;
      $father_name = $result->fathers_name;
      $mother_name = $result->mothers_name;
      $birth_date = $result->date_of_birth;
      $gender = $result->sex;
      $perm_vill = $result->permanent_village;
      $present_villege = $result->village;
      $permanent_po = $result->permanent_post_office;
      $present_po = $result->post_office;
      $permanent_ps = $result->permanent_thana;
      $present_ps = $result->upozilla;
      $permanent_dist = $result->permanent_district;
      $present_dist = $result->district;
      $contact_no = $result->mobile;
      $religion = $result->religion;
      $guardian_name = $result->guardian_name;
      $guardian_relation = $result->relation;
      $guardian_occupation = $result->occupation;
      $guardian_income = $result->income;
      $ssc_roll = $result->ssc_roll;
      $ssc_session = $result->ssc_session;
      $ssc_reg_no = $result->ssc_reg_no;
      $ssc_group = $result->ssc_group;
      $ssc_institute = $result->ssc_institution;
      $ssc_board = $result->ssc_board;
      $ssc_pass_year = $result->ssc_passing_year;
      $ssc_gpa = $result->ssc_gpa;
      $email = $result->email;
      $group = $result->ssc_group;
      $photo = $result->photo;
      $session = $result->admission_session;
      $st_ref_id = $result->auto_id;
      $image_name = $result->photo;
      $ssc_roll = $result->ssc_roll;
      $hsc_group = $result->hsc_group;
      $compulsory = $result->compulsory;
      $selective = $result->selective;
      $optional = $result->optional;
    }

    $results2 =  DB::select("SELECT COUNT(*) as cnt FROM student_info_hsc WHERE groups='$hsc_group' and session='$session'");

    foreach ($results2 as $result) {
      $special_count = $result->cnt;
    }


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

    $compulsory = explode(",", $compulsory);
    $selective = explode(",", $selective);
    $optional = explode(",", $optional);

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

    $results = DB::select("SELECT * FROM hsc_merit_list WHERE ssc_roll = $ssc_roll AND  ssc_board='$ssc_board' AND passing_year='$ssc_pass_year' AND admission_status!='admitted'");
    if (count($results) < 1) {
      return Redirect::route('student.hsc.admission.HscConfirmation')->with('res', 'Student not found in merit list');
    }

    $id = IdRollGenerate::id_generate_hsc($session, $hsc_group);
    $class_roll = IdRollGenerate::roll_generate_hsc($id);

    $results = DB::select("SELECT merit_status,rank FROM hsc_merit_list WHERE ssc_roll=$ssc_roll AND  ssc_board ='$ssc_board' AND passing_year='$ssc_pass_year'");
    foreach ($results as $result) {
      $rank = $result->rank;
      $merit_status = $result->merit_status;
    }

    DB::beginTransaction();

    try {
      DB::table('student_info_hsc')->insert(
        array('id' => $id, 'name' => $name, 'PIN_number' => $PIN_number, 'class_roll' => $class_roll, 'session' => $session, 'groups' => $hsc_group, 'current_level' => 'HSC 1st Year', 'father_name' => $father_name, 'mother_name' => $mother_name, 'birth_date' => $birth_date, 'gender' => $gender, 'permanent_village' => $perm_vill, 'present_village' => $present_villege, 'permanent_po' => $permanent_po, 'present_po' => $present_po, 'permanent_ps' => $permanent_ps, 'present_ps' => $present_ps, 'permanent_dist' => $permanent_dist, 'ssc_passing_year' => $ssc_pass_year, 'present_dist' => $present_dist, 'contact_no' => $contact_no, 'religion' => $religion, 'guardian' => $guardian_name, 'image' => $image_name, 'refference_id' => $st_ref_id, 'ssc_roll' => $ssc_roll, 'merit_status' => $merit_status, 'merit_rank' => $rank, 'hsc_subjects_info' => $all_string, 'ssc_session' => $ssc_session, 'ssc_reg_no' => $ssc_reg_no, 'ssc_board' => $ssc_board, 'ssc_group' => $ssc_group, 'gpa' => $ssc_gpa, 'total_amount' => $invoice->total_amount)
      );

      $oldPath = 'upload/college/hsc/draft/' . $image_name; // publc/images/1.jpg
      if (file_exists(public_path($oldPath))) {
        $folder = public_path('upload/college/hsc/' . $session);
        create_dir($folder);
        $newPath = public_path('upload/college/hsc/' . $session . '/' . $image_name); // publc/images/2.jpg
        $oldPath = public_path($oldPath);
        if (\File::copy($oldPath, $newPath)) {
        }
      }

      $d = date('Y-m-d');

      DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='hsc_{$hsc_group}'");


      $date = date('Y-m-d');


      DB::update("update hsc_admitted_students set payment_status='dbbl',paid_date='$date' and complete_sms=1 where auto_id='$st_ref_id'");

      DB::update("update hsc_merit_list set admission_status='admitted' where ssc_roll='$ssc_roll' AND  ssc_board='$ssc_board' AND passing_year='$ssc_pass_year'");

      DB::commit();

      return Redirect::route('student.hsc.admission.HscConfirmation')->with('res', 'টাকা সফল ভাবে জমা হয়েছে');
    } catch (\Illuminate\Database\QueryException $e) {
      DB::rollback();
      return Redirect::route('student.hsc.admission.signin')->with('error', $e->errorInfo[2]);
    }
  }


  public function hscimagedownload()
  {
    $results = DB::select("SELECT * FROM `student_info_hsc` WHERE `session` LIKE '2020-2021'");


    foreach ($results as $result) {
      $file_name = $result->id . '.jpg';
      $url = 'http://easycollegemate.com/ecmrgwc/public/upload/college/hsc/' . $result->image;


      try {
        $url = str_replace(" ", '%20', $url);

        $file_name = public_path('hscdownload/' . $file_name);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_REFERER, "http://www.xcontest.org");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        //print_r($result); // prints the contents of the collected file before writing..


        // the following lines write the contents to a file in the same directory (provided permissions etc)
        $fp = fopen($file_name, 'w');
        fwrite($fp, $result);
        fclose($fp);
      } catch (Exception $e) {
        return $e;
      }
    }

    return  'Ok';
  }

  public function payment_approve(Request $request)
  {
    $response = Payment::approve($request->transaction_id);

    if ($response == '') {
      return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");
    }

    if ($response['status'] == '402') {
      return redirect()->back()->withInput()->with('error', $response['msg']);
    }

    if ($response['status'] == '200') {
      return $this->dutchbangla(Session::get('auto_id'));
    }

    return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");
  }

  public function editForm()
  {
    if (get_config('hsc_form_edit') != 1) {
      $html = "<h1 style='text-align:center;color:red;'>You don't have enough permission to Edit. it is not opened!</h1>";
      $html .= "<center><a href=" . url()->previous() . " style='display:inline-block; padding:10px;color:white;background:#e67e22; border-radius: 5px;text-decoration:none;'>Return to Back</a></center>";
      return $html;
    }
    $tracking_id = Session::get('tracking_id');
    $invoice_id = Session::get('invoice_id');
    $admission_session = Session::get('admission_session');
    $auto_id = hsc_tracking_auto_id($tracking_id);

    $invoice = DB::table('invoices')->where('id', $invoice_id)->first();
    $admitted_student = DB::table('hsc_admitted_students')->where('admission_session', $admission_session)->where('auto_id', $auto_id)->first();

    if (is_null($admitted_student))
      return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");

    $student = DB::table('student_info_hsc')->where('refference_id', $auto_id)->where('session', $admission_session)->first();

    return view('admission.hsc.editForm');
  }

  public function updateForm(Request $request)
  {
    $tracking_id = Session::get('tracking_id');
    $invoice_id = Session::get('invoice_id');
    $admission_session = Session::get('admission_session');
    $auto_id = hsc_tracking_auto_id($tracking_id);

    $invoice = DB::table('invoices')->where('id', $invoice_id)->first();
    $admitted_student = DB::table('hsc_admitted_students')->where('admission_session', $admission_session)->where('auto_id', $auto_id)->first();

    if (is_null($admitted_student))
      return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");

    $student = DB::table('student_info_hsc')->where('refference_id', $auto_id)->where('session', $admission_session)->first();


    if ($request->hasFile('photo')) {
      $photo = $request->file('photo');
      $folder = public_path('upload/college/hsc/' . $admission_session);
      create_dir($folder);

      if (!empty($student->image) && \File::exists($folder . '/' . $student->image)) {
        \File::delete($folder . '/' . $student->image);
      }

      $filename = rand(1, 99999999999) . '.jpg';
      $upload_path = $folder . '/' . $filename;
      Image::make($photo->getRealPath())->save($upload_path);

      DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->update([
        'photo' => $filename
      ]);

      DB::table('student_info_hsc')->where('refference_id', $auto_id)->update([
        'image' => $filename
      ]);
    } else {
      DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->update([
        'photo' => $student->image
      ]);

      DB::table('student_info_hsc')->where('refference_id', $auto_id)->update([
        'image' => $student->image
      ]);
    }


    $hsc_group = $request->get('hsc_group');
    if ($hsc_group != '') {
      $compulsorycourse = $request->get('compulsorycourse');

      $compulsorycourse =  implode(",", $compulsorycourse);

      $selectivecourse = $request->get('selectivecourse');
      $selectivecourse =  implode(",", $selectivecourse);

      DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->update(
        array('compulsory' => $compulsorycourse, 'selective' => $selectivecourse, 'optional' => $request->get('selecting'))
      );

      $courses =  DB::select("SELECT * FROM course_hsc_new WHERE `groups` = '" . $hsc_group . "'");

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

      DB::table('student_info_hsc')->where('refference_id', $auto_id)->update(
        array('hsc_subjects_info' => $all_string)
      );
    }

    return redirect()->route('student.hsc.admission.HscConfirmation')->with('success', "Admission Information Updated Successfully!");
  }
}

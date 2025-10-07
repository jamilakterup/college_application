<?php

namespace App\Http\Controllers\Admission\HSC;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Libs\Payment;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;

class HSC2ndAdmissionController extends Controller
{
  public function index()
  {
    Session::flush();
    return view('admission.hsc2nd.index');
  }

  public function check(Request $request)
  {
    $prc = '';
    $imp = '';
    $current_level = '';
    if ($request->ajax()) {
      $sid = trim($request->get('roll'));
      $current_level = trim($request->get('current_level'));
      $results = DB::select("SELECT * FROM hsc2nd_merit_list WHERE id=$sid and current_level='$current_level'");
      foreach ($results as $result) {
        $current_level = $level = $result->current_level;
        $session = $result->session;
        $name = $result->name;
        $session = $result->session;
        $college_id  = $result->id;
        $groups = $result->groups;
        $student_type = $result->student_type;
        $registration_type = $result->registration_type;
        $total_papers = $result->total_papers;
      }

      if (count($results) > 0) {

        $configs = DB::table('admission_config')->where('current_level', $current_level)->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->where('course', 'hsc')->get();
        if (count($configs) > 0) {
          // form fillup not open
          $config = $configs->first();
          $current_level = $config->current_level;
          $opening_date = $config->opening_date;
        } else {
          // form fillup not open
          return $status = 0;
        }

        Session::put('name', $name);
        Session::put('session', $session);
        Session::put('groups', $groups);
        Session::put('registration_id', $sid);
        Session::put('current_level', $current_level);
        Session::put('ex_year', $config->exam_year);
        Session::put('opening_date', $config->opening_date);
        Session::put('admission_step', 1);
        Session::put('subject', $groups);
        Session::put('student_type', $student_type);
        Session::put('registration_type', $registration_type);
        Session::put('total_papers', $total_papers);


        $level = $current_level;
        $givelavel = explode(' ', $level);
        $hons_level = $givelavel[0];
        if ($hons_level == 'HSC') {

          $form_fillup_result = DB::select("SELECT * FROM student_info_hsc_add WHERE id=$sid and course='HSC' and session='$session' and  level_study='$current_level'");
          $adm_result_count = DB::table('student_info_hsc_add')->where('id', $sid)
            ->where('session', $session)
            ->where('level_study', $current_level)
            ->where('payment', 'paid')
            ->where('exam_year', $config->exam_year)
            ->where('course', 'HSC')
            ->count();

          if (count($form_fillup_result) > 0) {
            // form fillup done by student
            return $status = 1;
          } else {
            // payment not completed
            return $status = 2;
          }
        }
      } else
        // student not found
        return $status = 5;
      echo json_encode($status);
    }
  }


  public function checktype(Request $request)
  {

    $payType = $request->get('payType');
    $regNumber = $request->get('student_id');
    Session::put('payType', $payType);
    Session::put('regNumber', $regNumber);
    return 'Ok';
  }



  public function view()
  {
    $admission_step = Session::get('admission_step');
    $registration_id = $student_id = Session::get('registration_id');
    $current_level = Session::get('current_level');
    $opening_date = Session::get('opening_date');
    $prc = Session::get('prc');
    $imp = Session::get('imp');
    $payment_amount = '';
    
    // TODO::::
    $invoice = DB::table('invoices')->where('roll', $registration_id)->where('level', $current_level)->where('date_start', $opening_date)->first();

    if ($invoice && $invoice->status == 'Paid') {
      DB::table('student_info_hsc')
        ->where('id', $student_id)
        ->update(['current_level' => 'HSC 2nd Year']);


      $alreadyAdded = DB::table('student_info_hsc_add')
        ->where('id', $student_id)
        ->where('session', $invoice->admission_session)
        ->exists();

      if (!$alreadyAdded) {
        if ($invoice) {
          DB::table('student_info_hsc_add')->insert([
            'id' => $student_id,
            'name' => $invoice->name,
            'session' => $invoice->admission_session,
            'level_study' => 'HSC 2nd Year',
            'groups' => $invoice->pro_group,
            'course' => 'HSC',
            'payment' => 'Paid',
            'total_amount' => $invoice->total_amount,
            'exam_year' => $invoice->passing_year,
            'transaction_id' => $invoice->txnid,
            'date' => now(),
            'slip_name' => $invoice->slip_name,
            'slip_type' => $invoice->slip_type,
          ]);
        }
      }
    }

    //TODO_END::

    if (Session::has('payment_status')) {
      $payment_status = Session::get('payment_status');
      //$payment_amount=4000;

    } else {
      $payment_status = 'Pending';
      //$payment_amount=4000;
    }

    if ($admission_step < 1) {
      return Redirect::route('hsc2nd.student.adm');
    } else {
      $student_infos = DB::table('hsc2nd_merit_list')->where('current_level', $current_level)->where('id', $registration_id)->get();
      return view('admission.hsc2nd.view', compact('registration_id', 'student_infos', 'payment_amount', 'payment_status', 'prc', 'imp', 'admission_step', 'student_id'));
    }
  }

  public function dbblPageView()
  {
    if (!Session::has('registration_id'))
      return Redirect::route('hsc2nd.student.adm');

    $student_id    = Session::get('regNumber');
    $payType    = Session::get('payType');
    $current_level = $current_lev   = Session::get('current_level');
    $subject = Session::get('subject');
    $session = Session::get('session');
    $opening_date = Session::get('opening_date');
    $examyear =   Session::get('ex_year');


    $code = '';
    $admission_name = 'hsc_2nd_admission_' . $current_level . '_' . $examyear;
    $results_payslip = DB::select("select * from payslipheaders where id = $payType");
    foreach ($results_payslip as $result_payslip) {
      $code = $result_payslip->code;
      $title = $result_payslip->title;
      $start_date = $result_payslip->start_date;
      $end_date = $result_payslip->end_date;
      $total_papers = $result_payslip->total_papers;
      $formfillup_type = $result_payslip->formfillup_type;
    }

    $already_paid = Invoice::where('roll', $student_id)->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('passing_year', $examyear)->where('admission_session', $session)->orderBy('id', 'desc')->where('status', 'Paid')->get();

    if (count($already_paid) > 0) {
      return redirect()->route('hsc2nd.student.adm.view');
    }

    $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");
    $total_amount = 0;
    foreach ($amounts as $amount) {
      $total_amount = $total_amount + $amount->fees;
    }

    $student = DB::table('hsc2nd_merit_list')->where('current_level', $current_level)->where('id', $student_id)->first();

    if ($student->total_amount != '' && $student->total_amount != 0) {
      $total_amount = $student->total_amount;
    }

    if ($total_papers != '0') {
      $pay_type = 'paper';
    } else {
      $pay_type = 'general';
    }

    if ($formfillup_type == 'others') {
      $pay_type = 'others';
    }

    if ($student->total_amount > 0)
      $total_amount = $student->total_amount;


    $already_exists = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_2nd_admission')->where('level', $current_level)->where('date_start', '>=', $opening_date)->where('admission_session', $session)->where('status', 'Pending')->orderBy('id', 'desc')->get();

    if (count($already_exists) < 1) {
      $invoice_id = DB::table('invoices')->insertGetId(
        array(
          'name' => $student->name,
          'hsc_merit_id' => 0,
          'type' => 'hsc_2nd_admission',
          'roll' => $student->id,
          'mobile' => '',
          'ssc_board' => '',
          'pro_group' => $student->groups,
          'subject' => $student->groups,
          'level' => $student->current_level,
          'passing_year' => $examyear,
          'admission_session' => $student->session,
          'student_type' => $student->student_type,
          'registration_type' => $student->registration_type,
          'pay_type' => $pay_type,
          'total_papers' => $total_papers,
          'slip_name' => $title,
          'slip_type' => $code,
          'total_amount' => $total_amount,
          'status' => 'Pending',
          'date_start' => $start_date,
          'date_end' => $end_date,
          'father_name' => 'N/A',
          'institute_code' => INS_CODE,
          'refference_id' => 0,
          'payment_info_id' => 0
        )
      );
    } else {

      $invoice = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_2nd_admission')->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('admission_session', $session)->where('status', 'Pending')->orderBy('id', 'desc')->first();
      $invoice_id = $invoice->id;

      DB::table('invoices')->where('id', $invoice->id)->update(
        array(
          'name' => $student->name,
          'hsc_merit_id' => 0,
          'type' => 'hsc_2nd_admission',
          'roll' => $student->id,
          'mobile' => '',
          'ssc_board' => '',
          'pro_group' => $student->groups,
          'subject' => $student->groups,
          'level' => $student->current_level,
          'passing_year' => $examyear,
          'admission_session' => $student->session,
          'student_type' => $student->student_type,
          'registration_type' => $student->registration_type,
          'pay_type' => $pay_type,
          'total_papers' => $total_papers,
          'slip_name' => $title,
          'slip_type' => $code,
          'total_amount' => $total_amount,
          'status' => 'Pending',
          'date_start' => $start_date,
          'date_end' => $end_date,
          'father_name' => 'N/A',
          'institute_code' => INS_CODE,
          'refference_id' => 0,
          'payment_info_id' => 0
        )
      );
    }

    Session::put('invoice_id', $invoice_id);


    $payment_amount = $total_amount;

    $institute_code = INS_CODE;


    Session::put('payment_amount', $total_amount);
    $student_infos = DB::table('hsc2nd_merit_list')->where('current_level', $current_level)->where('id', $student_id)->get();

    return view('admission.hsc2nd.dbbl_view', compact('payment_amount', 'student_infos', 'student_id', 'institute_code', 'current_level'));
  }

  public function payment_view(Request $request)
  {
    if (!Session::has('registration_id'))
      return Redirect::route('hsc2nd.student.adm');


    $student_id    = Session::get('registration_id');
    $group = Session::get('groups');
    $current_level = Session::get('current_level');
    $opening_date = Session::get('opening_date');
    $session = Session::get('session');
    $examyear =   Session::get('ex_year');
    $invoices    = DB::table('invoices')->where('type', 'hsc_2nd_admission')->where('level', $current_level)->where('passing_year', $examyear)->where('roll', $student_id)->where('date_start', '>=', $opening_date)->where('admission_session', $session)->orderBy('id', 'desc')->get();

    if (count($invoices) < 1) {
      return "<h2>No bill found for this student, please contact to college</h2>";
    }

    $student_infos = DB::table('hsc2nd_merit_list')->where('current_level', $current_level)->where('id', $student_id)->get();


    $invoice = $invoices->first();
    $payment_amount = $invoice->total_amount;
    if ($invoice->status == 'Paid') {
      return Redirect::route('hsc2nd.student.adm.view');
    }

    return view('admission.hsc2nd.dbbl_view', compact('payment_amount', 'student_id', 'student_infos'));
  }


  public function createConfirmSlip(Request $request)
  {
    if ($request->ajax()) {
      if (!Session::has('registration_id'))
        return Redirect::route('hsc2nd.student.adm');
      $name          = Session::get('name');
      $session       = Session::get('session');
      $registration_id    = Session::get('registration_id');
      $current_level =  Session::get('current_level');
      $degree_config = DB::table('admission_config')->where('current_level', $current_level)->where('open', 1)->where('course', 'hsc')->first();
      $ff_level = $degree_config->current_level;

      $student_info = DB::table('hsc2nd_merit_list')->where('current_level', $current_level)->where('id', $registration_id)->first();

      $current_level = $student_info->current_level;
      $groups       = Session::get('groups');
      $exam_year =  Session::get('ex_year');
      $adm_result = DB::table('student_info_hsc_add')->where('id', $registration_id)
        ->where('level_study', $ff_level)
        ->where('groups', $groups)
        ->where('payment', 'Paid')
        ->where('exam_year', $exam_year)
        ->where('course', 'hsc')
        ->get();
      if (count($adm_result) > 0) {
        $adm_student = $adm_result->first();
      } else {
        echo "Student Not Found, Please Contact to College";
        return;
      }


      $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'times']);
      $mpdf->ignore_invalid_utf8 = true;
      $mpdf->autoScriptToLang = true;
      $mpdf->autoVietnamese = true;
      $mpdf->autoArabic = true;
      $mpdf->autoLangToFont = true;

      $html = view('admission.hsc2nd.slip_id', compact('adm_student'));

      $mpdf->writeHTML($html);
      $filename = $adm_student->id . "_admission_slip.pdf";
      $file_path = public_path() . "/download/hsc/";
      $mpdf->Output($file_path . '/' . $filename);
      echo "<center><a href='" . url('/') . "/download/hsc/" . $filename . "' target='_blank'>Click to Download</a></center>";
    }
  }

  public function formfillupLogout()
  {
    Auth::logout();
    Session::flush();
    return Redirect::route('hsc2nd.student.adm');
  }

  public function nextStep()
  {

    if (!Session::has('registration_id'))
      return Redirect::route('hsc2nd.student.adm');

    $student_id    = Session::get('registration_id');
    $current_level    = Session::get('current_level');
    $subject = Session::get('subject');
    $session = Session::get('session');
    $examyear =   Session::get('ex_year');
    $groups = Session::get('groups');

    return view('admission.hsc2nd.paymenttype', compact('student_id', 'current_level', 'groups', 'subject', 'session', 'examyear'))->withStudent_id($student_id)->withCurrent_level($current_level)->withGroups($groups);
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
      Session::flash('success', 'টাকা সম্পূর্ণভাবে সফল হয়েছে, দয়া করে আপনার পেস্লিপ ডাউনলোড করুন!');
      return $this->payment_view($request);
    }

    return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\FormFillup;
use App\Models\HscGpa;
use App\Models\Invoice;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;
use Session;

class HSCFormfillupController extends Controller
{
  public function index() { 
    Session::flush();
    return view('hscformfillup.index');

  }
  
  public function check(Request $request){
    if ($request->ajax()) {
      $error = '';
      try {
        $validator = Validator::make($request->all(), [
          'current_level' => 'required',
          'registration_id' => 'required|numeric',
          'student_id' => get_config('hsc_ff_promotion_checking') ? 'required' : 'nullable'
        ]);

        if ($validator->fails()) {
          $errors = $validator->errors()->all();
          $errorSpans = collect($errors)->map(function ($error) {
            return '<span>' . $error . '</span><br>';
          })->implode('');

          $error .= $errorSpans;
        }
        if ($error != '') {
          return response()->json(['error' => $error], 422);
        }

        $current_level= trim($request->get('current_level'));
        $sid = trim($request->get('student_id'));
        $registration_id= trim($request->get('registration_id'));


        $configs = DB::table('form_fillup_config')->where('current_level',$current_level)->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->where('course', 'hsc')->get();
        if (count($configs) > 0) {
          $config = $configs->first();
          $current_level = $config->current_level;
          $opening_date = $config->opening_date;

          $result = DB::table('student_info_hsc_formfillup')
          ->select('*')
          ->where('session', $config->session)
          ->where('id', $registration_id)
          ->where('current_level', $current_level)
          ->where('status', 1)
          ->orderBy('auto_id', 'desc')
          ->first();

          if (is_null($result)) {
            $error = 'Registration ID is Wrong';
          }else{
            $current_level = $level = $result->current_level;
            $session = $result->session;
            $name = $result->name;
            $college_id = $result->id;
            $class_roll = $result->class_roll;
            $groups = $result->groups;
            
            Session::put('name',$name);
            Session::put('session',$session);
            Session::put('groups',$groups);
            Session::put('student_id',$class_roll); 
            Session::put('sid',$sid); 
            Session::put('registration_id',$registration_id); 
            Session::put('current_level',$current_level); 
            Session::put('exam_year',$config->exam_year);        
            Session::put('opening_date',$config->opening_date);        
            Session::put('admission_step',1);

            $ff_result_count=DB::table('form_fillup')->where('id',$registration_id )
            ->where('session',$session)
            ->where('level_study',$current_level)
            ->where('payment','paid')
            ->where('exam_year',$config->exam_year)
            ->where('course','HSC')
            ->count();

            //check promotion errors
            $getError = $this->getStudentEligibleStatus($result);
            $promoted = DB::table('hsc_formfillup_promotion_status')->where('class_roll', $sid)->first();

            if ($ff_result_count > 0) {
                // form fillup done by student
              $status = 1;
            }elseif((!is_null($promoted) && $promoted->promoted == 0) && $result->promotion_status !='promoted'){
              $error = 'You are not allowed because of '.$promoted->reason. ' Please Contact to College';
            }elseif(!is_null($promoted) && $promoted->promoted == 1){
              // redirect to payment
              $status = 2;
              DB::table('student_info_hsc_formfillup')->where('auto_id', $result->auto_id)->update(['class_roll'=>$sid,'promotion_status' => 'promoted']);
            }elseif($getError && get_config('hsc_ff_promotion_checking')){
              
              $checkresult = DB::table('student_info_hsc_formfillup')->where('auto_id', $result->auto_id)->first();

              if($checkresult->promotion_status !='promoted'){

                $checkresultExist = DB::table('student_info_hsc_formfillup')->where('auto_id', $checkresult->auto_id)->first();

                $error = 'Student not Eligible for '.$checkresultExist->promotion_status. ' Please Contact to College';
              }else{
                // redirect to payment
                $status = 2;
                DB::table('student_info_hsc_formfillup')->where('auto_id', $result->auto_id)->update(['class_roll'=>$sid, 'promotion_status' => 'promoted']);
              }

            }else{

                // redirect to payment
              $status = 2;
              DB::table('student_info_hsc_formfillup')->where('auto_id', $result->auto_id)->update(['class_roll'=>$sid, 'promotion_status' => 'promoted']);
            }

            if($error == ''){
              return response()->json(['success' => true, 'status' => $status]);
            }
          }

        }else{
          // form fillup not open
          $error = 'form fillup is not opened!';
        }

        if($error != '')
          return response()->json(['error' => $error], 406);
      } catch (Exception $e) {
        $error .= $e->getMessage();
        return response()->json(['error' => $error], 500);
      }
    } 
  }


  public function checktype(Request $request){  

    $payType= $request->get('payType');
    $regNumber = $request->get('student_id'); 
    Session::put('payType', $payType);
    Session::put('regNumber', $regNumber);
    return 'Ok';

  }



  public function view()
  {
    $admission_step = Session::get('admission_step');
    $registration_id = $sid = Session::get('registration_id');
    $student_id = Session::get('student_id');
    $current_level = Session::get('current_level');
    $session = Session::get('session');
    $exam_year = Session::get('exam_year');
    $opening_date = Session::get('opening_date'); 
    $payment_amount='';

    if($admission_step<1)
    {
      return Redirect::route('hsc.student.formfillup')->with('Something went wrong. Please try again!');
    }

    $ff_student = DB::table('form_fillup')->where('id',$sid )
    ->where('session',$session)
    ->where('level_study',$current_level)
    ->where('payment','paid')
    ->where('exam_year',$exam_year)
    ->where('course','HSC')
    ->get();

    $invoice = Invoice::where('roll', $registration_id)->where('passing_year', $exam_year)->where('type', 'hsc_form_fillup')->where('date_start', '>=', $opening_date)->where('level', $current_level)->orderBy('id', 'desc')->first();

    if(is_null($invoice)){
      return Redirect::route('hsc.student.formfillup')->with('Something went wrong. Please try again!');
    }

    return view('hscformfillup.view', compact('ff_student', 'invoice' ,'registration_id', 'payment_amount', 'admission_step', 'student_id'));

  }

  public function dbblPageView() 
  {
    if(!Session::has('registration_id'))
      return Redirect::route('hsc.student.formfillup')->with('Something went wrong. Please try again!');

    $student_id    = Session::get('regNumber');
    $sid    = Session::get('sid');
    $payType    = Session::get('payType');
    $current_level = $current_lev   = Session::get('current_level');

    $session = Session::get('session');
    $opening_date = Session::get('opening_date');
    $exam_year =   Session::get('exam_year');
    $registration_id =   Session::get('registration_id');
    $student_id =   Session::get('student_id');
    $name =   Session::get('name');
    $code = '';
    $admission_name = 'hsc_form_fillup_'.$current_level.'_'.$exam_year;
    $results_payslip = DB::select("select * from payslipheaders where id = $payType") ;
    foreach($results_payslip as $result_payslip){
      $code = $result_payslip->code;
      $title = $result_payslip->title;
      $start_date = $result_payslip->start_date;
      $end_date = $result_payslip->end_date;
      $total_papers = $result_payslip->total_papers;
      $formfillup_type = $result_payslip->formfillup_type;
    }

    $already_paid = Invoice::where('roll', $student_id)->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('passing_year', $exam_year)->orderBy('id', 'desc')->where('status','Paid')->get();

    if (count($already_paid) > 0) {
      DB::table('student_info_hsc')->where('id', $sid)->update(['registration_id', $registration_id]);
      return redirect()->route('hsc.student.formfillup.view');
    }

    $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");
    $total_amount = 0;
    foreach($amounts as $amount){
      $total_amount = $total_amount + $amount->fees;
    }

    $student = DB::table('student_info_hsc_formfillup')->where('current_level', $current_level)->where('id', $registration_id)->first();

    if($student->total_amount != '' && $student->total_amount != 0){
      $total_amount = $student->total_amount;
    }

    if($total_papers != '0'){
      $pay_type = 'paper';
    }else{
      $pay_type = 'general';
    }

    if($formfillup_type == 'others'){
      $pay_type = 'others';
    }

    $already_exists = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $exam_year)->where('type', 'hsc_form_fillup')->where('level', $current_level)->where('date_start', '>=', $opening_date)->where('status', 'Pending')->orderBy('id', 'desc')->get();

    if (count($already_exists) < 1) {
      $invoice_id = DB::table('invoices')->insertGetId(
        array(
          'name'=>$name, 
          'hsc_merit_id' => 0, 
          'type'=>'hsc_form_fillup',
          'roll' => $student->id,
          'student_id' => $sid,
          'mobile' => '',
          'ssc_board' => '',
          'pro_group' => $student->groups,
          'subject' => $student->groups,
          'level' => $student->current_level,
          'passing_year' => $exam_year,
          'admission_session'=>$student->session,
          'registration_type'=>$formfillup_type,
          'pay_type'=>$pay_type,
          'total_papers'=>$total_papers,
          'slip_name'=>$title,
          'slip_type'=>$code,
          'total_amount'=>$total_amount,
          'status'=>'Pending',
          'date_start'=>$start_date, 
          'date_end'=>$end_date, 
          'father_name'=>'N/A', 
          'institute_code'=>'mmc',
          'refference_id' => 0,
          'payment_info_id' => 0
        )
      );
    }else{
      $invoice = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $exam_year)->where('type', 'hsc_form_fillup')->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('status', 'Pending')->orderBy('id', 'desc')->first();
      $invoice_id = $invoice->id;

      DB::table('invoices')->where('id', $invoice->id)->update(
        array(
          'name'=>$name, 
          'hsc_merit_id' => 0, 
          'type'=>'hsc_form_fillup',
          'roll' => $student->id,
          'student_id' => $student_id,
          'mobile' => '',
          'ssc_board' => '',
          'pro_group' => $student->groups,
          'subject' => $student->groups,
          'level' => $student->current_level,
          'passing_year' => $exam_year,
          'admission_session'=>$student->session,
          'registration_type'=>$formfillup_type,
          'pay_type'=>$pay_type,
          'total_papers'=>$total_papers,
          'slip_name'=>$title,
          'slip_type'=>$code,
          'total_amount'=>$total_amount,
          'status'=>'Pending',
          'date_start'=>$start_date, 
          'date_end'=>$end_date, 
          'father_name'=>'N/A', 
          'institute_code'=>'mmc', 
          'refference_id' => 0,
          'payment_info_id' => 0
        )
      );
    }
    Session::put('invoice_id', $invoice_id);
    return redirect()->route('hsc.student.formfillup.view')->with('info', 'Please complete your payment.');
  }

  public function createConfirmSlip(Request $request){
    if($request->ajax())
    {
      if(!Session::has('registration_id'))
        return Redirect::route('hsc.student.formfillup')->with('Something went wrong. Please try again!');
      $session       = Session::get('session');
      $registration_id    = Session::get('registration_id');
      $current_level =  Session::get('current_level');
      $exam_year =  Session::get('exam_year');
      $ff_result=FormFillup::where('id',$registration_id )
      ->where('level_study',$current_level)
      ->where('session',$session)
      ->where('payment','Paid')
      ->where('exam_year',$exam_year)
      ->where('course', 'HSC')                                       
      ->get();
      if(count($ff_result) > 0){
        $ff_student = $ff_result->first();
      }else{
        echo "Student Not Found, Please Contact to College";
        return;
      }

      $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
      $mpdf->ignore_invalid_utf8 = true;
      $mpdf->autoScriptToLang = true;
      $mpdf->autoVietnamese = true;
      $mpdf->autoArabic = true;
      $mpdf->autoLangToFont = true;

      $html = view('hscformfillup.slip_id', compact('ff_student'));

      $mpdf->writeHTML($html);
      $filename = $ff_student->id."_formfillup_slip.pdf";
      $file_path=public_path()."/download/hsc/";
      $mpdf->Output($file_path.'/'.$filename);
      echo "<center><a href='".url('/')."/download/hsc/".$filename."' target='_blank'>Click to Download</a></center>";
    }

  }

  public function formfillupLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('hsc.student.formfillup')->with('Something went wrong. Please try again!');
  }

  public function nextStep(){

    if(!Session::has('registration_id'))
      return Redirect::route('hsc.student.formfillup');

    $student_id    = Session::get('registration_id');
    $current_level    = Session::get('current_level');
    $session = Session::get('session');
    $exam_year =   Session::get('exam_year');   
    $groups=Session::get('groups');

    return view('hscformfillup.paymenttype', compact('student_id', 'current_level', 'groups','session', 'exam_year'))->withStudent_id($student_id)->withCurrent_level($current_level)->withgroups($groups);
  }

  private function getStudentEligibleStatus($result){
    $class_roll = session()->get('sid');

    $gp = HscGpa::where('student_id', $class_roll)->orderBy('id', 'desc')->first();
    if($result->promotion_status == 'promoted'){
      return false;
    }elseif(!is_null($gp) && $gp->grade == 'F'){
      DB::table('student_info_hsc_formfillup')->where('auto_id', $result->auto_id)->update(['promotion_status' => 'hsc_fail']);
      return true;
    }else{
      return false;
    }
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\FormFillup;
use App\Models\Invoice;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;

class Masters1stFormfillupController extends Controller
{
    public function index() { 

    Session::flush();
    return view('masters1stformfillup.index');

  }
  
  public function check(Request $request){ 
      
    $prc='';
    $imp='';
    $current_level = '';
    if($request->ajax())
    {
      $sid= trim($request->get('roll'));
      $current_level = 'Masters 1st Year';
      $results = DB::select("SELECT * FROM student_info_masters_formfillup WHERE id=$sid AND current_level='$current_level'");

      foreach($results as $result){
        $current_level=$level= $result->current_level;
        $session=$result->session;
        $name = $result->name;
        $subject = $result->dept_name;
        $session = $result->session;
        $college_id  = $result->id;
        $dept_name = $result->dept_name;
        $faculty_name= $result->faculty_name;
        $student_type= $result->student_type;
        $registration_type= $result->registration_type;
        $total_papers= $result->total_papers;
        $selectable= $result->selectable;
      }

      if(count($results) > 0){

        $configs = DB::table('form_fillup_config')->where('current_level',$current_level)->where('course', 'masters')->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->get();
        if (count($configs) > 0) {
            // form fillup not open
          $config = $configs->first();
          $current_level = $config->current_level;

        }else{
          // form fillup not open
          return $status = 0;
        }

       Session::put('name',$name);
       Session::put('session',$session);
       Session::put('faculty_name',$faculty_name);
       Session::put('registration_id',$sid); 
       Session::put('current_level',$current_level); 
       Session::put('ex_year',$config->exam_year);        
       Session::put('admission_step',1);
       Session::put('subject', $dept_name);
       Session::put('student_type', $student_type);
       Session::put('registration_type', $registration_type);
       Session::put('total_papers', $total_papers);
       Session::put('selectable', $selectable);


       $level= $current_level;
       $givelavel = explode(' ', $level);
       $degree_level=$givelavel[0];
       if($degree_level=='Masters'){

        $form_fillup_result = DB::select( "SELECT * FROM form_fillup WHERE id=$sid and course='Masters' and session='$session' and  level_study='$current_level'");
        $ff_result_count=DB::table('form_fillup')->where('id',$sid )
        ->where('session',$session)
        ->where('level_study',$current_level)
        ->where('payment','paid')
        ->where('exam_year',$config->exam_year)
        ->where('course','Masters')
        ->count();

        if ($ff_result_count > 0) {
          // form fillup done by student
          return $status = 1;

        }else{

          if ($selectable == 0) {

            // regular student cannot be entered
            return $status = 3;
          }else{
              // payment not completed
              return $status = 2;

          }

          if ($config->exam_year =='') {
            return $status = 4;
          }
        }

      }
    }
      else
        // student not found
        return $status=5;
      echo json_encode($status);
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
      $registration_id = $student_id = Session::get('registration_id');
      $current_level = Session::get('current_level'); 
      $prc = Session::get('prc');
      $imp = Session::get('imp');
      $payment_amount='';

        if(Session::has('payment_status'))
        {
            $payment_status=Session::get('payment_status');
            //$payment_amount=4000;

        }
        else
        {
            $payment_status='Pending';
            //$payment_amount=4000;
        }

        if($admission_step<1)
        {
          return Redirect::route('masters1st.student.formfillup');
        }
        else
        {
            $student_infos=DB::table('student_info_masters_formfillup')->where('id',$registration_id )->get();
           return view('masters1stformfillup.view', compact('registration_id', 'student_infos', 'payment_amount', 'payment_status', 'prc', 'imp', 'admission_step', 'student_id'));
        }

      
    }

    public function dbblPageView() 
    {

        if(!Session::has('registration_id'))
        return Redirect::route('masters1st.student.formfillup');

        $student_id    = Session::get('regNumber');
        $payType    = Session::get('payType');
        $current_level = $current_lev   = Session::get('current_level');
        $subject = Session::get('subject');
        $session = Session::get('session');
      $examyear =   Session::get('ex_year');
      $student_type =   Session::get('student_type');
      $registration_type =   Session::get('registration_type');

      $configs = DB::table('form_fillup_config')->where('current_level',$current_level)->where('course', 'masters')->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->get();
      $config = $configs->first();
    
        
        $code = '';
        $admission_name = 'masters_form_fillup_'.$current_level.'_'.$examyear.'_irregular';
        $results_payslip = DB::select("select * from payslipheaders where id = $payType") ;
        foreach($results_payslip as $result_payslip){
          $code = $result_payslip->code;
          $title = $result_payslip->title;
          $start_date = $result_payslip->start_date;
          $end_date = $result_payslip->end_date;
          $total_papers = $result_payslip->total_papers;
        }

        $already_paid = Invoice::where('roll', $student_id)->where('date_start', '>=', $config->opening_date)->where('level', $current_level)->where('admission_session', $config->session)->where('passing_year', $examyear)->where('status','Paid')->orderBy('id', 'desc')->get();

        if (count($already_paid) > 0) {
          return redirect()->route('masters1st.student.formfillup.view');
        }

        $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");
        $total_amount = 0;
        foreach($amounts as $amount){
          $total_amount = $total_amount + $amount->fees;
        }   
    
        $results_count = DB::select('select * from payment_info where roll ="'.$student_id.'" AND slip_type="'.$code.'" ');

        $student = DB::table('student_info_masters_formfillup')->where('current_level', $current_level)->where('id', $student_id)->first();

        $already_exists = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'masters_form_fillup')->where('level', $current_level)->where('date_start', '>=', $config->opening_date)->where('status', 'Pending')->where('admission_session', $config->session)->orderBy('id', 'desc')->get();

          if (count($already_exists) < 1) {
              $payment_info_id = DB::table('payment_info')->insertGetId(
               array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>'rc', 'exam_year' => $examyear)
                );
                
          DB::table('invoices')->insert(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'masters_form_fillup' ,
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->faculty_name,
                  'subject' => $student->dept_name,
                  'level' => $student->current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$student->session,
                  'student_type'=>$student->student_type,
                  'registration_type'=>$student->registration_type,
                  'pay_type'=>$student->pay_type,
                  'total_papers'=>$total_papers,
                  'slip_name'=>$title,
                  'slip_type'=>$code,
                  'total_amount'=>$total_amount,
                  'status'=>'Pending',
                  'date_start'=>$start_date, 
                  'date_end'=>$end_date, 
                  'father_name'=>'N/A', 
                  'institute_code'=>'rc', 
                  'refference_id' => 0,
                  'payment_info_id' => $payment_info_id
                  )
            );
          }else{

            $invoice = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'masters_form_fillup')->where('date_start', '>=', $config->opening_date)->where('level', $current_level)->where('status', 'Pending')->orderBy('id', 'desc')->first();

            $payment_info_id = $invoice->payment_info_id;

            DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
               array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>'rc', 'exam_year' => $examyear)
                );
                
          DB::table('invoices')->where('id', $invoice->id)->update(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'masters_form_fillup',
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->faculty_name,
                  'subject' => $student->dept_name,
                  'level' => $student->current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$student->session,
                  'student_type'=>$student->student_type,
                  'registration_type'=>$student->registration_type,
                  'pay_type'=>$student->pay_type,
                  'total_papers'=>$total_papers,
                  'slip_name'=>$title,
                  'slip_type'=>$code,
                  'total_amount'=>$total_amount,
                  'status'=>'Pending',
                  'date_start'=>$start_date, 
                  'date_end'=>$end_date, 
                  'father_name'=>'N/A', 
                  'institute_code'=>'rc', 
                  'refference_id' => 0,
                  'payment_info_id' => $payment_info_id
                  )
            );
          }


    $payment_amount = $total_amount;
$results = DB::select("select * from payment_info where id='$payment_info_id'");
    foreach($results as $payInfo){ 
            $slip_type=$payInfo->slip_type; 
      $institute_code=$payInfo->institute_code;
      $slip_name = $payInfo->slip_name;
      $payment_info_id = $payInfo->id;
    }

        
    Session::put('payment_amount', $payment_amount);
    Session::put('payment_info_id', $payment_info_id);  
    $student_infos = DB::table('student_info_masters_formfillup')->where('current_level', $current_level)->where('id', $student_id)->get();
        
        return view('masters1stformfillup.dbbl_view', compact('payment_amount','student_infos', 'student_id', 'slip_type', 'institute_code', 'slip_name', 'payment_info_id', 'current_level'));


    }

  public function payment_view(Request $request){
    if(!Session::has('registration_id'))
    return Redirect::route('masters1st.student.formfillup');


    $student_id    = Session::get('registration_id');
    $group = Session::get('groups');
    $current_level = Session::get('current_level');
    $session = Session::get('admission_session');
    $examyear =   Session::get('ex_year'); 

    $configs = DB::table('form_fillup_config')->where('current_level',$current_level)->where('course', 'masters')->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->get();
      $config = $configs->first();

    $invoices    = DB::table('invoices')->where('type', 'masters_form_fillup')->where('level', $current_level)->where('passing_year', $examyear)->where('roll', $student_id)->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->get();

    if (count($invoices) < 1) {
      return "<h2>No bill found for this student, please contact to college</h2>";
    }

    $student_infos = DB::table('student_info_masters_formfillup')->where('id', $student_id)->get();


    $invoice = $invoices->first();
    $payment_amount = $invoice->total_amount;
    if ($invoice->status == 'Paid') {
      return Redirect::route('masters1st.student.formfillup.view');
    }
    
    return view('masters1stformfillup.dbbl_view', compact('payment_amount', 'student_id', 'student_infos'));
  }


    public function createConfirmSlip(Request $request){
      if($request->ajax())
         {
            if(!Session::has('registration_id'))
                return Redirect::route('masters1st.student.formfillup'); 
            
            $name          = Session::get('name'); 
            $session       = Session::get('session');
            $registration_id    = Session::get('registration_id');
            $current_level =  Session::get('current_level');
            $degree_config = DB::table('form_fillup_config')->where('current_level',$current_level)->where('course', 'masters')->where('open', 1)->first();
            $ff_level = $degree_config->current_level;

            $student_info = DB::table('student_info_masters_formfillup')->where('id', $registration_id)->first();

            $current_level = $student_info->current_level;
            $faculty_name       = Session::get('faculty_name');
            $exam_year =  Session::get('ex_year');
            $ff_result=FormFillup::where('id',$registration_id )
                                        ->where('level_study',$ff_level)
                                        ->where('groups',$faculty_name)
                                        ->where('payment','Paid')
                                        ->where('exam_year',$exam_year)
                                        ->where('course', 'Masters')                                       
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

            $html = view('masters1stformfillup.slip_id', compact('ff_student'));

            $mpdf->writeHTML($html);
            $filename = $ff_student->id."_formfillup_slip.pdf";
            $file_path=public_path()."/download/masters/";
            $mpdf->Output($file_path.'/'.$filename);
            echo "<center><a href='".url('/')."/download/masters/".$filename."' target='_blank'>Click to Download</a></center>";
         }

  }

  public function formfillupLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('masters1st.student.formfillup');
  }

  public function nextStep(){

    if(!Session::has('registration_id'))
      return Redirect::route('masters1st.student.formfillup');

    $student_id    = Session::get('registration_id');
    $current_level    = Session::get('current_level');
    $subject = Session::get('subject');
    $session = Session::get('session');
    $examyear =   Session::get('ex_year');   
    $faculty_name=Session::get('faculty_name');
    $total_papers=Session::get('total_papers');

    return view('masters1stformfillup.paymenttype', compact('student_id', 'current_level', 'faculty_name', 'subject', 'session', 'examyear','total_papers'))->withStudent_id($student_id)->withCurrent_level($current_level)->withFaculty_name($faculty_name);
  }
}

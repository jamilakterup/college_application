<?php

namespace App\Http\Controllers;

use App\Models\FormFillup;
use App\Models\Invoice;
use Auth;
use DB;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;

class HonoursFormfillupController extends Controller
{
    public function index() { 

        Session::flush();
    return view('honoursformfillup.index');

  }
  
  public function check(Request $request){ 

      
    $prc='';
    $imp='';
    $current_level = '';
    if($request->ajax())
    {
      $sid= trim($request->get('roll')); 
      $ex_year = trim($request->get('examyear'));
      $current_level = trim($request->get('current_level'));
      $results = DB::select("SELECT * FROM student_info_hons_formfillup WHERE id=$sid and current_level ='$current_level'");
      foreach($results as $result){
        $std_current_level=$level= $result->current_level;
        $session=$result->session;
        $name = $result->name;
        $subject = $result->dept_name;
        $session = $result->session;
        $college_id  = $result->id;
        $dept_name = $result->dept_name;
        $faculty_name= $result->faculty_name;
      }

      if(count($results) > 0){

        $configs = DB::table('hons_form_fillup_config')->where('current_level',$current_level)->where('open', 1)->where('clossing_date', '>=', date('Y-m-d'))->get();
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
       Session::put('clossing_date', $config->clossing_date);


       $level= $current_level;
       $givelavel = explode(' ', $level);
       $hons_level=$givelavel[0];
       if($hons_level=='Honours'){

        $form_fillup_result = DB::select( "SELECT * FROM form_fillup WHERE id=$sid and course='Honours' and session='$session' and  level_study='$current_level'");
        $ff_result_count=DB::table('form_fillup')->where('id',$sid )
        ->where('session',$session)
        ->where('level_study',$current_level)
        ->where('payment','paid')
        ->where('exam_year',$config->exam_year)
        ->where('course','Honours')
        ->count();

        if ($ff_result_count > 0) {
          // form fillup done by student
          return $status = 1;

        }else{

          $already_regular = Invoice::where('roll', $sid)->where('slip_type', 'LIKE','%_regular%')->where('slip_type', 'NOT LIKE','%_irregular%')->where('level', $current_level)->where('type','honours_form_fillup')->where('date_start', '<=', date('Y-m-d'))->where('date_end', '<=', $config->clossing_date)->where('status', 'Pending')->get();

          if (count($already_regular) > 0) {

            // current level not matched
            if ($std_current_level != $current_level) {
              return $status = 2;
            }

            // regular student cannot be entered
            return $status = 3;
          }else{
              // payment not completed
              return $status = 2;

          }

          $already_paid_irregular = Invoice::where('roll', $sid)->where('slip_type', 'LIKE','%_irregular%')->where('type','honours_form_fillup')->where('level', $current_level)->where('date_start', '<=', date('Y-m-d'))->where('date_end', '<=', $config->clossing_date)->where('status', 'Paid')->get();

          if (count($already_paid_irregular) > 0) {
            return $status = 3;
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
          return Redirect::route('honours.student.formfillup');
        }
        else
        {
            $student_infos=DB::table('student_info_hons_formfillup')->where('id',$registration_id )->get();
           return view('honoursformfillup.view', compact('registration_id', 'student_infos', 'payment_amount', 'payment_status', 'prc', 'imp', 'admission_step', 'student_id'));
        }

      
    }

    public function dbblPageView() 
    {

        if(!Session::has('registration_id'))
        return Redirect::route('honours.student.formfillup');

        $student_id    = Session::get('regNumber');
        $payType    = Session::get('payType');
        $current_level = $current_lev   = Session::get('current_level');
        $subject = Session::get('subject');
        $session = Session::get('session');
      $examyear =   Session::get('ex_year');
      $clossing_date = Session::get('clossing_date');
    
        
        $code = '';
        $admission_name = 'honours_form_fillup_'.$current_level.'_'.$examyear.'_irregular';
        $results_payslip = DB::select("select * from payslipheaders where id = $payType") ;
        foreach($results_payslip as $result_payslip){
          $code = $result_payslip->code;
          $title = $result_payslip->title;
          $start_date = $result_payslip->start_date;
          $end_date = $result_payslip->end_date;
        }

        $already_paid = Invoice::where('roll', $student_id)->where('slip_type', $code)->where('level', $current_level)->where('date_start', '<=', date('Y-m-d'))->where('date_end', '<=', $clossing_date)->where('status','Paid')->get();

        if (count($already_paid) > 0) {
          return redirect()->route('honours.student.formfillup.view');
        }

        $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");
        $total_amount = 0;
        foreach($amounts as $amount){
          $total_amount = $total_amount + $amount->fees;
        }   
    
        $results_count = DB::select('select * from payment_info where roll ="'.$student_id.'" AND slip_type="'.$code.'" ');

        $student = DB::table('student_info_hons_formfillup')->where('current_level', $current_level)->where('id', $student_id)->first();

        $already_exists = DB::table('invoices')->where('roll', $student->id)->where('level', $current_level)->where('passing_year', $examyear)->where('type', 'honours_form_fillup')->where('slip_type', $code)->where('date_start', '<=', date('Y-m-d'))->where('date_end', '<=', $clossing_date)->where('status', 'Pending')->get();

          if (count($already_exists) < 1) {
              $payment_info_id = DB::table('payment_info')->insertGetId(
               array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$clossing_date, 'father_name'=>'', 'institute_code'=>'rc', 'exam_year' => $examyear)
                );
                
          DB::table('invoices')->insert(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'honours_form_fillup' ,
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->faculty_name,
                  'subject' => $student->dept_name,
                  'level' => $current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$student->session,
                  'slip_name'=>$title,
                  'slip_type'=>$code,
                  'total_amount'=>$total_amount,
                  'status'=>'Pending',
                  'date_start'=>$start_date, 
                  'date_end'=>$clossing_date, 
                  'father_name'=>'N/A', 
                  'institute_code'=>'rc', 
                  'refference_id' => 0,
                  'payment_info_id' => $payment_info_id
                  )
            );
          }else{

            $invoice = DB::table('invoices')->where('roll', $student->id)->where('level', $current_level)->where('passing_year', $examyear)->where('type', 'honours_form_fillup')->where('slip_type', $code)->where('date_start', '<=', date('Y-m-d'))->where('date_end', '>=', date('Y-m-d'))->where('status', 'Pending')->first();

            $payment_info_id = $invoice->payment_info_id;

            DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
               array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$clossing_date, 'father_name'=>'', 'institute_code'=>'rc', 'exam_year' => $examyear)
                );
                
          DB::table('invoices')->where('id', $invoice->id)->update(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'honours_form_fillup',
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->faculty_name,
                  'subject' => $student->dept_name,
                  'level' => $current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$student->session,
                  'slip_name'=>$title,
                  'slip_type'=>$code,
                  'total_amount'=>$total_amount,
                  'status'=>'Pending',
                  'date_start'=>$start_date, 
                  'date_end'=>$clossing_date, 
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
    $student_infos = DB::table('student_info_hons_formfillup')->where('id', $student_id)->get();
        
        return view('honoursformfillup.dbbl_view', compact('payment_amount','student_infos', 'student_id', 'slip_type', 'institute_code', 'slip_name', 'payment_info_id', 'current_level'));


    }

  public function payment_view(Request $request){
    if(!Session::has('registration_id'))
    return Redirect::route('honours.student.formfillup');


    $student_id    = Session::get('registration_id');
    $group = Session::get('groups');
    $current_level = Session::get('current_level');
    $session = Session::get('admission_session');
    $examyear =   Session::get('ex_year'); 
    $clossing_date = Session::get('clossing_date');
    $invoice    = DB::table('invoices')->where('type', 'honours_form_fillup')->where('passing_year', $examyear)->where('roll', $student_id)->where('level', $current_level)->where('date_start', '<=', date('Y-m-d'))->where('date_end', '<=', $clossing_date)->get();

    if (count($invoice) < 1) {
      return "<h2>No bill found for this student, please contact to college</h2>";
    }

    $student_infos = DB::table('student_info_hons_formfillup')->where('id', $student_id)->get();


    $invoice = $invoice->first();
    $payment_amount = $invoice->total_amount;
    if ($invoice->status == 'Paid') {
      return Redirect::route('honours.student.formfillup.view');
    }
    
    return view('honoursformfillup.dbbl_view', compact('payment_amount', 'student_id', 'student_infos'));
  }


    public function createConfirmSlip(Request $request){
      if($request->ajax())
         {
            if(!Session::has('registration_id'))
                return Redirect::route('honours.student.formfillup'); 
            
            $name          = Session::get('name'); 
            $session       = Session::get('session');
            $registration_id    = Session::get('registration_id');
            $subject       = Session::get('subject');
            $current_level =  Session::get('current_level');
            $hons_config = DB::table('hons_form_fillup_config')->where('current_level',$current_level)->where('open', 1)->first();
            $ff_level = $hons_config->current_level;

            $student_info = DB::table('student_info_hons_formfillup')->where('id', $registration_id)->first();

            $current_level = $student_info->current_level;
            $subject       = Session::get('subject');
            $exam_year =  Session::get('ex_year');
            $ff_result=FormFillup::where('id',$registration_id )
                                        ->where('level_study',$ff_level)
                                        ->where('dept_name',$subject)
                                        ->where('payment','Paid')
                                        ->where('exam_year',$exam_year)
                                        ->where('course', 'Honours')                                       
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

            $html = view('honoursformfillup.slip_id', compact('ff_student'));

            $mpdf->writeHTML($html);
            $filename = $ff_student->id."_formfillup_slip.pdf";
            $file_path=public_path()."/download/honours/";
            $mpdf->Output($file_path.'/'.$filename);
            echo "<center><a href='".url('/')."/download/honours/".$filename."' target='_blank'>Click to Download</a></center>";
         }

  }

  public function formfillupLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('honours.student.formfillup');
  }

  public function nextStep(){

    if(!Session::has('registration_id'))
      return Redirect::route('honours.student.formfillup');

    $student_id    = Session::get('registration_id');
    $current_level    = Session::get('current_level');
    $subject = Session::get('subject');
    $session = Session::get('session');
    $examyear =   Session::get('ex_year');   
    $faculty_name=Session::get('faculty_name');

    return view('honoursformfillup.paymenttype', compact('student_id', 'current_level', 'faculty_name', 'subject', 'session', 'examyear'))->withStudent_id($student_id)->withCurrent_level($current_level)->withFaculty_name($faculty_name);
  }
}

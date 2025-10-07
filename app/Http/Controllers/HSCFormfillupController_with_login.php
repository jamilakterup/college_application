<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FormFillup;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Image;
use Mpdf\Mpdf;
use Session;
use IdRollGenerate;

class HSCFormfillupController extends Controller
{
  public function index() {
    Auth::logout();
    Session::flush();
    return view('hscformfillup.index');
    
  }
  
  public function ffForm() {
    $name    = Session::get('name');
    $registration_id   = Session::get('registration_id');
    $groups       = Session::get('groups');
    $step = Session::get('step');
    
    if($step!=1  )
    return Redirect::route('hsc.student.formfillup');
    
    $blood_lists = ['A+' => 'A+','A-'=>'A-','B+' => 'B+','B-'=>'B-','O+' => 'O+','O-'=>'O-','AB+' => 'AB+','AB-'=>'AB-'];
    
    $dist=DB::table('district_thana')->distinct()->get(['district']);
    $step = Session::put('step_2', 2);
    return view('hscformfillup.form', compact('blood_lists'))
    ->withName($name);
    
  }
  public function districtChange(Request $request) {
    
    if($request->ajax())
    { 
      $dist=$request->get('dist'); 
      $result = DB::table('district_thana')
      ->select('thana')
      ->Where('district', $dist)
      ->get();
      
      foreach ($result as  $value)
      {                          
        
        echo  "<option value='{$value->thana}'>{$value->thana}</option>";               
      }  
      //echo json_encode($ssc_roll);    
    }
  }
  
  public function check(Request $request) {
    if($request->ajax())
    {
      
      $registration_id=$request->get('registration_id'); 
      $current_level=$request->get('current_level');
      Session::put('registration_id',$registration_id);
      Session::put('current_level',$current_level); 

      $configs = DB::table('form_fillup_config')->where('course', 'hsc')->where('current_level', $current_level)->where('open', 1)->get();
      if(count($configs) < 1){
        // admission is not open
        return $status = 0;
      }else{
        $config = $configs->first();
        if($config->clossing_date < date('Y-m-d')){
          // admission date is expired
          return $status = 4;
        }
      }
      
      $session=$config->session;
      $exam_year=$config->exam_year;
      Session::put('session',$session);
      Session::put('exam_year',$exam_year);
      $opening_date=$config->opening_date;
      
      $student_ff_infos = DB::table('student_info_hsc_formfillup')->where('id',$registration_id)
      ->where('current_level',$current_level)->get();

      if(count($student_ff_infos) > 0){

        $student_ff_info = $student_ff_infos->first();
        Session::put('name',$student_ff_info->name);
        Session::put('opening_date',$config->opening_date);        
        Session::put('step',1);
        Session::put('groups', $student_ff_info->groups);
        Session::put('student_type', $student_ff_info->student_type);
        Session::put('registration_type', $student_ff_info->registration_type);
        Session::put('total_papers', $student_ff_info->total_papers);

        $ff_result_count= DB::table('form_fillup')->where('id',$registration_id )
          ->where('session',$session)
          ->where('level_study',$current_level)
          ->where('groups',$student_ff_info->groups)
          ->where('payment','paid')
          ->where('exam_year',$config->exam_year)
          ->where('course','HSC')
          ->count();

          if($ff_result_count > 0){
            // done and redirect to view
            return $status = 1;
          }else{
            // open form
            return $status = 2;
          }

        }else{
          // student not found
          return $status = 5;
        }
      }else{
        // bad request
        return $status = 6;
      }
    
  }
  
  public function hscGroupChange() {
    
    $group = $_POST['group'];
    $course = $_POST['course'];
    
    return view('admission.hsc.hsc_group_change')
    ->withGroup($group)
    ->withCourse($course);
    
    
    
  }
  
  public function retrievepass(){
    
    $class_roll = $_POST['class_roll'];
    $registration_id = $_POST['registration_id'];
    
    $results = DB::table('hsc_formfillup_students')->where('class_roll',$class_roll )->where('id',$registration_id)->get();
    
    $auto_id = '';
    $password = '';
    foreach($results as $result){
      $password = $result->password;
      $auto_id = $result->auto_id;
    }
    
    $auto_id =str_pad($auto_id,'4','0',STR_PAD_LEFT);
    $auto_id = HSC_PREF.$auto_id;
    echo json_encode(array($password, $auto_id));
  }
  
  
  public function hscInformationSubmit(Request $request){
    $this->validate($request, [
      'password' => 'required',
      'student_name' => 'required',
      'class_roll' => 'required|numeric',
      'father_name' => 'required',
      'mother_name' => 'required',
      'student_mobile' => 'required|numeric',
      'permanent_village' => 'required',
      'permanent_post_office' => 'required',
      'permanent_district' => 'required',
      'permanent_thana' => 'required',
      'photo' => 'required|mimes:jpeg,jpg,png',
    ]);
    $temp_entry_time = date('Y-m-d G:i:s');
    $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
    $registration_id = $request->registration_id;
    $class_roll = $request->class_roll;
    $current_level = Session::get('current_level');
    $session = $request->session;
    $admission_session = $request->session;
    $step_2 = Session::get('step_2');
    $logo = $request->file('photo');

    if($step_2!=2  )
    return Redirect::route('hsc.student.formfillup');
    
    $filename = $registration_id.'_'.str_replace("-","_",$session).'.jpg';
    $upload_path = public_path('upload/college/hsc/formfillup/' . $filename);
    Image::make($logo->getRealPath())->save($upload_path);
    
    $compulsorycourse = $request->get('compulsorycourse');
    
    $compulsorycourse =  implode (",", $compulsorycourse);
    
    $selectivecourse = $request->get('selectivecourse');
    $selectivecourse =  implode (",", $selectivecourse);
    $optional_course = $request->get('optional_course');
    $optional_course =  implode (",", $optional_course);
    
    if ($registration_id == '') {
      return Redirect::back()->withInput()->with('error','Something Went Wrong. Please Try Again.');
    }
    
    $name    = Session::get('name');
    $groups       = Session::get('groups');
    $step = Session::get('step');
    $opening_date = Session::get('opening_date');
    
    $submitted_data = array(
      'entry_time'=>$entry_time,
      'photo'=>$filename,
      'entry_time'=>$entry_time,
      'name'=>$request->get('student_name'),
      'id'=>$registration_id,
      'class_roll'=>$class_roll,
      'current_level'=>$current_level,
      'compulsory'=>$compulsorycourse,
      'selective'=>$selectivecourse,
      'optional'=>$optional_course,
      'groups'=>$request->get('groups'),
      'fathers_name'=>$request->get('father_name'),
      'mothers_name'=>$request->get('mother_name'),
      'password'=>$request->get('password'),
      'mobile'=>$request->get('student_mobile'),
      'permanent_village'=>$request->get('permanent_village'),
      'permanent_post_office'=>$request->get('permanent_post_office'),
      'permanent_thana'=>$request->get('permanent_thana'),
      'permanent_district'=>$request->get('permanent_district'),
      'admission_session'=>$request->get('session'),
    );
    
    $formfillup_student = DB::table('hsc_formfillup_students')->where('admission_session', $admission_session)->where('id', $registration_id)->where('current_level', $current_level)->get();
    
    if(count($formfillup_student) > 0){
      DB::table('hsc_formfillup_students')->where('auto_id', $formfillup_student->first()->auto_id)
      ->update($submitted_data);
      $admitted_id = $formfillup_student->first()->auto_id;
    }else{
      $admitted_id = DB::table('hsc_formfillup_students')
      ->insertGetId($submitted_data);
    }

    $student_ff_infos = DB::table('student_info_hsc_formfillup')->where('id',$registration_id)
      ->where('current_level',$current_level)->update(['name'=> $request->get('student_name')]);
    
    $formfillup_student = DB::table('hsc_formfillup_students')->where('auto_id', $admitted_id)->where('admission_session', $admission_session)->get();
    
    foreach($formfillup_student as $result){
      $auto_id= auto_id_hsc($result->auto_id);
      $tracking_id = HSC_PREF.$auto_id;
      $password =$result->password;
      $refId=$result->auto_id;
    }
    
    Session::put('tracking_id', $tracking_id);
    Session::put('password',$password);
    
    return Redirect::route('hsc.student.formfillup.form');
  }
  
  public function hscSignin(){
    
    return view('hscformfillup.sign_in_form');
    
  }
  
  public function hscStudentSignin(){
    $status=0;   
    $tracking_id = trim($_POST['tracking_id']);
    $password = trim($_POST['password']);
    $number = 0;

    if ($auto_id = hsc_tracking_auto_id($tracking_id)) {
      $student = DB::table('hsc_formfillup_students')->where('auto_id',$auto_id)->where('password',$password)->first();
    }else{
      return $status=2;
    }

    if(!is_null($student)){

      $config = DB::table('form_fillup_config')->where('course', 'hsc')->where('current_level', $student->current_level)->where('open', 1)->get();
      if(count($config) < 1){
        // admission is not open
        return $status = 4;
      }else{
        $conf = $config->first();
        if($conf->clossing_date < date('Y-m-d')){
          return $status = 6;
        }
      }
      
      $opening_date = $conf->opening_date;

      Session::put('registration_id',$student->id);
      Session::put('tracking_id',$tracking_id);
      Session::put('auto_id',$auto_id);
      Session::put('session',$conf->session);
      Session::put('current_level',$conf->current_level);
      Session::put('opening_date',$opening_date);
      Session::put('groups',$student->groups);
      Session::put('exam_year',$conf->exam_year);
      Session::put('hsc_con', 1);


      $ff_student = DB::table('form_fillup')->where('id',$student->id )
        ->where('session',$conf->session)
        ->where('level_study',$conf->current_level)
        ->where('payment','paid')
        ->where('groups',$student->groups)
        ->where('exam_year',$conf->exam_year)
        ->where('course','HSC')
        ->get();

        if(count($ff_student) > 0){
          // ff student found
          return $status = 1;
        }else{
          // paytype
          return $status = 7;
        }

    }else{
      // student not found
      return $status=2;
    }
    
  }
  
  public function SubjectCodeSequence(){
    
    $status = $_POST['status'];
    $id = $_POST['id'];
    
    return view('admission.hsc.desired_subject_code_sequence')
    ->withStatus($status)
    ->withId($id);
    
  }
  
  public function downloadHscForm(){
    $tracking_id = Session::get('tracking_id');
    $registration_id = Session::get('registration_id');
    $admission_session = Session::get('session');
    $current_level = Session::get('current_level');
    $groups = Session::get('groups');
    $session = Session::get('session');
    $examyear =  Session::get('exam_year');
    
    if ($registration_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $auto_id = hsc_tracking_auto_id($tracking_id);
    
    $configs = DB::table('form_fillup_config')->where('course', 'hsc')->where('open', 1)->where('current_level',$current_level)->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $students = DB::table('hsc_formfillup_students')->where('current_level', $current_level)->where('admission_session', $config->session)->where('id', $registration_id)->get();
    
    if (count($students) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $student = $students->first();
    
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('hscformfillup.form_id', compact('student'));
    
    $mpdf->writeHTML($html);
    $filename = $student->id."_formfillup_form.pdf";
    $file_path=public_path()."/download/hsc/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/hsc/".$filename."' target='_blank'>Click to Download</a></center>";
    
  }
  
  public function downloadSlipId(Request $request){

    if($request->ajax())
         {
            if(!Session::has('registration_id'))
                return Redirect::route('hsc.student.formfillup'); 
            
            $registration_id = Session::get('registration_id');
            $admission_session = Session::get('session');
            $current_level = Session::get('current_level');
            $groups = Session::get('groups');
            $session = Session::get('session');
            $examyear =  Session::get('exam_year');
            $ff_result=FormFillup::where('id',$registration_id )
                                        ->where('level_study',$current_level)
                                        ->where('groups',$groups)
                                        ->where('payment','Paid')
                                        ->where('session',$session)
                                        ->where('exam_year',$examyear)
                                        ->where('course', 'HSC')                                       
                                        ->get();
            if(count($ff_result) > 0){
              $student = $ff_result->first();

            }else{
              echo "Student Not Found, Please Contact to College";
              return;
            }

            $student_infos = DB::table('hsc_formfillup_students')->where('current_level', $current_level)->where('admission_session', $session)->where('id', $registration_id)->get();
    
            if (count($student_infos) < 1) {
              return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
            }

            $stu_info = $student_infos->first();

            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
            $mpdf->ignore_invalid_utf8 = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;

            $html = view('hscformfillup.slip_id', compact('student', 'stu_info'));

            $mpdf->writeHTML($html);
            $filename = $student->id."_formfillup_slip.pdf";
            $file_path=public_path()."/download/hsc/";
            $mpdf->Output($file_path.'/'.$filename);
            echo "<center><a href='".url('/')."/download/hsc/".$filename."' target='_blank'>Click to Download</a></center>";
         }
  }
  
  public function formfillupLogout(){
    
    Auth::logout(); 
    Session::flush();
    return Redirect::route('hsc.student.formfillup.signin');
  }

  public function nextStep(){

    if(!Session::has('registration_id') || Session::get('hsc_con') != 1)
      return Redirect::route('hsc.student.formfillup');

    $registration_id    = Session::get('registration_id');
    $current_level    = Session::get('current_level');
    $groups = Session::get('groups');
    $session = Session::get('session');
    $exam_year =   Session::get('exam_year');

    return view('hscformfillup.paymenttype', compact('registration_id','groups', 'current_level', 'session', 'exam_year'));
  }

  public function checktype(Request $request){

    $payType= $request->get('payType'); 
    $registration_id = $request->get('registration_id');
    Session::put('payType', $payType);
    Session::put('registration_id', $registration_id);
  }

  public function submitPayType(){
    if(!Session::has('registration_id'))
        return Redirect::route('hsc.student.formfillup')->with('error', 'Something Went Wrong, Please Try again');

        $registration_id    = Session::get('registration_id');
        $payType    = Session::get('payType');
        $current_level = $current_lev   = Session::get('current_level');
        $groups = Session::get('groups');
        $session = Session::get('session');
        $opening_date = Session::get('opening_date');
        $examyear =   Session::get('exam_year');

        $code = '';
        $admission_name = 'hsc_form_fillup_'.$current_level.'_'.$examyear;
        $results_payslip = DB::select("select * from payslipheaders where id = $payType") ;
        foreach($results_payslip as $result_payslip){
          $code = $result_payslip->code;
          $title = $result_payslip->title;
          $start_date = $result_payslip->start_date;
          $end_date = $result_payslip->end_date;
          $total_papers = $result_payslip->total_papers;
          $formfillup_type = $result_payslip->formfillup_type;
        }

        $ff_student = DB::table('form_fillup')->where('id',$registration_id)
        ->where('session',$session)
        ->where('level_study',$current_level)
        ->where('groups',$groups)
        ->where('payment','paid')
        ->where('exam_year',$examyear)
        ->where('course','HSC')
        ->get();

        $already_paid = Invoice::where('roll', $registration_id)->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('passing_year', $examyear)->where('admission_session', $session)->orderBy('id', 'desc')->where('type','hsc_form_fillup')->where('status','Paid')->get();

        if (count($already_paid) > 0 || count($ff_student) > 0) {
          // already formfillup
          return redirect()->route('hsc.student.formfillup.view');
        }

        $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");
        $total_amount = 0;
        foreach($amounts as $amount){
          $total_amount = $total_amount + $amount->fees;
        }   

        $student = DB::table('student_info_hsc_formfillup')->where('current_level', $current_level)->where('id', $registration_id)->first();

        if($total_papers != '0'){
          $pay_type = 'paper';
        }else{
          $pay_type = 'general';
        }
        
        if($formfillup_type == 'others'){
          $pay_type = 'others';
        }

        $already_exists = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_form_fillup')->where('level', $current_level)->where('date_start', '>=', $opening_date)->where('admission_session', $session)->where('status', 'Pending')->orderBy('id', 'desc')->get();

          if (count($already_exists) < 1) {
          DB::table('invoices')->insert(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'hsc_form_fillup' ,
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->groups,
                  'subject' => $student->groups,
                  'level' => $current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$session,
                  'student_type'=>$student->student_type,
                  'registration_type'=>$student->registration_type,
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

            $invoice = DB::table('invoices')->where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_form_fillup')->where('date_start', '>=', $opening_date)->where('level', $current_level)->where('admission_session', $session)->where('status', 'Pending')->orderBy('id', 'desc')->first();
                
          DB::table('invoices')->where('id', $invoice->id)->update(
              array(
                  'name'=>$student->name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'hsc_form_fillup',
                  'roll' => $student->id,
                  'mobile' => '',
                  'ssc_board' => '',
                  'pro_group' => $student->groups,
                  'subject' => $student->groups,
                  'level' => $current_level,
                  'passing_year' => $examyear,
                  'admission_session'=>$session,
                  'student_type'=>$student->student_type,
                  'registration_type'=>$student->registration_type,
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
      return redirect()->route('hsc.student.formfillup.view');
  }

  public function view()
    {
        if(!Session::has('registration_id'))
        return Redirect::route('hsc.student.formfillup');

        $registration_id = Session::get('registration_id');
        $current_level = Session::get('current_level');
        $groups = Session::get('groups');
        $session = Session::get('session');
        $examyear =   Session::get('exam_year');
        $opening_date =   Session::get('opening_date');

        $student_infos = DB::table('hsc_formfillup_students')->where('current_level', $current_level)->where('id', $registration_id)->where('admission_session', $session)->get();
        if(count($student_infos) < 1) return Redirect::route('hsc.student.formfillup')->with('error', 'Something Went Wrong, Please Try again');

      $invoices = DB::table('invoices')->where('roll', $student_infos[0]->id)->where('passing_year', $examyear)->where('type', 'hsc_form_fillup')->where('level', $current_level)->where('date_start', '>=', $opening_date)->where('admission_session', $session)->orderBy('id', 'desc')->get();

      if(count($invoices) < 1) return Redirect::route('hsc.student.formfillup')->with('error', 'Something Went Wrong, Please Try again Later');

      $invoice = $invoices->first();
      Session::put('invoice_id', $invoice->id);

      $payment_status = $invoice->status;

      $payment_amount = $invoice->total_amount;
      $institute_code= 'mmc';
      Session::put('payment_amount', $payment_amount);
          
      return view('hscformfillup.view', compact('payment_amount','payment_status','student_infos', 'registration_id', 'institute_code' ,'current_level'));
    }

    public function payment_approve(Request $request){
        $registration_id = Session::get('registration_id');
        $invoice_id = Session::get('invoice_id');
        if(!$registration_id || !$invoice_id)
        return Redirect::route('hsc.student.formfillup')->with('error', 'Something Went. Please Try Again');

        $current_level = Session::get('current_level');
        $groups = Session::get('groups');
        $session = Session::get('session');
        $examyear =   Session::get('exam_year');
        $opening_date =   Session::get('opening_date');
        $transaction_id = $request->transaction_id;
        $trx_ids = explode(',', $transaction_id);
        $total_amount = 0;

        $invoice = Invoice::where('id', $invoice_id)->where('type', 'hsc_form_fillup')->where('roll', $registration_id)->first();

        foreach ($trx_ids as $trx_id) {
          $transaction_array = get_info_by_dbbl_trxid($trx_id);

          if($transaction_array['response']=='Error')
          {
            return redirect()->back()->withInput()->with('error', "দুঃখিত ! আপনার TrxID টি ভুল হয়েছে, দয়া করে যাচাই করে নিন");
          }
          if($transaction_array['response']!='ok'){
              return redirect()->back()->withInput()->with('error', "Please Try After Sometime");
          }
          if($transaction_array['response']=='ok')
          {
            $total_amount += $transaction_array['amount'];
            $udate = date('Y-m-d', strtotime($transaction_array['payment_date']));
            $pdate = date('Y-m-d', strtotime($transaction_array['payment_date']));
          }
          //$addRoll
          if($transaction_array['response']=='ok' && $transaction_array['bill_id']!=$registration_id)
          {
            return redirect()->back()->withInput()->with('error', "Sorry! Your TrxID does not match with your Registration ID");
          }
          $results =  DB::select('select * from trx_id where tr_id= "'.$transaction_array['trx_id'].'"');
          if(count($results)>0){
            return redirect()->back()->withInput()->with('error','Sorry, This transaction number already used');
          }
        }

        if($total_amount < $invoice->total_amount){
          return Redirect::back()->withInput()->with('error', "দুঃখিত ! আপনি ভর্তির জন্য পর্যাপ্ত টাকা জমা দেননি");
        }
        DB::beginTransaction();
        try {
          DB::table('trx_id')->insert(
                           array('tr_id'=>$transaction_id, 'amount'=>$total_amount)
                            );
          $student_info_hsc = DB::table('student_info_hsc_formfillup')->where('id', $invoice->roll)->where('current_level', $invoice->level)->first();

          $current_level =  $invoice->level;
          $ff_student = DB::table('form_fillup')->where('id',$registration_id)
          ->where('session',$session)
          ->where('level_study',$current_level)
          ->where('groups',$groups)
          ->where('payment','paid')
          ->where('exam_year',$examyear)
          ->where('course','HSC')
          ->get();

          if(count($ff_student) < 1){
            $ff=new FormFillup();
            $ff->id=$invoice->roll;
            $ff->name=$invoice->name;
            $ff->level_study= $current_level;
            $ff->session=$invoice->admission_session;
            $ff->groups=$invoice->pro_group;
            $ff->dept_name=$invoice->pro_group;
            $ff->student_type=$invoice->student_type;
            $ff->formfillup_type=$invoice->registration_type;
            $ff->pay_type=$invoice->pay_type;
            $ff->total_papers=$invoice->total_papers;
            $ff->course='HSC';
            $ff->payment='Paid';
            $ff->total_amount= $invoice->total_amount;
            $ff->exam_year=$invoice->passing_year;
            $ff->date=$udate;
            $ff->slip_name= $invoice->slip_name;
            $ff->slip_type= $invoice->slip_type;
            $ff->transaction_id=$transaction_id;
            $ff->save();

            $invoice->status = 'Paid';
            $invoice->update_date = date('Y-m-d H:i:s', strtotime($pdate));
            $invoice->trx_id = $transaction_id;
            $invoice->txnid = $transaction_id;
            $invoice->txndate = $pdate;
            $invoice->payerMobileNo = '';
            $invoice->payForMobileNo = '';
            $invoice->biller_code = '244';
            $invoice->update();
          }

          DB::commit();

          return redirect()->route('hsc.student.formfillup.view')->with('success', 'টাকা সম্পূর্ণভাবে সফল হয়েছে, দয়া করে আপনার পেস্লিপ এবং ফর্ম ডাউনলোড করুন');
        } catch (Exception $e) {
          DB::rollback();
          return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");
        }

    }


}

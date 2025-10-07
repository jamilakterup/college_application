<?php

namespace App\Http\Controllers\Admission\Degree;

use App\Http\Controllers\Controller;
use App\Libs\Payment;
use App\Models\Invoice;
use App\Models\PayslipHeader;
use Auth;
use DB;
use IdRollGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Image;
use Mpdf\Mpdf;
use Session;

class DegreeAdmissionController extends Controller
{

  public function index() {
    Auth::logout();
    Session::flush();
    return view('admission.degree.index');

  }


  public function checkMerit(Request $request){
    $admission_roll = $request->admission_roll;
    Session::put('admission_step',1);
    Session::put('admission_roll',$admission_roll);
    $status=1;
    
    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Degree 1st Year')->where('course', 'degree')->where('type', 'admission')->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();

      if ($config->clossing_date < date('Y-m-d')) {
        //date is expired
        return $status = 6;
      }
      
      $admission_session = $config->session;
      Session::put('admission_session',$config->session);
      Session::put('examyear',$config->exam_year);
      Session::put('opening_date',$config->opening_date);
      
      $check_merit = DB::table('deg_merit_list')->where('admission_roll', $admission_roll)->get();
      
      if (count($check_merit) > 0) {

        $merit = $check_merit->first();
        
        $invoices = Invoice::where('roll', $admission_roll)->where('type', 'degree_admission')->where('admission_session', $config->session)->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->get();
        
        if(count($invoices) < 1){
          return $status = 4;
        }else{
          $invoice = $invoices->first();
          Session::put('invoice_id', $invoice->id);
        }
        
        Session::put('subject', $merit->groups);
        Session::put('faculty', $merit->groups);
        
        $student_infos = DB::table('student_info_degree')->where('current_level', 'Degree 1st Year')->where('admission_roll', $admission_roll)->where('session', $config->session)->get();
        
        if (count($student_infos) > 0) {
          // if student already admitted
          Session::put('deg_con', 1);
          $auto_id= auto_id_deg($student_infos[0]->refference_id);
          Session::put('tracking_id', DEGREE_PREF.$auto_id);
          return $status = 5;
        }
        
        
        Session::put('name', $merit->name);
        Session::put('major_degree', 'degree');
        Session::put('merit',$merit->merit_pos); 
        Session::put('admit_faculty',$merit->groups);
        Session::put('admit_subject',$merit->groups);
        
        // find merit student
        return $status = 1;
        
      }else{
        // if not exists student
        return $status = 2;
      }
      
    }else{
      
      // if admission not open
      return $status = 3;
    }
    
    echo json_encode($status);
  }

  public function dbblapplication(){
    $admission_roll = Session::get('admission_roll');
    $invoice_id = Session::get('invoice_id');
    $admission_session = Session::get('admission_session');
    $subject = Session::get('subject');

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Degree 1st Year')->where('course', 'degree')->where('type', 'admission')->where('type', 'admission')->get();

    if(count($configs) < 1){
      return 'closed';
    }
    $config = $configs->first();

    if ($admission_roll == '' || $admission_session=='') {
      return Redirect::route('student.degree.admission')->with('res', 'Please try again.');
    }

    $invoice = Invoice::where('id', $invoice_id)->where('type', 'degree_admission')->where('admission_session', $admission_session)->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->first();

    $invoice_id = $invoice->id;

    $payment_status = $invoice->status;
    $total_amount = $invoice->total_amount;

    $admitted_students = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('subject', $subject)->get();

    if (count($admitted_students) < 1) {
      return view('admission.degree.form', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
    }

    $addmitted_student = $admitted_students->first();

    Session::put('auto_id', auto_id_deg($addmitted_student->auto_id));

    return view('admission.degree.confirmation', compact('admission_roll', 'payment_status','total_amount'));
  }

  public function admissionForm(){

    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $invoice_id = Session::get('invoice_id');
    $subject = Session::get('subject');
    $faculty = Session::get('faculty');
    $major_degree = Session::get('major_degree');
    $merit = Session::get('merit'); 
    $admit_faculty = Session::get('admit_faculty');
    $admit_subject =  Session::get('admit_subject');
    $name =  Session::get('name');

    $admission_step =  Session::get('admission_step');
    if($admission_step!=1  )
      return Redirect::route('student.degree.admission');

    if ($admission_roll != '' && $invoice_id != '') {

      $invoice = Invoice::where('id',$invoice_id)->where('type', 'degree_admission')->where('admission_session', $admission_session)->first();
      return view('admission.degree.form', compact('admission_roll', 'invoice_id', 'subject', 'faculty', 'major_degree', 'merit', 'admit_faculty', 'admit_subject','name'));
    }

    return redirect()->route('student.degree.admission');
  }

  public function degAdmInformationSubmit(Request $request){
    $this->validate($request, [
      'photo' => 'required|mimes:jpeg,jpg,png|max:500000',
      'income' => 'required|numeric',
      ]);
      
      $temp_entry_time = date('Y-m-d H:i:s');
      $entry_time = date('Y-m-d H:i:s', strtotime($temp_entry_time));
      
      $admission_roll = $request->get('admission_roll');
      $faculty=$request->get('faculty');
      
      $config = DB::table('admission_config')->where('open', 1)->where('current_level', 'Degree 1st Year')->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->first();
      $admission_session = $config->session;
      
      $sub_facs = DB::select("SELECT * FROM deg_merit_list WHERE admission_roll= $admission_roll");
      foreach($sub_facs as $sub_fac){
        $m_faculty = $sub_fac->groups;
        $m_subject = $sub_fac->groups;
      }

      if(isset($request->payType) && $request->payType != ''){
        $this->generate_invoice($request->payType);
      }
      
      $invoices = Invoice::where('roll', $admission_roll)->where('admission_session', $admission_session)->where('subject', $m_subject)->where('type', 'degree_admission')->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->get();
      
      if(count($invoices) < 1){
        return redirect()->back()->with('warning', 'Your Invoice is not generated. Please contact to the college!');
      }
      
      $invoice = $invoices->first();
      
      $logo = $request->file('photo');
      $filename = rand(1, 99999999999) .'.jpg';
      $upload_path = public_path('upload/college/degree/draft/' . $filename);
      $db_path = 'upload/college/degree/' . $filename;
      Image::make($logo->getRealPath())->save($upload_path);
      
      $submitted_data = array(
            'entry_time'=>$entry_time,
            'name'=>$request->get('student_name'),
            'father_name'=>$request->get('fathers_name'),
            'mother_name'=>$request->get('mothers_name'),
            'birth_date'=>date('Y-m-d', strtotime($request->get('birth_date'))),
            'blood_group'=>$request->get('blood_group'),
            'gender'=>$request->get('gender'),
            'permanent_village'=>$request->get('permanent_village'),
            'present_village'=>$request->get('present_village'),
            'permanent_po'=>$request->get('permanent_post_office'),
            'present_po'=>$request->get('present_po'),
            'permanent_ps'=>$request->get('permanent_thana'),
            'present_ps'=>$request->get('present_thana'),
            'permanent_dist'=>$request->get('permanent_district'),
            'present_dist'=>$request->get('present_dist'),
            'contact_no'=>$request->get('student_mobile'),
            'religion'=>$request->get('religion'),
            'guardian_name'=>$request->get('guardian_name'),
            'guardian_contact'=>$request->get('guardian_mobile'),
            'guardian_relation'=>$request->get('guardian_relation'),
            'admission_roll'=>$admission_roll,
            'ssc_roll'=>$request->get('ssc_roll'),
            'ssc_institute'=>$request->get('ssc_institution'),
            'ssc_board'=>$request->get('ssc_board'),
            'ssc_gpa'=>$request->get('ssc_gpa'),
            'ssc_pass_year'=>$request->get('ssc_passing_year'),
            'ssc_reg'=>$request->get('ssc_reg'),
            'hsc_roll'=>$request->get('hsc_roll'),
            'hsc_institute'=>$request->get('hsc_institution'),
            'hsc_board'=> $request->get('hsc_board'),
            'hsc_gpa'=>$request->get('hsc_gpa'),
            'hsc_pass_year'=>$request->get('hsc_passing_year'),
            'hsc_reg'=>$request->get('hsc_reg'),
            'photo'=>$filename,
            'password'=>$request->get('password'),
            'session'=> $admission_session ,
            'permanent_mobile'=>$request->get('student_mobile'),
            'guardian_income'=>$request->get('income'),
            'faculty' => $m_faculty,
            'subject' => $m_subject,
            'deg_subjects' => implode(',',$request->get('deg_sub')),
            'guardian_occupation'=>$request->get('occupation'),
            'admission_invoice_id'=>$invoice->id
      );
      $admitted_student = DB::table('deg_admitted_student')->where('session', $admission_session)->where('subject', $m_subject)->where('admission_roll',$admission_roll)->get();

        if(count($admitted_student) > 0){
            DB::table('deg_admitted_student')->where('auto_id', $admitted_student->first()->auto_id)
              ->update($submitted_data);
            $admitted_id = $admitted_student->first()->auto_id;
        }else{
            $admitted_id = DB::table('deg_admitted_student')
              ->insertGetId($submitted_data);
        }

        $admitted_student = DB::table('deg_admitted_student')->where('auto_id', $admitted_id)->where('session', $admission_session)->where('admission_roll',$admission_roll)->get();

      foreach($admitted_student as $result){
        $auto_id= auto_id_deg($result->auto_id);
        $tracking_id = DEGREE_PREF.$auto_id;
        $password =$result->password;
        $refId=$result->auto_id;
      }
      Session::put('tracking_id', $tracking_id);
      Session::put('password',$password);
      $toatalPayAmount = '';
      $d = date('Y-m-d');
      
      return Redirect::route('student.degree.admission.form');
    }

  public function confirmslip(){
    return view('admission.degree.confirmslip');
  }

  public function degSignin(){
      return view('admission.degree.sign_in_form');
    }          
  
  public function retrievepass(Request $request){
    $admission_roll = $request->admission_roll;
    $hsc_roll = $request->hsc_roll;
    
    $results = DB::table('deg_admitted_student')->where('admission_roll',$admission_roll )->where('hsc_roll',$hsc_roll)->orderBy('auto_id', 'desc')->get();
    
    $auto_id = '';
    $password = '';
    foreach($results as $result){
      $password = $result->password;
      $auto_id = $result->auto_id;
    }
    
    $auto_id =str_pad($auto_id,'4','0',STR_PAD_LEFT);
    $auto_id = DEGREE_PREF.$auto_id;
    echo json_encode(array($password, $auto_id));
  } 

public function degStudentSignin(Request $request){
  $status=0;   
  $tracking_id = trim($request->tracking_id);
  $password = trim($request->password);
  //$password = $database->passwod_encode($password);
  $number = 0;
  $query = DEGREE_PREF;
  
  $config = DB::table('admission_config')->where('current_level', 'Degree 1st Year')->where('open', 1)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
  if(count($config) > 0){
    
    $conf = $config->first();
    Session::put('admission_session', $conf->session);
    
  }else{
    // admission is not open
    return $status = 4;
  }
  
  if($auto_id = deg_tracking_auto_id($tracking_id)){
        $admitted_students = DB::table('deg_admitted_student')->where('session', $conf->session)->where('auto_id',$auto_id)->where('password',$password)->get();
        $number = count($admitted_students);
      }
  if($number>0){
    Session::put('tracking_id',$tracking_id);
    Session::put('deg_con', 1);
    
    $masters_admitted = $admitted_students->first();
    Session::put('admission_roll',$masters_admitted->admission_roll);

    $check_merit = DB::table('deg_merit_list')->where('admission_roll', $masters_admitted->admission_roll)->get();
    if (count($check_merit) > 0) {
      $merit = $check_merit->first();
      $invoices = Invoice::where('roll', $masters_admitted->admission_roll)->where('admission_session', $conf->session)->where('type', 'degree_admission')->where('date_start','>=', $conf->opening_date)->orderBy('id', 'desc')->get();
      if(count($invoices) > 0){
        $invoice = $invoices->first();
        Session::put('invoice_id', $invoice->id);

        }else{
          // invoice not generated
          return $status = 5;
        }
      }else{
        // you are not in merit list
        return $status = 3;
      }
      
      return $status=1;
    }else{
      
      // student not found
      return $status=2;
    }
    echo json_decode($status);
  }

  public function degConfirmation(){
    
    if(!Session::has('deg_con') ){
      return view('admission.degree.index');
    }

    $invoice_id = Session::get('invoice_id');

    $config = DB::table('admission_config')->where('current_level', 'Degree 1st Year')->where('open', 1)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    if(count($config) > 0){
      
      $config = $config->first();
      Session::put('admission_session', $config->session);
      
    }else{
      // admission is not open
      return $status = 4;
    }
    
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    
    $tracking_id =  Session::get('tracking_id');
    $auto_id = deg_tracking_auto_id($tracking_id);
    Session::put('auto_id', $auto_id);
    
    if ($tracking_id == '') {
      return view('admission.degree.index');
    }
    
    $admitted_student = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id',$auto_id)->first();
    $invoice = Invoice::where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_admission')->where('date_start', '>=', $config->opening_date)->first();
    $payment_status = 'Paid';
    $total_amount = $invoice->total_amount;

    Session::put('invoice_id' ,$invoice->id);
    
    if ($invoice->status == 'Pending') {
      $payment_status = 'Pending';
    }else{
      $student_infos = DB::table('student_info_degree')->where('current_level', 'Degree 1st Year')->where('session', $admitted_student->session)->where('refference_id', $admitted_student->auto_id)->get();
      
      if(count($student_infos) < 1 ){
        return $this->dutchbangla($admitted_student->auto_id);
      }
    }
    return view('admission.degree.confirmation',compact('payment_status','total_amount'));
  }


  public function admisionLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('student.degree.admission.signin'); 
  }

  public function roll_generate_deg($session,$subject,$prefix){
    
    $id_table_subject=$prefix.$subject;
    
    $results= DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'");
    //convert 1 as 001 for 3 digit roll
    foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
    
    $results= DB::select("select dept_code from departments where dept_name='$subject'");
    foreach($results as $result){ $dept_code=$result->dept_code; break; }
    
    // $session=substr($session,2,2);
    $session=substr($session,2,2);
    //$dept_code=substr($dept_code,0,2); // take first two digit of the department code
    
    $class_roll=$session.$dept_code.$digit;
    
    
    return $class_roll;
    
  }

  public  function id_generate_degree($session,$class_roll,$catagory){
    
    $session=substr($session,0,4);  // take session as first year of the session(ex: 2012-2013 , session is 2012)
    return $id=$session.$class_roll;
    
    
  }

  public function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }


  public function downloadSlipId(){
    
    $tracking_id = Session::get('tracking_id');
    $auto_id = deg_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Degree 1st Year')->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $invoice = DB::table('invoices')->where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_admission')->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->first();
    $admitted_student = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('auto_id', $auto_id)->get();
    
    
    $student = DB::table('student_info_degree')->where('current_level', 'Degree 1st Year')->where('refference_id', $auto_id)->where('session', $config->session)->where('admission_roll', $admission_roll)->first();
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('admission.degree.slip_id', compact('student', 'invoice', 'tracking_id'));
    
    $mpdf->writeHTML($html);
    $filename = $tracking_id."_admission_slip.pdf";
    $file_path=public_path()."/download/degree/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/degree/".$filename."' target='_blank'>Click to Download</a></center>";
  }


  public function dutchbangla($auto_id){
    $autoId= $auto_id;
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    $invoice_id = Session::get('invoice_id');
    $configs = DB::table('admission_config')->where('current_level', 'Degree 1st Year')->where('open', 1)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    if(count($configs) > 0){
      
      $config = $configs->first();
      Session::put('admission_session', $config->session);
      
    }else{
      // admission is not open
      return $status = 4;
    }

    $student = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->first();
    $invoice = Invoice::where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_admission')->where('date_start','>=', $config->opening_date)->first();
    
    $ssc_roll= $student->ssc_roll;
    $slip_type = $invoice->slip_type;
    $pay_am_floor = $invoice->total_amount;
    
    $trxid = $invoice->trx_id;
    $pay_da= date('Y-m-d h:i:s',strtotime($invoice->txndate));
    $billid=$invoice->roll;
    
    $results =   DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->get();
    
    if(count($results)<1){
      
      return Redirect::route('student.degree.admission.degConfirmation')->with('res','Sorry, Student not found');
      
    }
    
    foreach($results as $result){
      $admission_roll = $result->admission_roll;
      $name = $result->name;
      $fathers_name = $result->father_name;
      $mothers_name = $result->mother_name;
      $faculty = $result->faculty;
      $subject = $result->subject;
      $contact_info = $result->contact_no;
      $photo = $result->photo;
      $image_name = $result->photo;
      $session = $result->session;
      $gender = $result->gender;
      $blood_group = $result->blood_group;
      $religion = $result->religion;
      $guardian_name = $result->guardian_name;
      $guardian_contact = $result->guardian_contact;
      $guardian_relation = $result->guardian_relation;
      $guardian_income = $result->guardian_income;
      $guardian_occupation = $result->guardian_occupation;

      $present_village = $result->present_village;
      $present_po = $result->present_po;
      $present_ps = $result->present_ps;
      $present_dist  = $result->present_dist ;
      $contact_no = $result->contact_no;
      $email = $result->email;
      //$division = $result->division;

      $permanent_village = $result->permanent_village;
      $permanent_po = $result->permanent_po;
      $permanent_ps = $result->permanent_ps;
      $permanent_dist  = $result->permanent_dist ;
      $permanent_email = $result->permanent_email;
      //$permanent_division = $result->permanent_division;
      $permanent_mobile = $result->permanent_mobile;

      $ssc_roll = $result->ssc_roll;
      $ssc_institute = $result->ssc_institute;
      $ssc_board = $result->ssc_board;
      $ssc_gpa = $result->ssc_gpa;

      $hsc_roll = $result->hsc_roll;
      $hsc_board = $result->hsc_board;
      $hsc_institute = $result->hsc_institute;
      $hsc_gpa = $result->hsc_gpa;
      $birth_date=$result->birth_date;
      $ssc_passing_year = $result->ssc_pass_year;
      $hsc_passing_year = $result->hsc_pass_year;   
    }
    $prefix='degree_';
    $catagory="4"; // for masters
    $id= IdRollGenerate::id_generate_deg($session,$subject,$prefix);
    $class_roll = IdRollGenerate::roll_generate_deg($id);//$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
    $student_id=$id; //$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
    
    
    $results= DB::select("SELECT merit_pos FROM deg_merit_list WHERE admission_roll=$admission_roll");
    foreach($results as $result){
      $merit_position = $result->merit_pos;
    }
    
    $password= $this->randomPassword();
    $pass_show=$password;
    $password=Hash::make($password);
    
    $oldPath = 'upload/college/degree/draft/'.$image_name; // publc/images/1.jpg
    if (file_exists(public_path($oldPath))) {
      $folder = public_path('upload/college/degree/'.$session);
      create_dir($folder);
      $newPath = public_path('upload/college/degree/'.$session.'/'.$image_name); // publc/images/2.jpg
      $oldPath = public_path($oldPath);
      if (\File::copy($oldPath , $newPath)) {
      }
    }
    
    
    DB::table('student_info_degree')->insert(
      array('id'=>$id, 'name'=>$name, 'class_roll'=>$class_roll, 'groups'=>$subject, 'current_level'=>'Degree 1st Year', 'father_name'=>$fathers_name, 'mother_name'=>$mothers_name, 'birth_date'=>$birth_date,'blood_group' => $blood_group, 'gender'=>$gender, 'permanent_village'=>$permanent_village, 'present_village'=>$present_village, 'permanent_po'=>$permanent_po, 'present_po'=>$present_po, 'permanent_ps'=>$permanent_ps, 'present_ps'=>$present_ps, 'permanent_dist'=>$permanent_dist, 'present_dist'=>$present_dist, 'contact_no'=>$contact_no,'merit_pos'=> $merit_position, 'religion'=>$religion, 'guardian'=>$guardian_name, 'image'=>$image_name, 'refference_id'=>$auto_id, 'admission_roll'=>$admission_roll , 'session'=>$session, 'total_amount'=> $pay_am_floor)
    );
    
    $d=date('Y-m-d');
    
    DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='degree_{$subject}'");
    
    $date=date('Y-m-d');
    $adsession=$session;
    DB::update("update deg_admitted_student set payment_status='dbbl',paid_date='$date' where auto_id='$auto_id'"); 

    DB::update("update deg_merit_list set admission_status='admitted' where admission_roll='$admission_roll'");
    return Redirect::route('student.degree.admission.degConfirmation')->with('res','টাকা সফল ভাবে জমা হয়েছে');
  }

  public function downloadDegForm(){
    $tracking_id = Session::get('tracking_id');
    $admission_roll = Session::get('admission_roll');
    $auto_id = deg_tracking_auto_id($tracking_id);
    
    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Degree 1st Year')->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $admitted_student = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('session', $config->session)->where('auto_id', $auto_id)->get();
    
    if (count($admitted_student) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $student = DB::table('student_info_degree')->where('current_level', 'Degree 1st Year')->where('refference_id', $auto_id)->where('admission_roll', $admission_roll)->where('session', $config->session)->first();
    $admitted_student = $admitted_student->first();
    
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    addMpdfPageSetup($mpdf);
    
    $html = view('admission.degree.form_id', compact('admitted_student', 'student'));
    
    $mpdf->writeHTML($html);
    $filename = $student->id.'_'.$student->session."_admission.pdf";
    $file_path=public_path()."/download/degree/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/degree/".$filename."' target='_blank'>Click to Download</a></center>";
  }


  public function payment_approve(Request $request){
    $response = Payment::approve($request->transaction_id);
    
    if($response == ''){
      return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");
    }
    
    if($response['status'] == '402'){
      return redirect()->back()->withInput()->with('error', $response['msg']);
    }
    
    if($response['status'] == '200'){
        return $this->dutchbangla(Session::get('auto_id'));
    }

      return redirect()->back()->withInput()->with('error', "Something Went Wrong, Please try Again!");

  }

  public function generate_invoice($payType){

    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $subject = Session::get('subject');
    $faculty = Session::get('faculty');
    $examyear = Session::get('examyear');
    $major_degree = Session::get('major_degree');
    $merit = Session::get('merit'); 
    $admit_faculty = Session::get('admit_faculty');
    $opening_date =  Session::get('opening_date');
    $admit_subject =  Session::get('admit_subject');
    $name =  Session::get('name');

    $results = PayslipHeader::where('id',$payType)->get();
      foreach($results as $paySlip){
        $code = $paySlip->code;
        $title = $paySlip->title;
        $start_date = $paySlip->start_date;
        $end_date = $paySlip->end_date;
        $subjects = explode('_',$paySlip->subject);
        $payslip_subject = $paySlip->subject;
      $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
      $total_amount = 0;
      foreach($amounts as $amount){
        $total_amount = $total_amount + $amount->fees;
      }
    }

    $already_exists = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_admission')->where('subject', $faculty)->where('date_start','>=', $opening_date)->orderBy('id', 'desc')->get();

          if (count($already_exists) < 1) {
                
          $invoice_id = DB::table('invoices')->insertGetId(
              array(
                  'name'=>$name, 
                  'hsc_merit_id' => 0, 
                  'type'=>'degree_admission' ,
                  'roll' => $admission_roll,
                  'mobile' => '',
                  'pro_group' =>  $faculty,
                  'subject' =>  $faculty,
                  'level' => 'Degree 1st Year',
                  'passing_year' => $examyear,
                  'admission_session'=>$admission_session,
                  'slip_name'=>$title,
                  'slip_type'=>$code,
                  'total_amount'=>$total_amount,
                  'status'=>'Pending',
                  'date_start'=>$start_date, 
                  'date_end'=>$end_date, 
                  'father_name'=>'N/A', 
                  'institute_code'=>INS_CODE, 
                  'refference_id' => 0,
                  'payment_info_id' => 0
                  )
            );
          }else{

            $already_exists = DB::table('invoices')->where('roll', $admission_roll)->where('type', 'degree_admission')->where('subject', $faculty)->where('date_start','>=', $start_date)->where('admission_session', $admission_session)->orderBy('id', 'desc')->where('status', 'Pending')->get();

            if (count($already_exists) > 0) {
              $invoice = DB::table('invoices')->where('roll', $admission_roll)->where('type', 'degree_admission')->where('subject', $faculty)->where('admission_session', $admission_session)->where('date_start','>=', $opening_date)->orderBy('id', 'desc')->where('status', 'Pending')->first();
                  $invoice_id = $invoice->id;
            DB::table('invoices')->where('id', $invoice->id)->update(
                  array(
                      'name'=>$name, 
                      'hsc_merit_id' => 0, 
                      'type'=>'degree_admission' ,
                      'roll' => $admission_roll,
                      'mobile' => '',
                      'pro_group' =>  $faculty,
                      'subject' =>  $faculty,
                      'level' => 'Degree 1st Year',
                      'passing_year' => $examyear,
                      'admission_session'=>$admission_session,
                      'slip_name'=>$title,
                      'slip_type'=>$code,
                      'total_amount'=>$total_amount,
                      'status'=>'Pending',
                      'date_start'=>$start_date, 
                      'date_end'=>$end_date, 
                      'father_name'=>'N/A', 
                      'institute_code'=>INS_CODE, 
                      'refference_id' => 0,
                      'payment_info_id' => 0
                      )
              );
            }

          }

          Session::put('invoice_id', $invoice_id);
  }


}

<?php

namespace App\Http\Controllers\Admission\Masters;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Image;
use Session;
use Mpdf\Mpdf;
use IdRollGenerate;
use App\Libs\Payment;

class MastersAdmissionController extends Controller
{

  public function index() {
    Auth::logout();
    Session::flush();
    return view('admission.masters.index');

  }


  public function checkMerit(Request $request){
    $admission_roll = $request->honours_roll;
    Session::put('admission_step',1);
    Session::put('admission_roll',$admission_roll);
    $status=1;
    
    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('type', 'admission')->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();

      if ($config->clossing_date < date('Y-m-d')) {
        //date is expired
        return $status = 6;
      }
      
      $admission_session = $config->session;
      Session::put('admission_session',$config->session);
      
      $check_merit = DB::table('masters_merit_list')->where('admission_roll', $admission_roll)->get();
      
      if (count($check_merit) > 0) {
        
        $invoices = Invoice::where('roll', $admission_roll)->where('level', 'Masters 2nd Year')->where('type', 'masters_admission')->where('admission_session', $config->session)->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->get();
        
        if(count($invoices) < 1){
          return $status = 4;
        }else{
          $invoice = $invoices->first();
          Session::put('invoice_id', $invoice->id);
        }
        
        $merit = $check_merit->first();
        
        Session::put('subject', $merit->subject);
        Session::put('faculty', $merit->faculty);
        
        $student_infos = DB::table('student_info_masters')->where('current_level', 'Masters 2nd Year')->where('admission_roll', $admission_roll)->where('session', $config->session)->get();
        
        if (count($student_infos) > 0) {
          // if student already admitted
          Session::put('masters_con', 1);
          $auto_id= auto_id_msc($student_infos[0]->refference_id);
          Session::put('tracking_id', MSC2ND_PREF.$auto_id);
          return $status = 5;
        }
        
        
        Session::put('name', $merit->name);
        Session::put('hons_roll',$merit->hons_roll);
        Session::put('major_degree', 'major_degree');
        Session::put('merit',$merit->merit_pos); 
        Session::put('admit_faculty',$merit->faculty);
        Session::put('admit_subject',$merit->subject);
        
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

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('type', 'admission')->where('type', 'admission')->get();

    if(count($configs) < 1){
      return 'closed';
    }
    $config = $configs->first();

    if ($admission_roll == '' || $admission_session=='') {
      return Redirect::route('student.masters.application')->with('res', 'Please try again.');
    }

    $invoice = Invoice::where('id', $invoice_id)->where('type', 'masters_admission')->where('admission_session', $admission_session)->where('level', 'Masters 2nd Year')->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->first();

    $invoice_id = $invoice->id;

    $payment_status = $invoice->status;
    $total_amount = $invoice->total_amount;

    $addmitted_students = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('admission_invoice_id', $invoice->id)->get();

    if (count($addmitted_students) < 1) {
      return view('admission.masters.form', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
    }

    $addmitted_student = $addmitted_students->first();

    Session::put('auto_id', auto_id_msc($addmitted_student->auto_id));

    return view('admission.masters.confirmation', compact('admission_roll', 'payment_status','total_amount'));
  }

  public function admissionForm(){

    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $invoice_id = Session::get('invoice_id');
    $subject = Session::get('subject');
    $faculty = Session::get('faculty');
    $hons_roll = Session::get('hons_roll');
    $major_degree = Session::get('major_degree');
    $merit = Session::get('merit'); 
    $admit_faculty = Session::get('admit_faculty');
    $admit_subject =  Session::get('admit_subject');
    $name =  Session::get('name');
    $father_name =  Session::get('father_name');
    $mother_name =  Session::get('mother_name');

    $admission_step =  Session::get('admission_step');
    if($admission_step!=1  )
      return Redirect::route('student.masters.admission');

    if ($admission_roll != '' && $invoice_id != '') {

      $invoice = Invoice::where('id',$invoice_id)->where('type', 'masters_admission')->where('admission_session', $admission_session)->first();
      return view('admission.masters.form', compact('admission_roll', 'invoice_id', 'subject', 'faculty','hons_roll', 'major_degree', 'merit', 'admit_faculty', 'admit_subject','name','father_name','mother_name'));
    }

    return redirect()->route('student.masters.admission');
  }

  public function mscAdmInformationSubmit(Request $request){
    $this->validate($request, [
      'photo' => 'required|mimes:jpeg,jpg,png|max:500000',
      'income' => 'required|numeric',
      ]);
      
      $temp_entry_time = date('Y-m-d H:i:s');
      $entry_time = date('Y-m-d H:i:s', strtotime($temp_entry_time));
      
      $admission_roll = $request->get('admission_roll');
      $faculty=$request->get('to_faculty');
      $msc_session = $request->get('msc_session');
      
      $config = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->first();
      $admission_session = $config->session;
      
      $sub_facs = DB::select("SELECT * FROM masters_merit_list WHERE admission_roll= $admission_roll");
      foreach($sub_facs as $sub_fac){
        $m_faculty = $sub_fac->faculty;
        $m_subject = $sub_fac->subject;
      }
      
      $invoices = Invoice::where('roll', $admission_roll)->where('level', 'Masters 2nd Year')->where('admission_session', $admission_session)->where('type', 'masters_admission')->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->get();
      
      if(count($invoices) < 1){
        return redirect()->back()->with('warning', 'Your Invoice is not generated. Please contact to the college!');
      }
      
      $invoice = $invoices->first();
      
      $logo = $request->file('photo');
      $filename = rand(1, 99999999999) .'.jpg';
      $upload_path = public_path('upload/college/masters/draft/' . $filename);
      $db_path = 'upload/college/masters/' . $filename;
      Image::make($logo->getRealPath())->save($upload_path);
      
      $submitted_data = array(
        'sent_time'=> $entry_time,
        'name'=>$request->get('student_name'),
        'name_bangla'=>$request->get('name_bangla'),
        'father_name'=>$request->get('father_name'),
        'mother_name'=>$request->get('mother_name'),
        'birth_date'=>date('Y-m-d', strtotime($request->get('birth_date'))),
        'blood_group'=>$request->get('blood_group'),
        'gender'=>$request->get('gender'),
        'password'=>$request->get('password'),
        'permanent_mobile'=>$request->get('permanent_mobile_no'),
        'contact_no'=>$request->get('student_mobile'),
        'photo'=>$filename,
        'religion'=>$request->get('religion'),
        'permanent_village'=>$request->get('permanent_village'),
        'present_village'=>$request->get('present_village'),
        'permanent_po'=>$request->get('permanent_post_office'),
        'present_po'=>$request->get('present_po'),
        'permanent_ps'=>$request->get('permanent_thana'),
        'present_ps'=>$request->get('present_thana'),
        'permanent_dist'=>$request->get('permanent_district'),
        'present_dist'=>$request->get('present_dist'),
        'guardian_name'=>$request->get('guardian_name'),
        'guardian_contact'=>$request->get('guardian_mobile'),
        'guardian_relation'=>$request->get('guardian_relation'),
        'guardian_income'=>$request->get('income'),
        'guardian_occupation'=>$request->get('occupation'),
        'payment_status'=>'Pending',
        'status'=>'Pending',
        'current_level'=>'Masters 2nd Year',
        'honrs_roll'=>$request->get('honours_roll'),
        'to_faculty'=>$request->get('to_faculty'),
        'to_subject'=>$request->get('to_subject'),
        'session'=>$request->get('msc_session'),
        'admission_roll'=>$request->get('admission_roll'),
        'admission_invoice_id'=> $invoice->id
      );

      $admitted_student = DB::table('masters_admitted_student')->where('session', $admission_session)->where('admission_invoice_id', $invoice->id)->where('admission_roll',$admission_roll)->get();

        if(count($admitted_student) > 0){
            DB::table('masters_admitted_student')->where('auto_id', $admitted_student->first()->auto_id)
              ->update($submitted_data);
            $admitted_id = $admitted_student->first()->auto_id;
        }else{
            $admitted_id = DB::table('masters_admitted_student')
              ->insertGetId($submitted_data);
        }

        $admitted_student = DB::table('masters_admitted_student')->where('auto_id', $admitted_id)->where('session', $admission_session)->where('admission_roll',$admission_roll)->get();

      foreach($admitted_student as $result){
        $auto_id= auto_id_msc($result->auto_id);
        $tracking_id = MSC2ND_PREF.$auto_id;
        $password =$result->password;
        $refId=$result->auto_id;
      }
      Session::put('tracking_id', $tracking_id);
      Session::put('password',$password);
      $toatalPayAmount = '';
      $d = date('Y-m-d');
      
      $total_amount = $toatalPayAmount;
      $payment_status = 'Paid';
      
      return Redirect::route('student.masters.admission.form');
    }

  public function confirmslip(){
    return view('admission.masters.confirmslip');
  }

  public function mscSignin(){
      return view('admission.masters.sign_in_form');
    }          
  
  public function retrievepass(Request $request){
    $admission_roll = $request->admission_roll;
    $hons_roll = $request->hons_roll;
    
    $results = DB::table('masters_admitted_student')->where('admission_roll',$admission_roll )->where('honrs_roll',$hons_roll)->orderBy('auto_id', 'desc')->get();
    
    $auto_id = '';
    $password = '';
    foreach($results as $result){
      $password = $result->password;
      $auto_id = $result->auto_id;
    }
    
    $auto_id =str_pad($auto_id,'4','0',STR_PAD_LEFT);
    $auto_id = MSC2ND_PREF.$auto_id;
    echo json_encode(array($password, $auto_id));
  } 

public function mscStudentSignin(Request $request){
  $status=0;   
  $tracking_id = trim($request->tracking_id);
  $password = trim($request->password);
  //$password = $database->passwod_encode($password);
  $number = 0;
  $query = MSC2ND_PREF;
  
  $config = DB::table('admission_config')->where('current_level', 'Masters 2nd Year')->where('open', 1)->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
  if(count($config) > 0){
    
    $conf = $config->first();
    Session::put('admission_session', $conf->session);
    
  }else{
    // admission is not open
    return $status = 4;
  }
  
  if($auto_id = msc_tracking_auto_id($tracking_id)){
        $admitted_students = DB::table('masters_admitted_student')->where('session', $conf->session)->where('auto_id',$auto_id)->where('password',$password)->get();
        $number = count($admitted_students);
      }
  if($number>0){
    Session::put('tracking_id',$tracking_id);
    Session::put('masters_con', 1);
    
    $masters_admitted = $admitted_students->first();
    Session::put('admission_roll',$masters_admitted->admission_roll);
    Session::put('auto_id',$masters_admitted->auto_id);
    
    $check_merit = DB::table('masters_merit_list')->where('admission_roll', $masters_admitted->admission_roll)->get();
    if (count($check_merit) > 0) {
      $merit = $check_merit->first();
      $invoices = Invoice::where('roll', $masters_admitted->admission_roll)->where('level', 'Masters 2nd Year')->where('admission_session', $conf->session)->where('type', 'masters_admission')->where('date_start','>=', $conf->opening_date)->orderBy('id', 'desc')->get();
      if(count($invoices) > 0){
        $invoice = $invoices->first();
        Session::put('invoice_id', $invoice->id);
        
        DB::table('masters_admitted_student')->where('admission_roll', $masters_admitted->admission_roll)->where('auto_id',$auto_id)->update([
          'to_subject' => $merit->subject,
          'to_faculty' => $merit->faculty,
          'admission_invoice_id' => $invoice->id
          ]);
          
        }else{
          // invoice not generated
          return $status = 5;
        }
      }else{
        // you are not in merit list
        return $status = 3;
      }
        $invoices = Invoice::where('roll', $masters_admitted->admission_roll)->where('admission_session', $conf->session)->where('type', 'masters_admission')->where('date_start','>=', $conf->opening_date)->where('level', 'Masters 2nd Year')->orderBy('id', 'desc')->get();
        if (count($invoices) > 0) {
          Session::put('invoice_id', $invoices[0]->id);
        }
      
      return $status=1;
    }else{
      
      // student not found
      return $status=2;
    }
    echo json_decode($status);
  }

  public function mscConfirmation(){
    
    if(!Session::has('masters_con') ){
      return view('admission.masters.index');
    }
    
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    
    $tracking_id =  Session::get('tracking_id');
    $auto_id = msc_tracking_auto_id($tracking_id);
    
    if ($tracking_id == '') {
      return view('admission.masters.index');
    }
    
    $admitted_student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id',$auto_id)->first();
    $invoice = Invoice::where('roll', $admission_roll)->where('admission_session', $admission_session)->where('level', 'Masters 2nd Year')->where('type', 'masters_admission')->orderBy('id', 'desc')->first();
    
    $payment_status = 'Paid';
    
    if ($invoice->status == 'Pending') {
      $payment_status = 'Pending';
    }else{
      $student_infos = DB::table('student_info_masters')->where('current_level', 'Masters 2nd Year')->where('session', $admitted_student->session)->where('refference_id', $admitted_student->auto_id)->get();
      
      if(count($student_infos) < 1 ){
        return $this->dutchbangla($admitted_student->auto_id);
      }
    }
    return view('admission.masters.confirmation',compact('payment_status'));
  }


  public function admisionLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('student.masters.admission.signin'); 
  }

  public function roll_generate_msc($session,$subject,$prefix){
    
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

  public  function id_generate_honours($session,$class_roll,$catagory){
    
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
    $auto_id = msc_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $invoice = DB::table('invoices')->where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_admission')->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->first();
    $admitted_student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('admission_invoice_id', $invoice_id)->where('auto_id', $auto_id)->get();
    
    
    $student = DB::table('student_info_masters')->where('current_level', 'Masters 2nd Year')->where('refference_id', $auto_id)->where('session', $config->session)->where('admission_roll', $admission_roll)->first();
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('admission.masters.slip_id', compact('student', 'invoice', 'tracking_id'));
    
    $mpdf->writeHTML($html);
    $filename = $tracking_id."_admission_slip.pdf";
    $file_path=public_path()."/download/masters/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/masters/".$filename."' target='_blank'>Click to Download</a></center>";
  }


  public function dutchbangla($auto_id){
    $autoId= $auto_id;
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    $configs = DB::table('admission_config')->where('current_level', 'Masters 2nd Year')->where('open', 1)->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    if(count($configs) > 0){
      
      $config = $configs->first();
      Session::put('admission_session', $config->session);
      
    }else{
      // admission is not open
      return $status = 4;
    }

    $student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->first();
    $invoice = Invoice::where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_admission')->where('level', 'Masters 2nd Year')->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->first();
    
    $ssc_roll= $student->ssc_roll;
    $slip_type = $invoice->slip_type;
    $pay_am_floor = $invoice->total_amount;
    
    $trxid = $invoice->trx_id;
    $pay_da= date('Y-m-d h:i:s',strtotime($invoice->txndate));
    $billid=$invoice->roll;
    
    $results =   DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->get();
    
    if(count($results)<1){
      
      return Redirect::route('student.masters.admission.mscConfirmation')->with('res','Sorry, Student not found');
      
    }
    
    foreach($results as $result){
      $auto_id = $result->auto_id;
      $st_ref_id = $result->auto_id;
      $entry_time = $result->entry_time;
      $name = $result->name;
      $father_name = $result->father_name;
      $father_income = $result->father_income;
      $mother_name = $result->mother_name;
      $birth_date = $result->birth_date;
      $gender = $result->gender;
      $permanent_email = $result->permanent_email;
      $email = $result->email;
      $password = $result->password;
      $permanent_mobile = $result->permanent_mobile;
      $contact_no = $result->contact_no;
      $photo = $result->photo;
      $religion = $result->religion;
      $blood_group = $result->blood_group;
      $permanent_village = $result->permanent_village;
      $present_village = $result->present_village;
      $permanent_po = $result->permanent_po;
      $present_po = $result->present_po;
      $permanent_ps = $result->permanent_ps;
      $present_ps = $result->present_ps;
      $permanent_dist = $result->permanent_dist;
      $present_dist = $result->present_dist;
      $guardian_name = $result->guardian_name;
      $guardian_contact = $result->guardian_contact;
      $guardian_relation = $result->guardian_relation;
      $guardian_income = $result->guardian_income;
      $guardian_occupation = $result->guardian_occupation;
      $ssc_roll = $result->ssc_roll;
      $ssc_institute = $result->ssc_institute;
      $ssc_board = $result->ssc_board;
      $ssc_gpa = $result->ssc_gpa;
      $ssc_pass_year = $result->ssc_pass_year;
      $hsc_roll = $result->hsc_roll;
      $hsc_institute = $result->hsc_institute;
      $hsc_board = $result->hsc_board;
      $hsc_gpa = $result->hsc_gpa;
      $hsc_pass_year = $result->hsc_pass_year;
      $payment_status = $result->payment_status;
      $paid_date = $result->paid_date;
      $complete_sms = $result->complete_sms;
      $sent_time = $result->sent_time;
      $status = $result->status;
      $honrs_passing_institute = $result->honrs_passing_institute;
      $honrs_passing_year = $result->honrs_passing_year;
      $honrs_passing_cgpa = $result->honrs_passing_cgpa;
      $honrs_session = $result->honrs_session;
      $from_faculty = $result->from_faculty;
      $to_faculty = $result->to_faculty;
      $from_subject = $result->from_subject;
      $to_subject = $result->to_subject;
      $subject = $result->to_subject;
      $session = $result->session;
      $admission_roll= $result->admission_roll;
      $honrs_roll = $result->honrs_roll;
      $image_name = $result->photo;
      $session = $result->session;    
    }
    $prefix='masters_2_';
    $catagory="4"; // for masters
    $class_roll= IdRollGenerate::roll_generate_msc($session,$subject,$prefix);
    $id=$class_roll; //$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
    $student_id=$id; //$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
    
    // $class_roll=substr($class_roll, 4);
    $results= DB::select("SELECT merit_pos,merit_status FROM masters_merit_list WHERE admission_roll=$admission_roll");
    foreach($results as $result){
      $merit_position = $result->merit_pos;
      $merit_status = $result->merit_status;
    }
    
    $password= $this->randomPassword();
    $pass_show=$password;
    $password=Hash::make($password);
    
    $oldPath = 'upload/college/masters/draft/'.$image_name; // publc/images/1.jpg
    if (file_exists(public_path($oldPath))) {
      $folder = public_path('upload/college/masters/'.$session);
      create_dir($folder);
      $newPath = public_path('upload/college/masters/'.$session.'/'.$image_name); // publc/images/2.jpg
      $oldPath = public_path($oldPath);
      if (\File::copy($oldPath , $newPath)) {
      }
    }
    
    
    DB::table('student_info_masters')->insert(
      array('id'=>$id, 'name'=>$name, 'class_roll'=>$class_roll, 'faculty_name'=>$to_faculty, 'dept_name'=>$to_subject, 'current_level'=>'Masters 2nd Year', 'father_name'=>$father_name, 'mother_name'=>$mother_name, 'birth_date'=>$birth_date,'blood_group' => $blood_group, 'gender'=>$gender, 'permanent_village'=>$permanent_village, 'present_village'=>$present_village, 'permanent_po'=>$permanent_po, 'present_po'=>$present_po, 'permanent_ps'=>$permanent_ps, 'present_ps'=>$present_ps, 'permanent_dist'=>$permanent_dist, 'present_dist'=>$present_dist, 'contact_no'=>$contact_no,'merit_pos'=> $merit_position,'merit_status'=> $merit_status,  'religion'=>$religion, 'guardian'=>$guardian_name, 'image'=>$image_name, 'refference_id'=>$st_ref_id, 'admission_roll'=>$admission_roll , 'hons_roll'=>$honrs_roll , 'hons_subject'=>$to_subject,'session'=>$session)
    );
    
    $d=date('Y-m-d');
    
    
    DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='masters_2_{$subject}'");
    
    DB::table('trx_id')->insert(
      array('tr_id'=>$trxid, 'amount'=>$pay_am_floor)
    ); 
    
    $date=date('Y-m-d');
    $adsession=$session;
    DB::update("update masters_admitted_student set payment_status='dbbl',paid_date='$date' where auto_id='$st_ref_id'"); 
    DB::update("update masters_merit_list set admission_status='admitted' where admission_roll='$admission_roll'");
    return Redirect::route('student.masters.admission.mscConfirmation')->with('res','টাকা সফল ভাবে জমা হয়েছে');
  }

  public function downloadMscForm(){
    $tracking_id = Session::get('tracking_id');
    $admission_roll = Session::get('admission_roll');
    $auto_id = msc_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    
    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $admitted_student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $config->session)->where('auto_id', $auto_id)->get();
    
    if (count($admitted_student) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $student = DB::table('student_info_masters')->where('current_level', 'Masters 2nd Year')->where('refference_id', $auto_id)->where('admission_roll', $admission_roll)->where('session', $config->session)->first();
    $admitted_student = $admitted_student->first();

    $invoice = DB::table('invoices')->where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $config->session)->where('type', 'masters_admission')->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->first();
    
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('admission.masters.form_id', compact('admitted_student', 'student','invoice'));
    
    $mpdf->writeHTML($html);
    $filename = $student->id.'_'.$student->session."_admission.pdf";
    $file_path=public_path()."/download/masters/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/masters/".$filename."' target='_blank'>Click to Download</a></center>";
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



}

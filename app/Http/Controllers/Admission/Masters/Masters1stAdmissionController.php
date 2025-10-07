<?php

namespace App\Http\Controllers\Admission\Masters;

use DB;
use Auth;
use Image;
use Session;
use Mpdf\Mpdf;
use IdRollGenerate;
use App\Libs\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\AdmissionStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class Masters1stAdmissionController extends Controller
{
    public function index() {
    Auth::logout();
    Session::flush();
    return view('admission.masters1st.index');

  }


  public function checkMerit(Request $request){
    $admission_roll = trim($request->msc_roll);
    if(!is_numeric($admission_roll) || strlen($admission_roll) > 10) return $status = 7;
    Session::put('admission_step',1);
    Session::put('admission_roll',$admission_roll);
    $status=1;
    
    $configs = DB::table('admission_config')->where('course', 'masters')->where('type', 'admission')->where('open', 1)->where('current_level', 'Masters 1st Year')->get();
        $current_date = date('Y-m-d');

        if (count($configs) > 0) {
            $config = $configs->first();

            if ($config->clossing_date < $current_date) {
                //date expired
                return $status = 4;
            }elseif($config->opening_date > $current_date){
                return $status = 4;
            }

            $current_level = $config->current_level;

        }else{
            return $status = 3;
        }
      
      $admission_session = $config->session;
      Session::put('admission_session',$config->session);
      Session::put('config_id',$config->id);
      Session::put('open',1);
      
      $check_merit = DB::table('masters_merit_list')->where('current_level', $current_level)->where('admission_roll', $admission_roll)->get();
      
      if (count($check_merit) > 0) {
        $merit = $check_merit->first();
        
        $invoices = Invoice::where('roll', $admission_roll)->where('type', 'masters_admission')->where('level', $current_level)->where('date_start', '>=', $config->opening_date)->where('admission_session', $config->session)->get();
        
        if(count($invoices) < 1){
          //invoice not found
          return $status = 4;
        }else{
          $invoice = $invoices->first();
          Session::put('invoice_id', $invoice->id);
        }
        
        
        Session::put('subject', $merit->subject);
        Session::put('faculty', $merit->faculty);

        Session::put('merit_status', $merit->merit_status);

        $adm_std = DB::table('masters_admitted_student')->where('current_level', 'Masters 1st Year')->where('admission_roll', $admission_roll)->where('session',$config->session)->first();
        if(!is_null($adm_std)){
          DB::table('masters_admitted_student')->where('auto_id',$adm_std->auto_id)->update(['to_faculty'=> $merit->faculty,'to_subject'=> $merit->subject]);

          Session::put('tracking_id',MSC1ST_PREF.$adm_std->auto_id);
          Session::put('masters_con', 1);
          Session::put('auto_id',$adm_std->auto_id);
          return $status = 7;
        }
        
        $student_infos = DB::table('student_info_masters')->where('current_level', 'Masters 1st Year')->where('admission_roll', $admission_roll)->where('session', $config->session)->get();
        
        if (count($student_infos) > 0) {
          // if student already admitted
          Session::put('masters_con', 1);
          $auto_id= auto_id_msc1st($student_infos[0]->refference_id);
          Session::put('tracking_id', MSC1ST_PREF.$auto_id);
          return $status = 5;
        }
        
        
        Session::put('name', $merit->name);
        Session::put('major_degree', 'major_degree');
        Session::put('merit',$merit->merit_pos); 
        Session::put('admit_faculty',$merit->faculty);
        Session::put('admit_subject',$merit->subject);
        Session::put('current_level',$current_level);
        
        // find merit student
        return $status = 1;
        
      }else{
        // if not exists student
        return $status = 2;
      }
  }

  public function faculty() {
   
    $step = Session::get('admission_step');

    if($step < 1  ){
      return view('admission.masters1st.index');
    } 

    Session::put('admission_step',2);

    return view('admission.masters1st.faculty');

  }

  public function dbblapplication(){
    $admission_roll = Session::get('admission_roll');
    $invoice_id = Session::get('invoice_id');
    $admission_session = Session::get('admission_session');

    if ($admission_roll == '' || $admission_session=='') {
      return Redirect::route('student.masters1st.admission')->with('res', 'Please try again.');
    }

    $invoice = Invoice::where('id', $invoice_id)->where('type', 'masters_admission')->where('admission_session', $admission_session)->first();

    $invoice_id = $invoice->id;

    $payment_status = $invoice->status;
    $total_amount = $invoice->total_amount;

    $addmitted_students = DB::table('masters_admitted_student')->where('session', $admission_session)->where('admission_invoice_id', $invoice->id)->get();

    if (count($addmitted_students) < 1) {
      return view('admission.masters1st.form', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
    }

    $addmitted_student = $addmitted_students->first();

    Session::put('auto_id', auto_id_msc1st($addmitted_student->auto_id));

    return view('admission.masters1st.confirmation', compact('admission_roll', 'payment_status','total_amount'));
  }

  public function admissionForm(){

    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $invoice_id = Session::get('invoice_id');
    $subject = Session::get('subject');
    $faculty = Session::get('faculty');
    $msc_roll = Session::get('msc_roll');
    $major_degree = Session::get('major_degree');
    $merit = Session::get('merit'); 
    $admit_faculty = Session::get('admit_faculty');
    $admit_subject =  Session::get('admit_subject');
    $name =  Session::get('name');

    $admission_step =  Session::get('admission_step');
    if($admission_step!=2  )
      return Redirect::route('student.masters1st.admission');

    if ($admission_roll != '' && $invoice_id != '') {

      $invoice = Invoice::where('id',$invoice_id)->where('type', 'masters_admission')->where('admission_session', $admission_session)->first();
      return view('admission.masters1st.form', compact('admission_roll', 'invoice_id', 'subject', 'faculty','msc_roll', 'major_degree', 'merit', 'admit_faculty', 'admit_subject','name'));
    }

    return redirect()->route('student.masters1st.admission');
  }

  public function mastersInformationSubmit(Request $request){

      $this->validate($request, [
        'photo' => 'required|mimes:jpeg,jpg,png|max:500000',
        'ssc_roll' => 'required|numeric',
        'hsc_roll' => 'required|numeric',
        'hsc_gpa' => 'required|numeric',
        'ssc_gpa' => 'required|numeric',
        'deg_cgpa' => 'required|numeric',
        'income' => 'required|numeric',
        'student_mobile' => 'required|numeric',
      ]);
      
      $temp_entry_time = date('Y-m-d H:i:s');
      $entry_time = date('Y-m-d H:i:s', strtotime($temp_entry_time));
      
      $admission_roll = $request->get('admission_roll');
      $invoice_id = Session::get('invoice_id');
      $current_level = Session::get('current_level');
      $config_id = Session::get('config_id');
      
      $config = DB::table('admission_config')->where('id', $config_id)->where('course', 'masters')->where('type', 'admission')->where('open', 1)->where('current_level', 'Masters 1st Year')->first();
      $admission_session = $config->session;
      
      $sub_facs = DB::select("SELECT * FROM masters_merit_list WHERE admission_roll= '$admission_roll' and current_level= '$current_level'");
      foreach($sub_facs as $sub_fac){
        $m_faculty = $sub_fac->faculty;
        $m_subject = $sub_fac->subject;
        $hons_roll = $sub_fac->hons_roll;
      }
      
      $invoices = Invoice::where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_admission')->get();
      
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
        'honrs_passing_institute'=>$request->get('deg_institution'),
        'honrs_passing_year'=>$request->get('deg_passing_year'),
        'honrs_session'=>$request->get('deg_institution_session'),
        'honrs_passing_cgpa'=>$request->get('deg_cgpa'),
        'name'=>$request->get('student_name'),
        'name_bangla'=>$request->get('name_bangla'),
        'father_name'=>$request->get('father_name'),
        'mother_name'=>$request->get('mother_name'),
        'birth_date'=>date('Y-m-d',strtotime($request->get('birth_date'))),
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
        'to_faculty'=>$m_faculty,
        'to_subject'=>$m_subject,
        'hons_roll'=>$hons_roll,
        'session'=>$config->session,
        'opening_date'=>$config->opening_date,
        'current_level'=> 'Masters 1st Year',
        'ssc_roll'=>$request->get('ssc_roll'),
        'ssc_institute'=>$request->get('ssc_institution'),
        'ssc_board'=>$request->get('ssc_board'),
        'ssc_gpa'=>$request->get('ssc_gpa'),
        'hsc_roll'=>$request->get('hsc_roll'),
        'hsc_institute'=>$request->get('hsc_institution'),
        'hsc_board'=> $request->get('hsc_board'),
        'hsc_gpa'=>$request->get('hsc_gpa'),
        'ssc_pass_year'=>$request->get('ssc_passing_year'),
        'hsc_pass_year'=>$request->get('hsc_passing_year'),
        'admission_roll'=>$request->get('admission_roll'),
        'admission_invoice_id'=> $invoice->id
      );

       $admitted_student = DB::table('masters_admitted_student')->where('session', $admission_session)->where('current_level', $current_level)->where('admission_roll',$admission_roll)->get();

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
        $auto_id= auto_id_msc1st($result->auto_id);
        $tracking_id = MSC1ST_PREF.$auto_id;
        $password =$result->password;
        $refId=$result->auto_id;
      }
      Session::put('tracking_id', $tracking_id);
      Session::put('password',$password);
      Session::put('invoice_id',$invoice->id);
      $toatalPayAmount = '';
      $d = date('Y-m-d');
      
      return Redirect::route('student.masters1st.admission.form');
    }

  public function confirmslip(){
    return view('admission.masters1st.confirmslip');
  }

  public function mastersSignin(){
      return view('admission.masters1st.sign_in_form');
    }          
  
  public function retrievepass(Request $request){
    $admission_roll = $request->admission_roll;
    $hsc_roll = $request->hsc_roll;
    
    $results = DB::table('masters_admitted_student')->where('admission_roll',$admission_roll )->where('hsc_roll',$hsc_roll)->orderBy('id', 'desc')->get();
    
    $auto_id = '';
    $password = '';
    foreach($results as $result){
      $password = $result->password;
      $auto_id = $result->auto_id;
    }
    
    $auto_id =str_pad($auto_id,'4','0',STR_PAD_LEFT);
    $auto_id = MSC1ST_PREF.$auto_id;
    echo json_encode(array($password, $auto_id));
  } 

public function mastersStudentSignin(Request $request){
  $status=0;   
  $tracking_id = trim($request->tracking_id);
  $password = trim($request->password);
  //$password = $database->passwod_encode($password);
  $number = 0;
  $query = MSC1ST_PREF;

  $current_level = 'Masters 1st Year';
  
  if (substr( $tracking_id, 0, strlen($query) ) === $query) {
    $auto_id = substr($tracking_id, strlen($query));
    $results = DB::table('masters_admitted_student')->where('auto_id',$auto_id)->where('password',$password)->get();
    $number = count($results);
  }

  if($number>0){
    Session::put('tracking_id',$tracking_id);
    Session::put('masters_con', 1);
    
    $masters_admitted = $results->first();
    Session::put('admission_roll',$masters_admitted->admission_roll);
    Session::put('admission_session', $masters_admitted->session);
    Session::put('subject', $masters_admitted->to_subject);
    Session::put('auto_id', $masters_admitted->auto_id);

    $invoices = Invoice::where('roll', $masters_admitted->admission_roll)->where('admission_session', $masters_admitted->session)->where('type', 'masters_admission')->where('level', $current_level)->where('date_start', '>=', $masters_admitted->opening_date)->orderBy('id', 'desc')->get();

    if(count($invoices) > 0){
      $invoice = $invoices->first();
      Session::put('invoice_id', $invoice->id);
        return $status=1;
      }else{
        // invoice not generated
        return $status = 5;
      }
      
    }else{
      
      // student not found
      return $status=2;
    }
    echo json_decode($status);
  }

  public function MastersConfirmation(){
    
    if(!Session::has('masters_con') ){
      return view('admission.masters1st.index');
    }
    
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    
    $tracking_id =  Session::get('tracking_id');
    $invoice_id =  Session::get('invoice_id');
    $auto_id = msc1st_tracking_auto_id($tracking_id);
    Session::put('auto_id', $auto_id);
    
    if ($tracking_id == '') {
      return view('admission.masters1st.index');
    }
    
    $admitted_student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id',$auto_id)->first();

    $invoice = Invoice::where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_admission')->orderBy('id', 'desc')->first();
    
    
    $payment_status = 'Pending';

    if ($invoice->status == 'Pending') {
      $config_count = DB::table('admission_config')->where('course', 'masters')->where('type', 'admission')->where('open', 1)->where('current_level', 'Masters 1st Year')->where('clossing_date', '>=', date('Y-m-d'))->get();

      if(count($config_count) < 1){
        return Redirect::route('student.masters1st.admission.signin')->with('warning','Sorry, Admission is Closed!');
      }

      $payment_status = 'Pending';
    }else{
      $student_infos = DB::table('student_info_masters')->where('admission_roll', $admission_roll)->where('current_level', 'Masters 1st Year')->where('session', $admitted_student->session)->where('refference_id', $admitted_student->auto_id)->get();
      
      if(count($student_infos) < 1 ){
          return $this->dutchbangla($admitted_student->auto_id);
      }
    }
    return view('admission.masters1st.confirmation',compact('payment_status'));
  }


  public function admisionLogout(){
    Auth::logout(); 
    Session::flush();
    return Redirect::route('student.masters1st.admission.signin'); 
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

  public function dutchbangla($auto_id){

    $autoId= $auto_id;
    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');
    $invoice_id = Session::get('invoice_id');
    
    $student = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->first();

    $invoice = Invoice::where('id', $invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_admission')->first();
    
    $ssc_roll= $student->ssc_roll;
    $slip_type = $invoice->slip_type;
    $pay_am_floor = $invoice->total_amount;
    
    $trxid = $invoice->trx_id;
    $pay_da= date('Y-m-d h:i:s',strtotime($invoice->txndate));
    $billid=$invoice->roll;
    $current_level=$student->current_level;
    
    $results =   DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->get();
    
    if(count($results)<1){
      
      return Redirect::route('student.masters1st.admission.signin')->with('warning','Sorry, Student not found');
      
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
      $image_name = $result->photo;
      $session = $result->session;
    }
    $prefix='masters_1_';
    $catagory="4"; // for masters
    $id= IdRollGenerate::id_generate_msc1st($session,$subject,$prefix);
    $student_id=$id; //$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
    $class_roll= IdRollGenerate::roll_generate_msc1st($id);;
    $results= DB::select("SELECT merit_pos FROM masters_merit_list WHERE admission_roll='$admission_roll' and current_level= '$current_level'");
    foreach($results as $result){
      $merit_position = $result->merit_pos;
    }
    
    $password= $this->randomPassword();
    $pass_show=$password;
    $password=Hash::make($password);
    
    $oldPath = 'upload/college/masters/draft/'.$image_name; // publc/images/1.jpg
    if (file_exists(public_path($oldPath))) {
      $folder = public_path('upload/college/masters/'.$session);
      create_dir($folder);
      $newPath = 'upload/college/masters/'.$session.'/'.$image_name; // publc/images/2.jpg
      if (\File::copy($oldPath , $newPath)) {
      }
    }

    DB::beginTransaction();

    try {

        DB::table('student_info_masters')->insert(
          array('id'=>$id, 'name'=>$name, 'class_roll'=>$class_roll, 'faculty_name'=>$to_faculty, 'dept_name'=>$to_subject, 'current_level'=>'Masters 1st Year', 'father_name'=>$father_name, 'mother_name'=>$mother_name, 'birth_date'=>$birth_date,'blood_group' => $blood_group, 'gender'=>$gender, 'permanent_village'=>$permanent_village, 'present_village'=>$present_village, 'permanent_po'=>$permanent_po, 'present_po'=>$present_po, 'permanent_ps'=>$permanent_ps, 'present_ps'=>$present_ps, 'permanent_dist'=>$permanent_dist, 'present_dist'=>$present_dist, 'contact_no'=>$contact_no,'merit_pos'=> $merit_position, 'religion'=>$religion, 'guardian'=>$guardian_name, 'image'=>$image_name, 'refference_id'=>$st_ref_id, 'admission_roll'=>$admission_roll , 'hons_subject'=>$to_subject,'session'=>$session, 'total_amount'=> $pay_am_floor)
        );

          DB::table('admission_students')->insert([
            'id' => $id,
            'class_roll' => $class_roll,
            'name' => $name,
            'admission_roll' => $admission_roll,
            'current_level' => $current_level,
            'session' => $session,
            'groups' => $to_faculty,
            'dept_name' => $to_subject,
            'course' => 'masters',
            'payment_status' => 'paid',
            'total_amount' => $invoice->total_amount,
            'transaction_id' => $invoice->trx_id,
            'exam_year' => $invoice->passing_year,
            'date' => date('Y-m-d', strtotime($invoice->update_date)),
            'slip_name' => $invoice->slip_name,
            'slip_type' => $invoice->slip_type
          ]);

        $d=date('Y-m-d');
        
        DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='masters_1_{$subject}'");
        
        DB::table('trx_id')->insert(
          array('tr_id'=>$trxid, 'amount'=>$pay_am_floor)
        ); 
        
        $date=date('Y-m-d');
        $adsession=$session;
        DB::update("update masters_admitted_student set payment_status='dbbl',paid_date='$date' where auto_id='$st_ref_id'"); 
        DB::update("update masters_merit_list set admission_status='admitted' where admission_roll='$admission_roll' and current_level = '$current_level' and session ='$session'");

        DB::commit();
      } catch (\Illuminate\Database\QueryException $e) {
        DB::rollback();
        return redirect()->route('student.masters1st.admission.signin')->with('warning', $e->errorInfo[2]);
      }

    return Redirect::route('student.masters1st.admission.MastersConfirmation')->with('res','টাকা সফল ভাবে জমা হয়েছে');    
    
  }

  public function downloadSlipId(){
    
    $tracking_id = Session::get('tracking_id');
    $auto_id = msc1st_tracking_auto_id($tracking_id);
    $invoice_id = Session::get('invoice_id');
    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $subject = Session::get('subject');
    $current_level = 'Masters 1st Year';

    $configs = DB::table('admission_config')->where('course', 'masters')->where('open', 1)->where('current_level', $current_level)->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $student = AdmissionStudent::where('admission_roll', $admission_roll)->where('current_level', $current_level)->where('session', $admission_session)->first();
    
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('admission.masters1st.slip_id', compact('student', 'tracking_id'));
    
    $mpdf->writeHTML($html);
    $filename = $tracking_id."_admission_slip.pdf";
    $file_path=public_path()."/download/masters/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/masters/".$filename."' target='_blank'>Click to Download</a></center>";
  }

  public function downloadMscForm(){
    $tracking_id = Session::get('tracking_id');
    $auto_id = msc1st_tracking_auto_id($tracking_id);
    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    
    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $configs = DB::table('admission_config')->where('course', 'masters')->where('open', 1)->where('current_level', 'Masters 1st Year')->get();
    
    if (count($configs) > 0) {
      $config = $configs->first();
    }else{
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    
    $admitted_student = DB::table('masters_admitted_student')->where('session', $config->session)->where('auto_id', $auto_id)->get();
    
    if (count($admitted_student) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    $admitted_student = $admitted_student->first();
    
    $student = DB::table('student_info_masters')->where('current_level', 'Masters 1st Year')->where('refference_id', $auto_id)->where('admission_roll', $admission_roll)->where('session', $admitted_student->session)->first();

    $adm_student = AdmissionStudent::where('admission_roll', $admission_roll)->where('current_level', 'Masters 1st Year')->where('session', $admission_session)->first();
    
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    
    $html = view('admission.masters1st.form_id', compact('admitted_student', 'student','adm_student'));
    
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

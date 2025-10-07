<?php

namespace App\Http\Controllers\Admission\Honours;

use App\Http\Controllers\Controller;
use App\Libs\Payment;
use App\Models\AdmissionStudent;
use App\Models\Invoice;
use Auth;
use DB;
use IdRollGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Image;
use Mpdf\Mpdf;
use Session;

class HonoursAdmissionController extends Controller
{
    public function index() {
    Auth::logout();
      Session::flush();
        return view('admission.honours.index');

  }


  public function check(Request $request){
    $admission_roll = $request->honours_roll;
    Session::put('admission_step',1);
    Session::put('admission_roll',$admission_roll);
    $status=1;

        $configs = DB::table('admission_config')->where('type', 'admission')->where('open', 1)->where('current_level', 'Honours 1st Year')->where('course', 'honours')->where('opening_date', '<=',date('Y-m-d'))->where('clossing_date','>=',date('Y-m-d'))->get();

        if (count($configs) > 0) {
          $config = $configs->first();

          $admission_session = $config->session;
          Session::put('admission_session',$config->session);

          $check_merit = DB::table('hons_merit_list')->where('admission_roll', $admission_roll)->get();

          if (count($check_merit) > 0) {

            $merit = $check_merit->first();

            Session::put('subject', $merit->subject);
            Session::put('faculty', $merit->faculty);
            Session::put('merit_status', $merit->merit_status);

            $invoices = Invoice::where('roll', $admission_roll)->where('type', 'honours_admission')->where('admission_session', $config->session)->where('subject', $merit->subject)->where('date_start', '>=', $config->opening_date)->orderBy('id', 'desc')->get();

            if(count($invoices) < 1){
              return $status = 4;
            }else{
              $invoice = $invoices->first();
              Session::put('invoice_id', $invoice->id);
            }

            // find merit student
            $status = 1;

            $student_infos = DB::table('student_info_hons')->where('current_level', 'Honours 1st Year')->where('admission_roll', $admission_roll)->where('dept_name', $merit->subject)->where('session', $config->session)->get();

            if (count($student_infos) > 0) {
              // if student already admitted
              Session::put('honours_con', 1);
              $auto_id= auto_id_hons($student_infos[0]->refference_id);
              Session::put('tracking_id', HONS_PREF.$auto_id);
              return $status = 5;
            }


          Session::put('name', $merit->name);

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

    if ($admission_roll == '' || $admission_session=='') {
      return Redirect::route('student.honours.admission')->with('error', 'Something Went Wrong, Please try again.');
    }

    $invoice = Invoice::where('id', $invoice_id)->where('type', 'honours_admission')->where('admission_session', $admission_session)->first();

    $invoice_id = $invoice->id;

    $payment_status = $invoice->status;
    $total_amount = $invoice->total_amount;

    $addmitted_students = DB::table('hons_admitted_student')->where('session', $admission_session)->where('subject', $subject)->orderBy('auto_id', 'desc')->get();

    if (count($addmitted_students) < 1) {
      return view('application.honours.form', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
    }

    $addmitted_student = $addmitted_students->first();

    Session::put('auto_id', auto_id_hons($addmitted_student->auto_id));

    return view('application.honours.confirmation', compact('admission_roll', 'payment_status','total_amount'));
  }

  public function admissionForm(){

    $admission_roll = Session::get('admission_roll');
    $admission_session = Session::get('admission_session');
    $invoice_id = Session::get('invoice_id');
    $subject = Session::get('subject');
    $faculty = Session::get('faculty');

    if ($admission_roll != '' && $invoice_id != '') {

      $invoice = Invoice::where('id',$invoice_id)->where('type', 'honours_admission')->where('admission_session', $admission_session)->first();
      return view('admission.honours.form', compact('admission_roll', 'invoice_id', 'subject', 'faculty'));
    }

    return redirect()->route('student.honours.admission');
  }

  public function honAdmInformationSubmit(Request $request){
    $this->validate($request, [
        'photo' => 'required|mimes:jpeg,jpg,png',
        'ssc_roll' => 'required|numeric',
        'hsc_roll' => 'required|numeric',
        'hsc_gpa' => 'required|numeric',
        'ssc_gpa' => 'required|numeric',
        'income' => 'required|numeric',
        'hostel_facilities' => 'required',
        'admission_form' => 'required'
      ]);

      $temp_entry_time = date('Y-m-d G:i:s');
      $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));

      $logo = $request->file('photo');
      $filename = rand(1, 99999999999) .'.jpg';
      $upload_path = public_path('upload/college/honours/draft/' . $filename);
      Image::make($logo->getRealPath())->save($upload_path);

      $admission_form = $request->file('admission_form');
      $admissionFromFileName = time().rand(1, 999999) .'.jpg';
      $upload_form_path = public_path('upload/college/honours/application/admission_form/' . $admissionFromFileName);
      Image::make($admission_form->getRealPath())->save($upload_form_path);

      $admission_roll = $request->get('admission_roll');
      $m_subject = Session::get('subject');
      $m_faculty = Session::get('faculty');
      $merit_status = Session::get('merit_status');

      $config = DB::table('admission_config')->where('open', 1)->where('current_level', 'Honours 1st Year')->where('course', 'honours')->first();

      $admission_session = $config->session;

      $submitted_data = array(
              'entry_time'=>$entry_time,
              'name'=>$request->get('student_name'),
              'father_name'=>$request->get('fathers_name'),
              'mother_name'=>$request->get('mothers_name'),
              'birth_date'=>$request->get('birth_date'),
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
              'hsc_roll'=>$request->get('hsc_roll'),
              'hsc_institute'=>$request->get('hsc_institution'),
              'hsc_board'=> $request->get('hsc_board'),
              'hsc_gpa'=>$request->get('hsc_gpa'),
              'faculty'=>$request->get('faculty'),
              'subject'=>$request->get('subject'),
              'photo'=>$filename,
              'password'=>$request->get('password'),
              'session'=> $admission_session ,
              'permanent_mobile'=>$request->get('student_mobile'),
              'guardian_income'=>$request->get('income'),
              'guardian_occupation'=>$request->get('occupation'),
              //'paid_date'=>$request->get('guardian_mobile'),
              //'complete_sms'=> $request->get('guardian_relation'),
              //'sent_time'=>$request->get('ssc_registration'),
              'ssc_pass_year'=>$request->get('ssc_passing_year'),
              'hsc_pass_year'=>$request->get('hsc_passing_year'),
              'ssc_reg'=>$request->get('ssc_reg'),
              'hsc_reg'=>$request->get('hsc_reg'),
              'merit_status'=>$merit_status,
              'admission_invoice_id'=>$request->get('invoice_id'),
              'admission_form' => $admissionFromFileName,
              'hostel_facilities' => $request->hostel_facilities
        );

    $admitted_student = DB::table('hons_admitted_student')->where('session', $admission_session)->where('admission_roll',$admission_roll)->where('subject', $request->get('subject'))->get();

        if(count($admitted_student) > 0){
            DB::table('hons_admitted_student')->where('auto_id', $admitted_student->first()->auto_id)
              ->update($submitted_data);
            $admitted_id = $admitted_student->first()->auto_id;
        }else{
            $admitted_id = DB::table('hons_admitted_student')
              ->insertGetId($submitted_data);
        }

        $results = DB::table('hons_admitted_student')->where('auto_id', $admitted_id)->where('session', $admission_session)->where('admission_roll',$admission_roll)->get();
        foreach($results as $result){
          $auto_id= auto_id_hons($result->auto_id);
          $tracking_id = HONS_PREF.$auto_id;
          $password =$result->password;
          $refId=$result->auto_id;
        }
      Session::put('tracking_id', $tracking_id);
      Session::put('password',$password);
                     
      return Redirect::route('student.honours.admission.form');
  }

  public function confirmslip(){
    return view('application.honours.confirmslip');
  }

  public function honSignin(){
      return view('admission.honours.sign_in_form');
    }          
  
  public function retrievepass(Request $request){
    $admission_roll = $request->admission_roll;
    $hsc_roll = $request->hsc_roll;

    $results = DB::table('hons_admitted_student')->where('admission_roll',$admission_roll )->where('hsc_roll',$hsc_roll)->orderBy('auto_id', 'desc')->get();
                                                                                 
        $auto_id = '';
        $password = '';
        foreach($results as $result){
          $password = $result->password;
          $auto_id = $result->auto_id;
        }

        $auto_id =str_pad($auto_id,'4','0',STR_PAD_LEFT);
        $auto_id = HONS_PREF.$auto_id;
        echo json_encode(array($password, $auto_id));
  } 

  public function honStudentSignin(Request $request){
      $status=0;   
      $tracking_id = trim($request->tracking_id);
      $password = trim($request->password);
      //$password = $database->passwod_encode($password);
      $number = 0;
      $query = HONS_PREF;

      if (substr( $tracking_id, 0, strlen($query) ) === $query) {
        $auto_id = substr($tracking_id, strlen($query));
        $results = DB::table('hons_admitted_student')->where('auto_id',$auto_id)->where('password',$password)->get();
        $number = count($results);
      }
      if($number>0){
        Session::put('tracking_id',$tracking_id);
        Session::put('auto_id',$auto_id);
        Session::put('honours_con', 1);

        $hons_admitted = $results->first();
        Session::put('admission_roll',$hons_admitted->admission_roll);
        Session::put('admission_session',$hons_admitted->session);

        return $status=1;
      }else{
        // admitted student not found
        return $status=2;
      }
      echo json_decode($status);
  }

  public function HonConfirmation(){

    if(!Session::has('honours_con') ){
           return view('admission.honours.index');
    }

    $admission_session = Session::get('admission_session');
    $admission_roll = Session::get('admission_roll');

    $tracking_id =  Session::get('tracking_id');
    $auto_id = hons_tracking_auto_id($tracking_id);

    if ($tracking_id == '') {
      return redirect()->route('student.honours.admission.signin')->with('error','Something went wrong, Please try again!');
    }

    $result = DB::table('hons_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id',$auto_id)->first();
    
    $invoice = Invoice::where('id', $result->admission_invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'honours_admission')->first();

    if(is_null($invoice)){
      return redirect()->route('student.honours.admission.signin')->with('error','Invoice not found. Please Contact to college!');
    }

    $payment_status = 'Paid';

    if ($invoice->status == 'Pending') {
      $payment_status = 'Pending';
      $config_count = DB::table('admission_config')->where('course', 'honours')->where('type', 'admission')->where('open', 1)->where('current_level', 'Honours 1st Year')->where('opening_date', '<=',date('Y-m-d'))->where('clossing_date', '>=', date('Y-m-d'))->get();

      if(count($config_count) < 1){
        return Redirect::route('student.honours.admission.signin')->with('warning','Sorry, Admission is Closed!');
      }

    }else{
      $student_infos = DB::table('student_info_hons')->where('current_level', 'Honours 1st Year')->where('session', $result->session)->where('refference_id', $result->auto_id)->get();

      if(count($student_infos) < 1 ){
        return $this->dutchbangla($result->auto_id);
      }
    }
      return view('admission.honours.confirmation',compact('payment_status'));
  }


  public function admisionLogout(){
   Auth::logout(); 
  Session::flush();
  return Redirect::route('student.honours.admission.signin'); 
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
      $auto_id = hons_tracking_auto_id($tracking_id);
      $admission_roll = Session::get('admission_roll');
      $admission_session = Session::get('admission_session');

      $admitted_student = DB::table('hons_admitted_student')->where('admission_roll', $admission_roll)->where('session',$admission_session)->where('auto_id', $auto_id)->first();

      $student = DB::table('student_info_hons')->where('current_level', 'Honours 1st Year')->where('refference_id', $auto_id)->where('session', $admitted_student->session)->first();

      $adm_student = AdmissionStudent::where('session', $admission_session)->where('admission_roll',$admission_roll)->where('id', $student->id)->where('dept_name', $admitted_student->subject)->where('course', 'honours')->first();

      if(is_null($adm_student)){
        return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
      }

      $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
      $mpdf->ignore_invalid_utf8 = true;
      $mpdf->autoScriptToLang = true;
      $mpdf->autoVietnamese = true;
      $mpdf->autoArabic = true;
      $mpdf->autoLangToFont = true;

      $html = view('admission.honours.slip_id', compact('student', 'adm_student', 'tracking_id'));

      $mpdf->writeHTML($html);
      $filename = $tracking_id."_admission_slip.pdf";
      $file_path=public_path()."/download/honours/";
      $mpdf->Output($file_path.'/'.$filename);
      echo "<center><a href='".url('/')."/download/honours/".$filename."' target='_blank'>Click to Download</a></center>";
}


public function dutchbangla($auto_id){
$autoId= $auto_id;
$admission_session = Session::get('admission_session');
$admission_roll = Session::get('admission_roll');

$student = DB::table('hons_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->first();
$invoice = Invoice::where('id', $student->admission_invoice_id)->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'honours_admission')->first();

if(is_null($invoice)){
  return Redirect::route('student.honours.admission.signin')->with('warning', 'Something Went Wrong, Please try again later');
}

$ssc_roll= $student->ssc_roll;
$slip_type = $invoice->slip_type;
$pay_am_floor = $invoice->total_amount;

$trxid = $invoice->trx_id;
$pay_da= date('Y-m-d h:i:s',strtotime($invoice->txndate));
$billid=$invoice->roll;

$results =   DB::table('hons_admitted_student')->where('admission_roll', $admission_roll)->where('session', $admission_session)->where('auto_id', $auto_id)->get();

  if(count($results)<1){
  
    return Redirect::route('student.honours.admission.HonConfirmation')->with('error','Sorry, Student not found');
  
  }

  foreach($results as $result){
    $name=$result->name;
    $father_name=$result->father_name;
    $mother_name=$result->mother_name;
    $birth_date=$result->birth_date;
    $blood_group=$result->blood_group;
    $gender=$result->gender;
    $perm_vill=$result->permanent_village;
    $present_villege=$result->present_village;
    $permanent_po=$result->permanent_po;
    $present_po=$result->present_po;
    $permanent_ps=$result->permanent_ps;
    $present_ps=$result->present_ps;
    $permanent_dist=$result->permanent_dist;
    $present_dist=$result->present_dist;
    $contact_no=$result->contact_no;
    $religion=$result->religion;
    $guardian_name=$result->guardian_name;
    $guardian_relation = $result->guardian_relation;
    $guardian_occupation = $result->guardian_occupation;
    $guardian_income = $result->guardian_income;
    $ssc_roll = $result->ssc_roll;
    $ssc_reg = $result->ssc_reg;
    $ssc_institute = $result->ssc_institute;
    $ssc_board = $result->ssc_board;
    $ssc_pass_year = $result->ssc_pass_year;
    $ssc_gpa = $result->ssc_gpa;
    $hsc_roll = $result->hsc_roll;
    $hsc_reg = $result->hsc_reg;
    $hsc_institute = $result->hsc_institute;
    $hsc_board = $result->hsc_board;
    $hsc_pass_year = $result->hsc_pass_year;
    $hsc_gpa = $result->hsc_gpa;
    $email = $result->email;
    $faculty=$result->faculty;
    $subject=$result->subject;
    $photo=$result->photo;
    $session=$result->session;
    $st_ref_id=$result->auto_id;
    $image_name=$result->photo;
    $admission_roll=$result->admission_roll;
    $ssc_reg=$result->ssc_reg;
    $hsc_reg=$result->hsc_reg;    
    $merit_status=$result->merit_status;    
  }
  $prefix='honours_';
  $catagory="4"; // for honours
  $id=IdRollGenerate::hons_id_generate($session,$subject,$prefix);
  $class_roll = IdRollGenerate::hons_roll_generate($id); //

 $results= DB::select("SELECT merit_pos,obtained_mark FROM hons_merit_list WHERE admission_roll=$admission_roll");
  foreach($results as $result){
  $merit_position = $result->merit_pos;
  $obtained_mark = $result->obtained_mark;
  }

        $password= $this->randomPassword();
        $pass_show=$password;
        $password=Hash::make($password);

  
  $oldPath = 'upload/college/honours/draft/'.$image_name; // publc/images/1.jpg
  if (file_exists(public_path($oldPath))) {
    $folder = public_path('upload/college/honours/'.$session);
    create_dir($folder);
    $newPath = public_path('upload/college/honours/'.$session.'/'.$image_name); // publc/images/2.jpg
    $oldPath = public_path($oldPath);
    if (\File::copy($oldPath , $newPath)) {
    }
  }

  $student_info = DB::table('student_info_hons')->where('admission_roll', $admission_roll)->where('dept_name', $subject)->where('session', $session)->get();

  if(count($student_info) < 1){

    DB::beginTransaction();

    try {
      DB::table('student_info_hons')->insert(
         array('id'=>$id, 'name'=>$name, 'class_roll'=>$class_roll, 'faculty_name'=>$faculty, 'dept_name'=>$subject, 'current_level'=>'Honours 1st Year', 'father_name'=>$father_name, 'mother_name'=>$mother_name, 'birth_date'=>$birth_date, 'gender'=>$gender, 'permanent_village'=>$perm_vill, 'present_village'=>$present_villege, 'permanent_po'=>$permanent_po, 'present_po'=>$present_po, 'permanent_ps'=>$permanent_ps, 'present_ps'=>$present_ps, 'permanent_dist'=>$permanent_dist, 'present_dist'=>$present_dist, 'contact_no'=>$contact_no, 'religion'=>$religion, 'guardian'=>$guardian_name, 'image'=>$image_name, 'refference_id'=>$st_ref_id, 'admission_roll'=>$admission_roll , 'merit_pos'=>$merit_position , 'obtained_mark'=>$obtained_mark,'session'=>$session, 'merit_status' => $merit_status, 'blood_group'=> $blood_group, 'ssc_reg'=> $ssc_reg, 'hsc_reg'=> $hsc_reg)
          );

      DB::table('admission_students')->insert([
            'id' => $id,
            'class_roll' => $class_roll,
            'name' => $name,
            'admission_roll' => $admission_roll,
            'current_level' => 'Honours 1st Year',
            'session' => $session,
            'groups' => $faculty,
            'course' => 'honours',
            'dept_name' => $subject,
            'payment_status' => 'paid',
            'total_amount' => $invoice->total_amount,
            'transaction_id' => $invoice->trx_id,
            'exam_year' => $invoice->passing_year,
            'date' => date('Y-m-d', strtotime($invoice->update_date)),
            'slip_name' => $invoice->slip_name,
            'slip_type' => $invoice->slip_type,
            'merit_status' => $merit_status,
          ]);

       $d=date('Y-m-d');
           
     
      DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='honours_{$subject}'");
      
    DB::table('trx_id')->insert(
         array('tr_id'=>$trxid, 'amount'=>$pay_am_floor)
          );
    $date=date('Y-m-d');
    $adsession=$session;
    DB::update("update hons_admitted_student set payment_status='dbbl',paid_date='$date' where auto_id='$st_ref_id'");
    DB::update("update hons_merit_list set admission_status='admitted' where admission_roll='$admission_roll'");

    DB::commit();
    } catch (\Illuminate\Database\QueryException $e) {
      DB::rollback();
      return Redirect::route('student.honours.admission.signin')->with('warning', $e->errorInfo[2]);
    }


  } 

return Redirect::route('student.honours.admission.HonConfirmation')->with('success','টাকা সফল ভাবে জমা হয়েছে, দয়া করে আপনার পেস্লিপ ডাউনলোড করুন');
}

  public function downloadHonForm(){
    $tracking_id = Session::get('tracking_id');
    $auto_id = hons_tracking_auto_id($tracking_id);

    if ($tracking_id == '') {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }

    $admitted_students = DB::table('hons_admitted_student')->where('auto_id', $auto_id)->get();

    if (count($admitted_students) < 1) {
      return '<p style="color:red">Something Went Wrong. Please Try Again.</p>';
    }
    $admitted_student = $admitted_students->first();

    $student = DB::table('student_info_hons')->where('current_level', 'Honours 1st Year')->where('refference_id', $auto_id)->where('session', $admitted_student->session)->first();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;
        $html = view('admission.honours.form_id', compact('admitted_student', 'student'));

        $mpdf->writeHTML($html);
        $filename = $student->id.'_'.$student->session."_admission.pdf";
        $file_path=public_path()."/download/honours/";
        $mpdf->Output($file_path.'/'.$filename);
        echo "<center><a href='".url('/')."/download/honours/".$filename."' target='_blank'>Click to Download</a></center>";
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

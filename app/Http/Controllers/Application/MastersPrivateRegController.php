<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Image;
use Session;
use Mpdf\Mpdf;

class MastersPrivateRegController extends Controller
{
    public function index() {
        Auth::logout();
        Session::flush();
        return view('application.mastersPrivate.index');
    }


    public function checkApplication(Request $request){
        $admission_roll = trim($request->roll);
        if(!is_numeric($admission_roll) || strlen($admission_roll) > 10) return $status = 7;
        Session::put('admission_step',1);
        Session::put('admission_roll',$admission_roll);
        $status=1;


        $configs = DB::table('admission_config')->where('course', 'masters')->where('type', 'registration')->where('open', 1)->where('current_level', 'Masters 2nd Year')->get();
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
        Session::put('opening_date', $config->opening_date);
        Session::put('clossing_date', $config->clossing_date);
        Session::put('current_level', $config->current_level);
        Session::put('admission_session', $config->session);

        $payslipheaders = DB::table('payslipheaders')->where('type', 'registration')->where('level', 'Masters 2nd Year')->where('pro_group', 'masters')->where('session', $admission_session)->get();

        if (count($payslipheaders) < 1) {
            return $status = 2;
        }else{
            $payslipheader = $payslipheaders->first();
            $admission_name = 'masters_registration_'.$payslipheader->level.'_'.$payslipheader->exam_year;
            $total_amount = DB::table('payslipgenerators')->where('payslipheader_id', $payslipheader->id)->sum('fees');

            $total_amount = (int) $total_amount;

            $invoices = Invoice::where('roll', $admission_roll)->where('level', $current_level)->where('date_start', '>=', $config->opening_date)->where('type', 'masters_registration')->orderBy('id', 'desc')->get();
            $invoice_id = '';
            if (count($invoices) > 0) {
                $invoice = $invoices->first();
                $invoice_id = $invoice->id;

                $application_student = DB::table('masters_student_applications')->where('current_level', 'Masters 2nd Year')->where('session', $admission_session)->where('admission_roll', $admission_roll)->where('exam_year', $config->exam_year)->where('registration_type', 'Registration')->get();

                if(count($application_student) > 0 || $invoice->status == 'Paid'){
                    // if student already applied exists
                    Session::put('invoice_id', $invoice_id);
                    return $status = 6;
                }

                if ($invoice->status == 'Pending') {

                    $payment_info_id = $invoice->payment_info_id;

                    DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
                           array('name'=>$admission_roll, 'admission_name'=>$admission_name , 'roll' => $admission_roll, 'pro_group' => $payslipheader->pro_group,'admission_session'=> $payslipheader->session,'slip_name'=>$payslipheader->title,'slip_type'=>$payslipheader->code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$payslipheader->start_date, 'date_end'=>$payslipheader->end_date, 'father_name'=>'', 'institute_code'=>'mmc', 'exam_year' => $payslipheader->exam_year)
                            );
                            
                    DB::table('invoices')->where('id', $invoice->id)->update(
                        array(
                            'name'=>$admission_roll, 
                            'hsc_merit_id' => 0, 
                            'type'=>'masters_registration',
                            'roll' => $admission_roll,
                            'mobile' => '',
                            'ssc_board' => '',
                            'pro_group' => $payslipheader->pro_group,
                            'level' => $current_level,
                            'passing_year' => $payslipheader->exam_year,
                            'admission_session'=>$payslipheader->session,
                            'slip_name'=>$payslipheader->title,
                            'slip_type'=>$payslipheader->code,
                            'total_amount'=>$total_amount,
                            'status'=>'Pending',
                            'date_start'=>$payslipheader->start_date, 
                            'date_end'=>$payslipheader->end_date, 
                            'father_name'=>'N/A', 
                            'institute_code'=>'mmc', 
                            'refference_id' => 0,
                            'payment_info_id' => $payment_info_id
                            )
                  );
                }else{
                    $admitted_student = DB::table('masters_application_admitted_student')->where('admission_roll',$admission_roll)->where('registration_type', 'Registration')->where('session', $admission_session)->get();
                    if (count($admitted_student) > 0) {
                        $auto= auto_id_msc($admitted_student[0]->auto_id);
                        Session::put('tracking_id', MSC2ND_PREF.$auto);
                    }
                }
            }else{
                $payment_info_id = DB::table('payment_info')->insertGetId(
                       array('name'=>$admission_roll, 'admission_name'=>$admission_name , 'roll' => $admission_roll, 'pro_group' => $payslipheader->pro_group,'admission_session'=> $payslipheader->session,'slip_name'=>$payslipheader->title,'slip_type'=>$payslipheader->code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$payslipheader->start_date, 'date_end'=>$payslipheader->end_date, 'father_name'=>'', 'institute_code'=>'mmc', 'exam_year' => $payslipheader->exam_year)
                        );
                        
                $invoice_id = DB::table('invoices')->insertGetId(
                    array(
                        'name'=>$admission_roll, 
                        'hsc_merit_id' => 0, 
                        'type'=>'masters_registration',
                        'roll' => $admission_roll,
                        'mobile' => '',
                        'ssc_board' => '',
                        'pro_group' => $payslipheader->pro_group,
                        'level' => $current_level,
                        'passing_year' => $payslipheader->exam_year,
                        'admission_session'=>$payslipheader->session,
                        'slip_name'=>$payslipheader->title,
                        'slip_type'=>$payslipheader->code,
                        'total_amount'=>$total_amount,
                        'status'=>'Pending',
                        'date_start'=>$payslipheader->start_date, 
                        'date_end'=>$payslipheader->end_date, 
                        'father_name'=>'N/A', 
                        'institute_code'=>'mmc', 
                        'refference_id' => 0,
                        'payment_info_id' => $payment_info_id
                        )
              );
            }
            Session::put('invoice_id', $invoice_id);
        }


        echo json_encode($status);
    }

    public function dbblapplication(){
        $admission_roll = Session::get('admission_roll');
        $admission_session = Session::get('admission_session');
        $invoice_id = Session::get('invoice_id');

        if ($admission_roll == '') {
            return Redirect::route('student.masters.private.reg')->with('res', 'Please try again.');
        }

        $invoice = Invoice::where('roll', $admission_roll)->where('id', $invoice_id)->where('admission_session', $admission_session)->where('type', 'masters_registration')->first();

        $invoice_id = $invoice->id;

        $payment_status = $invoice->status;
        $total_amount = $invoice->total_amount;

        $admitted_students = DB::table('masters_application_admitted_student')->where('session', $admission_session)->where('registration_type', 'Registration')->where('admission_roll',$admission_roll)->get();

        if (count($admitted_students) < 1) {
            return view('application.mastersPrivate.form', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
        }

        $student_applications = DB::table('masters_student_applications')->where('current_level', 'Masters 2nd Year')->where('session', $admission_session)->where('admission_roll', $admission_roll)->where('registration_type', 'Registration')->get();

        if(count($admitted_students) > 0 && count($student_applications) < 1 && $invoice->status == 'Paid'){
            $admitted_student = $admitted_students->first();
            DB::table('masters_student_applications')->insert(
                array('name'=>$admitted_student->name, 'current_level'=>'Masters 2nd Year', 'father_name'=>$admitted_student->father_name,'dept_name'=>$admitted_student->dept_name, 'mother_name'=>$admitted_student->mother_name, 'birth_date'=>$admitted_student->birth_date, 'gender'=>$admitted_student->gender, 'permanent_village'=>$admitted_student->permanent_village, 'present_village'=>$admitted_student->present_village, 'permanent_po'=>$admitted_student->permanent_po, 'present_po'=>$admitted_student->present_po, 'permanent_ps'=>$admitted_student->permanent_ps, 'present_ps'=>$admitted_student->present_ps, 'permanent_dist'=>$admitted_student->permanent_dist, 'present_dist'=>$admitted_student->present_dist, 'contact_no'=>$admitted_student->contact_no, 'religion'=>$admitted_student->religion, 'guardian'=>$admitted_student->guardian_name, 'image'=>$admitted_student->photo, 'refference_id'=>$admitted_student->auto_id, 'admission_roll'=>$admitted_student->admission_roll , 'session'=>$admitted_student->session,'ssc_reg'=>$admitted_student->ssc_reg,'hsc_reg'=>$admitted_student->hsc_reg,'exam_year'=> $invoice->passing_year,'registration_type'=> 'Registration', 'total_amount'=>$invoice->total_amount, 'date' =>date('Y-m-d', strtotime($invoice->updated_at)), 'admission_form'=> $admitted_student->admission_form,'hsc_transcript'=>$admitted_student->hsc_transcript)
            );
        }

        if (count($student_applications) < 1) {
            return view('application.mastersPrivate.dbblapplication', compact('admission_roll', 'payment_status','total_amount', 'invoice_id'));
        }

        if (count($admitted_students) > 0) {
            $admitted_student = $admitted_students->first();

            Session::put('auto_id', auto_id_msc($admitted_student->auto_id));
            Session::put('tracking_id',MSC2ND_PREF.$admitted_student->auto_id);
        }


        return view('application.mastersPrivate.dbblapplication', compact('admission_roll', 'payment_status','total_amount'));
    }

    public function applicationForm(){

        $admission_roll = Session::get('admission_roll');
        $invoice_id = Session::get('invoice_id');

        if ($admission_roll != ''  || $invoice_id == '') {
            return view('application.mastersPrivate.form', compact('admission_roll', 'invoice_id'));
        }

        return redirect()->route('student.masters.private.reg');
    }

    public function mscAppInformationSubmit(Request $request){
        $this->validate($request, [
            'student_name' => 'required',
            'contact_no' => 'required|numeric',
            'dept_name' => 'required',
            'father_name' => 'required',
        ]);

        $temp_entry_time = date('Y-m-d G:i:s');
        $entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));

        $admission_roll = Session::get('admission_roll');
        $invoice_id = Session::get('invoice_id');

        $config = DB::table('admission_config')->where('course', 'masters')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('type', 'registration')->first();

        $admission_session = $config->session;

        $invoices = Invoice::where('roll', $admission_roll)->where('id', $invoice_id)->where('admission_session', $admission_session)->where('type', 'masters_registration')->get();
      
        if(count($invoices) < 1){
            return redirect()->back()->with('warning', 'Your Invoice is not generated. Please contact to the college!');
        }
        $invoice = $invoices->first();

        $submitted_data = array(
                'entry_time'=>$entry_time,
                'name'=>$request->get('student_name'),
                'father_name'=>$request->get('father_name'),
                'contact_no'=>$request->get('contact_no'),
                'admission_roll'=>$admission_roll,
                'dept_name'=>$request->get('dept_name'),
                'session'=> $admission_session ,
                'registration_type' => 'Registration',
                'application_invoice_id'=>$invoice->id
          );

        $admitted_student = DB::table('masters_application_admitted_student')->where('session', $admission_session)->where('registration_type', 'Registration')->where('admission_roll',$admission_roll)->get();

        if(count($admitted_student) > 0){
            DB::table('masters_application_admitted_student')->where('auto_id', $admitted_student->first()->auto_id)
              ->update($submitted_data);
            $admitted_id = $admitted_student->first()->auto_id;
        }else{
            $admitted_id = DB::table('masters_application_admitted_student')
              ->insertGetId($submitted_data);
        }

        $admitted_student = DB::table('masters_application_admitted_student')->where('auto_id', $admitted_id)->where('session', $admission_session)->where('admission_roll',$admission_roll)->get();

        foreach($admitted_student as $result){
          $auto_id= auto_id_msc($result->auto_id);
          $tracking_id = MSC2ND_PREF.$auto_id;
          $password =$result->password;
          $refId=$result->auto_id;
        }
        Session::put('tracking_id', $tracking_id);
        Session::put('auto_id', $refId);
        Session::put('password',$password);
        $toatalPayAmount = $invoice->total_amount;
        $d = date('Y-m-d');
         
        $results2 = DB::table('payment_info')
            ->where('roll', $admission_roll)
          ->where('id',$invoice->payment_info_id)
          ->where('admission_session', $config->session)
            ->get();
            foreach($results2 as $result){
              // $toatalPayAmount = $result->total_amount;
              $headerName = $result->slip_name;
        }
            
        DB::table('payment_info')
          ->where('roll', $admission_roll)
          ->where('id',$invoice->payment_info_id)
          ->where('admission_session', $config->session)
          ->limit(1)  // optional - to ensure only one record is updated.
          ->update(array('refference_id'=>$tracking_id,'auto_id'=>$refId));

          DB::table('invoices')->where('type', 'masters_registration')->where('id', $invoice->id)->update(['name'=>$request->get('student_name'),'subject'=> $request->get('dept_name')]);

          $total_amount = $toatalPayAmount;
          $payment_status = 'Pending';

        return redirect()->route('student.masters.private.reg.dbbl');
    }

    public function confirmslip(){

        $tracking_id = Session::get('tracking_id');
        $invoice_id = Session::get('invoice_id');
        $admission_session = Session::get('admission_session');
        $admission_roll = Session::get('admission_roll');
        $auto_id = substr($tracking_id, 2);

        $invoice = Invoice::where('roll', $admission_roll)->where('id', $invoice_id)->where('admission_session', $admission_session)->where('type', 'masters_registration')->first();
        $student = DB::table('masters_student_applications')->where('current_level', 'Masters 2nd Year')->where('session', $admission_session)->where('admission_roll', $admission_roll)->where('registration_type', 'Registration')->first();
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;

        $html = view('application.mastersPrivate.slip_id', compact('student', 'invoice', 'tracking_id'));

        $mpdf->writeHTML($html);
        $filename = $tracking_id."_registration_slip.pdf";
        $file_path=public_path()."/download/masters/";
        $mpdf->Output($file_path.'/'.$filename);
        echo "<center><a href='".url('/')."/download/masters/".$filename."' target='_blank'>Click to Download</a></center>";
    }     
    public function applicationLogout(){
       Auth::logout(); 
      Session::flush();
      return Redirect::route('student.masters.private.reg'); 
    }  
}

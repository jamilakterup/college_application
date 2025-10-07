<?php

namespace App\Http\Controllers;

use App\Models\FormFillup;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use Mpdf\Mpdf;
use Auth;

class DegreeFormfillupController extends Controller
{
    public function index() {

        Session::flush();
		return view('degreeformfillup.index');

	}

	public function check(Request $request){	

      
    $prc='';
    $imp='';
    if($request->ajax())
    {
      $sid= trim($request->get('roll')); 
      $ex_year = trim($request->get('examyear'));
      $results = DB::select("SELECT * FROM student_info_degree WHERE id=$sid");
      foreach($results as $result){
        $current_level=$request->level;
        $session=$result->session;
        $name = $result->name;
        $fathers_name = $result->father_name;
        $mothers_name = $result->mother_name;
        $subject = $result->groups;
        $session = $result->session;
        $gender = $result->gender;
        $religion = $result->religion;
        $birth_date=$result->birth_date;
        $guardian_name = $result->guardian;
        $class_roll = $result->class_roll;
        $college_id  = $result->id;

      }

      if(count($results) > 0){
       Session::put('name',$name);
       Session::put('session',$session);
       Session::put('registration_id',$sid); 
       Session::put('current_level',$current_level); 
       Session::put('ex_year',$ex_year);        
       Session::put('admission_step',1);


       $level= $current_level;
       $givelavel = explode(' ', $level);
       $hons_level=$givelavel[0];
       if($hons_level=='Degree'){

        $form_fillup_result = DB::select( "SELECT * FROM form_fillup WHERE id=$sid and course='Degree' and session='$session' and  level_study='$current_level'");
        $ff_result_count=DB::table('form_fillup')->where('id',$sid )
        ->where('session',$session)
        ->where('level_study',$current_level)
        ->where('payment','paid')
        ->where('exam_year',$ex_year)
        ->where('course','Degree')
        ->count(); 

        $results = DB::table('degree_form_fillup_config')->where('open', 1)->where('current_level', $current_level)->where('clossing_date', '>=', date('Y-m-d'))->get();

        if (count($results) < 1) {
          // form fillup not open
          $status = 0;
        }

        elseif ($ff_result_count < 1) {
          // form fillup done by student
          $status = 1;

        }else{
          // form fillup not completed
          $status = 2;

        }

      }
    }
      else
        // student not found
        $status=5;
      echo json_encode($status);
    }
          
  }
	


  public function checktype(){  

  $payType= $request->get('payType'); 
  $regNumber = $request->get('student_id'); 
  Session::put('payType', $payType);
  Session::put('regNumber', $regNumber);
    return 'Ok';

}



	public function view() 
    {
    	$admission_step = Session::get('admission_step');
    	$registration_id = Session::get('registration_id');
    	$prc = Session::get('prc');
    	$imp = Session::get('imp');
    	$payment_amount='';
		$results = DB::select("SELECT * FROM payment_info WHERE roll ='".$registration_id."'");
		$count = count($results);
		if($count>0){
			foreach($results as $amount){
				$payment_amount=$amount->total_amount;
			}
		}
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
          return Redirect::route('degree.student.formfillup');
        }
        else
        {
            $student_infos=DB::table('student_info_degree')->where('id',$registration_id )->get();
           return view('degreeformfillup.view', compact('registration_id','student_infos','payment_amount','payment_status','prc','imp','admission_step'));        	
        }

    	
    }


    public function dbblPageView() 
{

  if(!Session::has('registration_id'))
    return Redirect::route('degree.student.formfillup');


  $student_id    = Session::get('registration_id');
  $group = Session::get('groups');
  $current_level = Session::get('current_level');
  $session = Session::get('admission_session');
  $examyear =   Session::get('ex_year'); 
  $invoice    = DB::table('invoices')->where('type', 'degree_form_fillup')->where('passing_year', $examyear)->where('roll', $student_id)->get();

  if (count($invoice) < 1) {
    return "<h2>No bill found for this student, please contact to college</h2>";
  }

  $student_infos = DB::table('student_info_degree')->where('id', $student_id)->get();


  $invoice = $invoice->first();
  $payment_amount = $invoice->total_amount;
  if ($invoice->status == 'Paid') {
    return Redirect::route('degree.student.formfillup.view');
  }
  
  return view('degreeformfillup.dbbl_view', compact('payment_amount', 'student_id', 'student_infos'));

}


	public function dbblApprove(){
         if(Request::ajax())
         {
            $registration_id=$request->get('registration_id'); 
            $exam_year=Session::get('ex_year');
            $trx=$request->get('registration_id');       
            $session=Session::get('session');
             $current_level=Session::get('current_level');
            $pay_am_floor=round(Session::get('payment_amount')); 
            
            $prc= Session::get('prc');
            $imp = Session::get('imp');
            $payType    = Session::get('payType');

            $student_infos=DB::select("SELECT * FROM student_info_degree WHERE id='$registration_id'");
            foreach($student_infos as $student_info){ 
            $name=$student_info->name;    
            $subject=$student_info->groups;
            $session=$student_info->session;
            }


             $result = DB::select("SELECT * FROM payment_info WHERE roll ='".$registration_id."' and status='Paid' and slip_type='".$payType."'");
             $rCount = count($result);
             if($rCount<1)
             {
             echo "দুঃখিত ! আপনি ভর্তির জন্য টাকা জমা দেননি";
             exit;
             } 
              
             if($rCount>0){

              foreach($result as $info){
                $sliphead = $info->slip_name;
              }
             } 

            $pay_da= date("Y-m-d"); //$response['payment_date'];
            $billid=$registration_id; //$response['bill_id'];

            /*echo $pay_da;
            exit;*/

            $ff_count=FormFillup::where('id',$registration_id)
                               ->where('level_study',$current_level)
                               ->where('session',$session)                               
                               ->count(); 
            if($ff_count>=1)
                {
                  echo "দুঃখিত ! এই শিক্ষার্থীর জন্য  ইতোপূর্বে টাকা জমা দেয়া হয়েছে";
                    exit;
                } 


            $entry_time = date('Y-m-d');

            // $acc_pay_status=new AccountPaymentStastus() ;
            // $acc_pay_status->student_id=$registration_id;
            // $acc_pay_status->total_amount=$pay_am_floor;                     
            // $acc_pay_status->entry_date=$entry_time;                     
            // $acc_pay_status->payment_status='Paid'; 
            // $acc_pay_status->paid_date=$entry_time; 
            // $acc_pay_status->payment_method='telecash'; 
            // $acc_pay_status->trxid=$trx; 
            // $acc_pay_status->dbbl_bill_no=$billid; 
            // $acc_pay_status->dbbl_date=$pay_da; 
            // $acc_pay_status->save();

            
 
            $ff=new FormFillup();

            $ff->id=$registration_id;
            $ff->level_study=$current_level;
            $ff->session=$session;

            $ff->groups=$subject;
            $ff->dept_name=$subject;
            $ff->course='Degree';
            $ff->payment='Paid';

            $ff->total_amount=$pay_am_floor;
            $ff->exam_year=$exam_year;
            $ff->date=$entry_time;

            $ff->slip_name=$sliphead; 

            $ff->transaction_id=$trx;

            $ff->save();
 
            DB::table('deg_merit_list')
              ->where('admission_roll', $registration_id)  
              ->limit(1)  // optional - to ensure only one record is updated.
              ->update(array('status'=>1)); 

            Session::put('student_id_found',$registration_id);

            Session::put('payment_status','Paid');
            
            Session::put('admission_step',1); 
            echo 'টাকা সফল ভাবে জমা হয়েছে,<br/>এখান থেকে <a target="_BLANK" href="view">কনফার্মেশন স্লিপ ডাউনলোড</a> করুন';

           // echo json_encode($exam_year);
         }

	}


public function createConfirmSlip(Request $request){
      if($request->ajax())
         {
            if(!Session::has('registration_id'))
                return Redirect::route('degree.student.formfillup'); 
            
            $name          = Session::get('name'); 
            $session       = Session::get('session');
            $registration_id    = Session::get('registration_id');
            $subject       = Session::get('subject');
            $current_level =  Session::get('current_level');
            $subject       = Session::get('subject');
            $exam_year =  Session::get('ex_year');
    		$ff_result=FormFillup::where('id',$registration_id )
                                        ->where('level_study',$current_level)
                                        ->where('dept_name',$subject)
                                        ->where('payment','Paid')
                                        ->where('exam_year',$exam_year)
                                        ->where('course', 'Degree')                                       
                                        ->get();


	        foreach ($ff_result as  $value) {  
	            $practical_subject   = $value->practical_subject;
	            $improvement_subject = $value->no_of_improvement_subject;     
	            $transaction_id= $value->transaction_id;
	            $total_amount = $value->total_amount;
	            $date = $value->date; 
	                  
	          }


          
		        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
            $mpdf->ignore_invalid_utf8 = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/degree_form_fillup.pdf');
            $tplId = $mpdf->ImportPage($pagecount);
            $actualsize = $mpdf->SetPageTemplate($tplId);
            $mpdf->AddPage();
		      
            $mpdf->SetFont('Times new Roman','B',16);  
		        $mpdf->WriteText(36.3, 56.3, $current_level);
		        $mpdf->SetFont('Times new Roman','',14);  
            $mpdf->WriteText(90, 67, $name);
		        $mpdf->WriteText(90, 77, $registration_id);
		        $mpdf->WriteText(90, 87, $subject);
		        $mpdf->WriteText(90, 98, $session);
		        $mpdf->WriteText(90, 108, $transaction_id);
		        $mpdf->WriteText(90, 118, $current_level);
		        
		        $mpdf->WriteText(90, 129, $transaction_id);        
		        $mpdf->WriteText(90, 140, $total_amount);
		        $mpdf->WriteText(90, 150, $date);    
            
                  

		        $pdf_name=$registration_id.'_'.$session.'_'.$subject;
		       $file_name=public_path()."/download/degree/{$pdf_name}.pdf";
			   $mpdf->Output($file_name);
				
				 echo "<center><a href='".url('/')."/download/degree/{$pdf_name}.pdf' target='_blank'>Click to Download</a></center>";
         }

}

    public function formfillupLogout(){
	  Auth::logout(); 
	  Session::flush();
	  return Redirect::route('degree.student.formfillup');
	}

	public function nextStep(){
        if(!Session::has('registration_id'))
        return Redirect::route('degree.student.formfillup');

        $student_id    = Session::get('registration_id');
        return view('degreeformfillup.paymenttype', compact('student_id'));
  }
}

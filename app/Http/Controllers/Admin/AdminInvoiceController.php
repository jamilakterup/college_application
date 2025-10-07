<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Invoice;
use App\Models\PayslipHeader;
use Illuminate\Http\Response;
use DB;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function generate_invoice(Request $request){
    	ini_set("pcre.backtrack_limit", "5000000");
    	DB::beginTransaction();

        try {
        	$payslip_header = PayslipHeader::find(request()->get('payslipheader_id'));
        	$status = 'success';
        	$msg = 'Bill Generated Successfully';
        	$status = 1;

			if ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'degree'):
				$response = $this->degree_form_fillup($payslip_header);

			elseif ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'masters'):
				$response = $this->masters_form_fillup($payslip_header);

			elseif ($payslip_header->type == 'formfillup' && $payslip_header->pro_group == 'honours'):
				$response = $this->honours_form_fillup($payslip_header);

			elseif ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'hsc'):
				$response = $this->hsc_admission($payslip_header);

			elseif ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'honours'):
				$response = $this->invoice_generate_honours_admission($payslip_header);

			elseif ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'masters' && $payslip_header->level == 'Masters 2nd Year'):
				$response = $this->masters_admission($payslip_header);

			elseif ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'masters' && $payslip_header->level == 'Masters 1st Year'):
				$response = $this->invoice_generate_masters1st_admission($payslip_header);

			elseif ($payslip_header->type == 'admission' && $payslip_header->pro_group == 'degree' && $payslip_header->level == 'Degree 1st Year'):
				$response = $this->degree_admission($payslip_header);
			else:
				$status = 0;
			endif;

			if($status == 0){
				$message = 'No Records found for invoice *'.'<b>'.$payslipheader->title.'</b>';
				return response()->json([
		            'status' => 'warning',
		            'message' => $message,
		            'table' => 'datatable',
		     		],Response::HTTP_OK);
			}

        	DB::commit();
			return $response;
    	} catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_NOT_ACCEPTABLE); // 400
        }
	}
	
	public function generate_invoice_2nd_promotion_hsc(Request $request){

	    $examyear =  $request->get('examyear');
	    $current_level =  $request->get('cur_level');
	    // $admission_session =  $request->get('session');
	    $admission_name = 'hsc_2nd_year_promotion_'.$examyear;
	    
	    $payslipheader_id = $request->payslipheader_id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
	    $group = $payslipheader->group_dept;
		// hsc first year admission

		$configs = DB::table('hsc_online_adm_config')->where('active', 1)->where('category', '2nd_year_promotion')->get();

		if (count($configs) < 1) {
			return 'Admission closed';
		}

		$config = $configs[0];

		$results = DB::select("select * from payslipheaders where pro_group='hsc' and type in('formfillup', '2nd_year_promotion') and level='$current_level' and  exam_year = '$examyear' and (group_dept='$group' or group_dept='0')");
				foreach($results as $paySlip){
					$code = $paySlip->code;
					$title = $paySlip->title;
					$start_date = $paySlip->start_date;
					$end_date = $paySlip->end_date;
					$admission_session = explode('_',$paySlip->session);
				$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
				$total_amount = 0;
				foreach($amounts as $amount){
					$total_amount = $total_amount + $amount->fees;
				}
        // $already_generated_rolls = Invoice::where('slip_type', $code)->pluck('roll');

        if ($group == '0') {
        	$student_infos_hsc = DB::table('student_info_hsc')->where('current_level', $current_level)->whereIn('session', $admission_session)->get();
        }else{
        	$student_infos_hsc = DB::table('student_info_hsc')->where('current_level', $current_level)->whereIn('session', $admission_session)->where('groups', $group)->get();

        }
        
        foreach($student_infos_hsc as $student){
        	$already_exists = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_2nd_year_promotion')->where('status', 'Pending')->get();

        	if (count($already_exists) < 1) {
	            $payment_info_id = DB::table('payment_info')->insertGetId(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->groups,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::insert(
	            array(
	                'name'=>$student->id, 
	                'hsc_merit_id' => 0, 
	                'type'=>'hsc_2nd_year_promotion' ,
	                'roll' => $student->id,
	                'mobile' => $student->contact_no,
	                'ssc_board' => '',
	                'pro_group' => $student->groups,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}else{
        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'hsc_2nd_year_promotion')->where('status', 'Pending')->first();

        		$payment_info_id = $invoice->payment_info_id;

        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->groups,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::where('id', $invoice->id)->update(
	            array(
	                'name'=>$student->id, 
	                'hsc_merit_id' => 0, 
	                'type'=>'hsc_2nd_year_promotion' ,
	                'roll' => $student->id,
	                'mobile' => $student->contact_no,
	                'ssc_board' => '',
	                'pro_group' => $student->groups,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}
		    
        }

	    $message = 'You have successfully generated invoice';
        return redirect()->route('admin.payslip_header.index')->with('success', $message);
		}
	}
	
	public function generate_invoice_others_fee_hsc(){
	    $examyear =  $request->get('examyear');
	    $current_level =  $request->get('cur_level');
	    $admission_session =  $request->get('session');
	    $admission_name = '';
	    
	    $group = '';
		// hsc first year admission

		$results = DB::select("select * from payslipheaders where pro_group='hsc' and type = 'others_fee' and level='$current_level' and  exam_year = '$examyear' and (group_dept='$group' or group_dept='0')");
				foreach($results as $paySlip){
					$code = $paySlip->code;
					$title = $paySlip->title;
					$start_date = $paySlip->start_date;
					$end_date = $paySlip->end_date;
				$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
				$total_amount = 0;
				foreach($amounts as $amount){
					$total_amount = $total_amount + $amount->fees;
				}
		$admission_name = $code;
        $already_generated_rolls = Invoice::where('slip_type', $code)->lists('roll');
        
        $student_infos_hsc = DB::table('student_info_hsc')->where('current_level', $current_level)->whereNotIn('class_roll', $already_generated_rolls)->get();
        
        foreach($student_infos_hsc as $student){
            $payment_info_id = DB::table('payment_info')->insertGetId(
			       array('name'=>$student->class_roll, 'admission_name'=>$admission_name , 'roll' => $student->class_roll, 'pro_group' => $student->groups,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
			        );
			        
		    Invoice::insert(
            array(
                'name'=>$student->class_roll, 
                'hsc_merit_id' => 0, 
                'type'=>'hsc_others_fee',
                'roll' => $student->class_roll,
                'mobile' => $student->contact_no,
                'ssc_board' => '',
                'pro_group' => $student->groups,
                'passing_year' => $examyear,
                'admission_session'=>$student->session,
                'slip_name'=>$title,
                'slip_type'=>$code,
                'total_amount'=>$total_amount,
                'status'=>'Pending',
                'date_start'=>$start_date, 
                'date_end'=>$end_date, 
                'father_name'=>'N/A', 
                'institute_code'=>INS_CODE, 
                'refference_id' => 0,
                'payment_info_id' => $payment_info_id
                )
          );
		    
        }

	    $message = 'You have successfully generated invoice';
        return redirect()->route('admin.payslip_header.index')->with('success', $message);
		}
	}

	public function invoice_generate_hsc2nd_adm(Request $request){

		set_time_limit(0);

		$payslip_header = PayslipHeader::find($request->payslipheader_id);

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    // $admission_session =  $request->get('session');
	    $formfillup_level = '';
	    $subject =  explode('_',$payslip_header->subject);

	    $admission_name = 'hsc2nd_admission_'.$current_level.'_'.$examyear;
	    
	    $payslipheader_id = $request->payslipheader_id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
	    $group = $payslipheader->group_dept;
		// hsc first year admission

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', $current_level)->where('course', 'hsc')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			return 'Admission closed';
		}

		$config = $configs[0];
		$results = PayslipHeader::where('id',$request->payslipheader_id)->get();
		foreach($results as $paySlip){
			$code = $paySlip->code;
			$title = $paySlip->title;
			$start_date = $paySlip->start_date;
			$end_date = $paySlip->end_date;
			$admission_session = explode('_',$paySlip->session);
			$formfillup_type = $paySlip->formfillup_type;
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}
        // $already_generated_rolls = Invoice::where('slip_type', $code)->pluck('roll');
		if($formfillup_type == 'regular'){
			$formfillup_type = ['regular','private'];
		}elseif($formfillup_type =='cc'){
			$formfillup_type = 'cc';
		}

		$formfillup_type = (array) $formfillup_type;

        if ($group == '0') {
        	$hsc2nd_merit_list = DB::table('hsc2nd_merit_list')->where('status', '1')->where('current_level', $current_level)->whereIn('session', $admission_session)->get();
        }else{
        	$hsc2nd_merit_list = DB::table('hsc2nd_merit_list')->where('status', '1')->where('current_level', $current_level)->whereIn('session', $admission_session)->where('faculty_name', $group)->get();
        }

        foreach($hsc2nd_merit_list as $student){
        	$already_exists = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('subject', $student->faculty_name)->where('type', 'hsc_2nd_admission')->where('date_start', '>=', $start_date)->where('level', $current_level)->where('status', 'Pending')->orderBy('id' , 'desc')->get();

        	if (count($already_exists) < 1) {
	            $payment_info_id = DB::table('payment_info')->insertGetId(
				       array('name'=>$student->name, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::insert(
		            array(
		                'name'=>$student->name, 
		                'hsc_merit_id' => 0, 
		                'type'=>'hsc_2nd_admission',
		                'roll' => $student->id,
		                'mobile' => '',
		                'ssc_board' => '',
		                'pro_group' => $student->faculty_name,
		                'subject' => $student->faculty_name,
		                'level' => $current_level,
		                'passing_year' => $examyear,
		                'admission_session'=>$student->session,
		                'slip_name'=>$title,
		                'slip_type'=>$code,
		                'total_amount'=>$total_amount,
		                'status'=>'Pending',
		                'date_start'=>$start_date, 
		                'date_end'=>$end_date, 
		                'father_name'=>'N/A', 
		                'institute_code'=>INS_CODE, 
		                'refference_id' => 0,
		                'payment_info_id' => $payment_info_id
		            )
	          );
        	}else{

        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('subject', $student->faculty_name)->where('type', 'hsc_2nd_admission')->where('date_start', '>=', $start_date)->where('level', $current_level)->where('status', 'Pending')->orderBy('id' , 'desc')->first();

        		$payment_info_id = $invoice->payment_info_id;

        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::where('id', $invoice->id)->update(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'hsc_2nd_admission',
	                'roll' => $student->id,
	                'mobile' => '',
	                'ssc_board' => '',
	                'pro_group' => $student->faculty_name,
	                'subject' => $student->faculty_name,
	                'level' => $current_level,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}
		    
        }

	    $message = 'You have successfully generated invoice';
        return redirect()->route('admin.payslip_header.index')->with('success', $message);
		}
	}

	public function invoice_generate_degree_formfillup(Request $request){
		set_time_limit(0);

		$query = PayslipHeader::where('id',$request->payslipheader_id);


		$payslip_headers = $query->get();

		if(count($payslip_headers) < 1){
			return 'wrong';
		}

		$payslip_header = $payslip_headers->first();
	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $admission_name = 'degree_form_fillup_'.$current_level.'_'.$examyear;
	    $formfillup_level = '';
	    $code = $payslip_header->code;
		$title = $payslip_header->title;
		$start_date = $payslip_header->start_date;
		$end_date = $payslip_header->end_date;
		$student_type = $payslip_header->student_type;
		$total_papers = $payslip_header->total_papers;
	    $subject =  explode('_',$payslip_header->subject);
	    $admission_session = explode('_',$payslip_header->session);
		$formfillup_type = explode('_',$payslip_header->formfillup_type);
	    $group = $payslip_header->group_dept;

		$configs = DB::table('form_fillup_config')->where('open', 1)->where('current_level', $current_level)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			return 'Form Fillup closed';
		}

		$config = $configs[0];

		$query_stu = DB::table('student_info_degree_formfillup')->whereIn('session', $admission_session)->where('current_level', $current_level);

		$config = Configurations()::where('details->session', $session)->where('details->current_level', $current_level)->where('details->type', 'formfillup')->get();

        if(count($config) < 1){
            return 'Please Setup Configurations';
        }

        $config_details = json_decode($config->first()->details);

        $general_min_sub = $config_details->general->min_length;

		if($group != '0'){
			$query_stu->where('faculty_name', $group);
		}

		if($student_type != ''){
			$query_stu->where('student_type', $student_type);
		}

		if(count(filter_empty_array($subject)) > 0){
			$query_stu->whereIn('dept_name', $subject);
		}

		if(count(filter_empty_array($formfillup_type)) > 0){
			$query_stu->whereIn('registration_type', $formfillup_type);
		}

		if(in_array('cc', filter_empty_array($formfillup_type))){
			$query_stu->where('faculty_name', 'cc');
		}

		$query->where('invoice_status', 0);

		$student_info_degree_formfillup = $query_stu->get();

        foreach($student_info_degree_formfillup as $student){
        	$already_exists = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'degree_form_fillup')->where('date_start', '>=', $start_date)->where('level', $current_level)->where('status', 'Pending')->get();

        	if (count($already_exists) < 1) {
	            $payment_info_id = DB::table('payment_info')->insertGetId(
				       array('name'=>$student->name, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'degree_form_fillup' ,
	                'roll' => $student->id,
	                'mobile' => '',
	                'ssc_board' => '',
	                'pro_group' => $student->faculty_name,
	                'level' => $current_level,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}else{

        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'degree_form_fillup')->where('date_start', '>=', $start_date)->where('level', $current_level)->where('status', 'Pending')->first();

        		$payment_info_id = $invoice->payment_info_id;

        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::where('id', $invoice->id)->update(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'degree_form_fillup',
	                'roll' => $student->id,
	                'mobile' => '',
	                'ssc_board' => '',
	                'pro_group' => $student->faculty_name,
	                'level' => $current_level,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}
		    
        }

	    $message = 'You have successfully generated invoice';
        return redirect()->route('admin.payslip_header.index')->with('success', $message);
	}

	public function invoice_generate_masters_formfillup(Request $request){

		set_time_limit(0);
		ini_set("pcre.backtrack_limit", "5000000");

		$query = PayslipHeader::where('id',$request->payslipheader_id);


		$payslip_headers = $query->get();

		if(count($payslip_headers) < 1){
			return 'wrong';
		}

		$payslip_header = $payslip_headers->first();
	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $admission_name = 'masters_form_fillup_'.$current_level.'_'.$examyear;
	    $formfillup_level = '';
	    $code = $payslip_header->code;
		$title = $payslip_header->title;
		$start_date = $payslip_header->start_date;
		$end_date = $payslip_header->end_date;
		$student_type = $payslip_header->student_type;
		$total_papers = $payslip_header->total_papers;
	    $subject =  explode('_',$payslip_header->subject);
	    $admission_session = explode('_',$payslip_header->session);
		$formfillup_type = explode('_',$payslip_header->formfillup_type);
	    $group = $payslip_header->group_dept;

		$configs = DB::table('form_fillup_config')->where('open', 1)->where('current_level', $current_level)->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			return 'Form Fillup closed';
		}

		$config = $configs[0];

		$query_stu = DB::table('student_info_masters_formfillup')->whereIn('session', $admission_session)->where('current_level', $current_level);

		if($group != '0'){
			$query_stu->where('faculty_name', $group);
		}

		if($student_type != ''){
			$query_stu->where('student_type', $student_type);
		}

		if(count(filter_empty_array($subject)) > 0){
			$query_stu->whereIn('dept_name', $subject);
		}

		if(count(filter_empty_array($formfillup_type)) > 0){
			$query_stu->whereIn('registration_type', $formfillup_type);
		}

		if(in_array('cc', filter_empty_array($formfillup_type))){
			$query_stu->where('faculty_name', 'cc');
		}

		$query->where('invoice_status', 0);

		$student_info_masters_formfillup = $query_stu->get();

        foreach($student_info_masters_formfillup as $student){

        	if($student->selectable == 0){

        		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payslip_header->id");
				$total_amount = 0;
				foreach($amounts as $amount){
					$total_amount = $total_amount + $amount->fees;
				}

        		$ff_subject = DB::table('formfillup_subjects')->where('subject', $student->dept_name)->where('current_level', $current_level)->whereIn('session', $admission_session)->first();

        		$headers = PayslipHeader::where('level', $current_level)->whereIn('session', $admission_session)->where('exam_year', $examyear)->where('formfillup_type', 'improvement')->where('total_papers', '<=',$ff_subject->min_general_length)->get();

        		if(count($headers) < 1){
        			return 'no header found for irregular student';
        		}

        		if($student->registration_type == 'general' && $student->registration_type == 'irregular'){

        			if($student->total_papers <= $ff_subject->min_general_length){
	        			$header = PayslipHeader::where('level', $current_level)->whereIn('session', $admission_session)->where('exam_year', $examyear)->where('type', 'formfillup')->where('formfillup_type', 'improvement')->where('total_papers', $student->total_papers)->first();

	        			$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $header->id");
						$total_amount = 0;
						foreach($amounts as $amount){
							$total_amount = $total_amount + $amount->fees;
						}
        			}

        		}

        		if($student->registration_type == 'special' && $student->registration_type == 'irregular'){

        			if($student->total_papers <= $ff_subject->min_special_length){
        				
	        			$header = PayslipHeader::where('level', $current_level)->whereIn('session', $admission_session)->where('exam_year', $examyear)->where('type', 'formfillup')->where('formfillup_type', 'improvement')->where('total_papers', $student->total_papers)->first();

	        			$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $header->id");
						$total_amount = 0;
						foreach($amounts as $amount){
							$total_amount = $total_amount + $amount->fees;
						}
        			}

        		}

	        	$already_exists = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('subject', $student->dept_name)->where('type', 'masters_form_fillup')->where('date_start', '>=', $start_date)->where('student_type', $student->student_type)->where('level', $current_level)->where('status', 'Pending')->orderBy('id' , 'desc')->get();

	        	if (count($already_exists) < 1) {
		            $payment_info_id = DB::table('payment_info')->insertGetId(
					       array('name'=>$student->name, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
					        );
					        
				    Invoice::insert(
		            array(
		                'name'=>$student->name, 
		                'hsc_merit_id' => 0, 
		                'type'=>'masters_form_fillup' ,
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
		                'student_type'=>$student->student_type,
		                'registration_type'=>$student->registration_type,
		                'total_papers'=>$student->total_papers,
		                'pay_type'=>$student->pay_type,
		                'date_start'=>$start_date, 
		                'date_end'=>$end_date, 
		                'father_name'=>'N/A', 
		                'institute_code'=>INS_CODE, 
		                'refference_id' => 0,
		                'payment_info_id' => $payment_info_id
		                )
		          );
	        	}else{

	        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('subject', $student->dept_name)->where('type', 'masters_form_fillup')->where('date_start', '>=', $start_date)->where('student_type', $student->student_type)->where('level', $current_level)->where('status', 'Pending')->orderBy('id' , 'desc')->first();

	        		$payment_info_id = $invoice->payment_info_id;

	        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
					       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
					        );
					        
				    Invoice::where('id', $invoice->id)->update(
		            array(
		                'name'=>$student->name, 
		                'hsc_merit_id' => 0, 
		                'type'=>'masters_form_fillup',
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
		                'student_type'=>$student->student_type,
		                'registration_type'=>$student->registration_type,
		                'total_papers'=>$student->total_papers,
		                'pay_type'=>$student->pay_type,
		                'status'=>'Pending',
		                'date_start'=>$start_date, 
		                'date_end'=>$end_date, 
		                'father_name'=>'N/A', 
		                'institute_code'=>INS_CODE, 
		                'refference_id' => 0,
		                'payment_info_id' => $payment_info_id
		                )
		          );
	        	}
        	}

        	DB::table('student_info_masters_formfillup')->where('id', $student->id)->whereIn('session', $admission_session)->where('current_level', $current_level)->update(['invoice_status' => 1]);

		    
        }

	    $message = 'You have successfully generated invoice';
        return redirect()->route('admin.payslip_header.index')->with('success', $message);
	}


	public function invoice_generate_honours_formfillup(Request $request){
		set_time_limit(0);

		$payslip_header = PayslipHeader::find($request->payslipheader_id);

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $group_dept =  $payslip_header->group_dept;
	    $subject =  explode('_',$payslip_header->subject);
	    // $admission_session =  $request->get('session');

	    $admission_name = 'honours_form_fillup_'.$current_level.'_'.$examyear.'_regular';
	    
	    $payslipheader_id = $request->payslipheader_id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
	    $group = default_zero($payslipheader->group_dept);
		// hsc first year admission

		$configs = DB::table('hons_form_fillup_config')->where('open', 1)->where('current_level', $current_level)->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			return 'Admission closed';
		}

		$config = $configs[0];
		$results = PayslipHeader::where('id',$request->payslipheader_id)->get();
		foreach($results as $paySlip){
			$code = $paySlip->code;
			$title = $paySlip->title;
			$start_date = $paySlip->start_date;
			$end_date = $paySlip->end_date;
			$admission_session = explode('_',$paySlip->session);
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $student_info_hons_formfillup = DB::table('student_info_hons_formfillup')->where('current_level', $current_level)->whereIn('session', $admission_session)->whereIn('dept_name', $subject)->whereIn('registration_type', ['regular','irregular_regular'])->get();

        foreach($student_info_hons_formfillup as $student){
        	$already_exists = Invoice::where('roll', $student->id)->where('date_start', '>=', $paySlip->start_date)->where('passing_year', $examyear)->where('subject', $student->dept_name)->where('type', 'honours_form_fillup')->where('admission_session', $student->session)->where('level', $current_level)->where('status', 'Pending')->get();

        	$already_exists_paid = Invoice::where('roll', $student->id)->where('date_start', '>=', $paySlip->start_date)->where('passing_year', $examyear)->where('subject', $student->dept_name)->where('type', 'honours_form_fillup')->where('admission_session', $student->session)->where('level', $current_level)->where('status', 'Paid')->get();

        	if(count($already_exists_paid) > 0){
        		continue;
        	}

        	if (count($already_exists) < 1) {
	            $payment_info_id = DB::table('payment_info')->insertGetId(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::insert(
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
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}else{

        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'honours_form_fillup')->where('level', $current_level)->where('date_start', '>=', $paySlip->start_date)->where('admission_session', $student->session)->where('status', 'Pending')->first();

        		$payment_info_id = $invoice->payment_info_id;

        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::where('id', $invoice->id)->update(
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
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}
		    
        }

        	$id = $request->payslipheader_id;
        	$count = PayslipHeader::where('id', '<=', $id)->count();
        	$page = ceil($count/Study::paginate());

		    $message = 'You have successfully generated invoice';
		    
	        return redirect()->route('admin.payslip_header.index',['page' => $page])
			        ->with('success', $message)
			        ->withId($id);
		}
	}

	public function invoice_generate_honours_formfillup_without_reg(Request $request){

		$payslip_header = PayslipHeader::find($request->payslipheader_id);

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $group_dept =  $payslip_header->group_dept;
	    $subject =  $payslip_header->subject;
	    // $admission_session =  $request->get('session');

	    $admission_name = 'honours_form_fillup_'.$current_level.'_'.$examyear.'_regular';
	    
	    $payslipheader_id = $request->payslipheader_id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
	    $group = default_zero($payslipheader->group_dept);
		// hsc first year admission

		$configs = DB::table('hons_form_fillup_config')->where('open', 1)->where('current_level', $current_level)->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			return 'Admission closed';
		}

		$config = $configs[0];
		$results = PayslipHeader::where('id',$request->payslipheader_id)->get();
		foreach($results as $paySlip){
			$code = $paySlip->code;
			$title = $paySlip->title;
			$start_date = $paySlip->start_date;
			$end_date = $paySlip->end_date;
			$admission_session = explode('_',$paySlip->session);
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}
  //       $already_generated_rolls = Invoice::where('slip_type', $code)->pluck('roll');
		// if($group == '0'){
		// 	$student_info_hons = DB::table('student_info_hons')->where('current_level', $current_level)->whereIn('session', $admission_session)->get();
		// }elseif ($group != '0' && $subject == '0') {
  //       	$student_info_hons = DB::table('student_info_hons')->where('current_level', $current_level)->whereIn('session', $admission_session)->where('faculty_name', $group)->get();
  //       }else{
  //       	$student_info_hons = DB::table('student_info_hons')->where('current_level', $current_level)->whereIn('session', $admission_session)->where('dept_name', $subject)->where('faculty_name', $group)->get();

  //       }

        $student_info_hons = DB::table('student_info_hons')->where('current_level', $current_level)->whereIn('session', $admission_session)->where('dept_name', $subject)->get();

        foreach($student_info_hons as $student){
        	$already_exists = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'honours_form_fillup')->where('slip_type', $code)->where('status', 'Pending')->get();

        	if (count($already_exists) < 1) {
	            $payment_info_id = DB::table('payment_info')->insertGetId(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'honours_form_fillup' ,
	                'roll' => $student->id,
	                'mobile' => $student->contact_no,
	                'ssc_board' => '',
	                'pro_group' => $student->faculty_name,
	                'subject' => $student->dept_name,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}else{

        		$invoice = Invoice::where('roll', $student->id)->where('passing_year', $examyear)->where('type', 'honours_form_fillup')->where('slip_type', $code)->where('status', 'Pending')->first();

        		$payment_info_id = $invoice->payment_info_id;

        		DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
				       array('name'=>$student->id, 'admission_name'=>$admission_name , 'roll' => $student->id, 'pro_group' => $student->faculty_name.'_'.$student->dept_name,'admission_session'=> $student->session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$total_amount,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>INS_CODE, 'exam_year' => $examyear)
				        );
				        
			    Invoice::where('id', $invoice->id)->update(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'honours_form_fillup',
	                'roll' => $student->id,
	                'mobile' => $student->contact_no,
	                'ssc_board' => '',
	                'pro_group' => $student->faculty_name,
	                'subject' => $student->dept_name,
	                'passing_year' => $examyear,
	                'admission_session'=>$student->session,
	                'slip_name'=>$title,
	                'slip_type'=>$code,
	                'total_amount'=>$total_amount,
	                'status'=>'Pending',
	                'date_start'=>$start_date, 
	                'date_end'=>$end_date, 
	                'father_name'=>'N/A', 
	                'institute_code'=>INS_CODE, 
	                'refference_id' => 0,
	                'payment_info_id' => $payment_info_id
	                )
	          );
        	}
		    
        }

        	$id = $request->payslipheader_id;
        	$count = PayslipHeader::where('id', '<=', $id)->count();
        	$page = ceil($count/Study::paginate());

		    $message = 'You have successfully generated invoice';
		    
	        return redirect()->route('admin.payslip_header.index',['page' => $page])
			        ->with('success', $message)
			        ->withId($id);
		}
	}

	public function hsc_admission($payslip_header){
		$payslip_header = PayslipHeader::find($payslip_header->id);

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;

	    $admission_name = 'hsc_admission_'.$current_level.'_'.$examyear;
	    
	    $payslipheader_id = $payslip_header->id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
		$group = explode('_',$payslip_header->group_dept);
		// hsc first year admission

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', $current_level)->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			$message = 'Admission Closed';
			return response()->json([
	            'status' => 'error',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
		}

		$config = $configs[0];
		$paySlip = PayslipHeader::where('id',$payslip_header->id)->first();
		$code = $paySlip->code;
		$title = $paySlip->title;
		$start_date = $paySlip->start_date;
		$end_date = $paySlip->end_date;
		$admission_session = explode('_',$paySlip->session);
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $query = DB::table('hsc_merit_list')->whereIn('session', $admission_session);

		if(count(filter_empty_array($group)) > 0){
			$query->whereIn('ssc_group', $group);
		}

        $hsc_merit_list = $query->get();

        if (count($hsc_merit_list) < 1) {
			$message = 'No Merit List Found for this paySlip';
			return response()->json([
	            'status' => 'error',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
        }

        foreach($hsc_merit_list as $student){
        	$already_exists = Invoice::where('roll', $student->ssc_roll)->where('passing_year', $student->passing_year)->where('ssc_board', $student->ssc_board)->where('admission_session', $student->session)->where('type', 'hsc_admission')->where('pro_group', $student->ssc_group)->orderBy('id', 'desc')->where('status', 'Pending')->get();

        	if (count($already_exists) < 1) {
			    Invoice::insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'hsc_admission' ,
	                'roll' => $student->ssc_roll,
	                'mobile' => '',
	                'ssc_board' => $student->ssc_board,
	                'pro_group' => $student->ssc_group,
	                'subject' => $student->ssc_group,
	                'level' => 'HSC 1st Year',
	                'passing_year' => $student->passing_year,
	                'admission_session'=>$student->session,
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

        		$invoice = Invoice::where('roll', $student->ssc_roll)->where('passing_year', $student->passing_year)->where('type', 'hsc_admission')->where('admission_session', $student->session)->where('pro_group', $student->ssc_group)->orderBy('id', 'desc')->where('status', 'Pending')->first();
				        
			    Invoice::where('id', $invoice->id)->update(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'hsc_admission',
	                'roll' => $student->ssc_roll,
	                'mobile' => '',
	                'ssc_board' => $student->ssc_board,
	                'pro_group' => $student->ssc_group,
	                'subject' => $student->ssc_group,
	                'level' => 'HSC 1st Year',
	                'passing_year' => $student->passing_year,
	                'admission_session'=>$student->session,
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

		$message = 'You have successfully generated invoice for *'.'<b>'.$payslip_header->title.'</b>';
		return response()->json([
            'status' => 'success',
            'message' => $message,
            'table' => 'datatable',
            'id' => $payslip_header->id
     		],Response::HTTP_OK);
	}

	// start honours admission invoice genrate
	public function invoice_generate_honours_admission($payslip_header){
	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $subject =  explode('_',$payslip_header->subject);
	    $admission_session = explode('_',$payslip_header->session);
		$formfillup_type = explode('_',$payslip_header->formfillup_type);
	    $group = explode('_',$payslip_header->group_dept);
	    $code = $payslip_header->code;
		$title = $payslip_header->title;
		$start_date = $payslip_header->start_date;
		$end_date = $payslip_header->end_date;

	    $admission_name = 'honours_admission_'.$current_level.'_'.$examyear;

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', $current_level)->where('clossing_date', '>=', date('Y-m-d'))->where('course', 'honours')->get();

		if (count($configs) < 1) {
			$message = 'Admission Closed';
			return response()->json([
	            'status' => 'danger',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
		}

		$config = $configs[0];

		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payslip_header->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $query_hons_merit = DB::table('hons_merit_list');

        if(count(filter_empty_array($group)) > 0){
			$query_hons_merit->whereIn('faculty', $group);
		}

		if(count(filter_empty_array($subject)) > 0){
			$query_hons_merit->whereIn('subject', $subject);
		}

        $hons_merit_list = $query_hons_merit->where('admission_status', 'running')->get();

        foreach($hons_merit_list as $student){
        	$already_exists = Invoice::where('roll', $student->admission_roll)->where('type', 'honours_admission')->whereIn('admission_session', $admission_session)->where('date_start', '>=', $config->opening_date)->where('subject', $student->subject)->orderBy('id', 'desc')->get();

        	if (count($already_exists) < 1) {

			    Invoice::insert(
		            array(
		                'name'=>$student->name, 
		                'hsc_merit_id' => 0, 
		                'type'=>'honours_admission' ,
		                'roll' => $student->admission_roll,
		                'mobile' => '',
		                'pro_group' =>  $student->faculty,
		                'subject' =>  $student->subject,
		                'level' => 'Honours 1st Year',
		                'passing_year' => $examyear,
		                'admission_session'=>$admission_session[0],
		                'slip_name'=>$title,
		                'slip_type'=>$code,
		                'total_amount'=>$total_amount,
		                'status'=>'Pending',
		                'date_start'=>$start_date, 
		                'date_end'=>$end_date, 
		                'father_name'=>'N/A',
		                'institute_code'=> INS_CODE, 
		                'refference_id' => 0
		            )
	          );
        	}else{

        		$already_exists = Invoice::where('roll', $student->admission_roll)->where('type', 'honours_admission')->whereIn('admission_session', $admission_session)->where('date_start', '>=', $config->opening_date)->where('subject', $student->subject)->where('status', 'Pending')->orderBy('id', 'desc')->get();

        		if (count($already_exists) > 0) {
	        		$invoice = Invoice::where('roll', $student->admission_roll)->where('type', 'honours_admission')->whereIn('admission_session', $admission_session)->where('date_start', '>=', $config->opening_date)->where('subject', $student->subject)->where('status', 'Pending')->orderBy('id', 'desc')->first();
					        
				    Invoice::where('id', $invoice->id)->update(
			            array(
			                'name'=>$student->name, 
			                'hsc_merit_id' => 0, 
			                'type'=>'honours_admission' ,
			                'roll' => $student->admission_roll,
			                'mobile' => '',
			                'pro_group' =>  $student->faculty,
	                		'subject' =>  $student->subject,
	                		'level' => 'Honours 1st Year',
			                'passing_year' => $examyear,
			                'admission_session'=>$admission_session[0],
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
        }

	    $message = 'You have successfully generated invoice for *'.'<b>'.$payslip_header->title.'</b>';
		return response()->json([
            'status' => 'success',
            'message' => $message,
            'table' => 'datatable',
            'id' => $payslip_header->id
     		],Response::HTTP_OK);
	}


	// start masters 2nd invoice generate

	public function masters_admission($payslip_header){
	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
		$group_dept = $payslip_header->group_dept;
	    $group_dept =  $group_dept != '' ? explode('_',$group_dept) : 0;
	    $subject =  $payslip_header->subject;
	    $subject = $subject != '' ? explode('_',$subject) : 0;
	    // $admission_session =  $request->get('session');

	    $admission_name = 'masters-admission_'.$current_level.'_'.$examyear;
	    
	    $payslipheader_id = $payslip_header->id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			$message = 'Admission Closed';
			return response()->json([
	            'status' => 'danger',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
		}

		$config = $configs[0];
		$results = PayslipHeader::where('id',$payslipheader_id)->get();
		foreach($results as $paySlip){
			$code = $paySlip->code;
			$title = $paySlip->title;
			$start_date = $paySlip->start_date;
			$end_date = $paySlip->end_date;
			$admission_session = explode('_',$paySlip->session);
			$subjects = explode('_',$paySlip->subject);
			$payslip_subject = $paySlip->subject;
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $query_msc_merit = DB::table('masters_merit_list');

        $m_subjects = filter_empty_array($subjects);
        $m_group_dept = filter_empty_array($group_dept);

        if(count($m_subjects) > 0)
        	$query_msc_merit->whereIn('subject', $m_subjects);
        if (count($m_group_dept) > 0)
        	$query_msc_merit->whereIn('faculty', $m_group_dept);

        $msc_merit_list = $query_msc_merit->where('admission_status','running')->get();
        foreach($msc_merit_list as $student){
        	$already_exists = Invoice::where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->get();

        	if (count($already_exists) < 1) {
			    Invoice::insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'masters_admission' ,
	                'roll' => $student->admission_roll,
	                'mobile' => '',
	                'pro_group' =>  $student->faculty,
	                'subject' =>  $student->subject,
	                'level' => 'Masters 2nd Year',
	                'passing_year' => $examyear,
	                'admission_session'=>$admission_session[0],
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

        		$already_exists = Invoice::where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->where('status', 'Pending')->get();

        		if (count($already_exists) > 0) {
	        		$invoice = Invoice::where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->where('status', 'Pending')->first();
					        
				    Invoice::where('id', $invoice->id)->update(
			            array(
			                'name'=>$student->name, 
			                'hsc_merit_id' => 0, 
			                'type'=>'masters_admission' ,
			                'roll' => $student->admission_roll,
			                'mobile' => '',
			                'pro_group' =>  $student->faculty,
			                'subject' =>  $student->subject,
			                'level' => 'Masters 2nd Year',
			                'passing_year' => $examyear,
			                'admission_session'=>$admission_session[0],
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
		    
        }

	    $message = 'You have successfully generated invoice for *'.'<b>'.$payslip_header->title.'</b>';
		return response()->json([
            'status' => 'success',
            'message' => $message,
            'table' => 'datatable',
            'id' => $payslip_header->id
     		],Response::HTTP_OK);
		}
	}

	// start masters 2nd invoice generate

	public function invoice_generate_masters1st_admission($payslip_header){
		set_time_limit(0);
		ini_set("pcre.backtrack_limit", "5000000");

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $admission_name = 'masters_admission_'.$current_level.'_'.$examyear;
	    $formfillup_level = '';
	    $code = $payslip_header->code;
		$title = $payslip_header->title;
		$start_date = $payslip_header->start_date;
		$end_date = $payslip_header->end_date;
		$student_type = $payslip_header->student_type;
		$total_papers = $payslip_header->total_papers;
	    $subject =  explode('_',$payslip_header->subject);
	    $admission_session = explode('_',$payslip_header->session);
		$formfillup_type = explode('_',$payslip_header->formfillup_type);
	    $group = explode('_',$payslip_header->group_dept);
	    $payslipheader_id = $payslip_header->id;

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', $current_level)->where('course', 'masters')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			$message = 'Admission Closed';
			return response()->json([
	            'status' => 'warning',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
		}

		$config = $configs[0];

		$query_msc_merit = DB::table('masters_merit_list')->whereIn('session', $admission_session)->where('current_level', $current_level);

		if(count(filter_empty_array($group)) > 0){
			$query_msc_merit->whereIn('faculty', $group);
		}

		if(count(filter_empty_array($subject)) > 0){
			$query_msc_merit->whereIn('subject', $subject);
		}

		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payslip_header->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $msc_merit_list = $query_msc_merit->get();
        foreach($msc_merit_list as $student){
        	$masters_applications = DB::table('masters_student_applications')->where("admission_roll", $student->admission_roll)->where("session", $config->session)->where('current_level', 'Masters 1st Year')->get();

        	if(count($masters_applications) < 1){
        		// continue;
        	}

        	$already_exists = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->get();

        	if (count($already_exists) < 1) {

			    DB::table('invoices')->insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'masters_admission',
	                'roll' => $student->admission_roll,
	                'mobile' => '',
	                'pro_group' =>  $student->faculty,
	                'subject' =>  $student->subject,
	                'level' => 'Masters 1st Year',
	                'passing_year' => $examyear,
	                'admission_session'=>$admission_session[0],
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

        		$already_exists = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->where('status', 'Pending')->get();

        		if (count($already_exists) > 0) {
	        		$invoice = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'masters_admission')->where('subject', $student->subject)->where('date_start','>=', $config->opening_date)->orderBy('id', 'desc')->where('status', 'Pending')->first();
					        
				    DB::table('invoices')->where('id', $invoice->id)->update(
			            array(
			                'name'=>$student->name, 
			                'hsc_merit_id' => 0, 
			                'type'=>'masters_admission' ,
			                'roll' => $student->admission_roll,
			                'mobile' => '',
			                'pro_group' =>  $student->faculty,
			                'subject' =>  $student->subject,
			                'level' => 'Masters 1st Year',
			                'passing_year' => $examyear,
			                'admission_session'=>$admission_session[0],
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
		    
        }

        $message = 'You have successfully generated invoice for *'.'<b>'.$payslip_header->title.'</b>';
		return response()->json([
            'status' => 'success',
            'message' => $message,
            'table' => 'datatable',
            'id' => $payslip_header->id
     		],Response::HTTP_OK);
	}

	public function degree_admission($payslip_header){
		ini_set("pcre.backtrack_limit", "5000000");

	    $examyear =  $payslip_header->exam_year;
	    $current_level =  $payslip_header->level;
	    $group_dept =  $payslip_header->group_dept;
	    $subject =  $payslip_header->subject;
	    $subject = $subject != '' ? explode('_',$subject) : 0;
	    // $admission_session =  $request->get('session');

	    $admission_name = 'degree-admission_'.$current_level.'_'.$examyear;
	    
	    $payslipheader_id = $payslip_header->id;
	    $payslipheader = DB::table('payslipheaders')->where('id', $payslipheader_id)->first();
	    $group = default_zero($payslipheader->group_dept);
		// hsc first year admission
		$count = PayslipHeader::where('id', '<=', $payslipheader_id)->count();
        $page = ceil($count/Study::paginate());

		$configs = DB::table('admission_config')->where('open', 1)->where('current_level', $current_level)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->get();

		if (count($configs) < 1) {
			$message = 'Admission Closed';
			return response()->json([
	            'status' => 'warning',
	            'message' => $message,
	     		],Response::HTTP_NOT_ACCEPTABLE);
		}

		$config = $configs[0];
		$results = PayslipHeader::where('id',$payslipheader_id)->get();
		foreach($results as $paySlip){
			$code = $paySlip->code;
			$title = $paySlip->title;
			$start_date = $paySlip->start_date;
			$end_date = $paySlip->end_date;
			$admission_session = explode('_',$paySlip->session);
			$subjects = explode('_',$paySlip->subject);
			$payslip_subject = $paySlip->subject;
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
		$total_amount = 0;
		foreach($amounts as $amount){
			$total_amount = $total_amount + $amount->fees;
		}

        $query_msc_merit = DB::table('deg_merit_list');

        $m_subjects = filter_empty_array($subjects);

        if(count($m_subjects) > 0)
        	$query_msc_merit->whereIn('groups', $m_subjects);

        $msc_merit_list = $query_msc_merit->get();
        foreach($msc_merit_list as $student){
        	$degree_applications = DB::table('degree_student_applications')->where("admission_roll", $student->admission_roll)->where("session", $config->session)->where('current_level', 'Degree 1st Year')->get();

        	if(count($degree_applications) < 1){
        		// continue;
        	}
        	
        	$already_exists = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'degree_admission')->where('subject', $student->groups)->where('date_start','>=', $start_date)->orderBy('id', 'desc')->get();

        	if (count($already_exists) < 1) {
				        
			    DB::table('invoices')->insert(
	            array(
	                'name'=>$student->name, 
	                'hsc_merit_id' => 0, 
	                'type'=>'degree_admission' ,
	                'roll' => $student->admission_roll,
	                'mobile' => '',
	                'pro_group' =>  $student->groups,
	                'subject' =>  $student->groups,
	                'level' => 'Degree 1st Year',
	                'passing_year' => $examyear,
	                'admission_session'=>$admission_session[0],
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

        		$already_exists = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'degree_admission')->where('subject', $student->groups)->where('date_start','>=', $start_date)->orderBy('id', 'desc')->where('status', 'Pending')->get();

        		if (count($already_exists) > 0) {
	        		$invoice = DB::table('invoices')->where('roll', $student->admission_roll)->where('type', 'degree_admission')->where('subject', $student->groups)->where('date_start','>=', $start_date)->orderBy('id', 'desc')->where('status', 'Pending')->first();
					        
				    DB::table('invoices')->where('id', $invoice->id)->update(
			            array(
			                'name'=>$student->name, 
			                'hsc_merit_id' => 0, 
			                'type'=>'degree_admission' ,
			                'roll' => $student->admission_roll,
			                'mobile' => '',
			                'pro_group' =>  $student->groups,
			                'subject' =>  $student->groups,
			                'level' => 'Degree 1st Year',
			                'passing_year' => $examyear,
			                'admission_session'=>$admission_session[0],
			                'slip_name'=>$title,
			                'slip_type'=>$code,
			                'total_amount'=>$total_amount,
			                'status'=>'Pending',
			                'date_start'=>$start_date, 
			                'date_end'=>$end_date, 
			                'father_name'=>'N/A', 
			                'institute_code'=>INS_CODE, 
			                'refference_id' => 0,
			                'payment_info_id' =>0
			                )
		          );
        		}

        	}
		    
        }

		$message = 'You have successfully generated invoice for *'.'<b>'.$payslip_header->title.'</b>';
		return response()->json([
            'status' => 'success',
            'message' => $message,
            'table' => 'datatable',
            'id' => $payslip_header->id
     		],Response::HTTP_OK);
		}

	}
}

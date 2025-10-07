<?php

namespace App\Libs;
use App\Models\FormFillup;
use App\Models\Invoice;
use App\Models\StudentInfoHsc;
use App\Models\HSCStudentAdmInfo;
use DB;
use Illuminate\Http\Response;
use Session;

class Payment{

	public static function approve($transaction_id, $type = null){
		$biller_code = config('settings.college_biller_id');
		$validator_passes = false;

		$response = self::validate_invoice($transaction_id, $type);

		if($response['status'] == '402'){
            return $response;
        }elseif($response['status'] == '200'){
            $validator_passes = true;
        }

        if(!$validator_passes){
        	$response = [
                'status'=>'402',
                'msg'=>"Something Went Wrong, Please try Again!"
            ];
            return $response;
        }

        DB::beginTransaction();

        try {
        	$invoice = Invoice::find($response['invoice_id']);
        	$pdate = $response['pdate'];
        	$msg = $response['msg'];
        	$trx_total_amount = $response['total_amount'];
            $udate = date('Y-m-d', strtotime($pdate));

        	$invoice->status = 'Paid';
            $invoice->update_date = date('Y-m-d H:i:s', strtotime($pdate));
            $invoice->trx_id = $transaction_id;
            $invoice->txnid = $transaction_id;
            $invoice->txndate = $pdate;
            $invoice->payerMobileNo = '';
            $invoice->payForMobileNo = '';
            $invoice->biller_code = $biller_code;
            $invoice->update();

          	DB::table('trx_id')->insert(
               array('tr_id'=>$transaction_id, 'amount'=>$trx_total_amount)
                );

          	if($invoice->type == 'hsc_form_fillup'){

          		$student_info_hsc_formfillup = DB::table('student_info_hsc_formfillup')->where('id', $invoice->roll)->where('current_level', $invoice->level)->first();

	          $ff_student = DB::table('form_fillup')->where('id',$invoice->roll)
	          ->where('session',$invoice->admission_session)
	          ->where('level_study',$invoice->level)
	          ->where('payment','paid')
	          ->where('exam_year',$invoice->passing_year)
	          ->where('course','HSC')
	          ->get();

	          if(count($ff_student) < 1){
	            $ff=new FormFillup();
	            $ff->id=$invoice->roll;
	            $ff->student_id=$invoice->student_id;
	            $ff->name=$invoice->name;
	            $ff->level_study= $invoice->level;
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
	          }
          	}else if($invoice->type == 'honours_form_fillup'){
                $current_level = $invoice->level;
                
                $ff=new FormFillup();
                $ff->name= $invoice->name;
                $ff->id=$invoice->roll;
                $ff->level_study= $invoice->level;
                $ff->session=$invoice->admission_session;
                $ff->groups=$invoice->pro_group;
                $ff->dept_name=$invoice->subject;
                $ff->student_type=$invoice->student_type;
                $ff->formfillup_type=$invoice->registration_type;
                $ff->pay_type=$invoice->pay_type;
                $ff->total_papers=$invoice->total_papers;
                $ff->course='Honours';
                $ff->payment='Paid';
                $ff->total_amount= $invoice->total_amount;
                $ff->exam_year=$invoice->passing_year;
                $ff->date=$udate;
                $ff->slip_name= $invoice->slip_name;
                $ff->slip_type= $invoice->slip_type;
                $ff->transaction_id=$transaction_id;
                $ff->save();
            }else if($invoice->type == 'masters_form_fillup'){
                $current_level = $invoice->level;
                
                $ff=new FormFillup();
                $ff->name= $invoice->name;
                $ff->id=$invoice->roll;
                $ff->level_study= $invoice->level;
                $ff->session=$invoice->admission_session;
                $ff->groups=$invoice->pro_group;
                $ff->dept_name=$invoice->subject;
                $ff->student_type=$invoice->student_type;
                $ff->formfillup_type=$invoice->registration_type;
                $ff->pay_type=$invoice->pay_type;
                $ff->total_papers=$invoice->total_papers;
                $ff->course='Masters';
                $ff->payment='Paid';
                $ff->total_amount= $invoice->total_amount;
                $ff->exam_year=$invoice->passing_year;
                $ff->date=$udate;
                $ff->slip_name= $invoice->slip_name;
                $ff->slip_type= $invoice->slip_type;
                $ff->transaction_id=$transaction_id;
                $ff->save();
            }else if($invoice->type == 'degree_form_fillup'){
                $current_level = $invoice->level;

                $ff = new FormFillup();
                $ff->id=$invoice->roll;
                $ff->name=$invoice->name;
                $ff->level_study= $current_level;
                $ff->session=$invoice->admission_session;
                $ff->groups=$invoice->pro_group;
                $ff->dept_name=$invoice->subject;
                $ff->student_type=$invoice->student_type;
                $ff->formfillup_type=$invoice->registration_type;
                $ff->pay_type=$invoice->pay_type;
                $ff->total_papers=$invoice->total_papers;
                $ff->course='Degree';
                $ff->payment='Paid';
                $ff->total_amount= $invoice->total_amount;
                $ff->exam_year=$invoice->passing_year;
                $ff->date=$udate;
                $ff->slip_name= $invoice->slip_name;
                $ff->slip_type= $invoice->slip_type;
                $ff->transaction_id=$transaction_id;
                $ff->save();
            }else if($invoice->type == 'hsc_2nd_admission'){
                $student_info_hsc = DB::table('hsc2nd_merit_list')->where('id', $invoice->roll)->first();

                $current_level = $invoice->level;
                
                $ff = new HSCStudentAdmInfo();
                $ff->id=$invoice->roll;
                $ff->name=$invoice->name;
                $ff->level_study= $current_level;
                $ff->session=$invoice->admission_session;
                $ff->groups=$student_info_hsc->groups;
                $ff->course='HSC';
                $ff->payment='Paid';
                $ff->total_amount= $invoice->total_amount;
                $ff->exam_year=$invoice->passing_year;
                $ff->date=$udate;
                $ff->slip_name= $invoice->slip_name;
                $ff->slip_type= $invoice->slip_type;
                $ff->transaction_id=$transaction_id;
                $ff->save();

                $student = StudentInfoHsc::where("id", $invoice->roll)->where('current_level', 'HSC 1st Year')->first();

                if(!is_null($student)){
                    $student->current_level = 'HSC 2nd Year';
                    $student->save();
                }
            }

          DB::commit();

          $response = [
                'status'=>'200',
                'msg'=>'টাকা সম্পূর্ণভাবে সফল হয়েছে, দয়া করে আপনার পেস্লিপ ডাউনলোড করুন'
            ];
          return $response;

        } catch (Exception $e) {
          DB::rollback();
          	$response = [
                'status'=>'402',
                'msg'=>"Something Went Wrong, Please try Again!"
            ];
            return $response;
        }

        return $response;
	}

	public static function validate_invoice($transaction_id, $type=null){
		$passes = false;
		$trx_ids = array_unique(explode(',', $transaction_id));
        $total_amount = 0;

        $invoice_id = Session::get('invoice_id');

        if($invoice_id == ''){
        	$response = [
                'status'=>'402',
                'msg'=>'Invoice Not Found, Please Try Again',
            ];
            return $response;
        }

        $invoice = Invoice::where('id', $invoice_id)->first();

        if(is_null($invoice)){
        	$msg = "Something Went Wrong, Please Try Again";
            $response = [
	            'status'=>'402',
	            'msg'=> $msg
            ];
            return $response;
        }

        foreach ($trx_ids as $trx_id) {
          $transaction_array = get_info_by_dbbl_trxid($trx_id);

          if($transaction_array['response']=='Error')
          {
            $msg = "দুঃখিত ! আপনার TrxID টি ভুল হয়েছে, দয়া করে যাচাই করে নিন";
            $response = [
	            'status'=>'402',
	            'msg'=> $msg
            ];
            return $response;
          }
          if($transaction_array['response']!='ok'){
              $msg = "Please Try After Sometime";
              $response = [
                 'status'=>'402',
                 'msg'=> $msg
	          ];
	          return $response;
          }
          //$addRoll
          if($transaction_array['response']=='ok' && $transaction_array['bill_id']!=$invoice->roll)
          // if($transaction_array['response']=='ok' && $transaction_array['bill_id']!=$invoice->roll && $invoice->roll !='1712612405')
          {
            $msg = "Sorry! Your TrxID does not match with your Registration ID";
            $response = [
                 'status'=>'402',
                 'msg'=> $msg
	          ];
	          return $response;
          }
          $results =  DB::select('select * from trx_id where tr_id= "'.$transaction_array['trx_id'].'"');
          if(count($results)>0){
            $msg = 'Sorry, This transaction number already used';
            $response = [
                 'status'=>'402',
                 'msg'=> $msg
	        ];
	          return $response;
          }

          if($transaction_array['response'] =='ok')
          {
          	$passes = true;
            $total_amount += $transaction_array['amount'];
            $pdate = date('Y-m-d', strtotime($transaction_array['payment_date']));
          }
        }

        if($total_amount < $invoice->total_amount){
          	$msg = "দুঃখিত ! আপনি ভর্তির জন্য পর্যাপ্ত টাকা জমা দেননি";
            $response = [
                 'status'=>'402',
                 'msg'=> $msg
	        ];
	        return $response;
        }

        if($passes){
        	$msg = 'টাকা সম্পূর্ণভাবে সফল হয়েছে, দয়া করে আপনার পেস্লিপ ডাউনলোড করুন';
            $response = [
                 'status'=>'200',
                 'invoice_id' => $invoice->id,
                 'pdate' => $pdate,
                 'total_amount' => $total_amount,
                 'msg'=> $msg
	        ];
	        return $response;

        }else{
        	$msg = "Sometime Went Wrong, Please Try Again Later!";
            $response = [
                 'status'=>'402',
                 'msg'=> $msg
	        ];
	        return $response;
        }




	}

}
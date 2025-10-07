<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FormFillupHscPromotion;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;
use Auth;

class HscPromotionController extends Controller
{
	public function index()
	{

		Session::flush();
		return view('hsc_promotion.index');
	}

	public function check(Request $request)
	{

		$status_delay = 0;
		$status = 0;
		if ($request->ajax()) {
			$roll = trim($request->get('roll'));
			$count_roll = strlen($roll);

			$session = trim($request->get('session'));
			$group = trim($request->get('group'));
			$examyear = trim($request->get('session'));
			Session::put('examyear', $examyear);
			$results = DB::select("SELECT * FROM student_info_hsc WHERE id='$roll' AND groups='$group'");

			foreach ($results as $result) {
				$student_id = $result->id;
				$current_level = $result->current_level;
				$session = $result->session;
				$group = $result->groups;
				$admission_session = $result->session;
				Session::put('groups', $group);
				Session::put('admission_session', $admission_session);
				Session::put('student_id_found', $student_id);
			}



			if ($results != null) {

				// $results =DB::select("SELECT * FROM hsc_form_fillup_delay WHERE student_id='$roll'");
				// foreach($results as $result){
				// 	$status_delay = $result->status;

				// }

				// if($status_delay==0 && $status_delay!=null)
				// {
				// 	$status=10;
				// 	echo json_encode($status);
				// 	exit;
				// }  


				$level = $current_level;
				$givelavel = explode(' ', $level);
				$hsc_level = $givelavel[0];

				if ($hsc_level == 'HSC') {

					$ff_result = DB::table('form_fillup_hsc_promotion')->where('id', $student_id)

						->where('level_study', $current_level)
						->where('dept_name', $group)
						->where('payment', 'Paid')
						->where('exam_year', $examyear)
						->get();
					$ff_result_count = DB::table('form_fillup_hsc_promotion')->where('id', $student_id)

						->where('level_study', $current_level)
						->where('dept_name', $group)
						->where('payment', 'paid')
						->where('exam_year', $examyear)
						->count();
					$count = 1;

					// $ff_config_result=DB::table('hsc_form_fillup_config')->where('current_level',$current_level )

					// ->get();

					// $count=  DB::table('hsc_form_fillup_config')->where('current_level',$current_level )

					// ->count()  ;              							           
					// $config_session =  DB::table('hsc_form_fillup_config')->where('current_level',$current_level )->where('open', 1)->first();

					Session::put('session', $session);


					if ($count != 0) {
						// foreach($ff_config_result as $result){
						// 	$session = $result->session;
						// 	$current_level = $result->current_level;  
						// 	$open1 = $result->open; 

						// 	$clossing_date = $result->clossing_date;
						// }

						// $date1=date_create($clossing_date);
						//         //$date2=date_create(date('Y-m-d'));

						// $date2=new DateTime(date('Y-m-d'));
						// $diff=date_diff($date2,$date1);
						// $daysbetween= $diff->format("%R%a ");

						/*echo json_encode($daysbetween);
               exit();*/
						if ($count != 0 && $ff_result_count != 0) {

							Session::put('student_id_found', $student_id);
							Session::put('current_level', $current_level);
							Session::put('admission_step', 1);
							Session::put('roll', $roll);

							if ($ff_result_count > 0) {
								Session::put('payment_status', 'Paid');
							}
							$status = 1;
							if ($ff_result_count <= 0) {
								$status = 8;
							}
						}
						// else if($open1==0)
						// {
						// 	$status=2;
						// }
						// else if($daysbetween<0)
						// {

						// 	$status=4;
						// }
						// else if($exam_year!=$year)
						// {

						// 	$status=3;
						// }


						else {

							$status = 8;
						}
					} else  $status = 7;
				} else
					$status = 6;
			} else
				$status = 5;

			echo json_encode($status);
		}
	}
	public function view()
	{
		$student_id    = Session::get('student_id_found');
		$admission_step = Session::get('admission_step');
		$current_level = Session::get('current_level');
		$payment_info_id = Session::get('payment_info_id');

		$payment_amount = '';
		$roll =  Session::get('roll');
		$results = DB::select("SELECT * FROM invoices WHERE roll ='" . $student_id . "'");
		$count = count($results);
		if ($count > 0) {
			foreach ($results as $amount) {
				$payment_amount = $amount->total_amount;
			}
		}
		if (Session::has('payment_status')) {
			$payment_status = Session::get('payment_status');
			//$payment_amount=4000;

		} else {
			$payment_status = 'Pending';
			//$payment_amount=4000;
		}

		// return $payment_status;

		if ($admission_step < 1) {
			return Redirect::route('hsc.promotion');
		} else {
			$student_infos = DB::table('student_info_hsc')->where('id', $student_id)->get();
			return view('hsc_promotion.view', compact('student_infos', 'student_id', 'payment_amount', 'payment_status', 'admission_step'));
		}
	}

	public function dbblPageView()
	{
		if (!Session::has('student_id_found'))
			return Redirect::route('hsc.promotion');


		$student_id    = Session::get('student_id_found');
		$group = Session::get('groups');
		$current_level = Session::get('current_level');
		$session = Session::get('admission_session');
		$examyear =   Session::get('examyear');
		$invoice    = DB::table('invoices')->where('type', 'hsc_2nd_year_promotion')->where('passing_year', $examyear)->where('roll', $student_id)->get();

		if (count($invoice) < 1) {
			return "<h2>No bill found for this student, please contact to college</h2>";
		}
		$invoice = $invoice->first();

		$payType = DB::table('payslipheaders')->where('code', $invoice->slip_type)->first()->id;
		Session::put('payType', $payType);

		$code = '';
		$admission_name = 'hsc_formfillup_' . $examyear;
		$results_payslip = DB::select("select * from payslipheaders where id = $payType");
		foreach ($results_payslip as $result_payslip) {
			$code = $result_payslip->code;
			$title = $result_payslip->title;
			$start_date = $result_payslip->start_date;
			$end_date = $result_payslip->end_date;
		}
		$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $payType");

		$total_amount = 0;
		foreach ($amounts as $amount) {
			$total_amount = $total_amount + $amount->fees;
		}


		$results_count = DB::select('select *,payment_info.id payment_info_id from payment_info join invoices on payment_info.id = invoices.payment_info_id where payment_info.roll ="' . $student_id . '" AND payment_info.slip_type="' . $code . '"');

		$payment_info_id = $results_count[0]->payment_info_id;

		$results = DB::select("select * from payment_info where id='$payment_info_id'");
		foreach ($results as $payInfo) {
			$payment_amount = $payInfo->total_amount;
			$slip_type = $payInfo->slip_type;
			$institute_code = $payInfo->institute_code;
			$slip_name = $payInfo->slip_name;
			$payment_info_id = $payInfo->id;
		}

		$invoices = DB::select("SELECT * FROM invoices WHERE roll ='" . $student_id . "'");
		$count = count($invoices);
		if ($count > 0) {
			foreach ($invoices as $amount) {
				$payment_amount = $amount->total_amount;
			}
		}

		$student_infos = DB::table('student_info_hsc')->where('id', $student_id)->get();

		Session::put('payment_amount', $payment_amount);
		Session::put('payment_info_id', $payment_info_id);
		return view('hsc_promotion.dbbl', compact('payment_amount', 'student_id', 'slip_type', 'institute_code', 'slip_name', 'payment_info_id', 'student_infos'));
	}

	public function dbblApprove(Request $request)
	{
		if ($request->ajax()) {
			$student_id = $request->get('student_id');
			$trx = $request->get('trx');
			$payType    = Session::get('payType');
			$current_level = 'HSC 2nd Year';
			$exam_year = Session::get('ex_year');
			$pay_am = Session::get('payment_amount');
			$pay_am_floor = round($pay_am);
			$student_infos = DB::select("SELECT * FROM student_info_hsc WHERE id='$student_id'");
			foreach ($student_infos as $student_info) {
				$name = $student_info->name;
				$groups = $student_info->groups;
				$session = $student_info->session;
			}
			$roll = $student_id;

			//$results = DB::select("SELECT * FROM payment_info WHERE roll ='".$roll."' and admission_name='hsc_admission' and slip_type='$payType'");

			$payment_info_id = Session::get('payment_info_id');
			$results = DB::select("SELECT * FROM payment_info WHERE id ='" . $payment_info_id . "'");
			foreach ($results as $info) {
				$sliphead = $info->slip_name;
			}

			foreach ($results as $payInfo) {
				$payment_amount = $payInfo->total_amount;
			}

			$results =  DB::select('select * from trx_id where tr_id= "' . $trx . '"');

			if (count($results) > 0) {

				return 'Sorry, This transaction number already used';
			}

			$check_invoice_count = DB::table('invoices')->where('payment_info_id', $payment_info_id)->where('txnid', $trx)->where('status', 'Paid')->count();
			if ($check_invoice_count < 1) {
				return "দুঃখিত ! আপনার TrxID টি ভুল হয়েছে, দয়া করে যাচাই করে নিন";
			}


			$check_invoice = DB::table('invoices')->where('payment_info_id', $payment_info_id)->where('txnid', $trx)->where('status', 'Paid')->first();

			$trxid = $trx;
			$pay_da = $check_invoice->txndate;
			$billid = '';
			$pay_am_floor = $check_invoice->total_amount;




			$pay_da = date("Y-m-d"); //$response['payment_date'];
			$billid = $student_id; //$response['bill_id'];

			/*echo $pay_da;
            exit;*/
			$examyear =   Session::get('examyear');
			$ff_count = FormFillupHscPromotion::where('id', $student_id)
				->where('level_study', $current_level)
				->where('session', $session)
				->count();


			$entry_time = date('Y-m-d');

			// $acc_pay_status=new AccountPaymentStastus() ;
			// $acc_pay_status->student_id=$student_id;
			// $acc_pay_status->total_amount=$pay_am_floor;                     
			// $acc_pay_status->entry_date=$entry_time;                     
			// $acc_pay_status->payment_status='Paid'; 
			// $acc_pay_status->paid_date=$entry_time; 
			// $acc_pay_status->payment_method='dbbl'; 
			// $acc_pay_status->trxid=$trx; 
			// $acc_pay_status->dbbl_bill_no=$billid; 
			// $acc_pay_status->dbbl_date=$pay_da; 
			// $acc_pay_status->slip_name=$sliphead; 
			// $acc_pay_status->save();
			$udate = date('Y-m-d');

			DB::update("update payment_info set trx_id='$trx', status='Paid', payment_type = 'DBBL' , update_date='$udate' where id='$payment_info_id'");
			DB::table('trx_id')->insert(
				array('tr_id' => $trx, 'amount' => $payment_amount)
			);

			$ff = new FormFillupHscPromotion();
			$ff->id = $student_id;
			$ff->level_study = $current_level;
			$ff->session = $session;
			$ff->groups = $groups;
			$ff->dept_name = $groups;
			$ff->course = 'HSC';
			$ff->payment = 'Paid';
			$ff->total_amount = $pay_am_floor;
			$ff->exam_year = $examyear;
			$ff->date = $entry_time;
			$ff->slip_name = $sliphead;
			$ff->transaction_id = $trx;
			$ff->payment_info_id = $payment_info_id;
			$ff->save();

			Session::put('student_id_found', $student_id);
			Session::put('payment_status', 'Paid');

			Session::put('admission_step', 1);

			DB::update("update student_info_hsc set current_level='HSC 2nd Year' where id  ='" . $roll . "'");
			echo 'টাকা সফল ভাবে জমা হয়েছে,<br/>এখান থেকে <a target="_BLANK" href="view">কনফার্মেশন স্লিপ ডাউনলোড</a> করুন';

			// echo json_encode($exam_year);
		}
	}

	public function createConfirmSlip(Request $request)
	{
		if ($request->ajax()) {
			if (!Session::has('student_id'))
				return Redirect::route('hsc.promotion');


			// $class_roll = Session::get('roll');
			$class_roll = Session::get('student_id');
			$session       = Session::get('session');
			$admission_session = Session::get('admission_session');

			//$student_info=DB::table('student_info_hsc')->where('session', $session)->where('class_roll',$class_roll )->get();

			$student_info = DB::table('student_info_hsc')->where('id', $class_roll)->get();

			$name          = $student_info[0]->name;
			$session = $student_info[0]->session;
			$student_id    = $student_info[0]->id;
			//$current_level =  Session::get('current_level');
			$current_level =  $student_info[0]->current_level;
			$subject       = Session::get('subject');


			$ff_result = FormFillupHscPromotion::where('id', $class_roll)
				//->where('session',$session)
				->where('level_study', $current_level)
				->where('dept_name', $subject)
				->where('payment', 'paid')
				->get();


			foreach ($ff_result as  $value) {
				$paid_amount   = $value->total_amount;
				$date          = $value->date;
				$transaction_id = $value->transaction_id;
			}

			if ($subject == 'science')
				$subject = 'Science';
			else  if ($subject == 'arts')
				$subject = 'HUMANITIES';
			else  if ($subject == 'commerce')
				$subject = 'BUSINESS STUDIES';

			$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'times']);
			$mpdf->ignore_invalid_utf8 = true;
			$mpdf->autoScriptToLang = true;
			$mpdf->autoVietnamese = true;
			$mpdf->autoArabic = true;
			$mpdf->autoLangToFont = true;
			$pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/hsc_promotion_confirmation.pdf');
			$tplId = $mpdf->ImportPage($pagecount);
			$actualsize = $mpdf->SetPageTemplate($tplId);
			$mpdf->AddPage();

			$mpdf->SetFont('Times new Roman', 'B', 17);
			//$mpdf->WriteText(43, 83, $current_level);  
			$mpdf->SetFont('Times new Roman', '', 12);
			$mpdf->WriteText(90, 67, $name);
			$mpdf->WriteText(90, 77, $student_id);
			//$mpdf->WriteText(90, 107, $faculty);
			$mpdf->WriteText(90, 87, $subject);
			$mpdf->WriteText(90, 98, $admission_session);
			$mpdf->WriteText(90, 108, $class_roll);
			$mpdf->WriteText(90, 118, $current_level);

			$mpdf->WriteText(90, 129, $transaction_id);
			//$mpdf->WriteText(90, 161, $exam_year);
			$mpdf->WriteText(90, 139, $paid_amount);
			$mpdf->WriteText(90, 150, $date);





			$pdf_name = $student_id . '_' . 'hsc_promotion_confirmation_slip';
			$file_name = public_path() . "/download/{$pdf_name}.pdf";
			$mpdf->Output($file_name);

			echo "<center><a href='" . url('/') . "/download/{$pdf_name}.pdf' target='_blank'>Click to Download</a></center>";
		}
	}

	public function promotionLogout()
	{
		Auth::logout();
		Session::flush();
		return Redirect::route('hsc.promotion');
	}

	public function payType()
	{
		if (!Session::has('student_id_found')) {
			return Redirect::route('hsc.promotion');
		}
		$student_id    = Session::get('student_id_found');
		$group = Session::get('groups');
		$current_level = Session::get('current_level');
		$session = Session::get('session');
		$examyear =   Session::get('examyear');
		return view('hsc_promotion.paymenttype')->withStudent_id($student_id)->withGroup($group)->withCurrent_level($current_level)->withSession($session)->withExamyear($examyear);
	}

	public function checktype(Request $request)
	{
		$payType = $request->get('payType');
		$regNumber = $request->get('regNumber');
		Session::put('payType', $payType);
		Session::put('regNumber', $regNumber);
		return 'Ok';
	}
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libs\Study;
use App\Models\Invoice;
use App\Models\Department;
use Illuminate\Support\Facades\Redirect;
use Session;
use DB;
use App\Models\HscPromotion;
use Mpdf\Mpdf;

class Hsc2ndYearController extends Controller
{
	
	public function hsc_2nd_year_promotion(Request $request) {
		
		$id = $request->get('id');
		
		
		$dept_name = $request->get('dept_name');       
		$level_study = 'HSC 2nd Year';
		$exam_year =  $request->get('exam_year');
		
		$title = 'Easy CollegeMate - Hsc 2nd Year Management';
		$breadcrumb = 'student.hsc.2nd.promotion.index:Hsc 2nd Year Promotion|Dashboard';
		
		$query = Study::searchHscPromotionStudent($id, $dept_name, $level_study, $exam_year);
		
		$num_rows = $query->count();
		$total_amount = $query->sum('total_amount');
		
		$form_fillup= $query->paginate(Study::paginate()); 
		
		
		
		
		$dept_lists = Department::pluck('dept_name','dept_name');
		
		return view('BackEnd.student.hsc_2nd_year.hsc_promotion.index', compact('title', 'breadcrumb', 'dept_lists', 'form_fillup', 'id', 'dept_name', 'exam_year', 'level_study', 'num_rows', 'total_amount'));
		
	}
	
	public function hscgenerateFFReport(Request $request)
	{
		$exam_year = $request->get('hsc_exam_year');    
		$dept_name = $request->get('hsc_dept_name');
		$level_study = 'HSC 2nd Year';
		$id = $request->id;
		
		$form_fillup = HscPromotion::where(function($query) use ($id, $dept_name , $level_study, $exam_year) {
			
			if(isset($id) && $id != '') :
				$query->where('id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($dept_name) && $dept_name != '') :
				$query->where('dept_name','=', $dept_name );
			endif;
			
			if(isset($level_study) && $level_study != '') :
				$query->where('level_study', $level_study );
			endif;
			
			if(isset($exam_year) && $exam_year != '') :
				$query->where('exam_year', $exam_year );
			endif;                              
			
		})->select('form_fillup_hsc_promotion.id','form_fillup_hsc_promotion.exam_year','form_fillup_hsc_promotion.total_amount','form_fillup_hsc_promotion.id as name','form_fillup_hsc_promotion.course','form_fillup_hsc_promotion.groups', 'form_fillup_hsc_promotion.session','form_fillup_hsc_promotion.date','form_fillup_hsc_promotion.level_study as level_study', 'form_fillup_hsc_promotion.dept_name')->get();
		
		$mpdf = new Mpdf();
		$mpdf->ignore_invalid_utf8 = true;
		$mpdf->autoScriptToLang = true;
		$mpdf->autoVietnamese = true;
		$mpdf->autoArabic = true;
		$mpdf->autoLangToFont = true;
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';  
		$mpdf->WriteHTML(view('BackEnd.student.pdf.hscffreport', compact('form_fillup', 'exam_year')));
		$mpdf->Output();
		
	}
	
	public function invoice(Request $request){
		
		$exam_year = $request->exam_year;
		$student_id = $request->student_id;
		$dept_name = $request->dept_name;
		$status = $request->status;
		
		Session::put('exam_year',$exam_year);
		Session::put('student_id',$student_id);
		Session::put('dept_name',$dept_name);
		Session::put('status',$status);
		
		$query = Invoice::where('type', 'hsc_2nd_year_promotion');
		
		if ($student_id != '') {
			$query->where('roll', $student_id);
		}
		
		if ($exam_year != '') {
			$query->where('passing_year', $exam_year);
		}
		
		if ($dept_name != '') {
			$query->where('pro_group', $dept_name);
		}
		
		if ($status != '') {
			$query->where('status', $status);
		}
		
		query_has_permissions($query, ['adm_hsc_session', 'pro_group','passing_year']);
		
		
		$num_rows = $query->get();
		$invoices = $query->paginate(Study::paginate());
		
		return view('BackEnd.student.hsc_2nd_year.hsc_promotion_invoice.invoice', compact('invoices', 'num_rows'));
	}
	
	public function invoice_action(Request $request){
		$student_ids = $request->student_ids;
		
		if (!isset($request->student_ids)) {
			$error_message = 'Please select at least 1 student id';
			return Redirect::route('student.hsc.promotion.invoice')->with('error',$error_message);
		}
		
		if (isset($request->student_ids) && count($request->student_ids) > 0) {
			if ($request->action_type == 'delete') {
				$student_ids = $request->student_ids;
				
				for ($i=0; $i < count($student_ids); $i++) {
					Invoice::where('roll', $student_ids[$i])->where('status', 'Pending')->delete();
				}
			}
			
			$message = 'Invoice deleted successfully';
			return Redirect::route('student.hsc.promotion.invoice')->with('success',$message);
		}
	}
	
}

<?php

namespace App\Libs;
use App\Libs\Study;
use App\Models\Course;
use App\Models\Libmember;
use App\Models\Maccession;
use App\Models\DeptProgram;
use App\Models\PayslipItem;
use App\Models\HscMeritList;
use App\Models\HscPromotion;
use App\Models\PayslipTitle;
use App\Models\HonsMeritList;
use App\Models\MigatedStudent;
use App\Models\StudentInfoHsc;
use App\Models\StudentInfoHons;
use App\Models\MastersMeritList;
use App\Models\StudentInfoHscTc;
use App\Models\StudentInfoDegree;
use App\Models\HscAdmittedStudent;
use App\Models\StudentInfoMasters;
use App\Models\HonsAdmittedStudent;
use App\Models\DegreeAdmittedStudent;
use Illuminate\Support\Facades\Session;
// use Session;

class Study {



	const PAGINATION = 100;



	public static $permissions = ['view', 
								  'create', 
								  'edit', 
								  'delete',

								  'module.administration', 
								  'module.students', 
								  'module.teachers', 
								  'module.employees', 
								  'module.library',
								  'module.hall',
								  'module.accounts',
								  'module.inventory',								  
								  'module.hrm_and_payroll',
								  'module.support',

								  'administration.role',
								  'administration.user',
								  'administration.college',
								  'administration.program',
								  'administration.faculty',
								  'administration.department',
								  'administration.admission',
								  'administration.course',
								  'administration.inventory',
								  'administration.library',						  
								  'administration.hall',
								  'administration.human_resource_management',
								  'administration.id_and_roll',
								  'administration.exam',
								  'administration.yearly',
								  'administration.certificate',
								  'administration.user_log',
								  'administration.rajit',

								  'students.admission',
								  'students.migration',
								  'students.registration',
								  'students.result_processing',
								  'students.certificate',
								  'students.reporting',

								  'teachers.teachers_list',

								  'employees.2nd_and_3rd_class_regular',
								  'employees.4th_class_regular',

								  'library.library_material',
								  'library.member',
								  'library.circulation',
								  'library.report',

								  'hall.allocate_seat',
								  'hall.cash_book_entry',
								  'hall.report',

								  'accounts.journal_entry',
								  'accounts.fees_collection',
								  'accounts.expenditure',
								  'accounts.income',
								  'accounts.fund_transfer',
								  'accounts.general_report',

								  'inventory.item',
								  'inventory.sub_item',
								  'inventory.cost',								  								  

								  'hrm_and_payroll.leave_approve',
								  'hrm_and_payroll.leave_report',
								  'hrm_and_payroll.leave_summary',
								  'hrm_and_payroll.salary_sheet',
								  'hrm_and_payroll.organogram',

								  'support.ticket_form'];



	public static $user_type = ['operator' => 1, 'student' => 2, 'teacher' => 3];	



	public static function userType($user_type) {

		if($user_type == self::$user['operator']) :
			return 'Operator';
		elseif($user_type == self::$user['student']) :
			return 'Student';
		elseif($user_type == self::$user['teacher']) :
			return 'Teacher';
		else :
			return 'Invalid user type';
		endif;

	}	



	public static function userStatus($status) {

		if($status == 0) :
			return 'Inactive';
		elseif($status == 1) :
			return 'Active';
		else :
			return 'Invalid status';
		endif;	
	}					  



	public static function paginate() {

		return self::PAGINATION;

	}	

	

	public static function level($num) {

		$level = $num . 'th';

		$lastdigit = substr($num, -1);

		if($lastdigit == 1) :
			$level = $num . 'st';
		endif;	

		if($lastdigit == 2) :
			$level = $num . 'nd';
		endif;

		if($lastdigit == 3) :
			$level = $num . 'rd';
		endif;

		return $level;

	}



	public static function deptHasProgram($department_id, $program_id) {

		$check = true;

		$dept_program = DeptProgram::where('department_id', $department_id)->where('program_id',$program_id)->get();
		
		if($dept_program->count() == 0) :
			$check = false;
		endif;	

		return $check;		

	}



	public static function courseCode($code, $session) {

		$check = true;

		$course_code = Course::whereCode($code)->whereSession($session)->get();

		if($course_code->count() > 0) :
			$check = false;
		endif;	

		return $check;

	}



	public static function courseCodeUpdate($id, $code, $session) {

		$check = true;

		$course_code = Course::whereCode($code)->whereSession($session)->where('id', '!=', $id)->get();

		if($course_code->count() > 0) :
			$check = false;
		endif;	

		return $check;

	}



	public static function filterInput($variable, $data) {

		$outcomes = $data;

		if(isset($data)) :
			Session::put($variable, $data);
		endif;

		if(Session::has($variable)) :
			$outcomes = Session::get($variable);
		endif;	

		return $outcomes;

	}



	public static function searchCourse($code, $department_id, $level, $session) {

		$outcomes = Course::where(function($query) use ($code, $department_id, $level, $session) {
			
			if(isset($code) && $code != '') :
				$query->whereCode($code);
			endif;

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($level) && $level != '') :
				$query->whereLevel($level);
			endif;	

			if(isset($session) && $session != '') :
				$query->whereSession($session);
			endif;	

		})->paginate(self::paginate());

		return $outcomes;

	}



	public static function updatedRow($flash_variable, $this_row_id) {

		if(Session::has($flash_variable)) :

			if(!is_array(Session::get($flash_variable))) :
				if(Session::get($flash_variable) == $this_row_id) :

					return 'update_row';

				endif;
			else :
				if(in_array($this_row_id,Session::get($flash_variable))) :
					return 'update_row';
				endif;	
			endif;	

		endif;	

		return NULL;

	}



	public static function searchInventoryCost($start_date, $end_date, $inventory_id) {

		$count = Icost::where(function($query) use ($start_date, $end_date, $inventory_id) {

			if(isset($start_date) && $start_date != '') :
				$query->where('date', '>=', $start_date);
			endif;	

			if(isset($end_date) && $end_date != '') :
				$query->where('date', '<=', $end_date);
			endif;	

			if(isset($inventory_id) && $inventory_id != '') :
				$query->whereInventory_id($inventory_id);
			endif;	

		})->count();

		if($count <= 50) :

			$outcomes = Icost::where(function($query) use ($start_date, $end_date, $inventory_id) {

				if(isset($start_date) && $start_date != '') :
					$query->where('date', '>=', $start_date);
				endif;	

				if(isset($end_date) && $end_date != '') :
					$query->where('date', '<=', $end_date);
				endif;	

				if(isset($inventory_id) && $inventory_id != '') :
					$query->whereInventory_id($inventory_id);
				endif;	

			})->orderBy('date', 'desc')->get();

		else :

			$outcomes = Icost::where(function($query) use ($start_date, $end_date, $inventory_id) {

				if(isset($start_date) && $start_date != '') :
					$query->where('date', '>=', $start_date);
				endif;	

				if(isset($end_date) && $end_date != '') :
					$query->where('date', '<=', $end_date);
				endif;	

				if(isset($inventory_id) && $inventory_id != '') :
					$query->whereInventory_id($inventory_id);
				endif;

			})->orderBy('date', 'desc')->paginate(self::paginate());

		endif;	

		return $outcomes;

	}



	public static function totalIcosts($start_date, $end_date, $inventory_id) {

		$outcomes = Icost::where(function($query) use ($start_date, $end_date, $inventory_id) {

			if(isset($start_date) && $start_date != '') :
				$query->where('date', '>=', $start_date);
			endif;	

			if(isset($end_date) && $end_date != '') :
				$query->where('date', '<=', $end_date);
			endif;	

			if(isset($inventory_id) && $inventory_id != '') :
				$query->whereInventory_id($inventory_id);
			endif;	

		})->sum('total_cost');

		return $outcomes;

	}



	public static function searchSubItem($icost_id, $inventory_id, $identifier_tag, $status) {

		$outcomes = InventSubItem::where(function($query) use ($icost_id, $inventory_id, $identifier_tag, $status) {
			
			if(isset($icost_id) && $icost_id !='') :
				$query->whereIcost_id($icost_id);
			endif;

			if(isset($inventory_id) && $inventory_id != '') :
				$query->whereInventory_id($inventory_id);
			endif;

			if(isset($identifier_tag) && $identifier_tag != '') :
				$query->whereIdentifier_tag($identifier_tag);
			endif;	

			if(isset($status) && $status != '') :
				$query->whereStatus($status);
			endif;	

		})->paginate(self::paginate());

		return $outcomes;

	}	



	public static function searchAdmission($session, $department_id, $program_id, $status) {

		$outcomes = Admission::where(function($query) use ($session, $department_id, $program_id, $status) {
			
			if(isset($session) && $session != '') :
				$query->whereSession($session);
			endif;

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($program_id) && $program_id != '') :
				$query->whereProgram_id($program_id);
			endif;	

			if(isset($status) && $status != '') :
				$query->whereStatus($status);
			endif;	

		})->orderBy('open_date', 'desc')->paginate(self::paginate());

		return $outcomes;

	}



	public static function searchPayslipItem($payslipheader_id) {

		$outcomes = PayslipItem::where(function($query) use ($payslipheader_id) {
			
			if(isset($payslipheader_id) && $payslipheader_id != '') :
				$query->wherePayslipheader_id($payslipheader_id);
			endif;

		})->paginate(self::paginate());

		return $outcomes;

	}	



	public static function searchPayslipTitle($title, $status) {

		$outcomes = PayslipTitle::where(function($query) use ($title, $status) {
			
			if(isset($title) && $title != '') :
				$query->where('title','LIKE','%' . $title . '%');
			endif;

			if(isset($status) && $status != '') :
				$query->whereStatus($status);
			endif;			

		})->paginate(self::paginate());

		return $outcomes;

	}	



	public static function searchMaterial($physical_form, $accession_no, $call_no, $title, $author) {

		$outcomes = Maccession::join('materials', 'maccessions.material_id', '=', 'materials.id')->where(function($query) use ($physical_form, $accession_no, $call_no, $title, $author) {

			if(isset($physical_form) && $physical_form != '') :
				$query->wherePhysical_form($physical_form);
			endif;
			
			if(isset($accession_no) && $accession_no != '') :
				$query->where('accession_no','LIKE','%' . $accession_no . '%');
			endif;

			if(isset($call_no) && $call_no != '') :
				$query->where('call_no','LIKE','%' . $call_no . '%');
			endif;

			if(isset($title) && $title != '') :
				$query->where('title','LIKE','%' . $title . '%');
			endif;

			if(isset($author) && $author != '') :
				$query->where('author','LIKE','%' . $author . '%');
			endif;									

		})->select('maccessions.*')->paginate(self::paginate());

		return $outcomes;

	}	


public static function searchHscStudent($id, $ssc_roll, $groups,$gender, $current_level, $session) {

		$outcomes = StudentInfoHsc::where(function($query) use ($id, $ssc_roll, $groups, $gender, $current_level, $session) {

			if(isset($id) && $id != '') :
				$query->where('id','LIKE','%' . $id . '%');
				//$query->whereId($id);
			endif;
			
			if(isset($ssc_roll) && $ssc_roll != '') :
				$query->where('ssc_roll','LIKE','%' . $ssc_roll . '%');
			endif;

			if(isset($groups) && $groups != '') :
				$query->where('groups','LIKE','%' . $groups . '%');
			endif;

			if(isset($gender) && $gender != '') :
				$query->where('gender',$gender);
			endif;

			if(isset($current_level) && $current_level != '') :
				$query->where('current_level','LIKE','%' . $current_level . '%');
			endif;

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;

			query_has_permissions($query, ['current_level', 'session', 'hsc_group']);

		})->select('student_info_hsc.*');

		return $outcomes;

	}	



public static function searchHscTcStudent($id, $ssc_roll, $groups, $current_level, $session) {

		$outcomes = StudentInfoHscTc::where(function($query) use ($id, $ssc_roll, $groups, $current_level, $session) {

			if(isset($id) && $id != '') :
				$query->where('id','LIKE','%' . $id . '%');
				//$query->whereId($id);
			endif;
			
			if(isset($ssc_roll) && $ssc_roll != '') :
				$query->where('ssc_roll','LIKE','%' . $ssc_roll . '%');
			endif;

			if(isset($groups) && $groups != '') :
				$query->where('groups','LIKE','%' . $groups . '%');
			endif;

			if(isset($current_level) && $current_level != '') :
				$query->where('current_level','LIKE','%' . $current_level . '%');
			endif;

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;

			query_has_permissions($query, ['current_level', 'session', 'hsc_group']);								

		})->select('student_info_hsc_tc.*')->paginate(self::paginate());

		return $outcomes;

	}




	public static function searchFFStudent($id,  $dept_name, $level_study, $session) {

		$outcomes = FormFillup::where(function($query) use ($id,  $dept_name, $level_study, $session) {

			if(isset($id) && $id != '') :
				$query->where('id','LIKE','%' . $id . '%');
				//$query->whereId($id);
			endif;
			
			if(isset($dept_name) && $dept_name != '') :
				$query->where('dept_name','LIKE','%' . $dept_name . '%');
			endif;
			

			if(isset($level_study) && $level_study != '') :
				$query->where('level_study','LIKE','%' . $level_study . '%');
			endif;

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;									

		})->select('form_fillup.*')->paginate(self::paginate());

		return $outcomes;

	}

	public static function regSearchHscStudent($id, $ssc_roll, $session) {

		$outcomes = HscAdmittedStudent::where(function($query) use ($id, $ssc_roll, $session) {

			if(isset($id) && $id != '') :
				$query->where('auto_id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($ssc_roll) && $ssc_roll != '') :
				$query->where('ssc_roll','LIKE','%' . $ssc_roll . '%');
			endif;

			if(isset($session) && $session != '') :
				$query->where('admission_session','LIKE','%' . $session . '%');
			endif;

			query_has_permissions($query, ['adm_hsc_group', 'adm_hsc_session']);

		})->select('hsc_admitted_students.*');

		return $outcomes;

	}

public static function regSearchHonoursStudent($id, $adm_roll, $session) {

		$outcomes = HonsAdmittedStudent::where(function($query) use ($id, $adm_roll, $session) {

			if(isset($id) && $id != '') :
				$query->where('auto_id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($adm_roll) && $adm_roll != '') :
				$query->where('admission_roll','=', $adm_roll );
			endif;

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;

			query_has_permissions($query, ['session', 'subject']);						

		})->select('hons_admitted_student.*');

		return $outcomes;

	}

	
	
public static function regSearchMastersStudent($id, $adm_roll, $session) {

		$outcomes = MastersAdmittedStudent::where(function($query) use ($id, $adm_roll, $session) {

			if(isset($id) && $id != '') :
				$query->where('auto_id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($adm_roll) && $adm_roll != '') :
				$query->where('admission_roll','=', $adm_roll );
			endif;

					

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;									

		})->select('masters_admitted_student.*');

		return $outcomes;

	}	
	

public static function regSearchDegreeStudent($id, $adm_roll, $session) {

		$outcomes = DegreeAdmittedStudent::where(function($query) use ($id, $adm_roll, $session) {

			if(isset($id) && $id != '') :
				$query->where('auto_id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($adm_roll) && $adm_roll != '') :
				$query->where('admission_roll','=', $adm_roll );
			endif;

					

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;									

		})->select('deg_admitted_student.*');

		return $outcomes;

	}

	public static function searchHscPromotionStudent($id, $dept_name , $level_study, $exam_year) {

		$outcomes = HscPromotion::where(function($query) use ($id, $dept_name , $level_study, $exam_year) {

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

			query_has_permissions($query, ['level_study', 'session', 'hsc_group','exam_year']);

		})->select('form_fillup_hsc_promotion.*');


		return $outcomes;

	}	
	
	
public static function regSearchMigrationStudent($id, $adm_roll, $session, $course) {

		$outcomes = MigatedStudent::where(function($query) use ($id, $adm_roll, $session, $course) {

			if(isset($id) && $id != '') :
				$query->where('previous_id','=', $id );
				//$query->whereId($id);
			endif;
			
			if(isset($adm_roll) && $adm_roll != '') :
				$query->where('admission_roll','=', $adm_roll );
			endif;

					

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;	

			if(isset($course) && $course != '') :
				$query->where('course',$course);
			endif;									

		})->select('migrated_student.*');

		return $outcomes;

	}

public static function searchHonsStudent($id, $admission_roll, $faculty, $dept, $current_level, $session, $from_date=null, $to_date=null) {

		$outcomes = StudentInfoHons::leftJoin('admission_students','student_info_hons.id','=','admission_students.id')->where(function($query) use ($id, $admission_roll, $faculty, $dept, $current_level, $session, $from_date, $to_date) {

			if(isset($id) && $id != '') :
				$query->where('student_info_hons.id','LIKE','%' . $id . '%');
				//$query->whereId($id);
			endif;
			
			if(isset($admission_roll) && $admission_roll != '') :
				$query->where('student_info_hons.admission_roll','LIKE','%' . $admission_roll . '%');
			endif;

			if(isset($faculty) && $faculty != '') :
				$query->where('student_info_hons.faculty_name','=', $faculty );
			endif;

			if(isset($dept) && $dept != '') :
				$query->where('student_info_hons.dept_name','=' , $dept);
			endif;

			if(isset($current_level) && $current_level != '') :
				$query->where('student_info_hons.current_level',$current_level);
			endif;

			if(isset($session) && $session != '') :
				$query->where('student_info_hons.session',$session);
			endif;

			if ($from_date != '') {
	          $query->where('admission_students.date', '>=',$from_date);
	        }

	        if ($to_date != '') {
	          $query->where('admission_students.date', '<=',$to_date);
	        }

			// $query->where('admission_students.course','honours');

			query_has_permissions($query, ['student_info_hons.current_level', 'student_info_hons.session', 'student_info_hons.faculty_name', 'student_info_hons.dept_name']);						

		})
		->select('student_info_hons.*','admission_students.total_amount','admission_students.transaction_id','admission_students.date','student_info_hons.merit_status');

		return $outcomes;

	}

	public static function searchMastersStudent($id, $admission_roll, $faculty, $dept, $current_level, $session, $from_date = null, $to_date = null)
	{
	    $outcomes = StudentInfoMasters::leftJoin('invoices', function ($join) {
	        $join->on('student_info_masters.admission_roll', '=', 'invoices.roll')
	             ->on('student_info_masters.session', '=', 'invoices.admission_session')
	             ->on('student_info_masters.current_level', '=', 'invoices.level')
	             ->where('invoices.status', '=', 'Paid')
	             ->where('invoices.type', '=', 'masters_admission');
	    })
	    ->where(function ($query) use ($id, $admission_roll, $faculty, $dept, $current_level, $session, $from_date, $to_date) {
	        if (isset($id) && $id != '') {
	            $query->where('student_info_masters.id', 'LIKE', '%' . $id . '%');
	        }

	        if (isset($admission_roll) && $admission_roll != '') {
	            $query->where('student_info_masters.admission_roll', 'LIKE', '%' . $admission_roll . '%');
	        }

	        if (isset($faculty) && $faculty != '') {
	            $query->where('student_info_masters.faculty_name', '=', $faculty);
	        }

	        if (isset($dept) && $dept != '') {
	            $query->where('student_info_masters.dept_name', '=', $dept);
	        }

	        if (isset($current_level) && $current_level != '') {
	            $query->where('student_info_masters.current_level', '=', $current_level);
	        }

	        if (isset($session) && $session != '') {
	            $query->where('student_info_masters.session', '=', $session);
	        }

	        if ($from_date != '') {
	            $query->where('invoices.update_date', '>=', $from_date);
	        }

	        if ($to_date != '') {
	            $query->where('invoices.update_date', '<=', $to_date);
	        }

	        query_has_permissions($query, [
	            'student_info_masters.current_level',
	            'student_info_masters.session',
	            'student_info_masters.faculty_name',
	            'student_info_masters.dept_name'
	        ]);
	    })
	    ->select('student_info_masters.*', 'invoices.total_amount', 'invoices.txnid as transaction_id', 'invoices.update_date as date', 'student_info_masters.merit_status')
	    ->latest('invoices.created_at');

	    return $outcomes;
	}


public static function searchDegreeStudent($id, $admission_roll, $faculty, $current_level, $session) {

		$outcomes = StudentInfoDegree::where(function($query) use ($id, $admission_roll, $faculty, $current_level, $session) {

			if(isset($id) && $id != '') :
				$query->where('id','LIKE','%' . $id . '%');
				//$query->whereId($id);
			endif;
			
			if(isset($admission_roll) && $admission_roll != '') :
				$query->where('admission_roll','LIKE','%' . $admission_roll . '%');
			endif;

			if(isset($faculty) && $faculty != '') :
				$query->where('groups','=', $faculty );
			endif;

			if(isset($current_level) && $current_level != '') :
				$query->where('current_level','LIKE','%' . $current_level . '%');
			endif;

			if(isset($session) && $session != '') :
				$query->where('session','LIKE','%' . $session . '%');
			endif;

			query_has_permissions($query, ['current_level', 'session', 'deg_group']);

		})->select('student_info_degree.*');

		return $outcomes;

	}



	

	public static function searchHscMeritlistStudent($ssc_roll) {

		$outcomes = HscMeritList::where(function($query) use ($ssc_roll) {

			
			
			if(isset($ssc_roll) && $ssc_roll != '') :
				$query->where('ssc_roll','LIKE','%' . $ssc_roll . '%');
			endif;

			query_has_permissions($query, ['session', 'ssc_group']);

											

		})->select('hsc_merit_list.*')->where('admitted', 0);

		return $outcomes;

	}

	public static function searchHonsMeritlistStudent($admission_roll) {

		$hons_merit_students = HonsMeritList::where('delete_status',0)->paginate(Study::paginate());
		$outcomes = HonsMeritList::where(function($query) use ($admission_roll) {

			if(isset($admission_roll) && $admission_roll != '') :
				$query->where('admission_roll','LIKE','%' . $admission_roll . '%');
			endif;
			query_has_permissions($query, ['adm_faculty', 'subject']);

		})->select('hons_merit_list.*');

		return $outcomes;

	}

	public static function searchMastersMeritlistStudent($admission_roll) {

		$outcomes = MastersMeritList::where(function($query) use ($admission_roll) {

			if(isset($admission_roll) && $admission_roll != '') :
				$query->where('admission_roll','LIKE','%' . $admission_roll . '%');
			endif;							

		})->select('masters_merit_list.*')->where('delete_status', 0);

		return $outcomes;

	}
	
	public static function searchDegreeMeritlistStudent($admission_roll) {

		$hons_merit_students = DegreeMeritList::where('delete_status',0)->paginate(Study::paginate());
		$outcomes = DegreeMeritList::where(function($query) use ($admission_roll) {

			
			
			if(isset($admission_roll) && $admission_roll != '') :
				$query->where('admission_roll','LIKE','%' . $admission_roll . '%');
			endif;

											

		})->select('deg_merit_list.*')->where('delete_status', 0)->paginate(self::paginate());

		return $outcomes;

	}	
	
	public static function searchMember($member_id, $full_name, $libraryuser_id) {

		$outcomes = Libmember::where(function($query) use ($member_id, $full_name, $libraryuser_id) {

			if(isset($member_id) && $member_id != '') :
				$query->where('id','LIKE','%' . $member_id . '%');
			endif;			
			
			if(isset($full_name) && $full_name != '') :
				$query->where('full_name','LIKE','%' . $full_name . '%');
			endif;

			if(isset($libraryuser_id) && $libraryuser_id != '') :
				$query->whereLibraryuser_id($libraryuser_id);
			endif;								

		})->paginate(self::paginate());

		return $outcomes;

	}		



	public static function searchLibcirculation($status, $libmember_id, $accession_no, $call_no) {

		$outcomes = Libcirculation::join('libmembers', 'libcirculations.libmember_id', '=', 'libmembers.id')
								  ->join('maccessions', 'libcirculations.maccession_id', '=', 'maccessions.id')
								  ->join('materials', 'maccessions.material_id', '=', 'materials.id')
								  ->where(function($query) use ($status, $libmember_id, $accession_no, $call_no) {

			if(isset($status) && $status != '') :
				$query->whereStatus($status);
			endif;
			
			if(isset($libmember_id) && $libmember_id != '') :
				$query->where('libmember_id','LIKE','%' . $libmember_id . '%');
			endif;

			if(isset($accession_no) && $accession_no != '') :
				$query->where('accession_no','LIKE','%' . $accession_no . '%');
			endif;

			if(isset($call_no) && $call_no != '') :
				$query->where('call_no','LIKE','%' . $call_no . '%');
			endif;

		})->select('libcirculations.*')->paginate(self::paginate());

		return $outcomes;

	}			



}
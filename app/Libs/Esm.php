<?php

class Esm {

	const PAGINATION = 10;

	public static $permissions = ['view', 
								  'create', 
								  'edit', 
								  'delete',

								  'module.setting', 
								  'module.student', 
								  'module.exam',
								  'module.inventory',
								  'module.messaging', 

								  'setting.class_setup',
								  'setting.section_setup',
								  'setting.shift_setup',
								  'setting.subject_setup',	
								  'setting.exam_setup',	
								  'setting.letter_grade_setup',
								  'setting.publish_result',
								  'setting.student_panel',

								  'student.admission',
								  'student.fourth_subject',
								  'student.class_reset',
								  'student.update',
								  'student.iD_card',
								  'student.promotion',

								  'exam.exam_marks',
								  'exam.tabulation_sheet',
								  'exam.transcript',
								  'exam.meritlist',

								  'inventory.item',
								  'inventory.cost',
								  'inventory.sub_item'];	

	public static function paginate() {

		return self::PAGINATION;

	}	

	public static function stripSlashes($input_value) {
		$input_value=stripslashes($input_value);
		$input_value=trim($input_value);
		return $input_value;
	}

	public static function schoolDB() {

		if(Session::has('school_db')) :
			return Session::get('school_db');
		else :	
			return 'school_demo';
		endif;	

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

	public static function userStatus($status) {

		if($status == 0) :
			return 'Inactive';
		elseif($status == 1) :
			return 'Active';
		else :
			return 'Invalid status';
		endif;	
	}	

	public static function gradePoint($mark, $total) {

		$percentage_mark = ceil(($mark*100)/$total);

		$active_grade_system = GradeSystem::whereStatus(1)->first();
		$grade_scales = GradeScale::whereGradesystem_id($active_grade_system->id)->get();

		foreach($grade_scales as $grade_scale) :
			$range_low = $grade_scale->range_low;
			$range_high = $grade_scale->range_high;
			if($percentage_mark >= $range_low && $percentage_mark <= $range_high) :
				$grade = $grade_scale->letter_grade;
				$point = $grade_scale->point;
			endif;	
		endforeach;	

		$result = [];
		$result['grade'] = $grade;
		$result['point'] = $point;

		return $result;

	}

	public static function gpa($points, $subjects) {

		if($subjects == 0) :
			$subjects = 1;
		endif;	

		$gpa = $points/$subjects;

		$active_grade_system = GradeSystem::whereStatus(1)->first();
		$highest_point = GradeScale::whereGradesystem_id($active_grade_system->id)->max('point');

		if($gpa > $highest_point) :
			$gpa = $highest_point;
		endif;	

		$gpa = number_format($gpa, 2, '.', '');

		return $gpa;

	}

	public static function percentageValue($subject_mark, $subject_total) {

		$percentage_value = ($subject_mark*100)/$subject_total;

		$percentage_value = number_format($percentage_value, 2, '.', '');

		return $percentage_value;

	}

	public static function filterInput($variable, $data) {

		$outcomes = $data;

		if(isset($data)) :
			Session::set($variable, $data);
		endif;	

		if(Session::has($variable)) :
			$outcomes = Session::get($variable);
		endif;	

		return $outcomes;

	}	

	public static function filterFieldValue($fieldvalue) {

		start:
		if(strlen($fieldvalue) > 60) :
			$pos = strrpos($fieldvalue, ' ');	
			if($pos !== false) :
				$fieldvalue = substr($fieldvalue, 0, $pos);
				goto start;
			else :
				$fieldvalue = substr($fieldvalue, 0, 60);
				goto end;				
			endif;
		else :
			goto end;		
		endif;	

		end:
		return $fieldvalue;

	}	

	public static function dateBD($date) {

		$date_array = explode('-', $date);
		$year = $date_array[0];
		$month = $date_array[1];
		$day = $date_array[2];
		$date_bd = $day . '-' . $month . '-' . $year;
		return $date_bd;

	}

	public static function smsType($type) {

		if($type == 1) :
			$outcome = 'Guardian';
		elseif($type == 2) :
			$outcome = 'Employee';
		elseif($type == 3) :
			$outcome = 'Attendance';
		elseif($type == 4) :
			$outcome = 'Result';
		elseif($type == 5) :
			$outcome = 'Phonebook';
		else :
			$outcome = NULL;
		endif;

		return $outcome;	

	}

	public static function searchAdmission($academic_year, $classe_id, $department_id, $section_id, $shift_id, $roll) {

		$outcomes = AcademicInfo::where(function($query) use ($academic_year, $classe_id, $department_id, $section_id, $shift_id, $roll) {
			
			if(isset($academic_year) && $academic_year != '') :
				$query->whereAcademic_year($academic_year);
			endif;

			if(isset($classe_id) && $classe_id != '') :
				$query->whereClasse_id($classe_id);
			endif;			

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($section_id) && $section_id != '') :
				$query->whereSection_id($section_id);
			endif;	

			if(isset($shift_id) && $shift_id != '') :
				$query->whereShift_id($shift_id);
			endif;	

			if(isset($roll) && $roll != '') :
				$query->whereRoll($roll);
			endif;				

		})->orderBy('roll', 'asc')->paginate(self::paginate());

		return $outcomes;

	}	

	public static function searchStudentList($academic_year, $classe_id, $department_id, $section_id) {

		$outcomes = AcademicInfo::where(function($query) use ($academic_year, $classe_id, $department_id, $section_id) {
			
			if(isset($academic_year) && $academic_year != '') :
				$query->whereAcademic_year($academic_year);
			endif;

			if(isset($classe_id) && $classe_id != '') :
				$query->whereClasse_id($classe_id);
			endif;			

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($section_id) && $section_id != '') :
				$query->whereSection_id($section_id);
			endif;	

		})->orderBy('academic_year')
		  ->orderBy('classe_id')
		  ->orderBy('department_id')
		  ->orderBy('section_id')
		  ->orderBy('roll')
		  ->get();

		return $outcomes;

	}	

	public static function searchConfigMark($academic_year, $classe_id, $department_id, $section_id, $exam_id, $subject_id) {

		$outcomes = ConfigMark::where(function($query) use ($academic_year, $classe_id, $department_id, $section_id, $exam_id, $subject_id) {
			
			if(isset($academic_year) && $academic_year != '') :
				$query->whereAcademic_year($academic_year);
			endif;

			if(isset($classe_id) && $classe_id != '') :
				$query->whereClasse_id($classe_id);
			endif;			

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($section_id) && $section_id != '') :
				$query->whereSection_id($section_id);
			endif;	

			if(isset($exam_id) && $exam_id != '') :
				$query->whereExam_id($exam_id);
			endif;	

			if(isset($subject_id) && $subject_id != '') :
				$query->whereSubject_id($subject_id);
			endif;				

		})->orderBy('id', 'desc')->paginate(self::paginate());

		return $outcomes;

	}	

	public static function searchSubject($subject_name, $parent_subject, $optional) {

		$outcomes = Subject::leftjoin('paper_subjects', 'subjects.id', '=' , 'paper_subjects.subject_id')->where(function($query) use ($subject_name, $parent_subject, $optional) {

			if(isset($subject_name) && $subject_name != '') :
				$query->where('name','LIKE','%' . $subject_name . '%');
			endif;	

			if(isset($parent_subject) && $parent_subject != '') :
				$query->whereParentsubject_id($parent_subject);
			endif;	

			if(isset($optional) && $optional != '') :
				$query->whereOptional($optional);
			endif;	

		})->select('subjects.*')->paginate(self::paginate());

		return $outcomes;

	}	

	public static function searchConfigMerit($academic_year, $classe_id, $department_id, $section_id, $exam_id) {

		$outcomes = ConfigMerit::where(function($query) use ($academic_year, $classe_id, $department_id, $section_id, $exam_id) {
			
			if(isset($academic_year) && $academic_year != '') :
				$query->whereAcademic_year($academic_year);
			endif;

			if(isset($classe_id) && $classe_id != '') :
				$query->whereClasse_id($classe_id);
			endif;			

			if(isset($department_id) && $department_id != '') :
				$query->whereDepartment_id($department_id);
			endif;

			if(isset($section_id) && $section_id != '') :
				$query->whereSection_id($section_id);
			endif;	

			if(isset($exam_id) && $exam_id != '') :
				$query->whereExam_id($exam_id);
			endif;	

		})->orderBy('id', 'desc')->paginate(self::paginate());

		return $outcomes;

	}	

	public static function searchUser($name, $school_id, $status, $role_id) {

		$outcomes = User::join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
						->where(function($query) use ($name, $school_id, $status, $role_id) {

							if(isset($name) && $name != '') :
								$query->where('name','LIKE','%' . $name . '%');
							endif;	

							if(isset($school_id) && $school_id != '') :
								$query->whereSchool_id($school_id);
							endif;	

							if(isset($status) && $status != '') :
								$query->whereStatus($status);
							endif;	

							if(isset($role_id) && $role_id != '') :
								$query->whereRole_id($role_id);
							endif;							

						})->whereType(2)
						  ->select('users.*')
						  ->orderBy('users.id')						  
						  ->paginate(self::paginate());

		return $outcomes;

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

	public static function searchPhonebook($phonebookcategory_id, $contact_name, $mobile_no) {

		$outcomes = Phonebook::where(function($query) use ($phonebookcategory_id, $contact_name, $mobile_no) {

								if(isset($phonebookcategory_id) && $phonebookcategory_id != '') :
									$query->wherePhonebookcategory_id($phonebookcategory_id);
								endif;

								if(isset($contact_name) && $contact_name != '') :
									$query->where('contact_name','LIKE','%' . $contact_name . '%');
								endif;

								if(isset($mobile_no) && $mobile_no != '') :
									$query->where('mobile_no','LIKE','%' . $mobile_no . '%');
								endif;								

							})->paginate(self::paginate());

		return $outcomes;

	}		

	public static function getMessageCount($message) {

		$character_count=0;
		$message_count=0;
		$character_count=strlen($message);

		if($character_count<=160) :			
			$message_count=1;
		endif;

		if($character_count>160 && $character_count<=(160+145)) :
			$message_count=2;
		endif;

		if($character_count>(160+145)) :
			$extra=$character_count-(160+145);
			$extra_msg=$extra/152;
			$message_count=2+ceil($extra_msg);
		endif;

		return $message_count;

	}	

	public static function sendSMS($number, $message) {

		if($number!='' && $message!='') :

			$school = School::find(Session::get('school_id'));

			$username= $school->sms_username;
			$password= $school->sms_password;
			$message=urlencode($message);
		
			$url="http://smsgw.rajit.net/public/api/sendsms?username=".$username."&password=".$password."&message=".$message."&number=88".$number;

			$ch = curl_init();  
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);  
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
			curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
			$st=curl_exec($ch);  

        	return $st;

        else :	

        	return false;

		endif;

	}		

	public static function smsBalanceQuery() {

		$school = School::find(Session::get('school_id'));

		$username= $school->sms_username;
		$password= $school->sms_password;		

		$url="http://smsgw.rajit.net/public/api/balance?username=".$username."&password=".$password;

		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);  
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
		curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
		$st=curl_exec($ch);  

        return $st;

	}			
	
}

?>
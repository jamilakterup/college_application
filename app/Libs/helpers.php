<?php

use App\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (! function_exists('test')) {
	function test()
	{
		echo 'ok';
	}
}

if (! function_exists('active')) {
	function active()
	{
		foreach (func_get_args() as $arg) {
			if (Request::is($arg))
				return 'active';
		}
	}
}

if (! function_exists('has_sub_open')) {
	function has_sub_open(array $data)
	{
		foreach ($data as $d) {
			if (Request::is($d))
				return 'opened';
		}
	}
}

function activeMenu($uri = '')
{
	$active = '';
	if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri)) {
		$active = 'active';
	}
	return $active;
}

if (! function_exists('active_with_type')) {
	function active_with_type($type)
	{
		return request()->get('type') == $type ? 'active' : '';
	}
}

if (! function_exists('slip_type_lists')) {
	function slip_type_lists()
	{
		return ['' => '--Select Type--', 'Admission' => 'Admission', 'Formfillup' => 'Formfillup'];
	}
}

if (! function_exists('invalid_feedback')) {
	function invalid_feedback($name)
	{
		if (session()->get('errors')) {
			$errors = json_decode(session()->get('errors'), true);
			if (array_key_exists($name, $errors)) {
				return "<div class='invalid-feedback'>" . $errors[$name][0] . "</div>";
			}
		}
	}
}

function user_permissions()
{
	return auth()->user()->user_permissions;
}

function user_permissions_array($data, $type, $selective_name)
{
	$user_permissions = user_permissions()->where('type', $type)->pluck('value', 'value')->toArray();
	if (count($user_permissions)) $data = array_intersect($user_permissions, $data);
	return $data = options_by_array($data, $selective_name);
}

function get_user_permissions($type)
{
	return $user_permissions = user_permissions()->where('type', $type)->pluck('value')->toArray();
}

function check_auth_user()
{
	if (auth()->check() && auth()->user()->user_type != 'suadmin') {
		return true;
	}

	return false;
}

function query_has_permissions($query, array $type)
{
	if (check_auth_user()) {
		foreach ($type as $val) {
			$get_val = explode('.', $val);
			if (count($get_val) > 1) {
				$column = $get_val[0] . '.' . $get_val[1];
				$permission = $get_val[1];
			} else {
				$column = $get_val[0];
				$permission = $get_val[0];
				if ($column == 'hsc_group') $column = 'groups';
				else if ($column == 'faculty') $column = 'faculty_name';
				else if ($column == 'deg_group') $column = 'groups';
				else if ($column == 'adm_hsc_group') $column = 'hsc_group';
				else if ($column == 'adm_hsc_session') $column = 'admission_session';
				else if ($column == 'adm_faculty') $column = 'faculty';
			}

			if ($permission == 'current_level' && count(get_user_permissions('level')))
				$query->whereIn($column, get_user_permissions('level'));
			if ($permission == 'exam_year' && count(get_user_permissions('exam_year')))
				$query->whereIn($column, get_user_permissions('exam_year'));
			if ($permission == 'hsc_group' && count(get_user_permissions('hsc_group')))
				$query->whereIn($column, get_user_permissions('hsc_group'));
			if ($permission == 'faculty' && count(get_user_permissions('faculty')))
				$query->whereIn($column, get_user_permissions('faculty'));
			if ($permission == 'dept_name' && count(get_user_permissions('department')))
				$query->whereIn($column, get_user_permissions('department'));
			if ($permission == 'session' && count(get_user_permissions('session')))
				$query->whereIn($column, get_user_permissions('session'));
			if ($permission == 'deg_group' && count(get_user_permissions('faculty')))
				$query->whereIn($column, get_user_permissions('faculty'));
			if ($permission == 'subject' && count(get_user_permissions('department')))
				$query->whereIn($column, get_user_permissions('department'));
			if ($permission == 'adm_hsc_group' && count(get_user_permissions('hsc_group')))
				$query->whereIn($column, get_user_permissions('hsc_group'));
			if ($permission == 'adm_hsc_session' && count(get_user_permissions('session')))
				$query->whereIn($column, get_user_permissions('session'));
			if ($permission == 'ssc_group' && count(get_user_permissions('hsc_group')))
				$query->whereIn($column, get_user_permissions('hsc_group'));
			if ($permission == 'adm_faculty' && count(get_user_permissions('faculty')))
				$query->whereIn($column, get_user_permissions('faculty'));
			if ($permission == 'level_study' && count(get_user_permissions('level')))
				$query->whereIn($column, get_user_permissions('level'));
			if ($permission == 'pro_group' && count(get_user_permissions('hsc_group')))
				$query->whereIn($column, get_user_permissions('hsc_group'));
			if ($permission == 'groups' && count(get_user_permissions('hsc_group')))
				$query->whereIn($column, get_user_permissions('hsc_group'));
			if ($permission == 'passing_year' && count(get_user_permissions('exam_year')))
				$query->whereIn($column, get_user_permissions('exam_year'));
		}
	}
}

function selective_multiple_level()
{
	$data = array('' => '<--Select Level-->', 'HSC 1st Year' => 'HSC 1st Year', 'HSC 2nd Year' => 'HSC 2nd Year', 'Honours 1st Year' => 'Honours 1st Year', 'Honours 2nd Year' => 'Honours 2nd Year', 'Honours 3rd Year' => 'Honours 3rd Year', 'Honours 4th Year' => 'Honours 4th Year', 'Masters 1st Year' => 'Masters 1st Year', 'Masters 2nd Year' => 'Masters 2nd Year', 'Degree 1st Year' => 'Degree 1st Year', 'Degree 2nd Year' => 'Degree 2nd Year', 'Degree 3rd Year' => 'Degree 3rd Year');

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}

function selective_multiple_hsc_subject()
{

	$data = create_option_array('subjects', 'name', 'name', 'Subject');

	return $data;
}

function selective_multiple_degree_level()
{
	$data =  array('' => '<--Select Level-->', 'Degree 1st Year' => 'Degree 1st Year', 'Degree 2nd Year' => 'Degree 2nd Year', 'Degree 3rd Year' => 'Degree 3rd Year');

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}
function selective_degree_level()
{
	return array('' => '<--Select Level-->', 'Degree 3rd Year' => 'Degree 3rd Year');
}

function selective_masters_level()
{
	return array('' => '<--Select Level-->', 'Masters 1st Year' => 'Masters 1st Year');
}

function selective_multiple_masters_level()
{
	$data = array('' => '<--Select Level-->', 'Masters 1st Year' => 'Masters 1st Year', 'Masters 2nd Year' => 'Masters 2nd Year');

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}

function selective_multiple_honours_level()
{
	$data = array('' => '<--Select Level-->', 'Honours 1st Year' => 'Honours 1st Year', 'Honours 2nd Year' => 'Honours 2nd Year', 'Honours 3rd Year' => 'Honours 3rd Year', 'Honours 4th Year' => 'Honours 4th Year');

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}
function selective_honours_level()
{
	return array('' => '<--Select Level-->', 'Degree 2nd Year' => 'Degree 2nd Year');
}

function selective_degree_subjects()
{
	$data = array('' => '<--Select Subject-->', 'B.A' => 'B.A', 'B.S.S' => 'B.S.S', 'B.B.S' => 'B.B.S', 'B.Sc' => 'B.Sc');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}

function selective_multiple_hsc_level()
{
	$data =  array('' => '<--Select Level-->', 'HSC 1st Year' => 'HSC 1st Year', 'HSC 2nd Year' => 'HSC 2nd Year');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}
	return $data;
}

function selective_multiple_faculty()
{
	$data = create_option_array('faculties', 'faculty_code', 'faculty_name', 'Faculty');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'level', 'Level');
	}

	return $data;
}

function selective_blood_lists()
{
	return ['' => '<--Select Blood Group-->', 'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'O+' => 'O+', 'O-' => 'O-', 'AB+' => 'AB+', 'AB-' => 'AB-'];
}

function selective_multiple_session()
{
	$current_year = date('Y');
	$start_year = 2012;
	$end_year = $current_year;
	$data = [];

	for ($i = $start_year; $i <= $end_year; $i++) {
		$last_year = $i + 1;
		$data[] = [
			"{$i}-{$last_year}" => "{$i}-{$last_year}"
		];
	}

	$academic_session = [];
	$academic_session[''] = '<--Select Session-->';
	foreach ($data as $r) {
		foreach ($r as $key => $val) {
			$academic_session[$val] = $val;
		}
	}

	if (auth()->check() && auth()->user()->user_type != 'admin') {
		$academic_session = user_permissions_array($academic_session, 'session', 'Session');
	}

	return $academic_session;
}

function selective_current_session()
{
	$current_year = date('Y');
	$last_year = $current_year + 1;
	$academic_session[''] = '<--Select Exam Year-->';
	$academic_session["{$current_year}-{$last_year}"] = "{$current_year}-{$last_year}";
	return $academic_session;
}

function selective_multiple_exam_year()
{
	$current_year = date('Y') + 1;
	$start_year = 2012;
	$end_year = $current_year;
	$data = [];

	for ($i = $start_year; $i <= $end_year; $i++) {
		$data[] = [
			$i => $i
		];
	}

	$exam_year = [];
	$exam_year[''] = '<--Select Exam Year-->';
	foreach ($data as $r) {
		foreach ($r as $key => $val) {
			$exam_year[$val] = $val;
		}
	}

	if (check_auth_user()) {
		$exam_year = user_permissions_array($exam_year, 'exam_year', 'Year');
	}

	return $exam_year;
}

function selective_multiple_passing_year()
{
	$current_year = date('Y');
	$start_year = 2012;
	$end_year = $current_year;
	$data = [];

	for ($i = $start_year; $i <= $end_year; $i++) {
		$data[] = [
			$i => $i
		];
	}

	$exam_year = [];
	$exam_year[''] = '--Select Passing Year--';
	foreach ($data as $r) {
		foreach ($r as $key => $val) {
			$exam_year[$val] = $val;
		}
	}

	if (check_auth_user()) {
		return $data = user_permissions_array($exam_year, 'exam_year', 'Year');
	}

	return $exam_year;
}

function selective_current_exam_year()
{
	$current_year = date('Y') - 1;
	$exam_year[''] = '<--Select Exam Year-->';
	$exam_year["{$current_year}"] = "{$current_year}";
	return $exam_year;
}

function selective_multiple_subject($except = null)
{
	// $data = array('Bangla' => 'Bangla','English' => 'English','Arabic' => 'Arabic','History' => 'History','Islamic History And Culture' => 'Islamic History And Culture','Philosophy' => 'Philosophy','Political Science' => 'Political Science','Sociology' => 'Sociology' ,'Social Work' => 'Social Work' ,'Economics' => 'Economics' ,'Accounting' => 'Accounting' ,'Management' => 'Management' ,'Physics' => 'Physics' ,'Chemistry' => 'Chemistry' ,'Botany' => 'Botany' ,'Zoology' => 'Zoology' ,'Geography And Environment' => 'Geography And Environment' ,'Psychology' => 'Psychology' ,'Statistics' => 'Statistics' ,'Mathematics' => 'Mathematics' ,'Marketing' => 'Marketing' ,'Finance and Banking' => 'Finance and Banking' ,'Islamic Studies' => 'Islamic Studies' ,'Home Economics' => 'Home Economics' ,'Sanskrit' => 'Sanskrit' ,'Urdu' => 'Urdu','B.S.S'=>'B.S.S','B.A'=>'B.A','B.B.S'=>'B.B.S','B.S.C'=>'B.S.C');

	if ($except == 'degree') {
		$exceptSubjects = [
			'Sociology' => 'Sociology',
			'Social Work' => 'Social Work',
			'Social Science' => 'Social Science',
			'Psychology' => 'Psychology',
			'Statistics' => 'Statistics'
		];
	}

	$data = create_option_array('departments', 'dept_name', 'dept_name', 'Subject');

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'subject', 'Subject');
	}

	if (isset($exceptSubjects)) {
		$data = array_diff_key($data, $exceptSubjects);
	}

	return $data;
}

function selective_masters1st_subjects()
{
	$data = array('' => '<--Please Select a Subject-->', 'Bangla' => 'Bangla', 'Islamic History And Culture' => 'Islamic History And Culture', 'Political Science' => 'Political Science', 'Economics' => 'Economics', 'Accounting' => 'Accounting', 'Zoology' => 'Zoology', 'Geography And Environment' => 'Geography And Environment');
	return $data;
}
function selective_masters_subjects()
{
	$data = array('' => '<--Please Select a Subject-->', 'Bangla' => 'Bangla', 'English' => 'English', 'History' => 'History', 'Islamic History And Culture' => 'Islamic History And Culture', 'Philosophy' => 'Philosophy', 'Political Science' => 'Political Science', 'Sociology' => 'Sociology', 'Economics' => 'Economics', 'Accounting' => 'Accounting', 'Management' => 'Management', 'Physics' => 'Physics', 'Chemistry' => 'Chemistry', 'Botany' => 'Botany', 'Zoology' => 'Zoology', 'Geography And Environment' => 'Geography And Environment', 'Mathematics' => 'Mathematics', 'Islamic Studies' => 'Islamic Studies');
	return $data;
}

function selective_masters_application_subjects()
{
	$data = array('' => '<--Please Select a Subject-->', 'Bangla' => 'Bangla', 'English' => 'English', 'Islamic History And Culture' => 'Islamic History And Culture', 'Philosophy' => 'Philosophy', 'Political Science' => 'Political Science', 'Economics' => 'Economics', 'Accounting' => 'Accounting', 'Management' => 'Management', 'Physics' => 'Physics', 'Chemistry' => 'Chemistry', 'Botany' => 'Botany', 'Zoology' => 'Zoology', 'Geography And Environment' => 'Geography And Environment', 'Mathematics' => 'Mathematics');
	return $data;
}

function selective_honours_departments()
{
	$data = create_option_array('departments', 'dept_name', 'dept_name', 'Department', ['active_status=' => 1, 'and course LIKE' => '%honours%']);

	if (check_auth_user()) {
		$data = user_permissions_array($data, 'subject', 'Subject');
	}

	return $data;
}

function selective_departments()
{
	$data = create_option_array('departments', 'dept_name', 'dept_name', 'Department');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'department', 'Department');
	}
	return $data;
}

function selective_student_type()
{
	return ['' => '<--Select Student Type-->', 'general' => 'General', 'special' => 'Special', 'cc' => 'CC', 'others' => 'Others'];
}

function selective_member_type()
{
	return ['' => '<--Select User Type-->', 'student' => 'Student', 'teacher' => 'Teacher'];
}

function selective_formfillup_type()
{
	return ['' => '<--Select Form Fillup Type-->', 'regular' => 'Regular', 'irregular' => 'Irregular', 'improvement' => 'Improvement', 'private' => 'Private', 'special' => 'Special', 'cc' => 'CC', 'others' => 'Others'];
}

function selective_pay_type()
{
	return ['' => '<--Select Pay Type-->', 'general' => 'General', 'paper' => 'Paper', 'others' => 'Others'];
}

function selective_multiple_group()
{
	return array('' => '<--Select Group-->', 'hsc' => 'HSC', 'honours' => 'Honours', 'degree' => 'Degree', 'masters' => 'Masters');
}

function selective_multiple_type()
{
	return array('' => '<--Select Type-->', 'application' => 'Application', 'admission' => 'Admission', 'registration' => 'Registration', 'formfillup' => 'Form Fillup', 'others' => 'Others Fee', '2nd_year_promotion' => 'HSC 2nd Year Promotion', 're_admission' => 'Re Admission', 'fees_payment' => 'Fees Payment');
}

function selective_faculties()
{
	$data = create_option_array('faculties', 'faculty_name', 'faculty_name', 'Faculty');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'faculty', 'Faculty');
	}
	return $data;
}

function selective_multiple_payslip_header()
{
	$options[''] = 'Select PaySlip Header';
	$headers = app('App\Models\PayslipHeader')->pluck('title', 'id');

	$options = array_merge_recursive($options, $headers->toArray());


	return $options;
}

function selective_boards()
{
	return ['' => '--Select Board--', 'Barishal' => 'Barisal', 'Chittagong' => 'Chittagong', 'Cumilla' => 'Cumilla', 'Dhaka' => 'Dhaka', 'Dinajpur' => 'Dinajpur', 'Jashore' => 'Jashore', 'Rajshahi' => 'Rajshahi', 'Sylhet' => 'Sylhet', 'Dibs' => 'Dibs Dhaka', 'Madrasah' => 'Madrasah', 'TEC' => 'TEC', 'BTEB' => 'BTEB', 'Mymensingh' => 'Mymensingh'];
}
function selective_stu_info_boards()
{
	$board_options = array('' => '--Select Board--');
	$boards = DB::table('hsc_admitted_students')->groupBy('ssc_board')->pluck('ssc_board')->toArray();
	foreach ($boards as $board) {
		$board = ucfirst(strtolower($board));
		$board_options[$board] = $board;
	}
	return $board_options;
}

function selective_multiple_study_group()
{
	$data = array('' => '--Select Group--', 'Science' => 'Science', 'Humanities' => 'Humanities', 'Business Studies' => 'Business Studies');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'hsc_group', 'Group');
	}

	return $data;
}

function selective_hsc_groups()
{
	$data =  create_option_array('groups', 'name', 'name', 'Group');
	if (check_auth_user()) {
		$data = user_permissions_array($data, 'hsc_group', 'Group');
	}

	return $data;
}

function selective_hsc_current_level()
{
	return ['' => '<--Select Status-->', 'HSC 1st Year' => 'HSC 1st Year', 'HSC 2nd Year' => 'HSC 2nd Year'];
}

function selective_gender_list()
{
	return ['' => '<--Select Gender-->', 'Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'];
}

function selective_religion_list()
{
	return ['' => '<--Select-->', 'Islam' => 'Islam', 'Hinduism' => 'Hinduism', 'Chirstian' => 'Chirstian', 'Buddhist' => 'Buddhist'];
}

function selective_income()
{
	return ['' => '<--Select-->', '10000-50000' => '10000-50000', '50000-100000' => '50000-100000', '100000-150000' => '100000-150000', '150000-200000' => '150000-200000', '200000-300000' => '200000-300000', '300000-400000' => '300000-400000', '400000-500000' => '400000-500000', '500000-900000' => '500000-900000'];
}

function selective_admission_status()
{
	return ['' => '<--Select Status-->', 'running' => 'Running', 'admitted' => 'Admitted', 'closed' => 'Closed'];
}

function current_session_generate()
{
	$current_year = date("Y");
	$current_year2 = $current_year + 1;
	$current_session = $current_year . '-' . $current_year2;

	return $current_session;
}

function default_zero($var)
{
	if ($var == '' || $var == null) {
		return 0;
	} else {
		return $var;
	}
}

if (! function_exists('create_option')) {
	function create_option($table, $value, $display, $selected = "", $where = NULL)
	{
		$options = "<option<--Select $display-->/option>";
		$condition = "";
		if ($where != NULL) {
			$condition .= "WHERE ";
			foreach ($where as $key => $v) {
				$condition .= $key . "'" . $v . "' ";
			}
		}

		$query = DB::select("SELECT $value, $display FROM $table $condition");
		foreach ($query as $d) {
			if ($selected != "" && $selected == $d->$value) {
				$options .= "<option value='" . $d->$value . "' selected='true'>" . ucwords($d->$display) . "</option>";
			} else {
				$options .= "<option value='" . $d->$value . "'>" . ucwords($d->$display) . "</option>";
			}
		}

		return $options;
	}
}

if (! function_exists('create_option_array')) {
	function create_option_array($table, $value, $display, $text = NULL, $where = NULL)
	{
		if ($text != NULL) {
			$text = $text;
		} else {
			$text = 'One';
		}
		$data = ['' => '--Please Select ' . $text . ' --'];
		$condition = "";
		if ($where != NULL) {
			$condition .= "WHERE ";
			foreach ($where as $key => $v) {
				$condition .= $key . "'" . $v . "' ";
			}
		}
		$query = DB::select("SELECT $value, $display FROM $table $condition");
		foreach ($query as $d) {
			$data[$d->$value] = ucwords($d->$display);
		}
		return $data;
	}
}
if (! function_exists('sendSms')) {
	function sendSms($message, $number)
	{
		$number = '88' . $number;
		$smsUsername =  SMS_USERNAME;
		$smsPassword =  SMS_PASSWORD;

		$url = "https://rajsms.net/api/quick/send?username=" . urlencode($smsUsername) . "&password=" . urlencode($smsPassword) . "&message=" . urlencode($message) . "&number=" . urlencode($number);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response, true);
		curl_close($ch);

		return $response['status'];
	}
}

if (!function_exists('mysql_escape')) {
	function mysql_escape($inp)
	{
		if (is_array($inp)) return array_map(__METHOD__, $inp);

		if (!empty($inp) && is_string($inp)) {
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
		}

		return $inp;
	}
}

function create_dir($folder)
{
	if (!is_dir($folder)) {
		mkdir($folder);
		//mkdir($folder, 0777, true);
		chmod($folder, 0777);
	}
}

function options_by_array($array, $name = null)
{
	$options = ['' => 'Select ' . $name];
	foreach ($array as $key => $val) {
		$options[$key] = $val;
	}
	return $options;
}

function auto_id_hons($auto_id)
{
	return $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
}

function hons_tracking_auto_id($auto_id)
{
	$query = HONS_PREF;
	if (substr($auto_id, 0, strlen($query)) === $query) {
		$tracking_id = substr($auto_id, strlen($query));
		return $tracking_id;
	} else {
		return false;
	}
}

function auto_id_msc($auto_id)
{
	return $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
}

function msc_tracking_auto_id($auto_id)
{
	$query = MSC2ND_PREF;
	if (substr($auto_id, 0, strlen($query)) === $query) {
		$tracking_id = substr($auto_id, strlen($query));
		return $tracking_id;
	} else {
		return false;
	}
}

function auto_id_deg($auto_id)
{
	return $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
}

function deg_tracking_auto_id($auto_id)
{
	$query = DEGREE_PREF;
	if (substr($auto_id, 0, strlen($query)) === $query) {
		$tracking_id = substr($auto_id, strlen($query));
		return $tracking_id;
	} else {
		return false;
	}
}

function auto_id_msc1st($auto_id)
{
	return $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
}

function msc1st_tracking_auto_id($auto_id)
{
	$query = MSC1ST_PREF;
	if (substr($auto_id, 0, strlen($query)) === $query) {
		$tracking_id = substr($auto_id, strlen($query));
		return $tracking_id;
	} else {
		return false;
	}
}

function auto_id_hsc($auto_id)
{
	return $auto_id = str_pad($auto_id, '4', '0', STR_PAD_LEFT);
}

function hsc_tracking_auto_id($auto_id)
{
	$query = HSC_PREF;
	if (substr($auto_id, 0, strlen($query)) === $query) {
		$tracking_id = substr($auto_id, strlen($query));
		return $tracking_id;
	} else {
		return false;
	}
}

function paginate_info($entries)
{
	echo 'Showing ' . $entries->firstItem() . ' to ' .  $entries->lastItem() . ' of ' . $entries->total() . ' entries';
}

function filter_empty_array($values)
{
	if (isset($values)) {
		$res = array_filter($values, function ($value) {
			return ($value !== null && $value !== false && $value !== '' && $value !== '0');
		});
		return $res;
	}
	return array();
}

function student_course_list()
{
	return ['' => '<--Select Course-->', 'hsc' => 'HSC', 'honours' => 'Honours', 'masters' => 'Masters',  'degree' => 'Degree'];
}

function ajax_modal()
{
	return view('BackEnd.common.ajax_modal');
}

function ajax_basic_modal()
{
	return view('BackEnd.common.ajax_basic_modal');
}

function ajax_crud_setup_dtable()
{
	return view('BackEnd.common.ajax_crud_module_with_dtable');
}

function ajax_crud_basic_setup()
{
	return view('BackEnd.common.ajax_crud_basic_module');
}

function getEnumValues($table, $column)
{
	$type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '{$column}'"))[0]->Type;
	preg_match('/^enum\((.*)\)$/', $type, $matches);
	$enum = array();
	foreach (explode(',', $matches[1]) as $value) {
		$v = trim($value, "'");
		$enum[] = $v;
	}
	$column = str_replace('_', ' ', ucwords($column));
	$value = array("" => "<--Select $column-->");
	foreach ($enum as $val) {
		$value[$val] = $val;
	}
	return $value;
}

function unset_empty_key($array)
{
	unset($array['0']);
	unset($array['']);
	return $array;
}

function get_badge_status($type, $val)
{
	$text = '';
	//admission config
	if ($type == 'open') {
		if ($val == 1) {
			$text = 'open';
			$status = 'success';
		} else {
			$text = 'closed';
			$status = 'danger';
		}
	}

	if ($type == 'admission_config_type') {
		$text = ucfirst($val);
		if ($val == 'application') {
			$status = 'primary';
		} else if ($val == 'admission') {
			$status = 'success';
		} else if ($val == 'migration' || $val == 'registration') {
			$status = 'info';
		} else {
			$status = 'warning';
		}
	}
	return "<span class='badge badge-{$status}'>{$text}</span>";
}

function Configurations()
{
	return app('App\Models\Configuration');
}

function makeZip($filename, $path)
{
	array_map('unlink', array_filter((array) glob(public_path($filename))));

	$zip = new \ZipArchive();
	$fileName = $filename;
	// return public_path($fileName);
	if ($zip->open(public_path($fileName), \ZipArchive::CREATE) == TRUE) {
		$files = File::files(public_path($path));
		foreach ($files as $key => $value) {
			$relativeName = basename($value);
			$zip->addFile($value, $relativeName);
		}
		$zip->close();
	}

	array_map('unlink', array_filter((array) glob(public_path($path . '/*'))));

	// return redirect()->to(url().'/'.($fileName));

	return response()->download(public_path($fileName));
}

function contains($needle, $haystack)
{
	return strpos($haystack, $needle) !== false;
}

function get_config($key)
{
	$con = Configuration::where('key_title', $key)->select('value')->first();
	if (!is_null($con)) {
		return $con->value;
	}
}

function configTempleteToBody($body, $data = null)
{
	$body = str_replace('[student_id]', @$data['student_id'], $body);
	$body = str_replace('[biller_id]', @$data['biller_id'], $body);
	$body = str_replace('[college_name_bn]', @$data['college_name_bn'], $body);
	$body = str_replace('[college_name]', @$data['college_name'], $body);
	$body = str_replace('[total_amount]', @$data['total_amount'], $body);
	return $body;
}

function numtobn($number)
{
	$replace_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০", "জানু", "ফেব্রু", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টে.", "অক্টো.", "নভে.", "ডিসে.");
	$search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	$bn_number = str_replace($search_array, $replace_array, $number);
	return $bn_number;
}

function groupBnName($group)
{
	$data = [
		'Humanities' => 'মানবিক',
		'Science' => 'বিজ্ঞান',
		'Business Studies' => 'ব্যবসায় শিক্ষা'
	];

	return $data[$group];
}

function addCustomFontToMpdf()
{
	$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
	$fontDirs = $defaultConfig['fontDir'];

	$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
	$fontData = $defaultFontConfig['fontdata'];

	return [
		'fontDir' => array_merge($fontDirs, [
			public_path() . '/fonts/custom',
		]),
		'fontdata' => $fontData + [
			'nikosh' => [
				'R' => 'NikoshBAN-Regular.ttf',
			],
			'lato' => [
				'R' => 'Lato-Regular.ttf',
				'BL' => 'Lato-Black.ttf',
				'B' => 'Lato-Bold.ttf',
				'H' => 'Lato-Heavy.ttf',
				'S' => 'Lato-Semibold.ttf',
				'I' => 'Lato-Italic.ttf',
			],
			'siyam-rupali' => [
				'R' => 'Siyam-Rupali-Regular.ttf'
			]
		]
	];
}

function addMpdfPageSetup($mpdf, array $options = array())
{
	$options = [
		'watermark' => true,
		'footer' => true,
	];

	$mpdf->ignore_invalid_utf8 = true;
	$mpdf->showImageErrors = true;
	$mpdf->autoScriptToLang = true;
	$mpdf->autoVietnamese = true;
	$mpdf->autoArabic = true;
	$mpdf->autoLangToFont = true;
	if (isset($options['watermark']) && $options['watermark']) {
		$mpdf->SetWatermarkImage(asset('upload/sites/' . config('settings.site_logo')), .09, array(110, 110));
		$mpdf->showWatermarkImage = true;
	}

	if (isset($options['footer']) && $options['footer']) {
		$mpdf->SetHTMLFooter('<p style="vertical-align: bottom; font-family: serif; 
		font-size: 8pt; color: #000000; font-weight: bold; font-style: italic; text-align:center">Developed & Maintained by <img style="width:75px; margin-bottom:-5px;" src="' . asset('img/company.png') . '"></p>');
	}
}

if (!function_exists('fileUpdate')) {
	function fileUpdate($databaseFile, $file, $destination)
	{

		$fileName = "";
		if ($file) {
			$fileName = fileUpload($file, $destination);

			if ($databaseFile && file_exists(public_path($databaseFile))) {

				unlink(public_path($databaseFile));
			}
		} elseif (!$file and $databaseFile) {
			$fileName = $databaseFile;
		}

		return $fileName;
	}
}

if (!function_exists('fileUpload')) {
	function fileUpload($file, $destination)
	{
		$fileName = "";

		if (!$file) {
			return $fileName;
		}

		if (in_array($file->getClientOriginalExtension(), ['php', 'js', 'cs', 'py'])) {
			$extension = 'bad';
		} else {
			$extension = $file->getClientOriginalExtension();
		}

		$fileName = md5($file->getClientOriginalName() . time()) . "." . $extension;

		if (!file_exists(public_path($destination))) {
			mkdir(public_path($destination), 0777, true);
		}
		Storage::disk('upload')->putFileAs($destination, $file, $fileName);
		$fileName = 'upload/' . $destination . $fileName;
		return $fileName;
	}
}

if (!function_exists('fileDelete')) {
	function fileDelete()
	{
		foreach (func_get_args() as $filePath) {
			$realPath = public_path('/') . $filePath;
			if (File::exists($realPath)) {
				File::delete($realPath);
			}
		}
	}
}

if (!function_exists('getFilePath3')) {
	function getFilePath3($data)
	{

		if ($data) {
			$name = explode('/', $data);
			return $name[3] ?? $name[0];
		} else {
			return '';
		}
	}
}

if (!function_exists('getFilePath4')) {
	function getFilePath4($data)
	{
		if ($data) {
			$name = explode('/', $data);
			if ($name[4]) {
				return $name[3];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}

if (!function_exists('showPicName')) {
	function showPicName($data)
	{
		if ($data) {
			$name = explode('/', $data);
			if ($name[4]) {
				return $name[4];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}

function year_generate()
{
	$cur_year = date("Y");
	$year = array();
	for ($k = 0; $k <= 60; $k++) {
		$year[$k] = $cur_year - $k;
	}
	return $year;
}

function educationLevels()
{
	return [
		''	=> '--Select--',
		'Secondary' => 'Secondary',
		'Higher Secondary' => 'Higher Secondary',
		'Diploma' => 'Diploma',
		'Bachelor/Honors' => 'Bachelor/Honors',
		'Masters' => 'Masters',
		'Doctoral' => 'Doctoral',
		'Others' => 'Others',
	];
}

function educationResults()
{
	return [
		'' => '--Select--',
		'First Division/Class' => 'First Division/Class',
		'Second  Division/Class' => 'Second  Division/Class',
		'Third Division/Class' => 'Third Division/Class',
		'Grade' => 'Grade',
		'Appeared' => 'Appeared',
		'Enrolled' => 'Enrolled',
		'Awarded' => 'Awarded',
		'Don not  mention' => 'Don not  mention'
	];
}

function sendError($error, $status = 'error', $code = 406)
{
	$response = [
		'success' => false,
		'status' => $status,
		'error' => $error,
	];

	return response()->json($response, $code);
}
function pages($path)
{
	return 'BackEnd.' . $path;
}

function activeStatus($status)
{
	$text = [
		0 => '<span class="badge badge-sm badge-gradient-danger">Inactive</span>',
		1 => '<span class="badge badge-sm badge-gradient-success">Active</span>',
		'active' => '<span class="badge badge-sm badge-gradient-success">Active</span>',
		'inactive' => '<span class="badge badge-sm badge-gradient-success">Inactive</span>'
	];
	return @$text[$status];
}

function booleanStatus($status)
{
	$text = [
		0 => '<span class="badge badge-gradient-danger">No</span>',
		1 => '<span class="badge badge-gradient-success">Yes</span>'
	];
	return @$text[$status];
}

function getOrdinalSuffix($number)
{
	if ($number % 100 >= 11 && $number % 100 <= 13) {
		$suffix = 'th';
	} else {
		switch ($number % 10) {
			case 1:
				$suffix = 'st';
				break;
			case 2:
				$suffix = 'nd';
				break;
			case 3:
				$suffix = 'rd';
				break;
			default:
				$suffix = 'th';
		}
	}

	return $suffix;
}

function isCgpaInRange($cgpa, $gpaRange)
{
	$rangeParts = explode('-', $gpaRange);

	if (count($rangeParts) === 2) {
		list($low, $high) = $rangeParts;
		return ($cgpa >= (float) trim($low) && $cgpa <= (float) trim($high));
	} elseif (count($rangeParts) === 1) {
		return ($cgpa == (float) trim($rangeParts[0]));
	}
	return false;
}

function getNumericalValue($gpaRange)
{
	// Extract the numerical value (e.g., "4.00 - 4.99" => 4)
	preg_match('/(\d+(\.\d+)?)/', $gpaRange, $matches);
	return $matches[1] ?? null;
}

function nameToBangla($sentense)
{
	$bangla = array(
		'HSC 1st Year' => 'এইচএসসি ১ম বর্ষ',
		'HSC 2nd Year' => 'এইচএসসি ২য় বর্ষ',
		'Honours 1st Year' => 'স্নাতক ১ম বর্ষ',
		'Honours 2nd Year' => 'স্নাতক ২য় বর্ষ',
		'Honours 3rd Year' => 'স্নাতক ৩য় বর্ষ',
		'Honours 4th Year' => 'স্নাতক ৪র্থ বর্ষ',
		'Degree 1st Year' => 'স্নাতক ১ম বর্ষ',
		'Degree 2nd Year' => 'স্নাতক ২য় বর্ষ',
		'Degree 3rd Year' => 'স্নাতক ৩য় বর্ষ',
		'Masters 1st Year' => 'স্নাতকোত্তর ১ম বর্ষ',
		'Masters 2nd Year' => 'স্নাতকোত্তর ২য় বর্ষ',
	);
	return @strtr($sentense, $bangla) ?? null;
}

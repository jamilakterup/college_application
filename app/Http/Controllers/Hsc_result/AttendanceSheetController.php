<?php

namespace App\Http\Controllers\Hsc_result;

use Ecm;
use Mpdf\Mpdf;
use App\Models\Exam;
use App\Models\Group;
use App\Models\Classe;
use App\Models\ClassExam;
use Illuminate\Http\Request;
use App\Models\StudentInfoHsc;
use App\Models\StudentSubInfo;
use App\Models\MarkInputConfig;
use App\Models\ConfigExamParticle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AttendanceSheetController extends Controller
{
	public function index()
	{

		$userId = Auth::id();
		$result_group = Group::join('user_group_assign', 'groups.id', '=', 'user_group_assign.group_id')->where('user_group_assign.user_id', $userId)->selectRaw('name, groups.id')->orderBy('groups.id')->pluck('name', 'id')->toArray();
		//return $result_group;
		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.attendance_sheet.index:attendance sheet|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = ['' => 'Select'] + Classe::orderBy('id')->pluck('name', 'id')->toArray();
		$group_lists = ['' => 'Select group'] + $result_group;
		$exam_lists = ['' => 'Select exam'] /*+ Exam::orderBy('id')->lists('name', 'id')*/;
		$subject_lists = ['' => 'Select Subject'];

		return view('BackEnd.hsc_result.attendance_sheet.index', compact('title', 'current_yr_lists', 'group_lists', 'exam_lists', 'subject_lists', 'breadcrumb'));
	}





	public function create() {}

	public function store(Request $request)
	{

		if ($request->isMethod('post')) {
			$session = $request->get('session');
			$group_id =  $request->get('group_id');
			$current_level = $request->get('current_level');
			$exam_id = $request->get('exam_id');
			$subject_id = $request->get('subject_id');
			$exam_date = $request->get('exam_date');
			$room_no = $request->get('room_no');
			$from_roll = $request->get('from_roll');
			$to_roll = $request->get('to_roll');
			$str = $request->get('str');


			$myArray = explode(',', $str);

			$subject_name = $request->get('subject_name');

			if ($session == '') :
				$error_message = 'Select Session';
				return Redirect::back()->withInput()->with('error', $error_message);
			endif;
			if ($current_level == '') :
				$error_message = 'Select Current Year';
				return Redirect::back()->withInput()->with('error', $error_message);
			endif;
			if ($group_id == '') :
				$error_message = 'Select Group';
				return Redirect::back()->withInput()->with('error', $error_message);
			endif;
			if ($exam_id == '') :
				$error_message = 'Select Exam';
				return Redirect::back()->withInput()->with('error', $error_message);
			endif;
			if ($subject_id == '') :
				$error_message = 'Select Subject';
				return Redirect::back()->withInput()->with('error', $error_message);
			endif;

			$curr_level = Classe::find($current_level);
			$group_name = Group::find($group_id);
			$exam_name = Exam::find($exam_id);


			$student_infos_ids = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group_name->name)->orderBy('id')->pluck('id');

			$student_infos = StudentSubInfo::whereSession($session)->whereCurrent_level($curr_level->name)->where('group_id', $group_id)->whereIn('student_id', $myArray)->where('student_id', '>=', $from_roll)->where('student_id', '<=', $to_roll)->whereIn('student_id', $student_infos_ids)->orderBy('id')->groupBy('student_id')->get();
			$student_info_ids = [];

			foreach ($student_infos as $student_info) :
				$field_name = 'info-' . $student_info->student_id;
				//if($student_info->id == $request->get($field_name)) :
				$student_info_ids[] = $student_info->student_id;
			//endif;
			endforeach;


			$cnt = count($student_info_ids);
			$student_info_hsc = StudentInfoHsc::whereIn('id', $student_info_ids)->get();
			$f_name = $student_info_ids[0] . '-' . $student_info_ids[$cnt - 1] . '.pdf';

			$orientation = ($subject_name === 'ICT') ? 'L' : 'P';
			$mpdf = new Mpdf([
				'format' => 'A4',
				'orientation' => $orientation,
			]);

			$mpdf->allow_charset_conversion = true;
			$mpdf->charset_in = 'UTF-8';
			$mpdf->AddPage();
			$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.attendence_sheet', compact('student_info_hsc', 'exam_name', 'group_id', 'exam_id', 'exam_date', 'room_no', 'subject_name')));



			$mpdf->Output($f_name, 'D');
		}
	}

	public function marklist(Request $request)
	{

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.attendance_sheet.index:attendance sheet|Dashboard';


		$session = Ecm::filterInput('session', $request->get('session'));
		$group = Ecm::filterInput('group', $request->get('group'));
		$current_level = Ecm::filterInput('current_year', $request->get('current_year'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));
		$subject_id = Ecm::filterInput('subject_id', $request->get('subject_id'));


		if ($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($current_level == '') :
			$error_message = 'Select Current Year';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($group == '') :
			$error_message = 'Select Group';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($exam_id == '') :
			$error_message = 'Select Exam';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		if ($subject_id == '') :
			$error_message = 'Select Subject';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		$chk_exam = ClassExam::whereExam_id($exam_id)->whereClasse_id($current_level)->count();
		if ($chk_exam == 0) :
			$error_message = 'Exam Not Assign';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		$check_exp_dates = MarkInputConfig::whereSession($session)->whereExam_id($exam_id)->get();
		$have_exam = count($check_exp_dates);
		if ($have_exam > 0) {
			foreach ($check_exp_dates as $check_exp_date) {
				$exm_exp_date = $check_exp_date->exp_date;
			}
		} else {
			$error_message = 'No exam mark input date set';
			return Redirect::back()->withInput()->with('error', $error_message);
		}
		$is_exam_controller = 0;

		if (Auth::user()->can('hsc_result.process')) {
			$is_exam_controller = 1;
		}

		$current_date = date('Y-m-d');



		$grp = Group::find($group);
		if ($grp->name == 'Science'):
			$append = 1;
		elseif ($grp->name == 'Humanities'):
			$append = 2;
		elseif ($grp->name == 'Business Studies'):
			$append = 3;
		endif;

		$session_arr = explode('-', $session);
		$like_vr = $session_arr[0] . $append;
		$group_name = Group::find($group);
		$curr_level = Classe::find($current_level);
		$student_infos_ids = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group_name->name)->orderBy('id')->pluck('id');

		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($grp->name)->orderBy('id')->paginate(50);
		$student_info_count = StudentInfoHsc::where('session', '=', $session)->whereCurrent_level($curr_level->name)->whereGroups($grp->name)->orderBy('id')->get();
		$config_exam_particles = ConfigExamParticle::whereClasse_id($curr_level->id)
			->whereGroup_id($group)
			->whereSubject_id($subject_id)
			->get();


		$sub1_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub1_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);


		$sub2_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub2_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);


		$sub3_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub3_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$sub4_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub4_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$sub5_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub5_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$sub6_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub6_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$sub7_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub21_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$sub8_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub22_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);
		$sub9_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub23_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);
		$sub10_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub24_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);
		$sub11_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub25_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);
		$sub12_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('sub26_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);
		$fourth_chk2 = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('fourth2_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$fourth_chk = DB::table('student_subject_info')
			->where('current_level', '=', $curr_level->name)
			->where('group_id', '=', $group)
			->where('session', '=', $session)
			->where('fourth_id', '=', $subject_id)
			->whereIn('student_id', $student_infos_ids);

		$student_info = $sub1_chk->union($sub2_chk)->union($sub3_chk)->union($sub4_chk)->union($sub5_chk)->union($sub6_chk)->union($fourth_chk)->union($sub7_chk)->union($sub8_chk)->union($sub9_chk)->union($sub10_chk)->union($sub11_chk)->union($sub12_chk)->union($fourth_chk2)->orderby('student_id')->paginate(50);


		$student_info_ids = $sub1_chk->union($sub2_chk)->union($sub3_chk)->union($sub4_chk)->union($sub5_chk)->union($sub6_chk)->union($fourth_chk)->union($sub7_chk)->union($sub8_chk)->union($sub9_chk)->union($sub10_chk)->union($sub11_chk)->union($sub12_chk)->union($fourth_chk2)->orderby('student_id')->get();

		$str = implode(",", array_column($student_info_ids->toArray(), 'student_id'));


		$page = $request->get('page', 1);
		$paginate = 50;


		$slice = array_slice($student_info->toArray(), $paginate * ($page - 1), $paginate);
		// $student_info = new Paginator($slice, count($student_info), $paginate);  	


		return view('BackEnd.hsc_result.attendance_sheet.list', compact('title', 'session', 'group', 'student_info_count', 'current_level', 'curr_level', 'student_info', 'exam_id', 'subject_id', 'config_exam_particles', 'str', 'breadcrumb'));
	}



	public function show($id) {}



	public function edit($id) {}



	public function update($id) {}

	public function getExam($id)
	{

		$exam_list = ClasseExam::where('classe_id', '=', $id)->get();

		$exam_arr = [];
		foreach ($exam_list as  $value):
			$exam_arr[$value->exam_id] = $value->exam->name;
		endforeach;

		return Response::json(['success' => true, 'exam_arr' => $exam_arr]);
	}

	public function getSubject($year, $id)
	{
		$userId = Auth::id();
		$sub_list = ClasseSubject::join('user_sub_assign', 'classe_subject.subject_id', '=', 'user_sub_assign.subject_id')->where('user_sub_assign.user_id', $userId)->whereClasse_id($year)->whereGroup_id($id)->get();
		//return $result_subject;
		//$sub_list =ClasseSubject::whereClasse_id($year)->whereGroup_id($id)->get();

		$sub_arr = [];
		foreach ($sub_list as  $value):
			$sub_arr[$value->subject_id] = $value->subject->name . '(' . $value->subject->code . ')';
		endforeach;

		return Response::json(['success' => true, 'sub_arr' => $sub_arr]);
	}


	public function getClasstest($examid)
	{

		$classtest_list = ClasseTestExam::whereExam_id($examid)->get();

		$classtest_arr = [];
		foreach ($classtest_list as  $value):
			$classtest_names = ClassTest::whereId($value->class_test_id)->get();
			foreach ($classtest_names as  $classtest_name):
				$classtest_arr[$classtest_name->id] = $classtest_name->name;
			endforeach;
		endforeach;
		if (count($classtest_arr) < 1) {
			$classtest_arr[0] = 'None';
		}
		return Response::json(['success' => true, 'sub_arr' => $classtest_arr]);
	}


	public function destroy($id) {}
}

<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\StudentSubInfo;
use App\Models\Subject;
use Ecm;
use Esm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingSubjectInfoController extends Controller
{
	public function index()
	{

		$title = 'Easy CollegeMate - Student Subject Info';
		$breadcrumb = 'hsc_result.subject_info.index:Student Subject Info|Dashboard';
		$student_id = request()->get('student_id');
		$session = request()->get('session');
		$query = StudentSubInfo::orderBy('id');
		if ($student_id != '') {
			$query->where('student_id', $student_id);
		}
		if ($session != '') {
			$query->where('session', $session);
		}

		$student_sub_infos = $query->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.subject_info.index')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->with('student_sub_infos', $student_sub_infos);
	}



	public function create()
	{

		$title = 'Easy CollegeMate - Add Exam';
		$breadcrumb = 'hsc_result.exam.index:Exam List|Add Exam';

		return view('hsc_result.exam.create')
			->withTitle($title)
			->withBreadcrumb($breadcrumb);
	}



	public function store(Request $request)
	{

		return 1;
		$data = $request->all();

		$validation = Exam::validate($data);

		if ($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		//Insert Exam
		$exam = new Exam;
		$exam->name = $request->get('name');
		$exam->save();

		//Page
		$page = ceil(Exam::count() / Esm::paginate());
		$id = $exam->id;

		$message = 'You have successfully created a new exam';
		return Redirect::route('hsc_result.exam.index', ['page' => $page])
			->with('success', $message)
			->withId($id);
	}



	public function show($id)
	{

		$exist = Exam::whereId($id)->count();

		if ($exist == 0) :
			$error_message = 'There is no exam with this id';
			return Redirect::route('hsc_result.exam.index')->with('error', $error_message);
		endif;

		$exam = Exam::find($id);

		$title = 'Easy CollegeMate - Exam - ' . $exam->name;
		$breadcrumb = 'hsc_result.exam.index:Exam List|Exam - ' . $exam->name;

		return view('hsc_result.exam.show')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withExam($exam);
	}



	public function edit($id)
	{

		$title = 'Easy CollegeMate - Edit Student Subject Info';
		$breadcrumb = 'hsc_result.subject_info.index:Student Subject Info|Edit Student Subject Info';
		$subject_info = StudentSubInfo::find($id);


		return view('BackEnd.hsc_result.subject_info.edit')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->with('subject_info', $subject_info);
	}



	public function update(Request $request, $id)
	{

		if ($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error', $error_message);
		endif;
		$data = $request->all();
		$validation = StudentSubInfo::updateValidate($data);
		if ($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;


		//Update Exam
		$update_1st_year = StudentSubInfo::find($id);
		$student_id = $update_1st_year->student_id;
		$update_1st_year->sub4_id = Subject::whereCode($request->get('sel_subject1'))->pluck('id')[0];
		$update_1st_year->sub5_id = Subject::whereCode($request->get('sel_subject2'))->pluck('id')[0];
		$update_1st_year->sub6_id = Subject::whereCode($request->get('sel_subject3'))->pluck('id')[0];
		$update_1st_year->fourth_id = Subject::whereCode($request->get('fourth_subject'))->pluck('id')[0];
		$update_1st_year->update();

		$stduent_2nd_year = StudentSubInfo::whereStudent_id($student_id)->whereCurrent_level('HSC 2nd Year')->get();
		$update_2nd_update = StudentSubInfo::find($stduent_2nd_year[0]->id);
		$update_2nd_update->sub4_id = Subject::whereCode($request->get('sel_subject1') + 1)->pluck('id')[0];
		$update_2nd_update->sub5_id = Subject::whereCode($request->get('sel_subject2') + 1)->pluck('id')[0];
		$update_2nd_update->sub6_id = Subject::whereCode($request->get('sel_subject3') + 1)->pluck('id')[0];
		$update_2nd_update->fourth_id = Subject::whereCode($request->get('fourth_subject') + 1)->pluck('id')[0];
		$update_2nd_update->update();

		//Page
		$count = StudentSubInfo::where('id', '<=', $id)->count();
		$page = ceil($count / Ecm::paginate());

		$message = 'You have successfully updated Subject Info';
		return Redirect::route('hsc_result.subject_info.index', ['page' => $page])
			->with('success', $message)
			->withId($id);
	}



	public function destroy($id) {}

	public function search()
	{

		$title = 'Easy CollegeMate - Edit Student Subject Info';
		$breadcrumb = 'hsc_result.subject_info.index:Student Subject Info|Dashboard';

		$student_id = Ecm::filterInput('student_id', $request->get('student_id'));
		$student_sub_infos = StudentSubInfo::whereStudent_id($student_id)->paginate(Ecm::paginate());

		return view('hsc_result.subject_info.index')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withStudent_sub_infos($student_sub_infos);
	}

	public function download_student_sub_data(Request $request)
	{
		$session = $request->get('session');
		$current_level = $request->get('current_level');

		if ($request->isMethod('post')) {

			$this->validate($request, [
				'session' => 'required',
				'current_level' => 'required'
			]);

			$subjects = Subject::all();
			$header = ['Roll', 'Name', 'Fathers Name', 'Mobile Number', 'groups'];

			foreach ($subjects as $subject) {

				$sub_infos = StudentSubInfo::join('student_info_hsc', 'student_info_hsc.id', '=', 'student_subject_info.student_id')->where('student_subject_info.session', $session)->where('student_subject_info.current_level', $current_level)->where('student_info_hsc.current_level', $current_level)
					->selectRaw('student_info_hsc.id as student_id,student_info_hsc.name,student_info_hsc.father_name,student_info_hsc.contact_no,student_info_hsc.groups,student_subject_info.sub1_id,student_subject_info.sub2_id,student_subject_info.sub3_id,student_subject_info.sub4_id,student_subject_info.sub5_id,student_subject_info.sub6_id,student_subject_info.fourth_id')
					->orderBy('student_info_hsc.groups', 'asc')
					->orderBy('student_info_hsc.id', 'asc')
					->get();

				$data = [];

				if (count($sub_infos) > 0) {
					foreach ($sub_infos as $info) {

						if ($info->sub1_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->sub2_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->sub3_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->sub4_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->sub5_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->sub6_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}

						if ($info->fourth_id == $subject->id) {
							$data[] = [$info->student_id, $info->name, $info->father_name, $info->contact_no, $info->groups];
						}
					}
					// return $data;
					array_unshift($data, $header);

					$file_name = public_path('generated_files/subject_students/' . $subject->name . '_' . $subject->code);

					if (count($data) > 1) {
						$handle = fopen($file_name . '.csv', 'w');
						foreach ($data as $row) {
							fputcsv($handle, $row);
						}
						fclose($handle);
					}
				}
			}

			$zipName = 'generated_files/' . $session . '.zip';
			$files_path = 'generated_files/subject_students';

			return makeZip($zipName, $files_path);
		}

		return view('BackEnd.hsc_result.subject_info.generate_subject_data', compact('session', 'current_level'));
	}
}

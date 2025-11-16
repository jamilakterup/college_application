<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassTestExam;
use App\Models\ClassTestMark;
use App\Models\Classe;
use App\Models\ClasseTestExam;
use App\Models\ConfigExamParticle;
use App\Models\Exam;
use App\Libs\ResultProcessHelper;
use App\Models\Group;
use App\Models\HscGpa;
use App\Models\HscRsltProcessing;
use App\Models\Mark;
use App\Models\StudentInfoHsc;
use App\Models\StudentSubInfo;
use App\Models\StudentSubMarkGp;
use App\Models\SubjectParticle;
use App\Models\GradeScale;
use DB;
use Ecm;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;
use App\Exports\HscResult\TabulationExport;
use App\Exports\HscResult\MeritListExport;

class ResultProcessingController extends Controller
{
	public function index()
	{
		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.process.index:Result Process|Dashboard';
		$processed_result = HscRsltProcessing::orderBy('id')->paginate(20);

		return view('BackEnd.hsc_result.process.index', compact('title', 'processed_result', 'breadcrumb'));
	}

	public function MeritListPdf($id)
	{

		$mpdf = new Mpdf(['format' => 'A4-L']);
		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = 'UTF-8';
		$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.meritlist')->withId($id));
		$mpdf->Output();
	}

	public function TotalMarksListPdf($id)
	{
		// return view('BackEnd.hsc_result.pdf.totalmarks', compact('id'));
		$mpdf = new Mpdf(['format' => 'A4-L']);
		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = 'UTF-8';
		$mpdf->ignore_invalid_utf8 = true;
		$mpdf->autoScriptToLang = true;
		$mpdf->autoVietnamese = true;
		$mpdf->autoArabic = true;
		$mpdf->autoLangToFont = true;
		$mpdf->AddPageByArray([
			'margin-top' => 10,
			'margin-bootom' => 10,
			'margin-left' => 10,
			'margin-right' => 10,
		]);
		$mpdf->SetHTMLFooter('<p style="vertical-align: bottom; font-family: serif; 
     	font-size: 8pt; color: #000000; font-weight: bold; font-style: italic; text-align:center">Developed & Maintained by <img style="width:75px; margin-bottom:-5px;" src="' . asset('img/company.png') . '"></p>');
		$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.totalmarks', compact('id')));
		$mpdf->Output();
	}

	public function TabulationPdf($id, $ex_id)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 9600);
		ini_set('request_terminate_timeout', 9600);
		ini_set('fastcgi_read_timeout', 9600);
		ini_set("pcre.backtrack_limit", "5000000");
		// error_reporting(E_ALL);
		error_reporting(E_ERROR | E_PARSE);
		ini_set('display_errors', 1);

		$exam = Exam::find($ex_id);
		$mpdf = new Mpdf(['mode' => 'c', 'format' => 'A4-L']);
		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = 'UTF-8';
		$mpdf->ignore_invalid_utf8 = true;
		$mpdf->autoScriptToLang = true;
		$mpdf->autoVietnamese = true;
		$mpdf->autoArabic = true;
		$mpdf->autoLangToFont = true;

		if ($exam->have_class_test >= 0) {
			$class_tests = DB::select("select * from class_test_assign where exam_id = $ex_id");
			$html = view('BackEnd.hsc_result.pdf.classtest_tabulation', compact('id', 'class_tests'));
		} else {
			$html = view('BackEnd.hsc_result.pdf.tabulation', compact('id'));
		}

		$mpdf->writeHTML($html);
		$mpdf->SetHTMLFooter('<p style="vertical-align: bottom; font-family: serif; 
     font-size: 8pt; color: #000000; font-weight: bold; font-style: italic; text-align:center">Developed & Maintained by <img style="width:75px; margin-bottom:-5px;" src="' . asset('img/company.png') . '"></p>');
		$mpdf->Output();
	}

	public function topTenIndex()
	{
		$exam_lists = create_option_array('exams', 'id', 'name', 'Exam');
		return view('BackEnd.hsc_result.process.topten', compact('exam_lists'));
	}

	public function topTenDownload(Request $request)
	{
		$this->validate($request, [
			'session' => 'required',
			'exam_year' => 'required',
			'exam_id' => 'required',
			'total_position' => 'required|numeric',
		]);

		$session = $request->session;
		$exam_year = $request->exam_year;
		$exam_id = $request->exam_id;
		$total_position = $request->total_position;

		$processes = HscRsltProcessing::where('session', $session)->where('exam_year', $exam_year)->where('exam_id', $exam_id)->get();
		$exam = Exam::find($exam_id);

		if (count($processes) < 1) {
			return redirect()->back()->with('warning', 'No Data Found');
		}

		DB::statement('UPDATE hsc_cgpa
		INNER JOIN (
		  SELECT student_id, SUM(total_mark) AS total_marks
		  FROM student_sub_mark_gp
		  WHERE session LIKE ? AND exam_year = ? AND exam_id = ?
		  GROUP BY student_id
		) AS sub_mark_totals
		ON sub_mark_totals.student_id = hsc_cgpa.student_id
		SET hsc_cgpa.total_marks = sub_mark_totals.total_marks
		WHERE hsc_cgpa.session LIKE ? AND hsc_cgpa.exam_year = ? AND hsc_cgpa.exam_id = ?', [
			$session,
			$exam_year,
			$exam_id,
			$session,
			$exam_year,
			$exam_id,
		]);

		$data = [];

		foreach ($processes as $process) {
			$groupName = Group::find($process->group_id)->name ?? null;

			$gpa = HscGpa::whereHas('subMark', function ($query) use ($session, $exam_year, $process, $exam_id) {
				$query->where('session', $session)
					->where('exam_year', $exam_year)
					->where('group_id', $process->group_id)
					->where('exam_id', $exam_id);
			})
				->select('hsc_cgpa.*')
				->withCount('subMark as sub_mark_count')
				->whereSession($session)
				->whereExam_year($exam_year)
				->whereGroup_id($process->group_id)
				->whereExam_id($exam_id)
				->orderByRaw('CASE WHEN grade = "F" THEN 2 ELSE 1 END')
				->orderBy('cgpa', 'desc')
				->orderBy('total_marks', 'desc')
				->orderBy('sub_mark_count', 'desc')
				->orderBy('without_4th', 'desc')
				->get();

			$i = 1;

			foreach ($gpa as $key => $gp) {
				$student = StudentInfoHsc::where('id', $gp->student_id)
					->select('name', 'id', 'contact_no')
					->first();

				if (is_null($student)) {
					continue;
				}

				$data[$groupName][$gp->student_id] = [
					'groups' => $groupName,
					'student_id' => $student->id,
					'name' => $student->name,
					'contact_no' => $student->contact_no,
					'total_mark' => $gp->total_marks,
					'cgpa' => $gp->cgpa,
					'without_4th' => $gp->without_4th,
					'grade' => $gp->grade,
					'position' => $gp->grade != 'F' ? $i . getOrdinalSuffix($i) : null
				];

				if ($gp->grade != 'F') {
					$i++;
				}
			}
		}

		$scales = GradeScale::orderBy('point', 'desc')->pluck('letter_grade')->toArray();
		$summary = [];

		foreach ($data as $group => $studentIds) {
			$summary[$group] = [
				'pass' => 0,
				'fail' => 0,
				'gpa_total' => 0
			];

			foreach ($studentIds as $student) {
				if ($student['grade'] != 'F') {
					$summary[$group]['pass']++;
					$summary[$group]['gpa_total'] += $student['cgpa'] ?? 0;
				} else {
					$summary[$group]['fail']++;
				}
			}
		}


		$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
		addMpdfPageSetup($mpdf);
		$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.topten', compact('data', 'exam', 'exam_year', 'total_position', 'summary', 'session', 'scales')));
		$mpdf->Output();
	}

	public function studentChartDetails(Request $request)
	{
		$session = $request->session;
		$exam_year = $request->exam_year;
		$exam_id = $request->exams;

		$processes = HscRsltProcessing::where('session', $session)->where('exam_year', $exam_year)->where('exam_id', $exam_id)->get();
		$exam = Exam::find($exam_id);

		if (count($processes) < 1) {
			return redirect()->back()->with('warning', 'No Data Found');
		}

		$data = [];

		foreach ($processes as $process) {
			$groupName = Group::find($process->group_id)->name ?? null;

			$gpa = HscGpa::whereHas('subMark', function ($query) use ($session, $exam_year, $process, $exam_id) {
				$query->where('session', $session)
					->where('exam_year', $exam_year)
					->where('group_id', $process->group_id)
					->where('exam_id', $exam_id);
			})
				->select('hsc_cgpa.*')
				->withCount('subMark as sub_mark_count')
				->whereSession($session)
				->whereExam_year($exam_year)
				->whereGroup_id($process->group_id)
				->whereExam_id($exam_id)
				->orderByRaw('CASE WHEN grade = "F" THEN 2 ELSE 1 END')
				->orderBy('cgpa', 'desc')
				->orderBy('total_marks', 'desc')
				->orderBy('sub_mark_count', 'desc')
				->orderBy('without_4th', 'desc')
				->get();

			$i = 1;

			foreach ($gpa as $key => $gp) {
				$student = StudentInfoHsc::where('id', $gp->student_id)
					->select('name', 'id', 'contact_no')
					->first();

				if (is_null($student)) {
					continue;
				}

				$data[$groupName][$gp->student_id] = [
					'groups' => $groupName,
					'student_id' => $student->id,
					'name' => $student->name,
					'contact_no' => $student->contact_no,
					'total_mark' => $gp->total_marks,
					'cgpa' => $gp->cgpa,
					'without_4th' => $gp->without_4th,
					'grade' => $gp->grade,
					'position' => getOrdinalSuffix($i)
				];

				$i++;
			}
		}

		$scales = GradeScale::orderBy('point', 'desc')->pluck('letter_grade')->toArray();
		$summary = [];

		foreach ($data as $group => $studentIds) {
			$summary[$group] = [
				'pass' => 0,
				'fail' => 0,
				'gpa_total' => 0
			];

			foreach ($studentIds as $student) {
				if ($student['grade'] != 'F') {
					$summary[$group]['pass']++;
					$summary[$group]['gpa_total'] += $student['cgpa'] ?? 0;
				} else {
					$summary[$group]['fail']++;
				}
			}
		}
		return view('BackEnd.hsc_result.result_reporting.student_report_chart', compact('data', 'exam', 'exam_year', 'summary', 'session', 'scales'));
	}


	public function create()
	{

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.process.index:Result Process|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Year');
		$group_lists = $group_lists = create_option_array('groups', 'id', 'name', 'Group');;
		$exam_lists = ['' => 'Select exam'];
		$subject_lists = ['' => 'Select Subject'];

		return view('BackEnd.hsc_result.process.create', compact('title', 'current_yr_lists', 'group_lists', 'exam_lists', 'subject_lists', 'breadcrumb'));
	}

	public function store(Request $request)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 9600);
		ini_set('request_terminate_timeout', 9600);
		ini_set('fastcgi_read_timeout', 9600);

		if ($request->isMethod('post')) {
			$this->validate($request, [
				'session' => 'required',
				'exam_year' => 'required',
				'current_level' => 'required',
				'group_id' => 'required',
				'exam_id' => 'required',
			], [
				'exam_id.required' => 'This exam field is required.',
				'group_id.required' => 'This group field is required.',
			]);

			$session = $request->get('session');
			$group_id = $request->get('group_id');
			$current_level = $request->get('current_level');
			$exam_id = $request->get('exam_id');
			$exam_year = $request->get('exam_year');
			$have_class_test = Exam::where('id', $exam_id)->pluck('have_class_test')->first();
			DB::beginTransaction();

			try {
				if ($have_class_test == 1) {
					$curr_level = Classe::find($current_level);
					$group_name = Group::find($group_id);
					$exam_name = Exam::find($exam_id);
					$student_infos = StudentInfoHsc::where('session', $session)
						->where('current_level', $curr_level->name)
						->where('groups', $group_name->name)
						->orderBy('id')
						->get();

					if ($student_infos->isEmpty()) {
						$error_message = "{$session} {$curr_level->name} {$group_name->name} have no students!";
						return Redirect::back()->withInput()->with('error', $error_message);
					}

					foreach ($student_infos as $student_info) {
						try {
							$maxSubjects = 26;
							$student_sub = StudentSubInfo::where('student_id', $student_info->id)
								->where('current_level', $student_info->current_level)
								->get();

							// Process subject marks
							for ($i = 1; $i <= $maxSubjects; $i++) {
								$subjectKey = "sub{$i}_id";
								if (property_exists($student_sub, $subjectKey)) {
									$subject_id = $student_sub->$subjectKey;

									if ($subject_id != 0) {
										ResultProcessHelper::processTestSubjectMarks($subject_id, $student_info, $session, $group_id, $exam_id, $curr_level);
									}
								}
							}

							// Process additional subjects
							ResultProcessHelper::processTestAdditionalSubjects($student_sub[0], $student_info, $session, $group_id, $exam_id, $curr_level);

							// Calculate CGPA
							$all_sub_mark = StudentSubMarkGp::where('student_id', $student_info->id)
								->where('session', $session)
								->where('group_id', $group_id)
								->where('exam_id', $exam_id)
								->get();

							$gpaData = ResultProcessHelper::calculateCGPA($all_sub_mark);
							ResultProcessHelper::saveGPA($student_info->id, $session, $group_id, $exam_id, $gpaData);
						} catch (\Illuminate\Database\QueryException $e) {
							dd($e->getMessage());
						}
					}
				} else {
					$curr_level = Classe::find($current_level);
					$group_name = Group::find($group_id);
					$exam_name = Exam::find($exam_id);

					$check = HscRsltProcessing::where('exam_year', $exam_year)
						->where('group_id', $group_id)
						->where('exam_id', $exam_id)
						->where('session', $session)
						->get();

					if ($check->isNotEmpty()) {
						$error_message = "{$check[0]->exam_year} {$check[0]->group->name} {$check[0]->exam->name} already generated";
						return Redirect::back()->withInput()->with('error', $error_message);
					}

					$student_infos = StudentInfoHsc::where('current_level', $curr_level->name)
						->where('session', $session)
						->where('groups', $group_name->name)
						->orderBy('id')
						->get();

					if ($student_infos->isEmpty()) {
						$error_message = "{$exam_year} {$curr_level->name} {$group_name->name} have no students!";
						return Redirect::back()->withInput()->with('error', $error_message);
					}

					foreach ($student_infos as $student_info) {
						try {
							$student_sub = StudentSubInfo::where('student_id', $student_info->id)
								->where('current_level', $student_info->current_level)
								->first();

							if (is_null($student_sub))
								continue;

							$maxSubjects = 26;
							$additionalSubjects = ['fourth_id', 'fourth2_id'];

							$subjectKeys = array_merge(
								array_map(fn($i) => "sub{$i}_id", range(1, $maxSubjects)),
								$additionalSubjects
							);

							foreach ($subjectKeys as $subjectKey) {
								$fourth = in_array($subjectKey, $additionalSubjects) ? 1 : 0;

								if (isset($student_sub->$subjectKey)) {
									$subject_id = $student_sub->$subjectKey;

									if ($subject_id != 0) {
										$processResult = ResultProcessHelper::processSubjectMarks(
											$subject_id,
											$student_info,
											$session,
											$group_id,
											$exam_id,
											$curr_level,
											$exam_year,
											$fourth
										);
									}
								}
							}
							// Calculate CGPA
							$all_sub_marks = StudentSubMarkGp::where('student_id', $student_info->id)
								->where('session', $session)
								->where('group_id', $group_id)
								->where('exam_id', $exam_id)
								->get();
							if (count($all_sub_marks) > 0) {
								$gpaData = ResultProcessHelper::calculateCGPA($all_sub_marks);
								$result = ResultProcessHelper::saveGPA($student_info->id, $session, $group_id, $exam_id, $exam_year, $all_sub_marks, $gpaData);
							}
						} catch (\Exception $e) {
							dd($e->getMessage() . ', for student id=' . $student_info->id);
						}
					}
				}
				// Insert result processing record
				$insert_row = new HscRsltProcessing();
				$insert_row->session = $session;
				$insert_row->exam_year = $exam_year;
				$insert_row->group_id = $group_id;
				$insert_row->classe_id = $current_level;
				$insert_row->exam_id = $exam_id;
				$insert_row->save();
				DB::commit();
			} catch (\Exception $e) {
				DB::rollback();
				dd($e->getMessage());
			}

			$message = 'You have successfully processed the result';
			return Redirect::back()->with('success', $message);
		}
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

		$sub_list = ClasseSubject::whereClasse_id($year)->whereGroup_id($id)->get();

		$sub_arr = [];
		foreach ($sub_list as  $value):
			$sub_arr[$value->subject_id] = $value->subject->name . '(' . $value->subject->code . ')';
		endforeach;

		return Response::json(['success' => true, 'sub_arr' => $sub_arr]);
	}

	public function destroy(Request $request, $id)
	{

		if ($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error', $error_message);
		endif;

		$result_pro = HscRsltProcessing::find($id);
		HscGpa::whereSession($result_pro->session)->whereGroup_id($result_pro->group_id)->whereExam_id($result_pro->exam_id)->delete();
		StudentSubMarkGp::whereSession($result_pro->session)->whereGroup_id($result_pro->group_id)->whereExam_id($result_pro->exam_id)->delete();
		HscRsltProcessing::find($id)->delete();
		$error_message = 'You have deleted the Processesd Result of ' . $result_pro->session . ' ' . $result_pro->group->name . ' of ' . $result_pro->exam->name;
		return Redirect::back()->with('error', $error_message);
	}

	public function MeritListExcel($id)
	{
		return Excel::download(new MeritListExport($id), 'merit_list.xlsx');
	}

	public function TabulationExcel($id, $ex_id)
	{
		set_time_limit(0);
		$exam = Exam::find($ex_id);
		if ($exam->have_class_test >= 0) {
			$class_tests = DB::select("select * from class_test_assign where exam_id = $ex_id");

			return Excel::download(new TabulationExport($id, $class_tests), 'tabulation_sheet.xlsx');
		} else {

			$mpdf = new Mpdf(['mode' => 'c', 'format' => 'A4-L']);
			$mpdf->allow_charset_conversion = true;
			$mpdf->charset_in = 'UTF-8';
			$html = view('BackEnd.hsc_result.download.tabulation_excel', compact('id'));
			$mpdf->writeHTML($html);			
		}
	}

	public function processIndivisualView($processId)
	{
		$result = HscRsltProcessing::find($processId);
		return view('BackEnd.hsc_result.process.indivisual', compact('result'));
	}

	public function processIndivisual(Request $request)
	{
		$this->validate($request, [
			'processing_id' => 'required',
			'student_id' => 'required',
		]);

		$processing_id = $request->processing_id;
		$student_id = $request->student_id;

		$result = HscRsltProcessing::find($processing_id);

		$student_info = StudentInfoHsc::where('current_level', $result->classe->name)
			->where('session', $result->session)
			->where('groups', $result->group->name)
			->where('id', $student_id)->first();

		if (is_null($student_info)) {
			return Redirect::back()->withInput()->with('error', 'Student Not Found with this ID');
		}

		DB::beginTransaction();
		try {
			$session = $result->session;
			$group_id = $result->group_id;
			$exam_id = $result->exam_id;
			$exam_year = $result->exam_year;
			$curr_level = $result->classe;

			// Check and delete HscGpa records if they exist
			$hscGpaRecords = HscGpa::where('student_id', $student_id)
				->where('session', $result->session)
				->where('group_id', $result->group_id)
				->where('exam_id', $result->exam_id)
				->where('exam_year', $result->exam_year);

			if ($hscGpaRecords->exists()) {
				$hscGpaRecords->delete();
			}

			// Check and delete StudentSubMarkGp records if they exist
			$studentSubMarkGpRecords = StudentSubMarkGp::where('student_id', $student_id)
				->where('session', $result->session)
				->where('group_id', $result->group_id)
				->where('exam_id', $result->exam_id)
				->where('exam_year', $result->exam_year);

			if ($studentSubMarkGpRecords->exists()) {
				$studentSubMarkGpRecords->delete();
			}

			$student_sub = StudentSubInfo::where('student_id', $student_info->id)
				->where('current_level', $student_info->current_level)
				->first();

			if (is_null($student_sub))
				return Redirect::back()->withInput()->with('error', 'No Assigned Subjects Found!');

			$maxSubjects = 26;
			$additionalSubjects = ['fourth_id', 'fourth2_id'];

			$subjectKeys = array_merge(
				array_map(fn($i) => "sub{$i}_id", range(1, $maxSubjects)),
				$additionalSubjects
			);

			foreach ($subjectKeys as $subjectKey) {
				$fourth = in_array($subjectKey, $additionalSubjects) ? 1 : 0;

				if (isset($student_sub->$subjectKey)) {
					$subject_id = $student_sub->$subjectKey;

					if ($subject_id != 0) {
						$processResult = ResultProcessHelper::processSubjectMarks(
							$subject_id,
							$student_info,
							$session,
							$group_id,
							$exam_id,
							$curr_level,
							$exam_year,
							$fourth
						);
					}
				}
			}


			// Calculate CGPA
			$all_sub_marks = StudentSubMarkGp::where('student_id', $student_info->id)
				->where('session', $session)
				->where('group_id', $group_id)
				->where('exam_id', $exam_id)
				->get();
			if (count($all_sub_marks) > 0) {
				$gpaData = ResultProcessHelper::calculateCGPA($all_sub_marks);
				$result = ResultProcessHelper::saveGPA($student_info->id, $session, $group_id, $exam_id, $exam_year, $all_sub_marks, $gpaData);
			}

			DB::commit();
		} catch (\Exception $e) {
			// continue;
			DB::rollback();

			dd($e->getMessage());
		}

		return Redirect::back()->withInput()->with('success', "Result processed successfully for student *{$student_info->name}");
	}
}

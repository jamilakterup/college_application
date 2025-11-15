<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\StudentInfoHsc;
use App\Models\Subject;
use App\Models\StudentSubMarkGp;
use App\Exports\HscResult\ResultReportExport;
use App\Exports\HscResult\SubjectWiseResultExport;
use App\Models\FeesDetails;

use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class HscResultController extends Controller
{
	public function index()
	{

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.index:HSC Result|Dashboard';
		return view('BackEnd.hsc_result.index')
			->withTitle($title)
			->withBreadcrumb($breadcrumb);
	}



	public function create()
	{

		$title = 'Easy CollegeMate - Add Faculty';
		$breadcrumb = 'admin.faculty.index:Faculty Management|Add New Faculty';

		return view('admin.faculty.create')
			->withTitle($title)
			->withBreadcrumb($breadcrumb);
	}



	public function store()
	{

		$data = $request->all();
		$validation = Faculty::validate($data);

		if ($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$faculty = new Faculty;
		$faculty->faculty_code = $request->get('faculty_code');
		$faculty->faculty_name = $request->get('faculty_name');
		$faculty->short_name = $request->get('short_name');
		$faculty->save();

		$id = $faculty->id;

		$page = ceil(Faculty::count() / Study::paginate());

		$message = 'You have successfully created a new faculty';
		return Redirect::route('admin.faculty.index', ['page' => $page])
			->withMessage($message)
			->withId($id);
	}



	public function show($id)
	{

		$faculty = Faculty::find($id);
		$title = 'Easy CollegeMate - ' . $faculty->faculty_name;
		$breadcrumb = 'admin.faculty.index:Faculty Management|Faculty - ' . $faculty->faculty_name;

		return view('admin.faculty.show')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withFaculty($faculty);
	}



	public function edit($id)
	{

		$title = 'Easy CollegeMate - Edit Faculty';
		$breadcrumb = 'admin.faculty.index:Faculty Management|Edit Faculty';
		$faculty = Faculty::find($id);

		return view('admin.faculty.edit')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withFaculty($faculty);
	}



	public function update($id)
	{

		if ($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error', $error_message);
		endif;

		$data = $request->all();
		$validation = Faculty::updateValidate($data);

		if ($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$faculty = Faculty::find($id);
		$faculty->faculty_code = $request->get('faculty_code');
		$faculty->faculty_name = $request->get('faculty_name');
		$faculty->short_name = $request->get('short_name');
		$faculty->update();

		$count = Faculty::where('id', '<=', $id)->count();
		$page = ceil($count / Study::paginate());

		$message = 'You have successfully updated the faculty';
		return Redirect::route('admin.faculty.index', ['page' => $page])
			->withMessage($message)
			->withId($id);
	}



	public function destroy(Request $request, $id)
	{

		if ($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error', $error_message);
		endif;

		$id = $request->get('id');
		$faculty = Faculty::find($id);
		$faculty->delete();

		//delete faculty head
		FacultyHead::where('faculty_id', $id)->delete();

		//delete departments, departments head and its program that are in the faculty 
		$departments = Department::where('faculty_id', $id)->get();
		foreach ($departments as $department) :
			$department_id = $department->id;
			DeptProgram::where('department_id', $department_id)->delete();
			DeptHead::where('department_id', $department_id)->delete();
			$department->delete();
		endforeach;

		$error_message = 'You have deleted the faculty';
		return Redirect::back()->with('error', $error_message);
	}

	// Controller
	public function result_reporting()
	{
		$data = [
			'title' => 'Easy CollegeMate - HSC Result',
			'breadcrumb' => 'hsc_result.index:HSC Result|Dashboard',
			'resultgp' => [],
			'groups' => '',
			'subjects' => '',
			'exams' => '',
			'session' => '',
			'grade_scales' => ''
		];

		return view('BackEnd.hsc_result.result_reporting.index', $data);
	}

	public function result_reporting_search(Request $request)
	{
		$params = $request->only(['groups', 'subjects', 'exams', 'session', 'grade_scales']);
		$resultgp = (new ResultReportExport($params))->collection();

		$data = array_merge($params, [
			'title' => 'Easy CollegeMate - HSC Result',
			'breadcrumb' => 'hsc_result.index:HSC Result|Dashboard',
			'resultgp' => $resultgp,
			'filter' => $request->grade_scales
		]);

		if ($request->has('export')) {
			return Excel::download(
				new ResultReportExport($params),
				'result_report_' . date('Y-m-d') . '.xlsx'
			);
		}

		if ($request->has('print')) {
			$total_students = $resultgp->count();

			$grouped = $resultgp->groupBy('Grade Point');
			$grade_summary = [];
			foreach ($grouped as $grade => $items) {
				$count = $items->count();
				$percentage = $total_students ? round(($count / $total_students) * 100, 2) : 0;

				$grade_summary[] = [
					'grade' => $grade,
					'count' => $count,
					'percentage' => $percentage,
					'students' => $items,
				];
			}


			$html = view('BackEnd.hsc_result.result_reporting.report_pdf', [
				'grade_summary' => $grade_summary,
				'session' => $params['session'],
				'groups' => $params['groups'],
				'exams' => $params['exams'],
				'total_students' => $total_students,
			])->render();


			$mpdf = new Mpdf([
				'format' => 'A4',
				'margin_top' => 10,
				'margin_bottom' => 10,
				'margin_left' => 10,
				'margin_right' => 10,
			]);

			$mpdf->WriteHTML($html);
			$mpdf->Output('HSC_Result_Report_' . date('YmdHis') . '.pdf', 'I');
		}

		return view('BackEnd.hsc_result.result_reporting.index', $data);
	}

	public function progress_report()
	{
		return view('BackEnd.hsc_result.progress_report.index');
	}

	public function progress_report_generate(Request $request)
	{
		// Set PHP configurations
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 9600);
		ini_set('request_terminate_timeout', 9600);
		ini_set('fastcgi_read_timeout', 9600);
		ini_set("pcre.backtrack_limit", "5000000");

		$session = $request->session;
		$current_level = $request->current_year;
		$group = $request->group;

		// Validate request
		$this->validate($request, [
			'session' => 'required',
			'current_year' => 'required',
			'group' => 'required',
		]);

		// Get students with their subject info
		$students = StudentInfoHsc::with(['studentSubjectInfo' => function ($query) use ($request) {
			$query->where('current_level', $request->current_year);
		}])
			->when($request->student_id, fn($q) => $q->where('id', $request->student_id))
			->when($request->session, fn($q) => $q->where('session', $request->session))
			->when($request->current_year, fn($q) => $q->where('current_level', $request->current_year))
			->when($request->group, fn($q) => $q->where('groups', $request->group))
			->get();

		if ($students->isEmpty()) {
			return redirect()->back()->withInput()->with('warning', 'No Data Found');
		}

		// Process student marks
		$studentSubjectMarks = [];
		foreach ($students as $student) {
			$subInfo = $student->studentSubjectInfo->first();
			if (!$subInfo) continue;

			$examIds = $student->studentSubMarkGp->unique('exam_id')->pluck('exam_id')->toArray();
			$subjectIds = collect(['sub1_id', 'sub2_id', 'sub3_id', 'sub4_id', 'sub5_id', 'sub6_id', 'fourth_id'])
				->mapWithKeys(fn($field) => [$field => $subInfo->$field]);

			$studentSubjectMarks[$student->id] = [
				'exam_count' => count($examIds),
				'studentInfo' => [
					'id' => $student->id,
					'name' => $student->name,
					'session' => $student->session,
					'groups' => $student->groups,
				],
				'headerExamInfos' => array_fill_keys($examIds, ['Marks', 'LG', 'GP'])
			];

			// Process subjects data
			foreach ($subjectIds as $subjectId) {
				$subject = Subject::find($subjectId);
				if (!$subject) continue;

				$subjectResults = [];
				foreach ($examIds as $examId) {
					$result = $student->studentSubMarkGp
						->where('exam_id', $examId)
						->where('subject_id', $subjectId)
						->first();

					if ($result) {
						$subjectResults[$examId] = [
							$result->total_mark,
							$result->grade,
							$result->point
						];
					}
				}
				$studentSubjectMarks[$student->id]['subjectsData'][$subject->name] = [
					$subject->code,
					$subjectResults
				];
			}
		}

		if (empty($studentSubjectMarks)) {
			return redirect()->back()->withInput()->with('warning', 'No Data Found');
		}

		// Generate PDF
		$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 10, 'times']);
		addMpdfPageSetup($mpdf);

		$html = view('BackEnd.hsc_result.pdf.progress_report', compact('studentSubjectMarks', 'session', 'current_level', 'group'));

		$mpdf->writeHTML($html);
		return $mpdf->Output();
	}

	public function fees_details()
	{
		$fees_details = FeesDetails::orderBy('current_year', 'asc')->get();

		return view('BackEnd.hsc_result.fees_details.index', compact('fees_details'));
	}

	public function fees_details_store(Request $request)
	{
		$validated = $request->validate([
			'session' => 'required|string|max:25',
			'current_year' => 'required|string|max:50',
			'fees_header' => 'required|string|max:255',
			'is_gov' => 'required',
			'science' => 'required|numeric|min:0',
			'humanities' => 'required|numeric|min:0',
			'business' => 'required|numeric|min:0',
		]);

		FeesDetails::create($validated);

		return redirect()
			->route('hsc_result.fees_details')
			->with('success', 'Fees details saved successfully!');
	}

	public function fees_Edit(Request $request)
	{
		$fees_details = FeesDetails::findOrFail($request->id);
		dd($fees_details);
	}


	public function fees_details_generate(Request $request)
	{
		$yearType = $request->input('year_type');

		$filtered = FeesDetails::query()
			->when($yearType, fn($q) => $q->where('current_year', $yearType))
			->orderBy('id', 'asc')
			->get();

		$groupwiseStudent = StudentInfoHsc::all();
		$scienceStudents = StudentInfoHsc::where('groups', 'Science')->where('current_level', $yearType)->get();
		$humanitiesStudents = StudentInfoHsc::where('groups', 'Humanities')->where('current_level', $yearType)->get();
		$businessStudents = StudentInfoHsc::where('groups', 'Business Studies')->where('current_level', $yearType)->get();

		$html = view('BackEnd.hsc_result.fees_details.pdf', [
			'fees_details' => $filtered,
			'year_type' => $yearType,
			'science_students' => $scienceStudents,
			'humanities_students' => $humanitiesStudents,
			'business_students' => $businessStudents,
		])->render();

		$mpdf = new Mpdf();

		$mpdf->WriteHTML($html);

		return $mpdf->Output("Fees_Details_{$yearType}.pdf", 'I');
	}




	public function result_reporting_subject_wise(Request $request)
	{
		$this->validate($request, [
			'session' => 'required',
			'groups' => 'sometimes',
			'exams' => 'required',
			'exam_year' => 'required'
		]);

		$session = $request->session;
		$exam_year = $request->exam_year;
		$exam_id = $request->exams;
		$group_id = $request->groups;

		$report = StudentSubMarkGp::where(function ($q) use ($session, $exam_year, $exam_id, $group_id) {
			$q->whereSession($session)
				->where('exam_year', $exam_year)
				->where('exam_id', $exam_id);

			if ($group_id) {
				$q->where('group_id', $group_id);
			}
		})
			->with(['subject', 'exam'])
			->get()
			->groupBy('subject_id')
			->map(function ($subjectGroup) {
				$totalStudents = $subjectGroup->count();

				return [
					'subject_name' => $subjectGroup->first()->subject->name,
					'exam_name' => $subjectGroup->first()->exam->name,
					'total_students' => $totalStudents,
					'grade_counts' => [
						'A+' => $subjectGroup->where('grade', 'A+')->count(),
						'A' => $subjectGroup->where('grade', 'A')->count(),
						'A-' => $subjectGroup->where('grade', 'A-')->count(),
						'B' => $subjectGroup->where('grade', 'B')->count(),
						'C' => $subjectGroup->where('grade', 'C')->count(),
						'D' => $subjectGroup->where('grade', 'D')->count(),
						'F' => $subjectGroup->where('grade', 'F')->count(),
					],
					'pass_count' => $subjectGroup->where('grade', '!=', 'F')->count(),
					'fail_count' => $subjectGroup->where('grade', 'F')->count(),
				];
			});

		return Excel::download(new SubjectWiseResultExport($report), 'subject_wise_result.xlsx');
	}
}

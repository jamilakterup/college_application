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
use App\Models\HscRsltProcessing;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TranscriptController extends Controller
{
	public function index()
	{

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.transcript.index:Transcript|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = ['' => 'Select'] + Classe::orderBy('id')->pluck('name', 'id')->toArray();
		$group_lists = ['' => 'Select group'] + Group::orderBy('id')->pluck('name', 'name')->toArray();
		$exam_lists = ['' => 'Select exam'] + Exam::orderBy('id')->pluck('name', 'id')->toArray();
		return view('BackEnd.hsc_result.transcript.index', compact('title', 'current_yr_lists', 'group_lists', 'exam_lists', 'breadcrumb'));
	}



	public function create() {}

	public function store(Request $request)
	{
		if (!$request->isMethod('post')) {
			return back();
		}

		$session = $request->get('session');
		$group = $request->get('group');
		$current_level = $request->get('current_level');
		$exam_id = $request->get('exam_id');

		$curr_level = Classe::find($current_level);
		$student_infos = StudentInfoHsc::whereSession($session)
			->whereCurrent_level($curr_level->name)
			->whereGroups($group)
			->get();

		$student_info_ids = [];
		ini_set('max_execution_time', 300);

		foreach ($student_infos as $student_info) {
			$field_name = 'studentinfo-' . $student_info->id;
			if ($student_info->id == $request->get($field_name)) {
				$student_info_ids[] = $student_info->id;
			}
		}

		$cnt = count($student_info_ids);
		if ($cnt === 0) {
			return back()->with('error', 'No students selected.');
		}

		$exam_name = Exam::find($exam_id);
		$group_id = Group::whereName($group)->pluck('id');
		$student_info_hsc = StudentInfoHsc::whereIn('id', $student_info_ids)->get();

		$f_name = $student_info_ids[0] . '-' . $student_info_ids[$cnt - 1] . '.pdf';

		// Configure mPDF
		$mpdf = new Mpdf([
			'format' => 'A4',
			'margin_left' => 2.83,
			'margin_right' => 2.83,
			'margin_top' => 2.83,
			'margin_bottom' => 2.83,
			'margin_header' => 0,
			'margin_footer' => 0,
			'default_font_size' => 10,
			'default_font' => 'dejavusans',
		]);

		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = 'UTF-8';

		// Ensure QR directory exists
		$qrDir = public_path('qrcodes');
		if (!File::exists($qrDir)) {
			File::makeDirectory($qrDir, 0755, true);
		}

		// Generate each student's page
		foreach ($student_info_hsc as $value) {
			$studentName    = $value->name ?? 'N/A';
			$studentRoll    = $value->class_roll ?? 'N/A';
			$studentSession = $value->session ?? $session ?? 'N/A';
			$studentGroup   = $value->groups ?? $group ?? 'N/A';

			// Build QR content (you can change to JSON if you want)
			$qrData = "Name: {$studentName}\n"
				. "Roll: {$studentRoll}\n"
				. "Session: {$studentSession}\n"
				. "Group: {$studentGroup}";

			// QR image path (use consistent name: $qrPath)
			$qrFilename = 'qrcode_' . $value->id . '.png';
			$qrPath = $qrDir . DIRECTORY_SEPARATOR . $qrFilename;

			// Generate QR code to filesystem
			QrCode::format('png')
				->size(150)
				->margin(1)
				->generate($qrData, $qrPath);

			// Add new page and render Blade (pass $qrPath)
			$mpdf->AddPage();
			$html = view('BackEnd.hsc_result.pdf.transcript', compact(
				'value',
				'exam_name',
				'group_id',
				'exam_id',
				'qrPath',
				'group'
			))->render();

			$mpdf->WriteHTML($html);
		}

		// Output PDF inline
		$mpdf->Output($f_name, 'I');

		// Cleanup generated QR images
		foreach ($student_info_hsc as $value) {
			$file = $qrDir . DIRECTORY_SEPARATOR . 'qrcode_' . $value->id . '.png';
			if (File::exists($file)) {
				File::delete($file);
			}
		}
	}



	public function transcriptlist(Request $request)
	{

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.transcript.index:Transcript|Dashboard';


		$session = Ecm::filterInput('session', $request->get('session'));
		$group = Ecm::filterInput('group', $request->get('group'));
		$current_level = Ecm::filterInput('current_year', $request->get('current_year'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));


		if ($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($group == '') :
			$error_message = 'Select Group';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($current_level == '') :
			$error_message = 'Select Current Year';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		if ($exam_id == '') :
			$error_message = 'Select Exam';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		$chk_exam = ClassExam::whereExam_id($exam_id)->whereClasse_id($current_level)->count();
		if ($chk_exam == 0) :
			$error_message = 'Exam Not Assign';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		$group_id = Group::whereName($group)->pluck('id');
		$curr_level = Classe::find($current_level);
		$chk = HscRsltProcessing::whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->count();
		$exam_name = Exam::find($exam_id);

		if ($chk == 0) :
			$error_message = $session . ' ' . $group . ' of ' . $exam_name->name . ' Result not Processed yet!';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;

		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->get();
		if (count($student_info) == 0) :
			$error_message = 'No Student Found';
			return Redirect::back()->withInput()->with('error', $error_message);
		endif;
		$student_info = StudentInfoHsc::whereSession($session)->whereCurrent_level($curr_level->name)->whereGroups($group)->orderBy('id')->paginate(50);

		return view('BackEnd.hsc_result.transcript.list', compact('title', 'session', 'group', 'current_level', 'curr_level', 'student_info', 'exam_id', 'breadcrumb'));
	}



	public function show($id) {}



	public function edit($id) {}



	public function update($id) {}



	public function destroy($id) {}
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Invoice;
use Illuminate\Http\Request;
use DB;
use Mpdf\Mpdf;

class ReportController extends Controller
{
  // hsc section
  public function hsc_report(Request $request)
  {
    return view('BackEnd.student.report.hsc.index');
  }

  public function hsc_admission(Request $request)
  {
    $id = $request->get('id');
    $ssc_roll = $request->get('ssc_roll');
    $groups = $request->get('groups');
    $gender = $request->get('gender');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Hsc Management|Dashboard';
    $current_level_lists = selective_multiple_hsc_level();

    // Students query
    // $query = Study::searchHscStudent($id, $ssc_roll, $groups, $gender, $current_level, $session);
    // $student_rolls = $query->pluck('ssc_roll')->toArray();
    $query = Study::searchHscStudent($id, $ssc_roll, $groups, $gender, $current_level, $session, $from_date, $to_date);




    // Convert dates to Y-m-d format if provided
    $from_date = $from_date ? date('Y-m-d', strtotime($from_date)) : null;
    $to_date   = $to_date   ? date('Y-m-d', strtotime($to_date))   : null;

    // Invoice query with optional filters
    $query_invoice = Invoice::where('type', 'hsc_admission')
      ->where('status', 'Paid')
      ->when($session, fn($q) => $q->where('admission_session', $session))
      ->when($from_date, fn($q) => $q->whereDate('date_start', '>=', $from_date))
      ->when($to_date, fn($q) => $q->whereDate('date_end', '<=', $to_date))
      ->when($groups, fn($q) => $q->orderByRaw("FIELD(pro_group, ? ) DESC", [$groups]))
      ->orderBy('update_date', 'DESC');

    $total_amount = $query_invoice->sum('total_amount');

    $invoices = $query_invoice->get();


    $num_rows = $query->count();
    $hscstudents = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.hsc.admreport', compact(
      'title',
      'breadcrumb',
      'hscstudents',
      'current_level_lists',
      'id',
      'ssc_roll',
      'groups',
      'gender',
      'current_level',
      'session',
      'num_rows',
      'from_date',
      'to_date',
      'total_amount',
      'invoices'
    ));
  }


  // public function hsc_admission(Request $request)
  // {
  //   $id = $request->get('id');
  //   $ssc_roll = $request->get('ssc_roll');
  //   $groups = $request->get('groups');
  //   $gender = $request->get('gender');
  //   $current_level = $request->get('current_level');
  //   $session = $request->get('session');
  //   $from_date = $request->from_date;
  //   $to_date = $request->to_date;

  //   $title = 'Easy CollegeMate - College Management';
  //   $breadcrumb = 'student:Student Hsc Management|Dashboard';
  //   $current_level_lists = selective_multiple_hsc_level();

  //   $query = Study::searchHscStudent($id, $ssc_roll, $groups, $gender, $current_level, $session);
  //   $student_rolls = $query->pluck('ssc_roll')->toArray();
  //   $total_amount = 0;
  //   $admission_fee = 0;

  //   $from_date = $from_date ? date('Y-m-d', strtotime($from_date)) : null;
  //   $to_date   = $to_date   ? date('Y-m-d', strtotime($to_date))   : null;


  //   $query_invoice = Invoice::where('type', 'hsc_admission')->where('status', 'Paid');
  //   if ($session != '')    $query_invoice->where('admission_session', $session);
  //   $query_invoice->whereIn('roll', $student_rolls);

  //   if ($from_date != '') {
  //     $from_date = date('Y-m-d', strtotime($request->from_date));
  //     $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
  //   }

  //   if ($to_date != '') {
  //     $to_date = date('Y-m-d', strtotime($request->to_date));
  //     $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
  //   }


  //   $invoices = $query_invoice->orderByRaw("FIELD(pro_group , '$groups') DESC");
  //   $invoices = $query_invoice->get();
  //   // dd($invoices);

  //   if (count($invoices)) {
  //     $total_amount = $query_invoice->sum('total_amount');
  //   }
  //   $num_rows = $query->count();
  //   $hscstudents = $query->paginate(Study::paginate());

  //   return view('BackEnd.student.report.hsc.admreport', compact('title', 'breadcrumb', 'hscstudents', 'current_level_lists', 'id', 'ssc_roll', 'groups', 'gender', 'current_level', 'session', 'num_rows', 'from_date', 'to_date', 'total_amount'));
  // }

  public function generateHscAdmReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");

    if ($request->type == 'csv_dept_report') {
      return $this->hsc_csv_dept_report($request);
    }

    $groups = $request->get('groups');
    $gender = $request->get('gender');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = DB::table('student_info_hsc')->orderBy('id', 'asc');

    if ($session != '')     $query->where('session', $session);
    if ($current_level != '')    $query->where('current_level', $current_level);
    if ($groups != '')    $query->where('groups', $groups);
    if ($gender != '')    $query->where('gender', $gender);
    // check permission
    query_has_permissions($query, ['groups', 'current_level', 'session']);

    $admissions = $query->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Student ID', 'Class Roll', 'Name', 'Father Name', 'Mother Name', 'Birth Date', 'SSC Roll', 'Group', 'Gender', 'Contact No', 'Current Level', 'Session', 'Payment Date', 'Total Amount'];

      foreach ($admissions as $val) {
        $invoice = Invoice::where('roll', $val->ssc_roll)->where('admission_session', $val->session)->where('type', 'hsc_admission')->where('ssc_board', $val->ssc_board)->get();
        $amount = 0;
        if (count($invoice) > 0) $amount = $invoice->first()->total_amount;

        $data[] = [
          $val->id,
          $val->class_roll,
          $val->name,
          $val->father_name,
          $val->mother_name,
          $val->birth_date,
          $val->ssc_roll,
          $val->groups,
          $val->gender,
          $val->contact_no,
          $val->current_level,
          $val->session,
          $val->payment_date,
          $amount
        ];
      }
      $filename = 'hsc_admission_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.hscadmreport', compact('admissions', 'session')));
    $mpdf->Output();
  }

  public function hsc_csv_dept_report($request)
  {
    $session = $request->session;
    $current_level = $request->current_level;
    $groups = $request->groups;
    $gender = $request->gender;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $faculty = DB::table('faculties');

    if ($groups != '') {
      $faculty->where('faculty_name', $groups);
    }
    // check permission
    query_has_permissions($faculty, ['groups', 'level_study', 'session', 'exam_year']);

    $departments = $faculty->get('faculty_name');
    $i = 1;

    $data[] = ['SI', 'Group Name', 'Total Num of Students', 'Gender', 'HSC Level', 'Session', 'Admission Fee', 'Total Amount', 'From Date', 'To Date'];

    foreach ($departments as $dept) {

      $total_amount = 0;
      $admission_fee = 0;

      $query_adm = DB::table('student_info_hsc')->orderBy('id', 'asc');

      if ($session != '') $query_adm->where('session', $session);
      if ($current_level != '')    $query_adm->where('current_level', $current_level);
      $query_adm->where('groups', $dept->faculty_name);
      if ($gender != '')    $query_adm->where('gender', $gender);

      $admissions = $query_adm->groupBy('id')->get();
      $student_rolls = $query_adm->pluck('ssc_roll')->toArray();

      $query_invoice = Invoice::where('type', 'hsc_admission')->where('status', 'Paid');
      if ($session != '')    $query_invoice->where('admission_session', $session);
      $query_invoice->whereIn('roll', $student_rolls);
      // if($dept != '')    $query_invoice->where('subject', $dept->groups);

      if ($from_date != '') {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
      }

      $invoices = $query_invoice->orderByRaw("FIELD(pro_group , '$dept->faculty_name') DESC");
      $invoices = $query_invoice->get();

      $total_amount = $query_invoice->sum('total_amount');
      if ($total_amount < 1) {
        continue;
      }

      $data[] = [$i, $dept->faculty_name, count($invoices), $gender, $current_level, $session, $total_amount, $from_date, $to_date];

      $i++;
    }


    $filename = 'hsc_admission_reports.csv';
    $file = fopen(public_path('temp/' . $filename), 'w');
    foreach ($data as $row) {
      fputcsv($file, (array) $row);
    }
    fclose($file);
    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
  }

  // honours section
  public function honours_report(Request $request)
  {
    return view('BackEnd.student.report.honours.index');
  }

  public function honours_admission(Request $request)
  {

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Hsc Management|Dashboard';
    $current_level_lists = selective_multiple_honours_level();

    $query = Study::searchHonsStudent($id, $admission_roll, $faculty, $dept_name, $current_level, $session);
    $student_rolls = $query->pluck('admission_roll')->toArray();

    $total_amount = 0;
    $admission_fee = 0;

    $query_invoice = Invoice::where('type', 'honours_admission')->where('status', 'Paid');
    if ($session != '')    $query_invoice->where('admission_session', $session);
    $query_invoice->whereIn('roll', $student_rolls);

    if ($from_date != '') {
      $from_date = date('Y-m-d', strtotime($request->from_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d', strtotime($request->to_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
    }

    $invoices = $query_invoice->orderByRaw("FIELD(subject , '$dept_name') DESC");
    $invoices = $query_invoice->get();

    if (count($invoices)) {
      $admission_fee =  $query_invoice->first()->total_amount;
      $total_amount = count($invoices) * $admission_fee;
    }

    $num_rows = $query->count();
    $students = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.honours.admreport', compact('title', 'breadcrumb', 'students', 'current_level_lists', 'id', 'admission_roll', 'faculty', 'dept_name', 'current_level', 'session', 'num_rows', 'from_date', 'to_date', 'admission_fee', 'total_amount'));
  }

  public function generateHonAdmReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");

    if ($request->type == 'csv_dept_report') {
      return $this->hons_csv_dept_adm_report($request);
    }

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = Study::searchHonsStudent($id, $admission_roll, $faculty, $dept_name, $current_level, $session);
    $student_rolls = $query->pluck('admission_roll')->toArray();

    $admissions = $query->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Student ID', 'Class Roll', 'Name', 'Father Name', 'Mother Name', 'Birth Date', 'Admission Roll', 'Faculty', 'Subject', 'Contact No', 'Current Level', 'Session'];

      foreach ($admissions as $val) {
        $data[] = [
          $val->id,
          $val->class_roll,
          $val->name,
          $val->father_name,
          $val->mother_name,
          $val->birth_date,
          $val->admission_roll,
          $val->faculty_name,
          $val->dept_name,
          $val->contact_no,
          $val->current_level,
          $val->session
        ];
      }
      $filename = 'hons_admission_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.honadmreport', compact('admissions', 'session')));
    $mpdf->Output();
  }

  public function hons_csv_dept_adm_report($request)
  {
    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query_dept = DB::table('departments');

    if ($dept_name != '') {
      $query_dept->where('dept_name', $dept_name);
    }
    // check permission
    query_has_permissions($query_dept, ['dept_name', 'level_study', 'session', 'exam_year']);

    $departments = $query_dept->get('dept_name');
    $i = 1;

    $data[] = ['SI', 'Department Name', 'Total Num of Students', 'Honours Level', 'Session', 'Admission Fee', 'Total Amount', 'From Date', 'To Date'];

    foreach ($departments as $dept) {

      $total_amount = 0;
      $admission_fee = 0;

      $query_adm = DB::table('student_info_hons')->orderBy('id', 'asc');

      if ($session != '') $query_adm->where('session', $session);

      if ($current_level != '')    $query_adm->where('current_level', $current_level);
      if ($faculty != '')    $query_adm->where('faculty_name', $faculty);
      $query_adm->where('dept_name', $dept->dept_name);

      $admissions = $query_adm->groupBy('id')->get();
      $student_rolls = $query_adm->pluck('admission_roll')->toArray();

      $query_invoice = Invoice::where('type', 'honours_admission')->where('status', 'Paid');
      if ($session != '')    $query_invoice->where('admission_session', $session);
      $query_invoice->whereIn('roll', $student_rolls);
      // if($dept != '')    $query_invoice->where('subject', $dept->dept_name);

      if ($from_date != '') {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
      }

      $invoices = $query_invoice->orderByRaw("FIELD(subject , '$dept->dept_name') DESC");
      $invoices = $query_invoice->get();
      $total_amount = $query_invoice->sum('total_amount');

      if ($total_amount < 1) {
        continue;
      }

      $admission_fee =  $query_invoice->first()->total_amount;
      $total_amount = count($invoices) * $admission_fee;

      $data[] = [$i, $dept->dept_name, count($invoices), $current_level, $session, $admission_fee, $total_amount, $from_date, $to_date];

      $i++;
    }


    $filename = 'hons_admission_departmental_reports.csv';
    $file = fopen(public_path('temp/' . $filename), 'w');
    foreach ($data as $row) {
      fputcsv($file, (array) $row);
    }
    fclose($file);
    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
  }

  public function honours_application(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - Honours Application Management';
    $breadcrumb = 'report.honours.application:Application|Dashboard';


    $query = DB::table('hons_student_applications')->orderBy('id', 'asc')
      ->where('current_level', 'Honours 1st Year');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }
    // check permission
    query_has_permissions($query, ['session', 'exam_year']);

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');

    $applications = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.honours.appreport', compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year', 'from_date', 'to_date', 'admission_roll'));
  }

  public function generateHonAppReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - Honours Application Reports';
    $breadcrumb = 'report.honours.application:Application|Dashboard';


    $query = DB::table('hons_student_applications')->orderBy('id', 'asc')
      ->where('current_level', 'Honours 1st Year');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }

    $applications = $query->orderBy('date', 'asc')->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Admission Roll', 'Name', 'Father Name', 'Mother Name', 'Contact No', 'Session', 'Admission Form', 'HSC Transcript'];

      foreach ($applications as $val) {
        $data[] = [
          $val->admission_roll,
          $val->name,
          $val->father_name,
          $val->mother_name,
          $val->contact_no,
          $val->session,
          $val->admission_form,
          $val->hsc_transcript != '' ? $val->admission_roll . '_' . $val->hsc_transcript : ''
        ];
      }
      $filename = 'hons_application_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.honappreport', compact('applications', 'exam_year', 'session')));
    $mpdf->Output();
  }

  // masters section
  public function masters_report(Request $request)
  {
    return view('BackEnd.student.report.masters.index');
  }

  public function masters_admission(Request $request)
  {

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Masters Management|Dashboard';
    $current_level_lists = selective_multiple_masters_level();

    $query = Study::searchMastersStudent($id, $admission_roll, $faculty, $dept_name, $current_level, $session);
    $stu_rolls = $query->pluck('admission_roll')->toArray();
    $student_rolls = array_values(filter_empty_array($stu_rolls));

    $total_amount = 0;
    $admission_fee = 0;

    $query_invoice = Invoice::where('type', 'masters_2nd_admission')->where('status', 'Paid');
    if ($session != '')    $query_invoice->where('admission_session', $session);
    $query_invoice->whereIn('roll', $student_rolls);

    if ($from_date != '') {
      $from_date = date('Y-m-d', strtotime($request->from_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d', strtotime($request->to_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
    }

    $invoices = $query_invoice->orderByRaw("FIELD(subject , '$dept_name') DESC");
    $invoices = $query_invoice->get();

    if (count($invoices)) {
      $admission_fee =  $query_invoice->first()->total_amount;
      $total_amount = count($invoices) * $admission_fee;
    }

    $num_rows = $query->count();
    $students = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.masters.admreport', compact('title', 'breadcrumb', 'students', 'current_level_lists', 'id', 'admission_roll', 'faculty', 'dept_name', 'current_level', 'session', 'num_rows', 'from_date', 'to_date', 'admission_fee', 'total_amount'));
  }

  public function generateMscAdmReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");

    if ($request->type == 'csv_dept_report') {
      return $this->msc_csv_dept_adm_report($request);
    }

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = Study::searchMastersStudent($id, $admission_roll, $faculty, $dept_name, $current_level, $session);
    $student_rolls = $query->pluck('admission_roll')->toArray();

    $admissions = $query->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Student ID', 'Class Roll', 'Name', 'Father Name', 'Mother Name', 'Birth Date', 'Admission Roll', 'Faculty ', 'Department Name', 'Contact No', 'Session'];

      foreach ($admissions as $val) {
        $admitted_student = DB::table('masters_admitted_student')->where('auto_id', $val->refference_id)->first();
        $data[] = [
          $val->id,
          $val->class_roll,
          $val->name,
          $val->father_name,
          $val->mother_name,
          $val->birth_date,
          $val->admission_roll,
          $val->faculty_name,
          $val->dept_name,
          $val->contact_no,
          $val->session
        ];
      }
      $filename = 'masters_admission_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.mscadmreport', compact('admissions', 'session')));
    $mpdf->Output();
  }

  public function msc_csv_dept_adm_report($request)
  {
    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $faculty = $request->get('faculty');
    $current_level = $request->get('current_level');
    $dept_name = $request->get('dept_name');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query_dept = DB::table('departments');

    if ($dept_name != '') {
      $query_dept->where('dept_name', $dept_name);
    }
    // check permission
    query_has_permissions($query_dept, ['dept_name', 'level_study', 'session', 'exam_year']);

    $departments = $query_dept->get('dept_name');
    $i = 1;

    $data[] = ['SI', 'Department Name', 'Total Num of Students', 'Honours Level', 'Session', 'Admission Fee', 'Total Amount', 'From Date', 'To Date'];

    foreach ($departments as $dept) {

      $total_amount = 0;
      $admission_fee = 0;

      $query_adm = Study::searchMastersStudent($id, $admission_roll, $faculty, $dept->dept_name, $current_level, $session);

      $admissions = $query_adm->groupBy('id')->get();
      $student_rolls = $query_adm->pluck('admission_roll')->toArray();

      $query_invoice = Invoice::where('type', 'masters_2nd_admission')->where('status', 'Paid');
      if ($session != '')    $query_invoice->where('admission_session', $session);
      $query_invoice->whereIn('roll', $student_rolls);
      // if($dept != '')    $query_invoice->where('subject', $dept->dept_name);

      if ($from_date != '') {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
      }

      $invoices = $query_invoice->orderByRaw("FIELD(subject , '$dept->dept_name') DESC");
      $invoices = $query_invoice->get();
      $total_amount = $query_invoice->sum('total_amount');

      if ($total_amount < 1) {
        continue;
      }

      if (count($invoices)) {
        $admission_fee =  $query_invoice->first()->total_amount;
        $total_amount = count($invoices) * $admission_fee;
      }

      $data[] = [$i, $dept->dept_name, count($invoices), $current_level, $session, $admission_fee, $total_amount, $from_date, $to_date];

      $i++;
    }


    $filename = 'masters_admission_departmental_reports.csv';
    $file = fopen(public_path('temp/' . $filename), 'w');
    foreach ($data as $row) {
      fputcsv($file, (array) $row);
    }
    fclose($file);
    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
  }

  public function masters_application(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $registration_type = $request->registration_type;
    $level = $request->level;
    $dept_name = $request->dept_name;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - Masters Application Management';
    $breadcrumb = 'report.masters.application:Application|Dashboard';


    $query = DB::table('masters_student_applications')->orderBy('id', 'asc');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }

    if ($level != '') {
      $query->where('current_level', $level);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }
    // check permission
    query_has_permissions($query, ['session', 'exam_year']);

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');

    $applications = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.masters.appreport', compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year', 'from_date', 'to_date', 'admission_roll', 'dept_name', 'level', 'registration_type'));
  }

  public function generateMscAppReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $dept_name = $request->dept_name;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $registration_type = $request->registration_type;
    $level = $request->level;

    $title = 'Easy CollegeMate - Masters Application Reports';
    $breadcrumb = 'report.masters.application:Application|Dashboard';


    $query = DB::table('masters_student_applications')->orderBy('id', 'asc');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }

    if ($level != '') {
      $query->where('current_level', $level);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }

    $applications = $query->orderBy('date', 'asc')->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Admission Roll', 'Name', 'Contact No', 'Department', 'Session', 'Total Amount', 'Payment Date'];

      foreach ($applications as $val) {
        $data[] = [
          $val->admission_roll,
          $val->name,
          $val->contact_no,
          $val->dept_name,
          $val->session,
          $val->total_amount,
          $val->date
        ];
      }
      $filename = 'masters_application_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.mscappreport', compact('applications', 'exam_year', 'session')));
    $mpdf->Output();
  }

  // degree section
  public function degree_report(Request $request)
  {
    return view('BackEnd.student.report.degree.index');
  }

  public function degree_admission(Request $request)
  {

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Degree Admission Reports|Dashboard';
    $current_level_lists = selective_multiple_degree_level();

    $query = Study::searchDegreeStudent($id, $admission_roll, $groups, $current_level, $session);
    $stu_rolls = $query->pluck('admission_roll')->toArray();
    $student_rolls = array_values(filter_empty_array($stu_rolls));

    $total_amount = 0;
    $admission_fee = 0;

    $query_invoice = Invoice::where('type', 'degree_admission')->where('status', 'Paid');
    if ($session != '')    $query_invoice->where('admission_session', $session);
    $query_invoice->whereIn('roll', $student_rolls);

    if ($from_date != '') {
      $from_date = date('Y-m-d', strtotime($request->from_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d', strtotime($request->to_date));
      $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
    }

    $invoices = $query_invoice->orderByRaw("FIELD(subject , '$groups') DESC");
    $invoices = $query_invoice->get();

    if (count($invoices)) {
      $admission_fee =  $query_invoice->first()->total_amount;
      $total_amount = count($invoices) * $admission_fee;
    }

    $num_rows = $query->count();
    $students = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.degree.admreport', compact('title', 'breadcrumb', 'students', 'current_level_lists', 'id', 'admission_roll', 'groups', 'current_level', 'session', 'num_rows', 'from_date', 'to_date', 'admission_fee', 'total_amount'));
  }

  public function generateDegAdmReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");

    if ($request->type == 'csv_dept_report') {
      return $this->deg_csv_dept_adm_report($request);
    }

    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = Study::searchDegreeStudent($id, $admission_roll, $groups, $current_level, $session);
    $student_rolls = $query->pluck('admission_roll')->toArray();

    $admissions = $query->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Student ID', 'Class Roll', 'Name', 'Father Name', 'Mother Name', 'Birth Date', 'Admission Roll', 'Groups ', 'Contact No', 'Session'];

      foreach ($admissions as $val) {
        $admitted_student = DB::table('deg_admitted_student')->where('auto_id', $val->refference_id)->first();
        $data[] = [
          $val->id,
          $val->class_roll,
          $val->name,
          $val->father_name,
          $val->mother_name,
          $val->birth_date,
          $val->admission_roll,
          $val->groups,
          $val->contact_no,
          $val->session
        ];
      }
      $filename = 'degree_admission_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.degadmreport', compact('admissions', 'session')));
    $mpdf->Output();
  }

  public function deg_csv_dept_adm_report($request)
  {
    $id = $request->get('id');
    $admission_roll = $request->get('admission_roll');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query_dept = DB::table('departments');

    if ($groups != '') {
      $query_dept->where('dept_name', $groups);
    }
    // check permission
    query_has_permissions($query_dept, ['dept_name', 'level_study', 'session', 'exam_year']);

    $departments = $query_dept->get('dept_name');
    $i = 1;

    $data[] = ['SI', 'Department Name', 'Total Num of Students', 'Degree Level', 'Session', 'Admission Fee', 'Total Amount', 'From Date', 'To Date'];

    foreach ($departments as $dept) {

      $total_amount = 0;
      $admission_fee = 0;

      $query_adm = Study::searchDegreeStudent($id, $admission_roll, $dept->dept_name, $current_level, $session);

      $admissions = $query_adm->groupBy('id')->get();
      $student_rolls = $query_adm->pluck('admission_roll')->toArray();

      $query_invoice = Invoice::where('type', 'degree_admission')->where('status', 'Paid');
      if ($session != '')    $query_invoice->where('admission_session', $session);
      $query_invoice->whereIn('roll', $student_rolls);
      // if($dept != '')    $query_invoice->where('subject', $dept->dept_name);

      if ($from_date != '') {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
      }

      $invoices = $query_invoice->orderByRaw("FIELD(subject , '$dept->dept_name') DESC");
      $invoices = $query_invoice->get();
      $total_amount = $query_invoice->sum('total_amount');

      if ($total_amount < 1) {
        continue;
      }

      if (count($invoices)) {
        $admission_fee =  $query_invoice->first()->total_amount;
        $total_amount = count($invoices) * $admission_fee;
      }

      $data[] = [$i, $dept->dept_name, count($invoices), $current_level, $session, $admission_fee, $total_amount, $from_date, $to_date];

      $i++;
    }


    $filename = 'degree_admission_departmental_reports.csv';
    $file = fopen(public_path('temp/' . $filename), 'w');
    foreach ($data as $row) {
      fputcsv($file, (array) $row);
    }
    fclose($file);
    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
  }

  public function degree_application(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $dept_name = $request->dept_name;

    $title = 'Easy CollegeMate - Masters Application Management';
    $breadcrumb = 'report.degree.application:Application|Dashboard';


    $query = DB::table('degree_student_applications')->orderBy('id', 'asc');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }
    // check permission
    query_has_permissions($query, ['session', 'exam_year', 'dept_name']);

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');

    $applications = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.degree.appreport', compact('title', 'breadcrumb', 'applications', 'dept_name', 'num_rows', 'total_amount', 'session', 'exam_year', 'from_date', 'to_date', 'admission_roll'));
  }

  public function generateDegAppReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $dept_name = $request->dept_name;

    $title = 'Easy CollegeMate - Degree Application Reports';
    $breadcrumb = 'report.degree.application:Application|Dashboard';


    $query = DB::table('degree_student_applications')->orderBy('id', 'asc');

    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }

    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }
    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }

    $applications = $query->orderBy('date', 'asc')->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Admission Roll', 'Name', 'Department', 'Contact No', 'Session', 'Total Amount', 'Payment Date'];

      foreach ($applications as $val) {
        $data[] = [
          $val->admission_roll,
          $val->name,
          $val->dept_name,
          $val->contact_no,
          $val->session,
          $val->total_amount,
          $val->date
        ];
      }
      $filename = 'degree_application_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.degappreport', compact('applications', 'exam_year', 'session')));
    $mpdf->Output();
  }

  public function hsc2nd_admission(Request $request)
  {
    $student_id = $request->get('id');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Hsc Management|Dashboard';

    $query = DB::table('student_info_hsc_add')->orderBy('id', 'desc');

    if ($student_id != '') {
      $query->where('id', $student_id);
    }

    if ($groups != '') {
      $query->where('groups', $groups);
    }

    if ($current_level != '') {
      $query->where('level_study', $current_level);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }

    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');

    $hscstudents = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.hsc.2ndadmreport', compact('title', 'breadcrumb', 'hscstudents', 'student_id', 'groups', 'current_level', 'session', 'exam_year', 'num_rows', 'from_date', 'to_date', 'total_amount'));
  }

  public function generateHsc2ndAdmReport(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");
    $student_id = $request->get('id');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Hsc Management|Dashboard';

    if ($request->type == 'csv_dept_report') {
      return $this->hsc2ndadmGroupReports($request);
    }

    $query = DB::table('student_info_hsc_add')->orderBy('id', 'desc');

    if ($student_id != '') {
      $query->where('id', $student_id);
    }

    if ($groups != '') {
      $query->where('groups', $groups);
    }

    if ($current_level != '') {
      $query->where('level_study', $current_level);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($from_date != '') {
      $query->where('date', '>=', $from_date);
    }

    if ($to_date != '') {
      $query->where('date', '<=', $to_date);
    }
    $query->orderBy("groups", "DESC");
    $query->orderBy('id', 'asc');

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    $records = $query->get();

    if ($request->get('type') == 'csv') {
      $data[] = ['Student ID', 'Name', 'Groups', 'Session', 'Total Amount', 'Payment Date'];

      foreach ($records as $val) {
        $data[] = [
          $val->id,
          $val->name,
          $val->groups,
          $val->session,
          $val->total_amount,
          $val->date
        ];
      }
      $filename = 'hsc2nd_year_admission_reports.csv';
      $file = fopen(public_path('temp/' . $filename), 'w');
      foreach ($data as $row) {
        fputcsv($file, (array) $row);
      }
      fclose($file);
      $headers = array(
        'Content-Type' => 'text/csv',
      );

      return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
    }

    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion = true;
    $mpdf->charset_in = 'UTF-8';
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.hsc2ndadmreport', compact('records', 'exam_year', 'session')));
    $mpdf->Output();
  }

  public function hsc2ndadmGroupReports(Request $request)
  {
    ini_set("pcre.backtrack_limit", "5000000");

    $student_id = $request->get('id');
    $groups = $request->get('groups');
    $level = $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query_adm = DB::table('student_info_hsc_add')->orderBy('id', 'desc');

    if ($student_id != '') {
      $query_adm->where('id', $student_id);
    }

    if ($groups != '') {
      $query_adm->where('groups', $groups);
    }

    if ($current_level != '') {
      $query_adm->where('level_study', $level);
    }

    if ($exam_year != '') {
      $query_adm->where('exam_year', $exam_year);
    }

    if ($session != '') {
      $query_adm->where('session', $session);
    }

    if ($from_date != '') {
      $from_date = date('Y-m-d', strtotime($request->from_date));
      $query_adm->where('date', '>=', $from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d', strtotime($request->to_date));
      $query_adm->where('date', '<=', $to_date);
    }

    $amount_ff_lists = $query_adm->pluck('total_amount')->toArray();
    sort($amount_ff_lists);
    $amount_ff_groups = array_unique($amount_ff_lists);
    $amount_ff_groups = array_values($amount_ff_groups);

    $column = ['SI', 'Groups Name', 'Total Number of Students', 'Session', 'From Date', 'To Date', 'Total Amount'];
    $data[] = array_merge($column, $amount_ff_groups);


    $query_fac = DB::table('faculties');

    if ($groups != '') {
      $query_fac->where('faculty_name', $groups);
    }
    // check permission
    query_has_permissions($query_fac, ['faculty_name']);

    $faculties = $query_fac->get('faculty_name');
    $i = 1;

    foreach ($faculties as $dept) {

      $total_amount = 0;
      $admission_fee = 0;

      $query = DB::table('student_info_hsc_add')->orderBy('id', 'desc');

      if ($student_id != '') {
        $query->where('id', $student_id);
      }

      $query->where('groups', $dept->faculty_name);

      if ($current_level != '') {
        $query->where('level_study', $level);
      }

      if ($exam_year != '') {
        $query->where('exam_year', $exam_year);
      }

      if ($session != '') {
        $query->where('session', $session);
      }

      if ($from_date != '') {
        $from_date = date('Y-m-d', strtotime($from_date));
        $query->where('date', '>=', $from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d', strtotime($to_date));
        $query->where('date', '<=', $to_date);
      }

      $total_amount = 0;
      $total_amount = $query->sum('total_amount');
      $records = $query->get();

      $total_amount_lists = $amount_lists = $query->pluck('total_amount')->toArray();
      sort($amount_lists);
      $amount_groups = array_unique($amount_lists);
      $amount_groups = array_values($amount_groups);


      if ($total_amount < 1) {
        continue;
      }

      $raw_data = [];

      for ($j = 0; $j < count($amount_ff_groups); $j++) {
        $total_count = count(array_keys($total_amount_lists, $amount_ff_groups[$j]));
        $raw_data[] = $total_count == 0 ? '' : $total_count;
      }

      $values = [$i, $dept->faculty_name, count($records), $session, $from_date, $to_date, $total_amount];

      $data[] = array_merge($values, $raw_data);

      $i++;
    }


    $filename = 'hsc_2nd_admission_groups_reports.csv';
    $file = fopen(public_path('temp/' . $filename), 'w');
    foreach ($data as $row) {
      fputcsv($file, (array) $row);
    }
    fclose($file);
    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download(public_path() . '/temp/' . $filename, $filename, $headers);
  }

  public function masters_form_fillup(Request $request)
  {
    $student_id = $request->get('id');
    $dept_name = $request->get('dept_name');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Masters Formfillup Reports|Dashboard';

    $query = DB::table('form_fillup')->where('course', 'Masters')->orderBy('id', 'desc');

    if ($student_id != '') {
      $query->where('id', $student_id);
    }

    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }

    if ($current_level != '') {
      $query->where('level_study', $level);
    }

    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($session != '') {
      $query->where('session', $session);
    }

    if ($from_date != '') {
      $from_date = date('Y-m-d', strtotime($from_date));
      $query->where('date', '>=', $from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d', strtotime($to_date));
      $query->where('date', '<=', $to_date);
    }

    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');

    $students = $query->paginate(Study::paginate());

    return view('BackEnd.student.report.masters.mscffmreport', compact('title', 'breadcrumb', 'students', 'student_id', 'dept_name', 'current_level', 'session', 'exam_year', 'num_rows', 'from_date', 'to_date', 'total_amount'));
  }
}

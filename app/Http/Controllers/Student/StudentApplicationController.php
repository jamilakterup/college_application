<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\FormFillup;
use Illuminate\Http\Request;
use Session;
use Mpdf\Mpdf;
use DB;
use Illuminate\Support\Facades\Redirect;

class StudentApplicationController extends Controller
{
    
  
  
  public function degreeapplication(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $date = $request->date;
    $dept_name = $request->dept_name;
    $level = $request->level;
    $registration_type = $request->registration_type;
    
    $title = 'Easy CollegeMate - Degree Application Management';
    $breadcrumb = 'student.application.degree:Application|Dashboard';
    
    
    $query =DB::table('degree_student_applications')->orderBy('id', 'asc');
    
    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }

    if ($date != '') {
      $query->where('date', $date);
    }

    if ($level != '') {
      $query->where('current_level', $level);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }

    // check permission
    query_has_permissions($query, ['dept_name']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $applications = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.application.degree.index',compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year','date','admission_roll', 'dept_name', 'level', 'registration_type'));
  }
  
  public function honoursapplication(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $date = $request->date;
    
    $title = 'Easy CollegeMate - Honours Application Management';
    $breadcrumb = 'student.application.honours:Application|Dashboard';
    
    
    $query =DB::table('hons_student_applications')->orderBy('id', 'asc')
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

    if ($date != '') {
      $query->where('date', $date);
    }
    // check permission
    query_has_permissions($query, ['session', 'exam_year']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $applications = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.application.honours.index',compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year','date','admission_roll'));
  }
  
  public function mastersapplication(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $date = $request->date;
    $dept_name = $request->dept_name;
    $level = $request->level;
    $registration_type = $request->registration_type;
    
    $title = 'Easy CollegeMate - Honours Application Management';
    $breadcrumb = 'student.application.honours:Application|Dashboard';
    
    
    $query =DB::table('masters_student_applications')->orderBy('id', 'asc');
    
    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }

    if ($date != '') {
      $query->where('date', $date);
    }

    if ($level != '') {
      $query->where('current_level', $level);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }

    // check permission
    query_has_permissions($query, ['dept_name']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $applications = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.application.masters.index',compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year','date','admission_roll', 'dept_name', 'level','registration_type'));
  }
  
  
  /**
  * Show the form for creating a new resource.
  *
  * @return Response
  */
  public function create()
  {
    //
  }
  
  public function generateFFReport()
  {
    if(Request::ajax()){
      
      
      if (Session::has('session'))
      {
        $session = Session::get('session');    
      }
      if (Session::has('id'))
      {
        $id = Session::get('id');    
      }
      if (Session::has('dept_name'))
      {
        $dept_name = Session::get('dept_name');    
      }
      
      if (Session::has('level_study'))
      {
        $level_study = Session::get('level_study');    
      }
      
      $bb=DB::table('form_fillup');
      $bb ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study', 'form_fillup.dept_name');
      if(isset($dept_name) && $dept_name != '')
      {
        $bb->where('form_fillup.dept_name',$dept_name);
      }
      if(isset($id) && $id != '')
      {
        $bb->where('form_fillup.id',$id);
      }
      if(isset($level_study) && $level_study != '')
      {
        $bb->where('level_study',$level_study);              
      }
      if(isset($session) && $session != '')
      {
        $bb->where('form_fillup.session',$session);                        
      }
      $form_fillup=$bb->get();
      
      
      
      return view('student.formfillup.report')
      ->withForm_fillup($form_fillup);
      //echo json_encode("hello");
    }
  }
  
  public function Search()
  {
    $id = Study::filterInput('id', $request->get('id'));
    
    
    $dept_name = Study::filterInput('dept_name', $request->get('dept_name'));       
    $level_study = Study::filterInput('current_level', $request->get('current_level'));
    $session = Study::filterInput('session', $request->get('session'));
    
    Session::put('session', $session);
    Session::put('id', $id);
    Session::put('dept_name', $dept_name);
    Session::put('level_study', $level_study);
    
    
    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'formfillup.form.index:Form Fillup|Dashboard';
    
    $bb=DB::table('form_fillup');
    $bb ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study', 'form_fillup.dept_name');
    if(isset($dept_name) && $dept_name != '')
    {
      $bb->where('form_fillup.dept_name',$dept_name);
    }
    if(isset($id) && $id != '')
    {
      $bb->where('form_fillup.id',$id);
    }
    if(isset($level_study) && $level_study != '')
    {
      $bb->where('level_study',$level_study);              
    }
    if(isset($session) && $session != '')
    {
      $bb->where('form_fillup.session',$session);                        
    }
    
    $form_fillup= $bb->paginate(Study::paginate()); 
    
    
    
    
    $level_lists = ['' => 'Select Level','Honours 1st Year' => 'Honours 1st Year','Honours 2nd Year' => 'Honours 2nd Year','Honours 3rd Year' => 'Honours 3rd Year','Honours 4th Year' => '2Honours 4th Year'];
    $session_lists = ['' => 'Select Session','2011-2012' => '2011-2012','2012-2013' => '2012-2013','2013-2014' => '2013-2014','2014-2015' => '2014-2015','2015-2016' => '2015-2016','2016-2017' => '2016-2017','2017-2018' => '2017-2018','2018-2019' => '2018-2019']; 
    $dept_lists = Department::lists('dept_name','dept_name');   
    
    return view('student.formfillup.index')
    ->withTitle($title)
    ->withBreadcrumb($breadcrumb)
    ->withLevel_lists($level_lists)
    ->withDept_lists($dept_lists)
    ->withSession_lists($session_lists)
    ->withForm_fillup($form_fillup);
  }
  
  
  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function destroy($id)
  {
    //
  }
  public function hscformfillup()
  {
    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'formfillup.form.index:Form Fillup|Dashboard';
    
    
    $form_fillup=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study as level_study', 'form_fillup.dept_name')
    ->paginate(Study::paginate()); 
    
    $count_form_fillup=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study as level_study', 'form_fillup.dept_name')->count();
    
    $sum_amount=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study as level_study', 'form_fillup.dept_name')->sum('total_amount');
    $level_lists = ['' => 'Select Level','HSC 1st Year' => 'HSC 1st Year','HSC 2nd Year' => 'HSC 2nd Year'];
    $session_lists = ['' => 'Select Exam Year','2019' => '2019','2020' => '2020','2021' => '2021']; 
    
    $dept_lists = ['' => 'Select Group','Science' => 'Science','Humanities' => 'Humanities','Business Studies' => 'Business Studies'];     
    
    return view('student.formfillup.hscformfillup')
    ->withTitle($title)
    ->withBreadcrumb($breadcrumb)
    ->withLevel_lists($level_lists)
    ->withDept_lists($dept_lists)
    ->withSession_lists($session_lists)
    ->withCount_form_fillup($count_form_fillup)
    ->withSum_amount($sum_amount)
    ->withForm_fillup($form_fillup);
  }
  public function hscgenerateFFReport()
  {
    
    
    
    if (Session::has('hsc_session'))
    {
      $session = Session::get('hsc_session');    
    }
    
    if (Session::has('hsc_dept_name'))
    {
      $dept_name = Session::get('hsc_dept_name');    
    }
    
    if (Session::has('hsc_level_study'))
    {
      $level_study = Session::get('hsc_level_study');    
    }
    
    $bb=DB::table('form_fillup');
    $bb->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study as level_study', 'form_fillup.dept_name');
    if(isset($dept_name) && $dept_name != '')
    {
      $bb->where('form_fillup.dept_name',$dept_name);
    }
    if(isset($id) && $id != '')
    {
      $bb->where('form_fillup.id',$id);
    }
    if(isset($level_study) && $level_study != '')
    {
      $bb->where('level_study',$level_study);              
    }
    if(isset($session) && $session != '')
    {
      $bb->where('form_fillup.exam_year',$session);                        
    }
    $form_fillup=$bb->get();
    //return    $form_fillup;       
    
    require app_path().'/libs/mpdf/third_party/mpdf60/mpdf.php'; 
    
    $mpdf = new mPDF(); 
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';      
    $mpdf->WriteHTML(view('pdf.hscffreport')->withForm_fillup($form_fillup));
    $mpdf->Output();
    
    
    
    //return view('student.formfillup.report')
    //->withForm_fillup($form_fillup);
    //echo json_encode("hello");
    
  }
  
  public function hscSearch()
  {
    
    
    
    $dept_name = Study::filterInput('dept_name', $request->get('dept_name'));       
    $level_study = Study::filterInput('current_level', $request->get('current_level'));
    $session = Study::filterInput('session', $request->get('session'));
    
    Session::put('hsc_session', $session);
    Session::put('hsc_dept_name', $dept_name);
    Session::put('hsc_level_study', $level_study);
    
    
    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'formfillup.form.index:Form Fillup|Dashboard';
    
    $bb=DB::table('form_fillup');
    $bb->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study as level_study', 'form_fillup.dept_name');
    if(isset($dept_name) && $dept_name != '')
    {
      $bb->where('form_fillup.dept_name',$dept_name);
    }
    
    if(isset($level_study) && $level_study != '')
    {
      $bb->where('level_study',$level_study);              
    }
    if(isset($session) && $session != '')
    {
      $bb->where('form_fillup.exam_year',$session);                        
    }
    
    $form_fillup= $bb->paginate(Study::paginate()); 
    
    $count_bb=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study', 'form_fillup.dept_name');
    if(isset($dept_name) && $dept_name != '')
    {
      $count_bb->where('form_fillup.dept_name',$dept_name);
    }
    
    if(isset($level_study) && $level_study != '')
    {
      $count_bb->where('level_study',$level_study);              
    }
    if(isset($session) && $session != '')
    {
      $count_bb->where('form_fillup.exam_year',$session);                        
    }
    $count_form_fillup =  $count_bb->count();
    $sum_ff=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study', 'form_fillup.dept_name');
    
    {
      $sum_ff->where('form_fillup.dept_name',$dept_name);
    }
    
    if(isset($level_study) && $level_study != '')
    {
      $sum_ff->where('level_study',$level_study);              
    }
    if(isset($session) && $session != '')
    {
      $sum_ff->where('form_fillup.exam_year',$session);                        
    }
    
    $sum_amount = $sum_ff->sum('total_amount');
    
    
    
    $level_lists = ['' => 'Select Level','HSC 1st Year' => 'HSC 1st Year','HSC 2nd Year' => 'HSC 2nd Year'];
    $session_lists = ['' => 'Select Exam Year','2019' => '2019','2020' => '2020','2021' => '2021']; 
    
    $dept_lists = ['' => 'Select Group','Science' => 'Science','Humanities' => 'Humanities','Business Studies' => 'Business Studies'];  
    
    return view('student.formfillup.hscformfillup')
    ->withTitle($title)
    ->withBreadcrumb($breadcrumb)
    ->withLevel_lists($level_lists)
    ->withDept_lists($dept_lists)
    ->withSession_lists($session_lists)
    ->withCount_form_fillup($count_form_fillup)
    ->withSum_amount($sum_amount)                   
    ->withForm_fillup($form_fillup);
  }
  
  public function generateHonAppReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $date = $request->date;
    
    $title = 'Easy CollegeMate - Honours Form Fillup Management';
    $breadcrumb = 'student.formfillup.honours:Form Fillup|Dashboard';
    
    
    $query =DB::table('hons_student_applications')->orderBy('id', 'asc')
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

    if ($date != '') {
      $query->where('date', $date);
    }
    
    $applications = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['Admission Roll','Name', 'Father Name','Mother Name', 'Contact No', 'Session', 'Admission Form', 'HSC Transcript'];
        
        foreach($applications as $val){
            $data[] = [
              $val->admission_roll,$val->name,$val->father_name,$val->mother_name,$val->contact_no,$val->session,$val->admission_form, 
              $val->hsc_transcript != '' ? $val->admission_roll.'_'.$val->hsc_transcript:''
            ];
        }
        $filename = 'hons_application_reports.csv';
        $file = fopen('temp/'.$filename, 'w');
        foreach ($data as $row) {
            fputcsv($file, (array) $row);
        }
        fclose($file);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        
        return response()->download(public_path().'/temp/'.$filename, $filename,$headers);
    }
    
    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';  
    $mpdf->WriteHTML(view('BackEnd.student.pdf.honappreport', compact('applications', 'exam_year','session')));
    $mpdf->Output();
    
  }
  
  public function generateDegFFReport(Request $request){
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    
    
    $query =FormFillup::orderBy('id', 'asc')
    ->select('id','exam_year','total_amount','id as name','course','groups', 'session','date','level_study', 'dept_name')
    ->where('course', 'Degree');
    
    if ($student_id != '') {
      $query->where('id', $student_id);
    }
    
    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }
    
    if ($level != '') {
      $query->where('level_study', $level);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }
    
    $form_fillup = $query->get();
    
    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';  
    $mpdf->WriteHTML(view('BackEnd.student.pdf.degreeffreport', compact('form_fillup', 'exam_year')));
    $mpdf->Output();
  }
  
  public function generateMastersFFReport(Request $request){
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    
    
    $query =FormFillup::orderBy('id', 'asc')
    ->select('id','exam_year','total_amount','id as name','course','groups', 'session','date','level_study', 'dept_name')
    ->where('course', 'Masters');
    
    if ($student_id != '') {
      $query->where('id', $student_id);
    }
    
    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }
    
    if ($level != '') {
      $query->where('level_study', $level);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }
    
    $form_fillup = $query->get();
    
    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';  
    $mpdf->WriteHTML(view('BackEnd.student.pdf.mastersffreport', compact('form_fillup', 'exam_year')));
    $mpdf->Output();
  }

  public function generateMastersAppReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $dept_name = $request->dept_name;
    $exam_year = $request->exam_year;
    $date = $request->date;
    $registration_type = $request->registration_type;
    
    $title = 'Easy CollegeMate - Masters Application Management';
    $breadcrumb = 'student.application.degree:Application|Dashboard';
    
    
    $query =DB::table('masters_student_applications')->orderBy('id', 'asc');
    
    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }

    if ($date != '') {
      $query->where('date', $date);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }


    
    $applications = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['Admission Roll','Name', 'Deptartment Name','Contact No', 'Session', 'Total Amount', 'Payment Date'];
        
        foreach($applications as $val){
            $data[] = [
              $val->admission_roll,$val->name,$val->dept_name,$val->contact_no,$val->session,$val->total_amount,$val->date
            ];
        }
        $filename = 'masters_application_reports.csv';
        $file = fopen('temp/'.$filename, 'w');
        foreach ($data as $row) {
            fputcsv($file, (array) $row);
        }
        fclose($file);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        
        return response()->download(public_path().'/temp/'.$filename, $filename,$headers);
    }
    
    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';  
    $mpdf->WriteHTML(view('BackEnd.student.pdf.mscappreport', compact('applications', 'exam_year','session')));
    $mpdf->Output();
    
  }

  public function generateDegAppReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $dept_name = $request->dept_name;
    $exam_year = $request->exam_year;
    $date = $request->date;
    $registration_type = $request->registration_type;
    
    $title = 'Easy CollegeMate - Masters Application Management';
    $breadcrumb = 'student.application.degree:Application|Dashboard';
    
    
    $query =DB::table('degree_student_applications')->orderBy('id', 'asc');
    
    if ($admission_roll != '') {
      $query->where('admission_roll', $admission_roll);
    }
    
    if ($session != '') {
      $query->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query->where('exam_year', $exam_year);
    }

    if ($registration_type != '') {
      $query->where('registration_type', $registration_type);
    }

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }

    if ($date != '') {
      $query->where('date', $date);
    }
    
    $applications = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['Admission Roll','Name', 'Deptartment Name','Contact No', 'Session', 'Total Amount', 'Payment Date'];
        
        foreach($applications as $val){
            $data[] = [
              $val->admission_roll,$val->name,$val->dept_name,$val->contact_no,$val->session,$val->total_amount,$val->date
            ];
        }
        $filename = 'degree_application_reports.csv';
        $file = fopen('temp/'.$filename, 'w');
        foreach ($data as $row) {
            fputcsv($file, (array) $row);
        }
        fclose($file);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        
        return response()->download(public_path().'/temp/'.$filename, $filename,$headers);
    }
    
    $mpdf = new Mpdf();
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    $mpdf->allow_charset_conversion=true;
    $mpdf->charset_in='UTF-8';  
    $mpdf->WriteHTML(view('BackEnd.student.pdf.degappreport', compact('applications', 'exam_year','session')));
    $mpdf->Output();
    
  }

  public function getApplicationDownload($type,$id){
        $application = DB::table('hons_student_applications')->where('id', $id)->get()[0];
        if($type == 'admission_form'){
            $file = public_path().'/upload/college/honours/application/'.$type.'/'.$application->admission_form;
            $filename = explode('_', $application->admission_form)[0].'.jpg';
        }
        if($type == 'hsc_transcript'){
            $file = public_path().'/upload/college/honours/application/'.$type.'/'.$application->hsc_transcript;
            $filename  = $application->admission_roll.'_'.$application->hsc_transcript.'.jpg';
        }
        
        $headers = array('Content-Type: image/jpeg');
        return response()->download($file, $filename,$headers);
    }
}

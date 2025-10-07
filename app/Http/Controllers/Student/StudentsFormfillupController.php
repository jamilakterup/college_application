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

class StudentsFormfillupController extends Controller
{
  public function index()
  {
    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'formfillup.form.index:Form Fillup|Dashboard';
    
    
    $form_fillup=DB::table('form_fillup')
    ->select('form_fillup.id','form_fillup.exam_year','form_fillup.total_amount','form_fillup.id as name','form_fillup.course','form_fillup.groups', 'form_fillup.session','form_fillup.date','form_fillup.level_study', 'form_fillup.dept_name')
    ->paginate(Study::paginate()); 
    
    
    
    
    $level_lists = ['' => 'Select Level','Honours 1st Year' => 'Honours 1st Year','Honours 2nd Year' => 'Honours 2nd Year','Honours 3rd Year' => 'Honours 3rd Year','Honours 4th Year' => 'Honours 4th Year'];
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
  
  
  public function degreeformfillup(Request $request)
  {

    $title = 'Easy CollegeMate - Degree Form Fillup Management';
    $breadcrumb = 'student.formfillup.degree:Form Fillup|Dashboard';

    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    
    $query =  FormFillup::orderBy('id', 'asc')
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

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query->where('date', '<=',$to_date);
    }
    // check permission
    query_has_permissions($query, ['dept_name', 'level_study','session', 'exam_year']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $form_fillup = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.formfillup.degree.index',compact('title', 'breadcrumb', 'form_fillup', 'num_rows', 'total_amount', 'student_id','dept_name','session', 'level', 'exam_year','from_date','to_date'));
  }
  
  public function honoursformfillup(Request $request)
  {
    $id = $request->id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->current_level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    $title = 'Easy CollegeMate - Honours Form Fillup Management';
    $breadcrumb = 'student.formfillup.honours:Form Fillup|Dashboard';
    
    
    $query =FormFillup::orderBy('auto_id', 'desc')
    ->where('course', 'Honours');
    
    if ($id != '') {
      $query->where('id', $id);
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

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query->where('date', '<=',$to_date);
    }
    // check permission
    query_has_permissions($query, ['dept_name', 'level_study','session', 'exam_year']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $students = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.formfillup.honours.index',compact('title', 'breadcrumb', 'students', 'num_rows', 'total_amount', 'id','dept_name','session', 'level', 'exam_year','from_date','to_date'));
  }
  
  public function mastersformfillup(Request $request)
  {
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $level = $request->level;
    $exam_year = $request->exam_year;
    
    $title = 'Easy CollegeMate - Masters Form Fillup Management';
    $breadcrumb = 'student.formfillup.masters:Form Fillup|Dashboard';
    
    
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
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $form_fillup = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.formfillup.masters.index',compact('title', 'breadcrumb', 'form_fillup', 'num_rows', 'total_amount', 'student_id','dept_name','session', 'level', 'exam_year'));
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
    $id = Study::filterInput('id', Input::get('id'));
    
    
    $dept_name = Study::filterInput('dept_name', Input::get('dept_name'));       
    $level_study = Study::filterInput('current_level', Input::get('current_level'));
    $session = Study::filterInput('session', Input::get('session'));
    
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
    //return 	$form_fillup;		
    
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
    
    
    
    $dept_name = Study::filterInput('dept_name', Input::get('dept_name'));       
    $level_study = Study::filterInput('current_level', Input::get('current_level'));
    $session = Study::filterInput('session', Input::get('session'));
    
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
  
  public function generateHonFFReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");

    if($request->type == 'csv_dept_report'){
      return $this->hons_csv_dept_report($request);
    }
    
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    $title = 'Easy CollegeMate - Honours Form Fillup Management';
    $breadcrumb = 'student.formfillup.honours:Form Fillup|Dashboard';
    
    
    $query =FormFillup::where('course', 'Honours');
    
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

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query->where('date', '<=',$to_date);
    }
    
    $form_fillup = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['SI','Student ID', 'Name','Current Level', 'Department', 'Exam Year', 'Paid Amount', 'Paid Date'];
        
          foreach($form_fillup as $key => $val){
              $data[] = [
                $key+1, $val->id,$val->name,$val->level_study,$val->dept_name,$val->exam_year,$val->total_amount,$val->date];
          }
          $filename = 'hons_form_fillup_reports.csv';
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
    $mpdf->WriteHTML(view('BackEnd.student.pdf.honffreport', compact('form_fillup', 'exam_year')));
    $mpdf->Output();
    
    
  }
  
  public function generateDegFFReport(Request $request){

    ini_set("pcre.backtrack_limit", "5000000");

    if($request->type == 'csv_dept_report'){
      return $this->deg_csv_dept_report($request);
    }
    
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    
    $query =FormFillup::where('course', 'Degree');
    
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

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query->where('date', '<=',$to_date);
    }
    
    $form_fillup = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['SI','Student ID', 'Name','Current Level', 'Department', 'Exam Year', 'Paid Amount', 'Paid Date'];
        
          foreach($form_fillup as $key => $val){
              $data[] = [
                $key+1, $val->id,$val->name,$val->level_study,$val->dept_name,$val->exam_year,$val->total_amount,$val->date];
          }
          $filename = 'deg_form_fillup_reports.csv';
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

  public function hons_csv_dept_report($request){
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = DB::table('departments');

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }
    // check permission
    query_has_permissions($query, ['dept_name', 'level_study','session', 'exam_year']);

    $departments = $query->get('dept_name');
    $i = 1;

    $query_f = FormFillup::where('course', 'Honours');

    if($dept_name != ''){
      $query_f->where('dept_name', $dept_name);
    }
    if ($level != '') {
      $query_f->where('level_study', $level);
    }
    
    if ($session != '') {
      $query_f->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query_f->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query_f->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query_f->where('date', '<=',$to_date);
    }

    $amount_ff_lists = $query_f->pluck('total_amount')->toArray();
    sort($amount_ff_lists);
    $amount_ff_groups = array_unique($amount_ff_lists);
    $amount_ff_groups = array_values($amount_ff_groups);

    $column = ['SI','Department Name','Total Number of Students', 'Honours Level','Session', 'From Date', 'To Date', 'Total Amount'];
    $data[] = array_merge($column, $amount_ff_groups);

    foreach($departments as $dept){
      $query =FormFillup::where('course', 'Honours');
    
      if ($student_id != '') {
        $query->where('id', $student_id);
      }
      
      $query->where('dept_name', $dept->dept_name);
      
      if ($level != '') {
        $query->where('level_study', $level);
      }
      
      if ($session != '') {
        $query->where('session', $session);
      }
      
      if ($exam_year != '') {
        $query->where('exam_year', $exam_year);
      }

      if ($from_date != '') {
        $from_date = date('Y-m-d',strtotime($request->from_date));
        $query->where('date', '>=',$from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d',strtotime($request->to_date));
        $query->where('date', '<=',$to_date);
      }
      
      // $form_fillup = $query->orderBy('date', 'asc')->get();
      $total_amount = 0;
      $total_amount = $query->sum('total_amount');
      $form_fillups = $query->get();

      $total_amount_lists = $amount_lists = $query->pluck('total_amount')->toArray();
      sort($amount_lists);
      $amount_groups = array_unique($amount_lists);
      $amount_groups = array_values($amount_groups);
      

      if($total_amount < 1){
        continue;
      }

      $raw_data = [];

      for ($j=0; $j < count($amount_ff_groups); $j++) {
        $total_count = count(array_keys($total_amount_lists,$amount_ff_groups[$j]));
        $raw_data[] = $total_count == 0 ? '' : $total_count;
      }

      $values = [$i, $dept->dept_name,count($form_fillups),$level,$session,$from_date, $to_date,$total_amount];

      $data[] = array_merge($values, $raw_data);

      $i++;
    }
    
    
    $filename = 'hons_form_fillup_reports.csv';
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

  public function deg_csv_dept_report($request){
    $student_id = $request->student_id;
    $dept_name = $request->dept_name;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $level = $request->level;
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query = DB::table('departments');

    if($dept_name != ''){
      $query->where('dept_name', $dept_name);
    }
    // check permission
    query_has_permissions($query, ['dept_name', 'level_study','session', 'exam_year']);

    $departments = $query->get('dept_name');
    $i = 1;

    $query_f = FormFillup::where('course', 'Degree');

    if($dept_name != ''){
      $query_f->where('dept_name', $dept_name);
    }
    if ($level != '') {
      $query_f->where('level_study', $level);
    }
    
    if ($session != '') {
      $query_f->where('session', $session);
    }
    
    if ($exam_year != '') {
      $query_f->where('exam_year', $exam_year);
    }

    if ($from_date != '') {
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query_f->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query_f->where('date', '<=',$to_date);
    }

    $amount_ff_lists = $query_f->pluck('total_amount')->toArray();
    sort($amount_ff_lists);
    $amount_ff_groups = array_unique($amount_ff_lists);
    $amount_ff_groups = array_values($amount_ff_groups);

    $column = ['SI','Department Name','Total Number of Students', 'Honours Level','Session', 'From Date', 'To Date', 'Total Amount'];
    $data[] = array_merge($column, $amount_ff_groups);

    foreach($departments as $dept){
      $query =FormFillup::where('course', 'Degree');
    
      if ($student_id != '') {
        $query->where('id', $student_id);
      }
      
      $query->where('dept_name', $dept->dept_name);
      
      if ($level != '') {
        $query->where('level_study', $level);
      }
      
      if ($session != '') {
        $query->where('session', $session);
      }
      
      if ($exam_year != '') {
        $query->where('exam_year', $exam_year);
      }

      if ($from_date != '') {
        $from_date = date('Y-m-d',strtotime($request->from_date));
        $query->where('date', '>=',$from_date);
      }

      if ($to_date != '') {
        $to_date = date('Y-m-d',strtotime($request->to_date));
        $query->where('date', '<=',$to_date);
      }
      
      // $form_fillup = $query->orderBy('date', 'asc')->get();
      $total_amount = 0;
      $total_amount = $query->sum('total_amount');
      $form_fillups = $query->get();

      $total_amount_lists = $amount_lists = $query->pluck('total_amount')->toArray();
      sort($amount_lists);
      $amount_groups = array_unique($amount_lists);
      $amount_groups = array_values($amount_groups);
      

      if($total_amount < 1){
        continue;
      }

      $raw_data = [];

      for ($j=0; $j < count($amount_ff_groups); $j++) {
        $total_count = count(array_keys($total_amount_lists,$amount_ff_groups[$j]));
        $raw_data[] = $total_count == 0 ? '' : $total_count;
      }

      $values = [$i, $dept->dept_name,count($form_fillups),$level,$session,$from_date, $to_date,$total_amount];

      $data[] = array_merge($values, $raw_data);

      $i++;
    }
    
    
    $filename = 'deg_form_fillup_reports.csv';
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
}

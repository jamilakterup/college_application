<?php

namespace App\Http\Controllers\Student\Report;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\AdmissionStudent;
use Illuminate\Http\Request;
use DB;
use Mpdf\Mpdf;
use Excel;
use App\Exports\Degree\Admission\DegAdmExport;
use App\Exports\Degree\Formfillup\DegFFExport;

class DegreeReportController extends Controller
{
    public function degree_admission(Request $request){
        $id = $request->get('id');
        $admission_roll = $request->get('admission_roll');
        $faculty = $request->get('faculty');
        $dept_name = $request->get('dept_name');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $from_date = $request->from_date !='' ? date('Y-m-d',strtotime($request->from_date)):null ;
        $to_date = $request->to_date != '' ? date('Y-m-d',strtotime($request->to_date)):null ;

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student:Student Degree Management|Dashboard';

        $query = Study::searchHonsStudent($id, $admission_roll, $faculty,$dept_name, $current_level, $session, $from_date, $to_date);

        $total_amount = 0;

        $total_amount = $query->sum('total_amount');

        $num_rows = $query->count();
        $students = $query->paginate(Study::paginate());

        return view('BackEnd.student.report.degree.admreport', compact('title', 'breadcrumb', 'students','id','admission_roll','faculty','dept_name','current_level','session','num_rows', 'from_date','to_date','total_amount'));

    }

    public function generateDegAdmReport(Request $request){
        ini_set("pcre.backtrack_limit", "5000000");

        $this->validate($request, [
          'session' => 'required'
        ]);

        $id = $request->get('id');
        $admission_roll = $request->get('admission_roll');
        $faculty = $request->get('faculty');
        $dept_name = $request->get('dept_name');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $from_date = $request->from_date !='' ? date('Y-m-d',strtotime($request->from_date)):null ;
        $to_date = $request->to_date != '' ? date('Y-m-d',strtotime($request->to_date)):null ;

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student:Student Degree Management|Dashboard';

        $query = Study::searchDegreeStudent($id, $admission_roll, $faculty,$dept_name, $current_level, $session, $from_date, $to_date);

        $students = $query->get();

        if($request->type == 'report_excel')
        return Excel::download(new DegAdmExport($request, $students), 'hons_admission_reports_'.$session.'.xlsx');

        if($request->get('type') =='csv'){
            $data[] = ['Student ID','Class Roll','Name','Father Name','Mother Name','Birth Date' ,'Gender','Admission Roll','Faculty','Department Name', 'Contact No','Current Level','Session', 'Total Amount','Transaction ID' ,'From Date', 'To Date'];
            
            foreach($students as $val){
                $data[] = [
                  $val->id,$val->class_roll,$val->name,$val->father_name,$val->mother_name,$val->birth_date,$val->gender,$val->admission_roll,$val->faculty_name,$val->dept_name,$val->contact_no,$val->current_level,$val->session,$val->total_amount,$val->transaction_id,$val->from_date,$val->to_date
                ];
            }
            $filename = 'deg_admission_reports_'.$session.'.csv';
            $file = fopen(public_path().'/temp/'.$filename, 'w');
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
        $mpdf->WriteHTML(view('BackEnd.student.report.pdf.degadmreport', compact('students','session')));
        $mpdf->Output();

    }

    public function degree_form_fillup(Request $request){
        $registraion_id  = $request->get('registraion_id');
        $faculty = $request->get('faculty');
        $dept_name = $request->get('dept_name');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $exam_year = $request->get('exam_year');
        $formfillup_type = $request->get('formfillup_type');
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student:Student Degree Formfillup Reports|Dashboard';

        $query =DB::table('form_fillup')->where('course', 'Degree')->orderBy('id', 'desc');
        
        if ($registraion_id  != '') {
          $query->where('id', $registraion_id );
        }
        
        if ($faculty != '') {
          $query->where('groups', $faculty);
        }

        if ($dept_name != '') {
          $query->where('dept_name', $dept_name);
        }
        
        if ($current_level != '') {
          $query->where('level_study', $current_level);
        }

        if ($formfillup_type != '') {
          $query->where('formfillup_type', $formfillup_type);
        }
        
        if ($exam_year != '') {
          $query->where('exam_year', $exam_year);
        }
        
        if ($session != '') {
          $query->where('session', $session);
        }

        if ($from_date != '') {
          $from_date = date('Y-m-d',strtotime($from_date));
          $query->where('date', '>=',$from_date);
        }

        if ($to_date != '') {
          $to_date = date('Y-m-d',strtotime($to_date));
          $query->where('date', '<=',$to_date);
        }
        
        $num_rows = $query->count();
        $total_amount = $query->sum('total_amount');
        
        $students = $query->paginate(Study::paginate());

        return view('BackEnd.student.report.degree.degffreport', compact('title', 'breadcrumb', 'students','registraion_id','faculty','dept_name','current_level','session','formfillup_type','exam_year','num_rows', 'from_date','to_date','total_amount'));
    }

    public function generateDegFFReport(Request $request){
        ini_set("pcre.backtrack_limit", "5000000");

        $this->validate($request, [
          'session' => 'required'
        ]);

        $registraion_id = $request->get('registraion_id');
        $faculty = $request->get('faculty');
        $dept_name = $request->get('dept_name');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $exam_year = $request->get('exam_year');
        $formfillup_type = $request->get('formfillup_type');
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query =DB::table('form_fillup')->where('course', 'Degree')->orderBy('id', 'desc');
        
        if ($registraion_id != '') {
          $query->where('id', $registraion_id);
        }
        
        if ($faculty != '') {
          $query->where('groups', $faculty);
        }

        if ($dept_name != '') {
          $query->where('dept_name', $dept_name);
        }
        
        if ($current_level != '') {
          $query->where('level_study', $current_level);
        }

        if ($formfillup_type != '') {
          $query->where('formfillup_type', $formfillup_type);
        }
        
        if ($exam_year != '') {
          $query->where('exam_year', $exam_year);
        }
        
        if ($session != '') {
          $query->where('session', $session);
        }

        if ($from_date != '') {
          $from_date = date('Y-m-d',strtotime($from_date));
          $query->where('date', '>=',$from_date);
        }

        if ($to_date != '') {
          $to_date = date('Y-m-d',strtotime($to_date));
          $query->where('date', '<=',$to_date);
        }

        $query->orderBy('id', 'asc');
        $query->orderBy("groups","ASC");
        
        $num_rows = $query->count();
        $total_amount = $query->sum('total_amount');
        $records = $query->get();

        if($request->type == 'report_excel')
            return Excel::download(new DegFFExport($request, $records), $current_level.'_deg_formfillup_reports_'.$exam_year.'.xlsx');

        if($request->get('type') =='csv'){
            $data[] = ['Registration ID','Name','Department', 'Session','Registration Type', 'Total Amount','Transaction ID', 'Payment Date', 'From Date', 'To Date'];
            
            foreach($records as $val){
                $data[] = [
                  $val->id,$val->name,$val->dept_name,$val->session,$val->formfillup_type,$val->total_amount,$val->transaction_id,$val->date, $from_date,$to_date
                ];
            }
            $filename = $current_level.'_deg_formfillup_reports_'.$exam_year.'.csv';
            $file = fopen(public_path().'/temp/'.$filename, 'w');
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
        $mpdf->WriteHTML(view('BackEnd.student.report.pdf.degffreport', compact('records', 'exam_year','session','current_level', 'from_date', 'to_date')));
        $mpdf->Output();
    }

    public function degree_application(Request $request)
  {
    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    $title = 'Easy CollegeMate - Degree Application Management';
    $breadcrumb = 'report.degree.application:Application|Dashboard';
    
    
    $query =DB::table('hons_student_applications')->orderBy('id', 'asc')
    ->where('current_level', 'Degree 1st Year');
    
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
      $query->where('date','>=' ,$from_date);
    }
    if ($to_date != '') {
      $query->where('date','<=' ,$to_date);
    }
    // check permission
    query_has_permissions($query, ['session', 'exam_year']);
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    
    $applications = $query->paginate(Study::paginate());
    
    return view('BackEnd.student.report.degree.appreport',compact('title', 'breadcrumb', 'applications', 'num_rows', 'total_amount', 'session', 'exam_year','from_date','to_date','admission_roll'));
  }

  public function generateDegAppReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");

    $this->validate($request, [
          'session' => 'required'
      ]);

    $admission_roll = $request->admission_roll;
    $session = $request->session;
    $exam_year = $request->exam_year;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    $title = 'Easy CollegeMate - Degree Application Reports';
    $breadcrumb = 'report.degree.application:Application|Dashboard';
    
    
    $query =DB::table('hons_student_applications')->orderBy('id', 'asc')
    ->where('current_level', 'Degree 1st Year');
    
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
      $query->where('date','>=' ,$from_date);
    }
    if ($to_date != '') {
      $query->where('date','<=' ,$to_date);
    }
    
    $applications = $query->orderBy('date', 'asc')->get();

    if($request->get('type') =='csv'){
        $data[] = ['Admission Roll','Name', 'Father Name','Mother Name', 'Contact No', 'Session', 'Admission Form', 'HSC Transcript', 'Total Amount', 'Payment Date'];
        
        foreach($applications as $val){
            $data[] = [
              $val->admission_roll,$val->name,$val->father_name,$val->mother_name,$val->contact_no,$val->session,$val->admission_form, 
              $val->hsc_transcript != '' ? $val->admission_roll.'_'.$val->hsc_transcript:'', $val->total_amount,$val->date
            ];
        }
        $filename = 'deg_application_reports.csv';
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
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.degappreport', compact('applications', 'exam_year','session')));
    $mpdf->Output();
    
  }
}

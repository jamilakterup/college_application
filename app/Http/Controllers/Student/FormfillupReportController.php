<?php

namespace App\Http\Controllers\Student;

use App\Exports\Msc1stFF\Msc1stFFExport;
use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Invoice;
use DB;
use Excel;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FormfillupReportController extends Controller
{
    public function masters_form_fillup(Request $request){

        $student_id = $request->get('id');
        $dept_name = $request->get('dept_name');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $exam_year = $request->get('exam_year');
        $student_type = $request->get('student_type');
        $formfillup_type = $request->get('formfillup_type');
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $title = 'Easy CollegeMate - College Management';

        $query =DB::table('form_fillup')->where('course', 'Masters')->orderBy('id', 'desc');
        
        if ($student_id != '') {
          $query->where('id', $student_id);
        }
        
        if ($dept_name != '') {
          $query->where('dept_name', $dept_name);
        }
        
        if ($current_level != '') {
          $query->where('level_study', $current_level);
        }

        if ($student_type != '') {
          $query->where('student_type', $student_type);
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

        if($current_level == 'Masters 1st Year'){
          $this->validate($request, ['session'=> 'required']);
        }
        
        $num_rows = $query->count();
        $total_amount = $query->sum('total_amount');
        
        $students = $query->paginate(Study::paginate());

        return view('BackEnd.student.report.masters.mscffmreport', compact('title', 'students','student_id','dept_name','current_level','session','exam_year','num_rows', 'from_date','to_date','total_amount', 'student_type', 'formfillup_type'));
    }


  public function generateMscFFReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");
    $student_id = $request->get('id');
    $dept_name = $request->get('dept_name');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $student_type = $request->get('student_type');
    $formfillup_type = $request->get('formfillup_type');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student Masters Formfillup Report|Dashboard';

    $query =DB::table('form_fillup')->where('course', 'Masters')->orderBy('id', 'desc');
    
    if ($student_id != '') {
      $query->where('id', $student_id);
    }
    
    if ($dept_name != '') {
      $query->where('dept_name', $dept_name);
    }
    
    if ($current_level != '') {
      $query->where('level_study', $current_level);
    }

    if ($student_type != '') {
          $query->where('student_type', $student_type);
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
    $query->orderBy("dept_name","ASC");
    
    $num_rows = $query->count();
    $total_amount = $query->sum('total_amount');
    $records = $query->get();

    if($current_level == 'Masters 1st Year'){
        if($request->type == 'report_excel')
            return $this->msc1stFFExcelReports($request, $records);
    }

    if($request->get('type') =='csv'){
        $data[] = ['Student ID','Name','Deptartment', 'Session', 'Total Amount', 'Payment Date', 'From Date', 'To Date'];
        
        foreach($records as $val){
            $data[] = [
              $val->id,$val->name,$val->dept_name,$val->session,$val->total_amount,$val->date, $from_date,$to_date
            ];
        }
        $filename = 'msc_formfillup_reports.csv';
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
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.mscffreport', compact('records', 'exam_year','session')));
    $mpdf->Output();
  }

  public function mscFFDeptReports(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");

    $student_id = $request->get('id');
    $dept_name = $request->get('dept_name');
    $level = $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query_adm =DB::table('form_fillup')->where('course', 'Masters')->orderBy('id', 'desc');
    
    if ($student_id != '') {
      $query_adm->where('id', $student_id);
    }

    if($dept_name != ''){
        $query_adm->where('dept_name', $dept_name);
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
      $from_date = date('Y-m-d',strtotime($request->from_date));
      $query_adm->where('date', '>=',$from_date);
    }

    if ($to_date != '') {
      $to_date = date('Y-m-d',strtotime($request->to_date));
      $query_adm->where('date', '<=',$to_date);
    }
    
    $amount_ff_lists = $query_adm->pluck('total_amount')->toArray();
    sort($amount_ff_lists);
    $amount_ff_groups = array_unique($amount_ff_lists);
    $amount_ff_groups = array_values($amount_ff_groups);

    $column = ['SI','Department Name','Total Number of Students','Session', 'From Date', 'To Date', 'Total Amount'];
    $data[] = array_merge($column, $amount_ff_groups);


    $query_dept = DB::table('departments');

    if($dept_name != ''){
      $query_dept->where('dept_name', $dept_name);
    }
    // check permission
    query_has_permissions($query_dept, ['dept_name']);

    $departments = $query_dept->get('dept_name');
    $i = 1;

        foreach($departments as $dept){
            
            $total_amount = 0;
            $admission_fee = 0;

            $query =DB::table('form_fillup')->where('course', 'Masters')->orderBy('id', 'desc');
    
            if ($student_id != '') {
              $query->where('id', $student_id);
            }
            
            $query->where('dept_name', $dept->dept_name);
            
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
              $from_date = date('Y-m-d',strtotime($from_date));
              $query->where('date', '>=',$from_date);
            }

            if ($to_date != '') {
              $to_date = date('Y-m-d',strtotime($to_date));
              $query->where('date', '<=',$to_date);
            }
            
              $total_amount = 0;
              $total_amount = $query->sum('total_amount');
              $records = $query->get();

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

              $values = [$i, $dept->dept_name,count($records),$session,$from_date, $to_date,$total_amount];

              $data[] = array_merge($values, $raw_data);

            $i++;
        }
        
        
        $filename = 'masters_formfillup_departmental_reports.csv';
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

  public function msc1stFFExcelReports($request, $data){
        return Excel::download(new Msc1stFFExport($request, $data), 'msc1st_ff_reports.xlsx');
  }
}

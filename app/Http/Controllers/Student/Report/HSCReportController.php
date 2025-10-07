<?php

namespace App\Http\Controllers\Student\Report;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Invoice;
use Illuminate\Http\Request;
use DB;
use Mpdf\Mpdf;
use Excel;
use App\Exports\HSCFF\HSCFFExport;

class HSCReportController extends Controller
{
    public function hsc_admission(Request $request){
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

        $query = Study::searchHscStudent($id, $ssc_roll, $groups,$gender, $current_level, $session);
        $student_rolls = $query->pluck('ssc_roll')->toArray();

        $total_amount = 0;
        $admission_fee = 0;

        $query_invoice = Invoice::where('type', 'hsc_admission')->where('status', 'Paid');
        if($session != '')    $query_invoice->where('admission_session', $session);
        $query_invoice->whereIn('roll', $student_rolls);

        if ($from_date != '') {
            $from_date = date('Y-m-d',strtotime($request->from_date));
            $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
        }

        if ($to_date != '') {
            $to_date = date('Y-m-d',strtotime($request->to_date));
            $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
        }

        $invoices = $query_invoice->orderByRaw("FIELD(pro_group , '$groups') DESC");
        $invoices = $query_invoice->get();

        if(count($invoices)){
            $total_amount = $query_invoice->sum('total_amount');
        }

        $num_rows = $query->count();
        $hscstudents = $query->paginate(Study::paginate());

        return view('BackEnd.student.report.hsc.admreport', compact('title', 'breadcrumb', 'hscstudents', 'current_level_lists','id', 'ssc_roll','groups','gender','current_level','session','num_rows', 'from_date','to_date','total_amount'));

    }

    public function generateHscAdmReport(Request $request){
        ini_set("pcre.backtrack_limit", "5000000");

        if($request->type == 'csv_dept_report'){
          return $this->hsc_csv_dept_report($request);
        }

        $groups = $request->get('groups');
        $gender = $request->get('gender');
        $current_level = $request->get('current_level');
        $session = $request->get('session');
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        
        $query =DB::table('student_info_hsc')->orderBy('id', 'asc');
        
        if ($session != '')     $query->where('session', $session);
        if($current_level != '')    $query->where('current_level', $current_level);
        if($groups != '')    $query->where('groups', $groups);
        if($gender != '')    $query->where('gender', $gender);
        // check permission
        query_has_permissions($query, ['groups', 'current_level', 'session']);
        
        $admissions = $query->get();

        if($request->get('type') =='csv'){
            $data[] = ['Student ID','Class Roll','Name','Father Name','Mother Name','Birth Date' ,'SSC Roll','Group','Gender', 'Contact No','Current Level','Session','Payment Date', 'Total Amount'];
            
            foreach($admissions as $val){
                $invoice = Invoice::where('roll', $val->ssc_roll)->where('admission_session',$val->session)->where('type', 'hsc_admission')->where('ssc_board', $val->ssc_board)->get();
                $amount = 0;
                if(count($invoice) > 0) $amount = $invoice->first()->total_amount;

                $data[] = [
                  $val->id,$val->class_roll,$val->name,$val->father_name,$val->mother_name,$val->birth_date,$val->ssc_roll,$val->groups,$val->gender,$val->contact_no,$val->current_level,$val->session,$val->payment_date,$amount
                ];
            }
            $filename = 'hsc_admission_reports.csv';
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
        $mpdf->WriteHTML(view('BackEnd.student.report.pdf.hscadmreport', compact('admissions','session')));
        $mpdf->Output();

    }

    public function hsc_csv_dept_report($request){
        $session = $request->session;
        $current_level = $request->current_level;
        $groups = $request->groups;
        $gender = $request->gender;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $faculty = DB::table('faculties');

        if($groups != ''){
          $faculty->where('faculty_name', $groups);
        }
        // check permission
        query_has_permissions($faculty, ['groups', 'level_study','session', 'exam_year']);

        $departments = $faculty->get('faculty_name');
        $i = 1;

        $data[] = ['SI','Group Name','Total Num of Students','Gender', 'HSC Level','Session','Admission Fee', 'Total Amount', 'From Date', 'To Date'];

        foreach($departments as $dept){
            
            $total_amount = 0;
            $admission_fee = 0;

            $query_adm =DB::table('student_info_hsc')->orderBy('id', 'asc');
            
            if ($session != '') $query_adm->where('session', $session);
            if($current_level != '')    $query_adm->where('current_level', $current_level);
            $query_adm->where('groups', $dept->faculty_name);
            if($gender != '')    $query_adm->where('gender', $gender);

            $admissions = $query_adm->groupBy('id')->get();
            $student_rolls = $query_adm->pluck('ssc_roll')->toArray();

            $query_invoice = Invoice::where('type', 'hsc_admission')->where('status', 'Paid');
            if($session != '')    $query_invoice->where('admission_session', $session);
            $query_invoice->whereIn('roll', $student_rolls);
            // if($dept != '')    $query_invoice->where('subject', $dept->groups);

            if ($from_date != '') {
                $from_date = date('Y-m-d',strtotime($request->from_date));
                $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), ">=", $from_date);
            }

            if ($to_date != '') {
                $to_date = date('Y-m-d',strtotime($request->to_date));
                $query_invoice->where(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), '<=', $to_date);
            }

            $invoices = $query_invoice->orderByRaw("FIELD(pro_group , '$dept->faculty_name') DESC");
            $invoices = $query_invoice->get();

            $total_amount = $query_invoice->sum('total_amount');
            if($total_amount < 1){
                continue;
            }

            $data[] = [$i, $dept->faculty_name,count($invoices),$gender,$current_level,$session,$total_amount,$from_date, $to_date];

            $i++;
        }
        
        
        $filename = 'hsc_admission_reports.csv';
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

  public function hsc_form_fillup(Request $request){
    $registraion_id  = $request->get('registraion_id');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $formfillup_type = $request->get('formfillup_type');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'student:Student HSC Formfillup Reports|Dashboard';

    $query =DB::table('form_fillup')->where('course', 'HSC')->orderBy('id', 'desc');
    
    if ($registraion_id  != '') {
      $query->where('id', $registraion_id );
    }
    
    if ($groups != '') {
      $query->where('groups', $groups);
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

    return view('BackEnd.student.report.hsc.hscffreport', compact('title', 'breadcrumb', 'students','registraion_id','groups','current_level','session','formfillup_type','exam_year','num_rows', 'from_date','to_date','total_amount'));
  }

  public function generateHSCFFReport(Request $request){
    ini_set("pcre.backtrack_limit", "5000000");
    $registraion_id = $request->get('registraion_id');
    $groups = $request->get('groups');
    $current_level = $request->get('current_level');
    $session = $request->get('session');
    $exam_year = $request->get('exam_year');
    $formfillup_type = $request->get('formfillup_type');
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    $query =DB::table('form_fillup')->where('course', 'HSC')->orderBy('id', 'desc');
    
    if ($registraion_id != '') {
      $query->where('id', $registraion_id);
    }
    
    if ($groups != '') {
      $query->where('groups', $groups);
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
        return Excel::download(new HSCFFExport($request, $records), 'hsc_ff_reports.xlsx');

    if($request->get('type') =='csv'){
        $data[] = ['Registration ID','Name','Groups', 'Session', 'Total Amount', 'Payment Date','Transaction ID', 'Registration Type', 'From Date', 'To Date'];
        
        foreach($records as $val){
            $data[] = [
              $val->id,$val->name,$val->groups,$val->session,$val->total_amount,$val->date,$val->transaction_id,$val->formfillup_type, $from_date,$to_date
            ];
        }
        $filename = 'hsc_formfillup_reports.csv';
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
    $mpdf->WriteHTML(view('BackEnd.student.report.pdf.hscffreport', compact('records', 'exam_year','session','current_level', 'from_date', 'to_date')));
    $mpdf->Output();
  }
}

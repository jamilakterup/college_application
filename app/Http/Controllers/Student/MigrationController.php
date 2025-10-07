<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\MigatedStudent;
use App\Models\MigrationList;
use App\Models\StudentInfoDegree;
use App\Models\StudentInfoHons;
use App\Models\StudentInfoMasters;
use App\Models\PayslipHeader;
use DB;
use IdRollGenerate;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function migration()
   {

      $title = 'Easy CollegeMate - College Management';
      $breadcrumb = 'students.migration:Student Migration';
      

      return view('BackEnd.student.migration.index')
          ->withTitle($title)
          ->withBreadcrumb($breadcrumb);            
    
  }

         public function migratedStudentList(Request $request)
        {

            $adm_roll = $request->get('adm_roll');
           $session = $request->get('session');
           $id = $request->get('id');  
           $course = $request->get('course');  

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';
           
           $migrated_students = MigatedStudent::paginate(Study::paginate());
           $session_lists = selective_multiple_session();
           $course_lists = student_course_list();

            return view('BackEnd.student.migration.list', compact('title', 'session_lists', 'migrated_students', 'breadcrumb', 'adm_roll', 'session', 'id', 'course_lists', 'course'));            
         
       }

        public function migratedStudentListSearch(Request $request)
        {

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';
          

           $adm_roll = $request->get('adm_roll');
           $session = $request->get('session');
           $id = $request->get('id'); 
           $course = $request->get('course');         

           $query = Study::regSearchMigrationStudent($id, $adm_roll, $session, $course);

           $migrated_students = $query->paginate(Study::paginate());
          
           $session_lists = selective_multiple_session();
           $course_lists = student_course_list();

            return view('BackEnd.student.migration.list', compact('title', 'session_lists', 'migrated_students', 'breadcrumb', 'adm_roll', 'session', 'id', 'course_lists', 'course'));        
         
       }

            public function migrationTable(Request $request)
        {

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';
          
           $adm_roll = $request->get('adm_roll');
           $course = $request->get('course');
                
           $query = MigrationList::where('is_registered',0);

           if($adm_roll != ''){
            $query->where('admission_roll', '=', $adm_roll);
           }

           if($course != ''){
            $query->where('course', $course);
           }

            $migration_list = $query->paginate(Study::paginate());

            return  view('BackEnd.student.migration.show',compact('title', 'migration_list', 'breadcrumb', 'adm_roll', 'course'));    
         
       }



        public function migrationStudentListUpload()
        {

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';
           $course = '';
          
           $migration_list = MigrationList::where('is_registered',0)->paginate(Study::paginate());
            $adm_roll = '';

            return view('BackEnd.student.migration.show', compact('title', 'migration_list', 'breadcrumb', 'adm_roll', 'course'));
         
       }

           public function migrationStudentSingleDelete(Request $request)
        {

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';

           $id=Study::filterInput('id', $request->get('id'));  
           $meritStudent = MigrationList::where('auto_id',$id);         
           $meritStudent->delete();

           $message = 'You have successfully Deleted';

         
          return redirect()->route('students.migration.table')
                        ->with('warning',$message);
       }

         public function migrationStudentEdit($id)
        {

           $title = 'Easy CollegeMate - College Management';
           $breadcrumb = 'students.migration:Student Migration|Student Migration List';
           $migration_student = MigrationList::find($id); 
           $faculty_list = selective_multiple_faculty();
           $dept_list = selective_multiple_subject();
           $session_lists = selective_multiple_session();

            
            return  view('BackEnd.student.migration.edit', compact('title', 'id', 'faculty_list', 'session_lists', 'dept_list', 'migration_student', 'breadcrumb'));        
         
       }

     public function migrationStudentEditComplete(Request $request)
        {

       $title = 'Easy CollegeMate - College Management';
       $breadcrumb = 'students.migration:Student Migration|Student Migration List';
           
       $id=$request->get('id');
           $admission_roll=$request->get('admission_roll');        
           $admitted_subject=$request->get('admitted_subject');
           $faculty=$request->get('faculty');
           $changed_subject=$request->get('changed_subject');
           $admission_session=$request->get('admission_session');
           

           $migration_student = MigrationList::find($id);

           $migration_student->admission_roll=$admission_roll;
           $migration_student->admitted_subject=$admitted_subject;
           $migration_student->faculty=$faculty;
           $migration_student->changed_subject=$changed_subject;
           $migration_student->admission_session=$admission_session;
           $migration_student->course=$request->course;

           $migration_student->save();

           $message = 'You have successfully Edited';

         
          return redirect()->route('students.migration.table')
                        ->with('info',$message);
       }

   public function migrationExe(Request $request)
        {

       $title = 'Easy CollegeMate - College Management';
       $breadcrumb = 'students.migration:Student Migration|Student Migration List';
       $migration_list = MigrationList::where('is_registered',0)->paginate(Study::paginate());

       foreach ($migration_list as  $value) 
       {
        if($value->course == 'honours'){
            $this->honours_exe($value);

        }else if($value->course == 'degree'){
            $this->degree_exe($value);
        }else if($value->course == 'masters'){
            $this->masters_exe($value);
        }

       }
        $message ="Migration Done Successfully";
         return redirect()->route('students.migration.list')
            ->with('success',$message);         
           
         
       }


 public function migrationTableCsvUpload(Request $request)
        {

       $title = 'Easy CollegeMate - College Management';
       $breadcrumb = 'students.migration:Student Migration|students.migration.table:Student Migration List|Migration Table';
       $course = $request->course;
       

         return  view('BackEnd.student.migration.uploadForm', compact('course'))
                    ->withTitle($title) 
                    ->withBreadcrumb($breadcrumb);             
           
         
       }

   public function formatDownload()
     {
      $download_path = ( public_path() . '/csv/migration_format.csv' );
     return response()->download($download_path);
     }

  public function migrationCsvUpload(Request $request)
    {

        $this->validate($request, [
            'migration_csv' => 'required'
        ]);
    
    if ($request->hasFile('migration_csv'))

   {
        $name = $request->file('migration_csv');
        $extension = $name->getClientOriginalExtension();
        
       if(strtolower($extension) == 'csv')   
  
        {

            function csv_to_array($filename='', $delimiter=',')
                    {
                        if(!file_exists($filename) || !is_readable($filename))
                            return FALSE;
                     
                        $header = NULL;
                        $data = array();
                        if (($handle = fopen($filename, 'r')) !== FALSE)
                        {
                            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                            {
                                if(!$header)
                                    $header = $row;
                                else
                                    $data[] = array_combine($header, $row);
                            }
                            fclose($handle);
                        }
                        return $data;
                    }

                    $csvFile = $request->file('migration_csv');
                 
                    $areas = csv_to_array($csvFile);
                   

                     DB::table('migration_list')->insert($areas);

                    
                         $message = 'You have successfully uploaded';
              return redirect()->route('students.migration.table')
            ->with('success',$message);
             }
                   


           
             
         $message = 'Format Not Match';
               


          }
        }

    public function honours_exe($value){
        $admission_roll= $value->admission_roll;
         $admission_session= $value->admission_session;
         $admitted_subject= $value->admitted_subject;
         $faculty= $value->faculty;
         $changed_subject= $value->changed_subject;

         $student_info=StudentInfoHons::where('admission_roll',$admission_roll)->where('session',$admission_session)->paginate(Study::paginate());

         if(count($student_info) > 0){
             foreach ($student_info as  $val) 
             {
              $previous_student_id=$val->id;
              $dept_name=$val->dept_name;  
              $current_level=$val->current_level; 
              $refference_id=$val->refference_id;
             }

             DB::beginTransaction();

             try {
              $prefix="honours_";
              $catagory="2";
              $student_id = IdRollGenerate::hons_id_generate($admission_session, $changed_subject,$prefix);
              $class_roll = IdRollGenerate::hons_roll_generate($student_id);
            

              $student_info= StudentInfoHons::find($previous_student_id);
              $student_info->dept_name=$changed_subject;
              $student_info->faculty_name=$faculty; 
              $student_info->class_roll=$class_roll;         
              $student_info->id=$student_id;
              $student_info->save();

              DB::table('hons_admitted_student')
                  ->where('auto_id', $refference_id)
                  ->update(array('faculty' => $faculty,'subject'=>$changed_subject));

              DB::table('migration_list')
                  ->where('admission_roll', $admission_roll)
                  ->where('admission_session', $admission_session)
                  ->update(array('is_registered' => 1));

                  $id_table_subject=$prefix.$changed_subject;
                DB::table('id_roll')
                  ->where('dept_name', $id_table_subject)
                  ->where('session', $admission_session)
                  ->increment('last_digit_used');

              $payslipheaders_admitted_subject = DB::table('payslipheaders')->where('session', $admission_session)->where('pro_group', 'honours')->where('subject','LIKE','%' . $admitted_subject .'%' )->where('type', 'admission')->where('level', 'Honours 1st Year')->get();

              $payslipheaders_changed_subject = DB::table('payslipheaders')->where('session', $admission_session)->where('pro_group', 'honours')->where('subject','LIKE','%' . $changed_subject .'%' )->where('type', 'admission')->where('level', 'Honours 1st Year')->get();

              if(count($payslipheaders_admitted_subject) > 0  && count($payslipheaders_changed_subject) > 0){
                  $header1 = $payslipheaders_admitted_subject->first();
                  $header2 = $payslipheaders_changed_subject->first();

                  $code = $header2->code;
                  $title = $header2->title;
                  $start_date = $header2->start_date;
                  $end_date = $header2->end_date;
                  $examyear = $header2->exam_year;

                  $admission_name = 'honours_migration_'.$admission_session.'_'.$examyear;

                  $amounts1 = DB::select("select * from payslipgenerators where payslipheader_id = $header1->id");
                  $previous_amount = 0;

                  foreach($amounts1 as $amount){
                    $previous_amount = $previous_amount + $amount->fees;
                  }

                  $amounts2 = DB::select("select * from payslipgenerators where payslipheader_id = $header2->id");
                  $present_amount = 0;
                  foreach($amounts2 as $amount){
                    $present_amount = $present_amount + $amount->fees;
                  }

                  $payment_diff = $present_amount - $previous_amount;

                  $payment_status = 'Pending';
                  if($payment_diff > 0){
                          $payment_status = 'Pending';

                          $already_exists = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'honours_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->get();

                        if (count($already_exists) < 1) {
                              
                          DB::table('invoices')->insert(
                            array(
                                'name'=>$student_info->name, 
                                'hsc_merit_id' => 0, 
                                'type'=>'honours_migration' ,
                                'roll' => $admission_roll,
                                'mobile' => $student_info->contact_no,
                                'ssc_board' => '',
                                'pro_group' => $student_info->faculty_name,
                                'subject' => $student_info->dept_name,
                                'level' => $current_level,
                                'passing_year' => $examyear,
                                'admission_session'=>$admission_session,
                                'slip_name'=>$title,
                                'slip_type'=>$code,
                                'total_amount'=>(int) $payment_diff,
                                'status'=>'Pending',
                                'date_start'=>$start_date, 
                                'date_end'=>$end_date, 
                                'father_name'=>'N/A', 
                                'institute_code'=> INS_CODE, 
                                'refference_id' => 0,
                                'payment_info_id' => 0
                                )
                          );
                        }else{

                          $invoice = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'honours_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->first();
                              
                        DB::table('invoices')->where('id', $invoice->id)->update(
                            array(
                                'name'=>$student_info->name, 
                                'hsc_merit_id' => 0, 
                                'type'=>'honours_migration',
                                'roll' => $admission_roll,
                                'mobile' => $student_info->contact_no,
                                'ssc_board' => '',
                                'pro_group' => $student_info->faculty_name,
                                'subject' => $student_info->dept_name,
                                'level' => $current_level,
                                'passing_year' => $examyear,
                                'admission_session'=>$admission_session,
                                'slip_name'=>$title,
                                'slip_type'=>$code,
                                'total_amount'=> (int) $payment_diff,
                                'status'=>'Pending',
                                'date_start'=>$start_date, 
                                'date_end'=>$end_date, 
                                'father_name'=>'N/A', 
                                'institute_code'=> INS_CODE, 
                                'refference_id' => 0,
                                'payment_info_id' => 0
                                )
                          );
                        }
                  }


              }

            if($payment_diff < 0) $payment_status = 'Refundable';
            if($payment_diff == 0) $payment_status = 'Paid';

              DB::table('migrated_student')->insert(
                    array('admission_roll' => $admission_roll,'current_level'=> 'Honours 1st Year','course'=> 'honours', 'previous_id' =>$previous_student_id,
                          'present_id'=>$student_id,'session'=>$admission_session,
                          'previous_subject'=>$admitted_subject,'present_subject'=>$changed_subject,'previous_paid_amount' => $previous_amount, 'present_paid_amount'=> $present_amount, 'payment_diff' => $payment_diff, 'payment_status'=> $payment_status)
                      );
             DB::commit();
             } catch (\Illuminate\Database\QueryException $e) {
              DB::rollback();
              return redirect()->back()->with('error',$e->errorInfo[2] );
             }
         }
    }

    public function degree_exe($value){
        $admission_roll= $value->admission_roll;
         $admission_session= $value->admission_session;
         $admitted_subject= $value->admitted_subject;
         $faculty= $value->faculty;
         $changed_subject= $value->changed_subject;



         $student_info=StudentInfoDegree::where('admission_roll',$admission_roll)->where('session',$admission_session)->paginate(Study::paginate());

         if(count($student_info) > 0){
             foreach ($student_info as  $val) 
             {
              $previous_student_id=$val->id;
              $groups=$val->groups;  
              $current_level=$val->current_level; 
              $refference_id=$val->refference_id;
             }

             $prefix="degree_";
             $student_id = IdRollGenerate::id_generate_deg($admission_session, $changed_subject,$prefix);
             $class_roll = IdRollGenerate::roll_generate_deg($student_id);
          

             $student_info= StudentInfoDegree::find($previous_student_id);
             $student_info->groups=$changed_subject;
             $student_info->class_roll=$class_roll;         
             $student_info->id=$student_id;
             $student_info->save();

             DB::table('deg_admitted_student')
                ->where('auto_id', $refference_id)
                ->update(array('faculty' => $changed_subject,'subject'=>$changed_subject));

             DB::table('migration_list')
                ->where('admission_roll', $admission_roll)
                ->where('course', 'degree')
                ->where('admission_session', $admission_session)
                ->update(array('is_registered' => 1));

                $id_table_subject=$prefix.$changed_subject;
              DB::table('id_roll')
                ->where('dept_name', $id_table_subject)
                ->where('session', $admission_session)
                ->increment('last_digit_used');

            $payslipheaders_admitted_subject = DB::table('payslipheaders')->where('session', $admission_session)->where('pro_group', 'degree')->where('subject','LIKE','%' . $admitted_subject . '%')->where('type', 'admission')->where('level', 'Degree 1st Year')->get();

            $payslipheaders_changed_subject = DB::table('payslipheaders')->where('session', $admission_session)->where('pro_group', 'degree')->where('subject','LIKE','%' . $changed_subject . '%')->where('type', 'admission')->where('level', 'Degree 1st Year')->get();

            if(count($payslipheaders_admitted_subject) > 0  && count($payslipheaders_changed_subject) > 0){
                $header1 = $payslipheaders_admitted_subject->first();
                $header2 = $payslipheaders_changed_subject->first();

                $code = $header2->code;
                $title = $header2->title;
                $start_date = $header2->start_date;
                $end_date = $header2->end_date;
                $examyear = $header2->exam_year;

                $admission_name = 'degree_migration_'.$admission_session.'_'.$examyear;

                $amounts1 = DB::select("select * from payslipgenerators where payslipheader_id = $header1->id");
                $previous_amount = 0;

                foreach($amounts1 as $amount){
                  $previous_amount = $previous_amount + $amount->fees;
                }

                $amounts2 = DB::select("select * from payslipgenerators where payslipheader_id = $header2->id");
                $present_amount = 0;
                foreach($amounts2 as $amount){
                  $present_amount = $present_amount + $amount->fees;
                }

                $payment_diff = $present_amount - $previous_amount;

                $payment_status = 'Pending';
                if($payment_diff > 0){
                        $payment_status = 'Pending';

                        $already_exists = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->get();

                      if (count($already_exists) < 1) {
                          $payment_info_id = DB::table('payment_info')->insertGetId(
                           array('name'=>$student_info->id, 'admission_name'=>$admission_name , 'roll' => $admission_roll, 'pro_group' => $student_info->groups,'admission_session'=> $admission_session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$payment_diff,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=>'mmc', 'exam_year' => $examyear)
                            );
                            
                      DB::table('invoices')->insert(
                          array(
                              'name'=>$student_info->name, 
                              'hsc_merit_id' => 0, 
                              'type'=>'degree_migration' ,
                              'roll' => $admission_roll,
                              'mobile' => $student_info->contact_no,
                              'ssc_board' => '',
                              'pro_group' => $student_info->groups,
                              'subject' => $student_info->groups,
                              'level' => $current_level,
                              'passing_year' => $examyear,
                              'admission_session'=>$admission_session,
                              'slip_name'=>$title,
                              'slip_type'=>$code,
                              'total_amount'=>(int) $payment_diff,
                              'status'=>'Pending',
                              'date_start'=>$start_date, 
                              'date_end'=>$end_date, 
                              'father_name'=>'N/A', 
                              'institute_code'=> institution_code, 
                              'refference_id' => 0,
                              'payment_info_id' => $payment_info_id
                              )
                        );
                      }else{

                        $invoice = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'degree_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->first();

                        $payment_info_id = $invoice->payment_info_id;

                        DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
                           array('name'=>$student_info->id, 'admission_name'=>$admission_name , 'roll' => $student_info->id, 'pro_group' => $student_info->groups,'admission_session'=> $admission_session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$payment_diff,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=> institution_code, 'exam_year' => $examyear)
                            );
                            
                      DB::table('invoices')->where('id', $invoice->id)->update(
                          array(
                              'name'=>$student_info->name, 
                              'hsc_merit_id' => 0, 
                              'type'=>'degree_migration',
                              'roll' => $admission_roll,
                              'mobile' => $student_info->contact_no,
                              'ssc_board' => '',
                              'pro_group' => $student_info->groups,
                              'subject' => $student_info->groups,
                              'level' => $current_level,
                              'passing_year' => $examyear,
                              'admission_session'=>$admission_session,
                              'slip_name'=>$title,
                              'slip_type'=>$code,
                              'total_amount'=> (int) $payment_diff,
                              'status'=>'Pending',
                              'date_start'=>$start_date, 
                              'date_end'=>$end_date, 
                              'father_name'=>'N/A', 
                              'institute_code'=> institution_code, 
                              'refference_id' => 0,
                              'payment_info_id' => $payment_info_id
                              )
                        );
                      }
                }


            }

        if($payment_diff < 0) $payment_status = 'Refundable';
        if($payment_diff == 0) $payment_status = 'Paid';

          DB::table('migrated_student')->insert(
                 array('admission_roll' => $admission_roll, 'previous_id' =>$previous_student_id,
                       'present_id'=>$student_id,'session'=>$admission_session,'course'=>'degree',
                       'previous_subject'=>$admitted_subject,'present_subject'=>$changed_subject,'previous_paid_amount' => $previous_amount, 'present_paid_amount'=> $present_amount, 'payment_diff' => $payment_diff, 'payment_status'=> $payment_status)
                  );
         }
    }

    public function masters_exe($value){
        $admission_roll= $value->admission_roll;
         $admission_session= $value->admission_session;
         $admitted_subject= $value->admitted_subject;
         $faculty= $value->faculty;
         $changed_subject= $value->changed_subject;
         $current_level= $value->current_level;
         $payment_diff = 0;
         $previous_amount = 0;
         $present_amount = 0;

         $student_info=StudentInfoMasters::where('admission_roll',$admission_roll)->where('current_level', $current_level)->where('session',$admission_session)->paginate(Study::paginate());

         if(count($student_info) > 0){
             foreach ($student_info as  $val) 
             {
              $previous_student_id=$val->id;
              $dept_name=$val->dept_name;  
              $current_level=$val->current_level; 
              $refference_id=$val->refference_id;
             }

             $prefix="masters_1_";
             $student_id = IdRollGenerate::id_generate_msc1st($admission_session,$changed_subject,$prefix);
             $class_roll = IdRollGenerate::roll_generate_msc1st($student_id);

            $payslipheaders_admitted_subject = PayslipHeader::where('session', $admission_session)->where('pro_group', 'masters')->where('subject','LIKE','%' . $admitted_subject. '%')->where('type', 'admission')->where('level',$current_level)->get();

            $payslipheaders_changed_subject = PayslipHeader::where('session', $admission_session)->where('pro_group', 'masters')->where('subject', 'LIKE','%' .$changed_subject. '%')->where('type', 'admission')->where('level', $current_level)->get();

            if(count($payslipheaders_admitted_subject) > 0  && count($payslipheaders_changed_subject) > 0){
                $header1 = $payslipheaders_admitted_subject->first();
                $header2 = $payslipheaders_changed_subject->first();

                $code = $header2->code;
                $title = $header2->title;
                $start_date = $header2->start_date;
                $end_date = $header2->end_date;
                $examyear = $header2->exam_year;

                $admission_name = 'masters_migration_'.$admission_session.'_'.$examyear;

                $amounts1 = DB::select("select * from payslipgenerators where payslipheader_id = $header1->id");
                $previous_amount = 0;

                foreach($amounts1 as $amount){
                  $previous_amount = $previous_amount + $amount->fees;
                }

                $amounts2 = DB::select("select * from payslipgenerators where payslipheader_id = $header2->id");
                $present_amount = 0;
                foreach($amounts2 as $amount){
                  $present_amount = $present_amount + $amount->fees;
                }

                $payment_diff = $present_amount - $previous_amount;

                $payment_status = 'Pending';
                if($payment_diff > 0){
                        $payment_status = 'Pending';

                        $already_exists = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->get();

                      if (count($already_exists) < 1) {
                          $payment_info_id = DB::table('payment_info')->insertGetId(
                           array('name'=>$student_info->id, 'admission_name'=>$admission_name , 'roll' => $admission_roll, 'pro_group' => $student_info->faculty_name.'_'.$student_info->dept_name,'admission_session'=> $admission_session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$payment_diff,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=> institution_code, 'exam_year' => $examyear)
                            );
                            
                      DB::table('invoices')->insert(
                          array(
                              'name'=>$student_info->name, 
                              'hsc_merit_id' => 0, 
                              'type'=>'masters_migration' ,
                              'roll' => $admission_roll,
                              'mobile' => $student_info->contact_no,
                              'ssc_board' => '',
                              'pro_group' => $student_info->faculty_name,
                              'subject' => $student_info->dept_name,
                              'level' => $current_level,
                              'passing_year' => $examyear,
                              'admission_session'=>$admission_session,
                              'slip_name'=>$title,
                              'slip_type'=>$code,
                              'total_amount'=>(int) $payment_diff,
                              'status'=>'Pending',
                              'date_start'=>$start_date, 
                              'date_end'=>$end_date, 
                              'father_name'=>'N/A', 
                              'institute_code'=> institution_code, 
                              'refference_id' => 0,
                              'payment_info_id' => $payment_info_id
                              )
                        );
                      }else{

                        $invoice = DB::table('invoices')->where('roll', $admission_roll)->where('admission_session', $admission_session)->where('type', 'masters_migration')->where('date_start', '<=', $start_date)->where('status', 'Pending')->first();

                        $payment_info_id = $invoice->payment_info_id;

                        DB::table('payment_info')->where('id', $invoice->payment_info_id)->update(
                           array('name'=>$student_info->id, 'admission_name'=>$admission_name , 'roll' => $student_info->id, 'pro_group' => $student_info->faculty_name.'_'.$student_info->dept_name,'admission_session'=> $admission_session,'slip_name'=>$title,'slip_type'=>$code,'total_amount'=>$payment_diff,'status'=>'Pending','date_start'=>$start_date, 'date_end'=>$end_date, 'father_name'=>'', 'institute_code'=> institution_code, 'exam_year' => $examyear)
                            );
                            
                      DB::table('invoices')->where('id', $invoice->id)->update(
                          array(
                              'name'=>$student_info->name, 
                              'hsc_merit_id' => 0, 
                              'type'=>'masters_migration',
                              'roll' => $admission_roll,
                              'mobile' => $student_info->contact_no,
                              'ssc_board' => '',
                              'pro_group' => $student_info->faculty_name,
                              'subject' => $student_info->dept_name,
                              'level' => $current_level,
                              'passing_year' => $examyear,
                              'admission_session'=>$admission_session,
                              'slip_name'=>$title,
                              'slip_type'=>$code,
                              'total_amount'=> (int) $payment_diff,
                              'status'=>'Pending',
                              'date_start'=>$start_date, 
                              'date_end'=>$end_date, 
                              'father_name'=>'N/A', 
                              'institute_code'=> institution_code, 
                              'refference_id' => 0,
                              'payment_info_id' => $payment_info_id
                              )
                        );
                      }
                }


            }

        if($payment_diff < 0) $payment_status = 'Refundable';
        if($payment_diff == 0) $payment_status = '';

        $student_info= StudentInfoMasters::find($previous_student_id);
         $student_info->dept_name=$changed_subject;
         $student_info->faculty_name=$faculty; 
         $student_info->class_roll=$class_roll;         
         $student_info->id=$student_id;
         $student_info->save();

         AdmissionStudent::where('admission_roll', $admission_roll)->where('current_level', $current_level)->where('session', $admission_session)->update([
            'is_migrated' => 1
         ]);

         DB::table('masters_admitted_student')
            ->where('auto_id', $refference_id)
            ->update(array('to_faculty' => $faculty,'to_subject'=>$changed_subject));

         DB::table('migration_list')
            ->where('admission_roll', $admission_roll)
            ->where('course', 'masters')
            ->where('admission_session', $admission_session)
            ->update(array('is_registered' => 1));

            $id_table_subject=$prefix.$changed_subject;
          DB::table('id_roll')
            ->where('dept_name', $id_table_subject)
            ->where('session', $admission_session)
            ->increment('last_digit_used');

          DB::table('migrated_student')->insert(
                 array('admission_roll' => $admission_roll, 'previous_id' =>$previous_student_id,
                       'present_id'=>$student_id,'session'=>$admission_session,'current_level'=> $current_level,
                       'previous_subject'=>$admitted_subject,'present_subject'=>$changed_subject,'course'=> 'masters','previous_paid_amount' => $previous_amount, 'present_paid_amount'=> $present_amount, 'payment_diff' => $payment_diff, 'payment_status'=> $payment_status)
                  );
         }
    }
}

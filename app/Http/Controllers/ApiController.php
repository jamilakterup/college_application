<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Exam;
use App\Models\Invoice;
use App\Models\Subject;
use App\Models\ClassExam;
use App\Models\DistrictThana;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StudentInfoHsc;
use App\Models\StudentSubInfo;

class ApiController extends Controller
{

    public function getAjaxModal(Request $request){
        $data = $request->all();
        $html = view('common.ajax_modal', \compact('data'))->render();
        return response()->json([
            'html' => $html
        ],Response::HTTP_OK);
    }

    public function get_deptartment_options($faculty){
        $faculty_id = DB::table('faculties')->where('faculty_name', $faculty)->first()->id;
        $query = DB::table('departments')->where('faculty_id', $faculty_id);
        if (check_auth_user()) {
            query_has_permissions($query, ['department']);
        }
        $data = $query->select('dept_name as id', 'dept_name as name')->get();
        return response()->json(['data' => @$data], 200);
    }

    public function get_district_options($district){
        $data = create_option('district_thana','thana','thana',null,['district=' => addslashes($district)]);
        return response()->json(['data' => @$data], 200);
    }

    public function getUpazila($district)
    {
        $options = DistrictThana::where('district_bn', $district)
                            ->pluck('upazila_bn', 'upazila_bn')
                            ->toArray();
        return response()->json($options);
    }

    public function hscGroupChange(Request $request) {
        $group = $request->group;
        $course = $request->course;
        return view('BackEnd.admin.new_student.hsc_group_change', \compact('group', 'course'));
    }

    public function getExamOptions($class_id){
        $examOptions = Exam::whereHas('classexams', function($q) use ($class_id){
            $q->where('classe_id', $class_id);
        })->pluck('name', 'id')->toArray();

        $html = "<option>--Select Exam--</option>";
        foreach($examOptions as $key =>  $value){
            $html .= "<option value='{$key}'>{$value}</option>";
        }
        
        return \response()->json([
            'status' => 'success',
            'html' => $html
        ]);

    }


    public function get_hsc_subject_data(){
        $session = '2019-2020';
        $current_level = 'HSC 1st Year';

        $subjects = Subject::all();
        $header = ['student_id', 'name', 'contact_no', 'groups'];

        foreach ($subjects as $subject) {

            $sub_infos = StudentSubInfo::join('student_info_hsc','student_info_hsc.id','=','student_subject_info.student_id')->where('student_subject_info.session', $session)->where('student_subject_info.current_level', $current_level)
            ->selectRaw('student_info_hsc.id as student_id,student_info_hsc.name,student_info_hsc.contact_no,student_info_hsc.groups,student_subject_info.sub1_id,student_subject_info.sub2_id,student_subject_info.sub3_id,student_subject_info.sub4_id,student_subject_info.sub5_id,student_subject_info.sub6_id,student_subject_info.fourth_id')
            ->orderBy('student_info_hsc.groups', 'asc')
            ->orderBy('student_info_hsc.id', 'asc')
            ->get();

            $data = [];

            if (count($sub_infos) > 0) {
                foreach ($sub_infos as $info) {

                    if ($info->sub1_id == $subject->id){
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->sub2_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->sub3_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->sub4_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->sub5_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->sub6_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }

                    if ($info->fourth_id == $subject->id) {
                        $data[] = [$info->student_id, $info->name,$info->contact_no, $info->groups];
                    }
                }
                // return $data;
                array_unshift($data, $header);

                $file_name = 'exported_files/'.$subject->name.'_'.$subject->code;


                if(count($data) > 1){
                    $handle = fopen($file_name.'.csv', 'w');
                    foreach ($data as $row) {
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                }

            }

        }
    }

    public function manage_data(){
        // $config = DB::table('hons_online_adm_config')->where('open', 1)->where('type', 'application')->where('current_level', 'Honours 1st Year')->first();
        // $invoices = Invoice::where('admission_session', $config->session)->where('type', 'honours_application')->where('status', 'Paid')->where('total_amount', 320)->get();

        // $invoice_rolls = Invoice::where('admission_session', $config->session)->where('type', 'honours_application')->where('status', 'Paid')->pluck('roll')->toArray();

        // $admitted_students = DB::table('hons_admitted_student')->where('session', $config->session)->whereIn('admission_roll', $invoice_rolls)->get();

        // foreach ($admitted_students as $admitted_student) {
        //         DB::table('hons_student_applications')->insert(
        //             array('name'=>$admitted_student->name, 'current_level'=>'Honours 1st Year', 'father_name'=>$admitted_student->father_name, 'mother_name'=>$admitted_student->mother_name, 'birth_date'=>$admitted_student->birth_date, 'gender'=>$admitted_student->gender, 'permanent_village'=>$admitted_student->permanent_village, 'present_village'=>$admitted_student->present_village, 'permanent_po'=>$admitted_student->permanent_po, 'present_po'=>$admitted_student->present_po, 'permanent_ps'=>$admitted_student->permanent_ps, 'present_ps'=>$admitted_student->present_ps, 'permanent_dist'=>$admitted_student->permanent_dist, 'present_dist'=>$admitted_student->present_dist, 'contact_no'=>$admitted_student->contact_no, 'religion'=>$admitted_student->religion, 'guardian'=>$admitted_student->guardian_name, 'image'=>$admitted_student->photo, 'refference_id'=>$admitted_student->auto_id, 'admission_roll'=>$admitted_student->admission_roll , 'session'=>$admitted_student->session,'ssc_reg'=>$admitted_student->ssc_reg,'hsc_reg'=>$admitted_student->hsc_reg)
        //         );
        // }

        $admission_session = '2019-2020';

        $invoices = Invoice::where('admission_session', $admission_session)->where('type', 'masters_2nd_application')->where('status', 'Paid')->get();

        foreach($invoices as $invoice){

            $invoice_id = $invoice->id;
            $admission_roll= $invoice->roll;

            $payment_status = $invoice->status;
            $total_amount = $invoice->total_amount;

            $admitted_students = DB::table('masters_application_admitted_student')->where('session', $admission_session)->where('application_invoice_id', $invoice->id)->where('admission_roll',$admission_roll)->get();

            $student_applications = DB::table('masters_student_applications')->where('current_level', 'Masters 2nd Year')->where('session', $admission_session)->where('admission_roll', $admission_roll)->get();

            if(count($admitted_students) > 0 && count($student_applications) < 1 && $invoice->status == 'Paid'){
                $admitted_student = $admitted_students->first();
                DB::table('masters_student_applications')->insert(
                    array('name'=>$admitted_student->name, 'current_level'=>'Masters 2nd Year', 'father_name'=>$admitted_student->father_name,'dept_name'=>$admitted_student->dept_name, 'mother_name'=>$admitted_student->mother_name, 'birth_date'=>$admitted_student->birth_date, 'gender'=>$admitted_student->gender, 'permanent_village'=>$admitted_student->permanent_village, 'present_village'=>$admitted_student->present_village, 'permanent_po'=>$admitted_student->permanent_po, 'present_po'=>$admitted_student->present_po, 'permanent_ps'=>$admitted_student->permanent_ps, 'present_ps'=>$admitted_student->present_ps, 'permanent_dist'=>$admitted_student->permanent_dist, 'present_dist'=>$admitted_student->present_dist, 'contact_no'=>$admitted_student->contact_no, 'religion'=>$admitted_student->religion, 'guardian'=>$admitted_student->guardian_name, 'image'=>$admitted_student->photo, 'refference_id'=>$admitted_student->auto_id, 'admission_roll'=>$admitted_student->admission_roll , 'session'=>$admitted_student->session,'ssc_reg'=>$admitted_student->ssc_reg,'hsc_reg'=>$admitted_student->hsc_reg,'exam_year'=> $invoice->passing_year, 'total_amount'=>$invoice->total_amount, 'date' =>date('Y-m-d', strtotime($invoice->updated_at)), 'admission_form'=> $admitted_student->admission_form,'hsc_transcript'=>$admitted_student->hsc_transcript)
                );
            }
        }


        return 'ok';
    }
    
    public function manage_ff_data(){
        // $students = DB::table('student_info_hons_formfillup_data')->where('registration_type','irregular')->get();
        
        // foreach($students as $stu){
        //     $subject = DB::table('formfillup_subjects')->where('subject', $stu->dept_name)->first();
        //     if($stu->subject != ''){
        //         $sub_array =  explode(",",$stu->subject);
                
        //         if(count($sub_array) == $subject->total_subject){
        //             $update_data = DB::table('student_info_hons_formfillup_data')->where('auto_id', $stu->auto_id)->update([
        //                 'registration_type' => 'irregular_regular'
        //                 ]);
        //         }
                
        //     }
            
        // }
        
        // $students = DB::table('student_info_hons_formfillup_data')->where('registration_id','special')->get();
        
        // foreach($students as $stu){
        //     $subject = DB::table('formfillup_subjects')->where('subject', $stu->dept_name)->first();
        //     if($stu->subject != ''){
        //         $sub_array =  explode(",",$stu->subject);
                
        //         if(count($sub_array) < 2){
        //             $update_data = DB::table('invoices')->where('roll', $stu->id)->where('admission_session', $stu->session)->where('type', 'honours_form_fillup')->where('level', $stu->current_level)->update([
        //                 'total_amount' => 260
        //                 ]);
        //         }else{
        //             $update_data = DB::table('invoices')->where('roll', $stu->id)->where('admission_session', $stu->session)->where('type', 'honours_form_fillup')->where('level', $stu->current_level)->update([
        //                 'total_amount' => 360
        //                 ]);
        //         }
                
        //     }
            
        // }
        
        $students = DB::table('hons_formfillup_data')->get();
        
        foreach($students as $stu){
              
                    $update_data = DB::table('invoices')->where('roll', $stu->registration_id)->where('admission_session', $stu->session)->where('type', 'honours_form_fillup')->where('level', $stu->current_level)->update([
                        'total_amount' => $stu->total_amount
                        ]);
            
        }
        // return $students;
    }


    public function masters_ff_total_sub_assign(){
        set_time_limit(0);

        $current_level = 'Masters 1st Year';

        if($current_level == 'Masters 1st Year'){
            return $this->generate_msc_1st_ff_student();
        }
    }

    public function generate_msc_1st_ff_student(){
        $current_level = 'Masters 1st Year';
        $session = '2018-2019';
        $config = Configurations()::where('details->session', $session)->where('details->current_level', $current_level)->where('details->type', 'formfillup')->get();

        if(count($config) < 1){
            return 'Please Setup Configurations';
        }

        $config_details = json_decode($config->first()->details);

        $general_min_sub = $config_details->general->min_length;
        $special_min_sub = $config_details->special->min_length;


        $ff_students = DB::table('student_info_masters_formfillup')->where('current_level', $current_level)->where('registration_type','regular')->groupBy('dept_name')->get();

        foreach($ff_students as $student){
            if($student->registration_type == 'regular'){

                $ff_subjects = DB::table('formfillup_subjects')->where('current_level', $student->current_level)->where('session', $student->session)->where("subject", $student->dept_name)->get();

                if(count($ff_subjects) < 1){
                    $number_of_papers = count(filter_empty_array(explode(',',$student->papers)));
                    DB::table('formfillup_subjects')->insert([
                        'current_level' => $student->current_level,
                        'session' => $student->session,
                        'subject' => $student->dept_name,
                        'min_special_length' => $special_min_sub,
                        'min_general_length' => $general_min_sub,
                        'total_papers' => $number_of_papers
                    ]);
                }
            }
        }

        $students = DB::table('student_info_masters_formfillup')->where('current_level', $current_level)->get();

        foreach($students as $student){
            $number_of_papers = count(filter_empty_array(explode(',',$student->papers)));
            DB::table('student_info_masters_formfillup')->where('id', $student->id)->update(['total_papers'=>$number_of_papers]);
        }

        $ff_subjects = DB::table('formfillup_subjects')->where('current_level',$current_level)->where('session',$session)->get();

        foreach ($ff_subjects as $subject) {
            $students = DB::table('student_info_masters_formfillup')->where('dept_name', $subject->subject)->get();

            foreach($students as $student){

                // if paytype selectable
                if($student->registration_type == 'improvement'){
                    DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['selectable'=> 1]);
                }

                if($student->registration_type == 'regular'){
                    DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['pay_type'=> 'general']);
                }

                if(($student->student_type =='special' && $student->registration_type != 'regular') && ($student->student_type =='special' && $student->registration_type != 'irregular')){
                    if($student->total_papers <= $special_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['selectable'=> 1]);
                    }
                }

                if(($student->student_type =='general' && $student->registration_type != 'regular') && ($student->student_type =='general' && $student->registration_type != 'irregular')){
                    if($student->total_papers <= $general_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['selectable'=> 1]);
                    }
                }
                // end paytype selectable

                // start pay_type[general,paper]
                if($student->student_type == 'general' && $student->registration_type != 'regular'){
                    if($student->total_papers <= $general_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['pay_type'=> 'paper']);
                    }
                }

                if($student->student_type == 'special' && $student->registration_type != 'regular'){
                    if($student->total_papers <= $special_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['pay_type'=> 'paper']);
                    }
                }

                if($student->student_type == 'general' && $student->registration_type != 'regular'){
                    if($student->total_papers > $general_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['pay_type'=> 'general']);
                    }
                }

                if($student->student_type == 'special' && $student->registration_type != 'regular'){
                    if($student->total_papers > $special_min_sub){
                        DB::table('student_info_masters_formfillup')->where('id', $student->id)->where('current_level',$current_level)->update(['pay_type'=> 'general']);
                    }
                }
            }

        }

        return 'Proccessing Completed!';
    }

    public function generate_deg_ff_student(){
        $current_level = 'Degree 3rd Year';
        $session = '2017-2018';
        $config = Configurations()::where('details->session', $session)->where('details->current_level', $current_level)->where('details->type', 'formfillup')->get();

        if(count($config) < 1){
            return 'Please Setup Configurations';
        }

        $config_details = json_decode($config->first()->details);

        $general_min_sub = $config_details->general->min_length;

        $students = DB::table('student_info_degree_formfillup')->where('current_level', $current_level)->get();

        foreach($students as $student){
            $number_of_papers = count(filter_empty_array(explode(',',$student->papers)));
            DB::table('student_info_degree_formfillup')->where('auto_id', $student->auto_id)->update(['total_papers'=>$number_of_papers]);
        }

        foreach($students as $student){
            if($student->total_papers > $general_min_sub){
                DB::table('student_info_degree_formfillup')->where('auto_id', $student->auto_id)->update(['pay_type'=>'general']);
            }else{
                DB::table('student_info_degree_formfillup')->where('auto_id', $student->auto_id)->update(['pay_type'=>'paper']);
            }
        }
        return 'Proccessing Completed!';
    }

    public function generate_hons_ff_student(){
        $current_level = 'Honours 1st Year';
        $session = '2020-2021';
        $config = Configurations()::where('details->session', $session)->where('details->current_level', $current_level)->where('details->type', 'formfillup')->get();

        if(count($config) < 1){
            return 'Please Setup Configurations';
        }

        $config_details = json_decode($config->first()->details);

        $general_min_sub = $config_details->general->min_length;

        $students = DB::table('student_info_hons_formfillup')->where('current_level', $current_level)->get();

        foreach($students as $student){
            $number_of_papers = count(filter_empty_array(explode(',',$student->papers)));
            DB::table('student_info_hons_formfillup')->where('auto_id', $student->auto_id)->update(['total_papers'=>$number_of_papers]);
        }

        return 'Proccessing Completed!';
    }

    public function imagedownloadOld(Request $request)
    {
        $session = $request->session;
        $course = $request->course;
        if(empty($course)){
            $course = 'hsc';
        }

        if ($session) {
            // Create a temporary directory to store the images
            $tempDir = storage_path('app/temp_images');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $students = StudentInfoHsc::where('session', $session)->get();

            foreach ($students as $student) {
                try {
                    $imagePath = public_path("upload/college/{$course}/" . $student->session . '/' . $student->image);
                    if (\File::exists($imagePath)) {
                        $imageName = $student->id . '.jpg';
                        $newImagePath = $tempDir . '/' . $imageName;

                        \File::copy($imagePath, $newImagePath);
                    }
                } catch (Exception $e) {
                    // Handle any exceptions that may occur during file copying.
                    continue;
                }
            }

            // Create a unique zip file name
            $zipFileName = 'images_' .$course.'_'. $session . '.zip';
            $zipFilePath = public_path("download/{$course}/" . $zipFileName);

            // Create a ZipArchive
            $zip = new \ZipArchive;
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                // Add the images to the zip file
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempDir));
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        continue;
                    }
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
                $zip->close();

                // Delete the temporary directory
                \File::deleteDirectory($tempDir);

                // Return the zip file as a download and delete it after download
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                // Log the failure
                error_log('Zip creation failed.');
            }

        } else {
            return '<h1 style="color:red; text-align:center;">Please add a query like ' . url('/') . '/imagedownload?course=hsc&session=' . date('Y') . '-' . (date('Y') + 1) . '</h1>';
        }

    }

    public function subjectassigntoiinfo()
    {
        $this->validate(request(), [
            'admission_session' => 'required',
        ]);

        $admission_session = request('admission_session');

        $students = DB::table('hsc_admitted_students')
            // ->join('tbl_temp', 'tbl_temp.ref_id', '=', 'hsc_admitted_students.ssc_roll')
            ->where('admission_session', $admission_session)
            ->get();

        foreach($students as $stu){
            // $this->updateSelectiveFieldUsingPhp($stu->auto_id);
        }
        // return 'ok';

        // $students = DB::table('hsc_admitted_students')
        // ->where('admission_session', $admission_session)
        // ->whereIn('auto_id', function ($query) {
        //     $query->select('refference_id')
        //         ->from('student_info_hsc');
        //         // ->whereIn('id', [
        //         //     20241001, 20241002, 20241014, 20241003, 20241004, 20241005, 20241006, 20241007,
        //         //     20241008, 20241009, 20241020, 20241012, 20241011, 20241013, 20241015, 20241016,
        //         //     20241017, 20241018, 20241019, 20241021, 20241022, 20241023, 20241024, 20241025,
        //         //     20241026, 20241027, 20241028, 20241029, 20241030, 20241031, 20241032, 20241033,
        //         //     20241034, 20241035, 20241036, 20241037, 20241038, 20241039, 20241040
        //         // ]);
        // })
        // ->get();

        if ($students->isEmpty()) {
            dd('No students found for the given admission session');
        }

        foreach ($students as $student) {
            $hsc_group = $student->hsc_group;
            $courses = DB::select("SELECT * FROM course_hsc_new WHERE `groups` = ?", [strtolower($hsc_group)]);

            $cods = [];
            foreach ($courses as $course) {
                $subjects = explode(",", $course->subjects);
                $codes = explode(",", $course->codes);
                foreach ($subjects as $key => $subject) {
                    $cods[$codes[$key]] = $subject;
                }
            }

            $compulsory_string = $this->formatSubjects($cods, explode(",", $student->compulsory ?? ''));
            $selective_string = $this->formatSubjects($cods, explode(",", $student->selective ?? ''));
            $optional_string = $this->formatSubjects($cods, explode(",", $student->optional ?? ''));

            // Combine all subject strings into one
            $all_string = implode(",", array_filter([$compulsory_string, $selective_string, $optional_string]));
            // Begin transaction for database update
            DB::beginTransaction();
            try {
                // Update the student's subject information in the database
                DB::table('student_info_hsc')
                    ->where('refference_id', $student->auto_id)
                    ->where('session', $student->admission_session)
                    ->update([
                        'hsc_subjects_info' => $all_string,
                    ]);

                // Commit the transaction
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                // Log the error and continue with the next student
                \Log::error('Error updating student info for auto_id: ' . $student->auto_id . ' - ' . $e->getMessage());
                continue; // Skip to the next student
            }
        }

        // Redirect with success message
        return 'ok';
    }

    public function formatSubjects($cods, $subjects)
    {
        $formatted = '';
        foreach ($subjects as $value) {
            if (isset($cods[$value])) { // Ensure the code exists in the mapping
                $formatted .= $cods[$value] . "($value),";
            }
        }
        return rtrim($formatted, ",");
    }

}

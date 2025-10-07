<?php

namespace App\Http\Controllers\Student\Attendance;

use App\AttendanceSetting;
use App\StudentAttendance;
use Illuminate\Http\Request;
use App\Models\StudentInfoHsc;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isNull;

class StudentAttendanceController extends Controller
{
    public function index(){
        return view('BackEnd.student.attendance.list');
    }

    public function datasource(Request $request){
        $records = StudentAttendance::query();

        return DataTables::of($records)
            ->editColumn('entry_time', function($records){
                if($records->entry_time)
                    return date('h:i A',strtotime($records->entry_time));
            })
            ->editColumn('out_time', function($records){
                if($records->out_time)
                    return date('h:i A',strtotime($records->out_time));
            })

            ->filter(function ($query) use ($request) {

                if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                    $query->where('student_id','LIKE',"%".$request->get('student_id')."%");
                }

                if ($request->has('student_name') && ! is_null($request->get('student_name'))) {
                    $query->where('student_name','LIKE',"%".$request->get('student_name')."%");
                }

                if ($request->has('start_date') && ! is_null($request->get('start_date'))) {
                    $query->where('attendance_date','>=', $request->get('start_date'));
                }

                if ($request->has('end_date') && ! is_null($request->get('end_date'))) {
                    $query->where('attendance_date', '<=', $request->get('end_date'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }

                if ($request->has('attendance_status') && ! is_null($request->get('attendance_status'))) {
                    $query->where('attendance_status', $request->get('attendance_status'));
                }

            })

            ->setRowAttr([
                'data-row-id' => function($records) {
                    return $records->id;
                }
            ])
            ->rawColumns(['actions'])
            // ->escapeColumns([])
            ->make(true);
    }

    public function sentAbsentStudentSMS(){
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set('memory_limit', '256M');

        $setting = AttendanceSetting::first();

        $sent_from = request()->get('sent_from');

        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        if($sent_from != 'system'){
            $lastAbsentDate = date('Y-m-d',strtotime($setting->last_absent_date));

            if($lastAbsentDate == $currentDate){
                return 'Already Sent';
            }
        }

        $absentTime = date('H:i:s', strtotime($setting->college_absent_time));
        if($absentTime > $currentTime)
            return 'not started';

        $attendanceSessions = json_decode($setting->attendance_student_session, true);

        $holiday = DB::table('holidays')->where('date',date('Y-m-d'))->first();
        if(!is_null($holiday))
            return 'its hoilday';

        $absent_time_after = date("H:i:s",strtotime($setting->college_absent_time));
        $currentTime=date('H:i:s');
        $dateNow = date('Y-m-d');
        $timeNow = date('H:i:s');

        $sessions = StudentAttendance::where('attendance_date', date('Y-m-d'))->whereIn('session', $attendanceSessions)->groupBy('session')->pluck('session')->toArray();

        $present_student_ids = StudentAttendance::where('attendance_date', date('Y-m-d'))->whereIn('session', $sessions)->groupBy('student_id')->pluck('student_id')->toArray();
        
        $absent_students = StudentInfoHsc::whereIn('session', $sessions)->whereNotIn('id', $present_student_ids)->where('status', 'active')->get();

        if(strtotime($currentTime) > strtotime($absent_time_after)){
            $data = [];
            foreach($absent_students as $student){
                if($student->admitted_student->exists()){
                    $admitted_student = $student->admitted_student;
                    $student_name = $admitted_student->bangla_name != '' ? $admitted_student->bangla_name: $admitted_student->name;
                    try {
                        $msg = $student_name.' আজ কলেজে উপস্থিত হয়নি.'.config('settings.college_short_name_bn');
                        $attendance = new StudentAttendance();
                        $attendance->attendance_date = $dateNow;
                        $attendance->class_roll = $student->class_roll;
                        $attendance->attendance_status = 'Absent';
                        $attendance->student_name = $student->name;
                        $attendance->session = $student->session;
                        $attendance->student_id = $student->id;
                        $attendance->is_present = 1;
                        $attendance->contact_no = $admitted_student->guardian_phone;
                        $attendance->sms_status = 0;
                        $attendance->sms_body = $msg;
                        $attendance->save();

                        $data[] = ['id' => $attendance->id, 'msg'=> $msg, 'contact_no' => $attendance->contact_no];
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $setting->last_absent_date = $currentDate;
            $setting->save();

            if($setting->active_absent_sms){

                $absent_students = StudentAttendance::where('attendance_date', date('Y-m-d'))->whereIn('session', $attendanceSessions)->where('attendance_status', 'Absent')->where('sms_status', 0)->select('student_id', 'sms_body', 'contact_no')->get();

                foreach($absent_students as $attendance){
                    if($attendance->contact_no != ''){
                        $response_sms = @sendSms($attendance->sms_body,$attendance->contact_no);
                        // if(isset($response_sms['code']) && $response_sms['code'] == 400 && $response_sms['status'] == 1){
                        // }
                        $attendance->sms_status = 1;
                        $attendance->save();

                        $log = new SmsLog;
                        $log->student_id = $attendance->student_id;
                        $log->student_name = $attendance->student_name;
                        $log->contact_no = $attendance->contact_no;
                        $log->sms_body = $attendance->sms_body;
                        $log->sms_date = date('Y-m-d');
                        $log->attendance_date = $attendance->attendance_date;
                        $log->sms_type = 'student_attendance';
                        $log->sms_status = 1;
                        $log->save();
                    }
                }
            }

        }

        return \redirect()->back()->with('info', 'Operation Successful');
    }

    public function sentSMSView(){
        return view('BackEnd.student.attendance.sentSmsView');
    }

    public function sentSMSPost(Request $request){
        $this->validate($request, [
            'session' => 'required',
            'year_month' => 'required',
            'attendance_percent'=> 'required|numeric',
            'message'=> 'required'
        ]);

        $requestYearMonth = date('Y-m', strtotime($request->year_month));
        $requestLastDay = date('t', strtotime($request->year_month));
        $currentYearMonth = date("Y-m");
        $currentLastDay = date("t");
        $session = $request->session;
        $attendance_percent = $request->attendance_percent;
        
        $fromDate = date('Y-m-01', strtotime($request->year_month));
        $toDate = date('Y-m-t', strtotime($request->year_month));
        $sms_body = $request->message;
        
        if(($requestYearMonth == $currentYearMonth) && ($requestLastDay != $currentLastDay)){
            return \redirect()->back()->with('error', 'You Cannot Send SMS Before Date -'.$currentYearMonth.'-'.$currentLastDay);
        }else{
            $attendances = StudentAttendance::whereBetween('attendance_date', [$fromDate, $toDate])
                ->where('session', $session)
                ->get();

            if (count($attendances) < 1) {
                return \redirect()->back()->withInput()->with('error', 'No Attendance Found!');
            }

            $studentAttds = [];
            $percentageStudents = [];

            foreach ($attendances as $attendance) {
                $studentAttds[$attendance->student_id][] = $attendance;
            }

            foreach ($studentAttds as $studentId => $attds) {
                $totalAttdCount = count($attds);

                $totalPresentAttdCount = collect($attds)->filter(function ($attd) {
                    return $attd->attendance_status != 'Absent' && $attd->attendance_status != 'Late Absent';
                })->count();
                
                $totalAbsentAttdCount = collect($attds)->filter(function ($attd) {
                    return $attd->attendance_status == 'Absent' || $attd->attendance_status == 'Late Absent';
                })->count();

                if($totalAbsentAttdCount > 0){
                    $student_percentage = ($totalPresentAttdCount / $totalAttdCount) * 100;
                    
                    if($student_percentage < $attendance_percent){
                        $firstAttd = $attds[0];
    
                        $message = str_replace('[student_id]', @$studentId , $sms_body);
                        $message = str_replace('[student_name]', @$firstAttd->student_name , $message);
                        $message = str_replace('[guardian_phone]', @$firstAttd->contact_no , $message);
                        $message = str_replace('[attendance_percentage]', @$attendance_percent.'%' , $message);
                        $message = str_replace('[student_percentage]', @$student_percentage.'%' , $message);
    
                        $percentageStudents[$studentId] = [
                            'student_name' => $firstAttd->student_name,
                            'contact_no' => $firstAttd->contact_no,
                            'sms_body' => $message
                        ];
                    
                    }
                }

            }
            $attdYear = date('Y',strtotime($request->year_month));
            $attdMonth = date('m',strtotime($request->year_month));

            foreach($percentageStudents as $studentId => $attd){
                $log = SmsLog::where('student_id', $studentId)->where('sms_type', 'perentage_attendance')->whereYear('attendance_date', $attdYear)->whereMonth('attendance_date', $attdMonth)->first();
                $isUpdated = false;

                if(is_null($log)){
                    $log = new SmsLog;
                    $isUpdated = true;
                }else{
                    if($log->sms_status == 0)
                        $isUpdated = true;
                }

                if($isUpdated){
                    $log->student_id = $studentId;
                    $log->student_name = $attd['student_name'];
                    $log->contact_no = $attd['contact_no'];
                    $log->sms_body = $attd['sms_body'];
                    $log->sms_date = date('Y-m-d');
                    $log->attendance_date = $toDate;
                    $log->sms_type = 'perentage_attendance';
                    $log->sms_status = 1;
                    $log->save();
                }
            }

            $setting = AttendanceSetting::first();

            if($setting->active_absent_sms){
                $smsLogs = SmsLog::where('sms_type', 'perentage_attendance')->whereYear('attendance_date', $attdYear)->whereMonth('attendance_date', $attdMonth)->where('sms_status', 0)->get();
    
                foreach($smsLogs as $log){
                    if($log->contact_no != ''){
                        $response_sms = @sendSms($log->sms_body,$log->contact_no);
                        // if(isset($response_sms['code']) && $response_sms['code'] == 400 && $response_sms['status'] == 1){
                        // }
                        $log->sms_status = 1;
                        $log->save();
                    }
                }
            }

            return \redirect()->back()->with('info', 'Operation Successful');
            
        }
            
    }

    public function smsLog(){
        return view('BackEnd.student.attendance.smsLog');
    }

    public function smsLogDatasource(Request $request){
        $records = SmsLog::query();

        return DataTables::of($records)
            ->editColumn('sms_status', function($records){
                return $records->sms_status ? '<span class="badge badge-sm badge-primary">Sent</span>':'<span class="badge badge-sm badge-danger">Pending</span>';
            })
            ->filter(function ($query) use ($request) {

                if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                    $query->where('student_id','LIKE',"%".$request->get('student_id')."%");
                }

                if ($request->has('student_name') && ! is_null($request->get('student_name'))) {
                    $query->where('student_name','LIKE',"%".$request->get('student_name')."%");
                }

                if ($request->has('start_date') && ! is_null($request->get('start_date'))) {
                    $query->where('attendance_date','>=', $request->get('start_date'));
                }

                if ($request->has('end_date') && ! is_null($request->get('end_date'))) {
                    $query->where('attendance_date', '<=', $request->get('end_date'));
                }

                if ($request->has('sms_type') && ! is_null($request->get('sms_type'))) {
                    $query->where('sms_type', $request->get('sms_type'));
                }

                if ($request->has('sms_status') && ! is_null($request->get('sms_status'))) {
                    $query->where('sms_status', $request->get('sms_status'));
                }

            })

            ->setRowAttr([
                'data-row-id' => function($records) {
                    return $records->id;
                }
            ])
            ->rawColumns(['actions', 'sms_status'])
            // ->escapeColumns([])
            ->make(true);
    }
}

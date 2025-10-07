<?php

namespace App\Http\Controllers\Student\Attendance;

use App\AttendanceSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    public function setting(){
        $setting = AttendanceSetting::first();
        return view('BackEnd.student.attendance.settings', \compact('setting'));
    }

    public function settingPost(Request $request){
        $this->validate($request, [
            'device_data_from' => 'required|date',
            'college_openning_time'=> 'required',
            'college_late_time'=> 'required',
            'college_absent_time' => 'required',
            'active_absent_sms' => 'required',
            'active_present_sms' => 'required',
            'attendance_student_session' => 'required',
        ]);

        $id = $request->id;
        $setting = AttendanceSetting::find($id);
        $setting->device_data_from = $request->device_data_from;
        $setting->college_openning_time = $request->college_openning_time;
        $setting->college_late_time = $request->college_late_time;
        $setting->college_absent_time = $request->college_absent_time;
        $setting->active_absent_sms = $request->active_absent_sms;
        $setting->active_present_sms = $request->active_present_sms;
        $setting->attendance_student_session = \json_encode($request->attendance_student_session);
        $setting->save();

        return redirect()->back()->with('info', 'Attendance Setting Updated Successfully');
        


    }
}

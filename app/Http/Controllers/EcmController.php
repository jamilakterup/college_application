<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Auth;
use Config;
use DB;

class EcmController extends Controller
{
    public function download_csv_format(){
        $type = request()->get('type');
        if($type == 'honsff')
            return response()->download(public_path().'/csv/hons_ff_format.csv');
        if($type == 'mscff')
            return response()->download(public_path().'/csv/msc_ff_format.csv');
        if($type == 'degff')
            return response()->download(public_path().'/csv/deg_ff_format.csv');
        if($type == 'hscff')
            return response()->download(public_path().'/csv/hsc_ff_format.csv');
        if($type == 'honsMerit')
            return response()->download(public_path().'/csv/hons_merit_list.csv');
        if($type == 'hscMerit')
            return response()->download(public_path().'/csv/hsc_merit_list.csv');
        if($type == 'degMerit')
            return response()->download(public_path().'/csv/deg_merit_list.csv');
        if($type == 'mscMerit')
            return response()->download(public_path().'/csv/masters_merit_list.csv');
    }

    public function truncate_table(){
        $type = request()->get('type');
        if($type == 'honsff')
            DB::table('student_info_hons_formfillup')->truncate();
        if($type == 'mscff')
            DB::table('student_info_masters_formfillup')->truncate();
        if($type == 'degff')
            DB::table('student_info_degree_formfillup')->truncate();
        if($type == 'hscff')
            DB::table('student_info_hsc_formfillup')->truncate();
        if($type == 'honsMerit')
            DB::table('hons_merit_list')->truncate();
        if($type == 'hscMerit')
            DB::table('hsc_merit_list')->truncate();
        if($type == 'degMerit')
            DB::table('deg_merit_list')->truncate();
        if($type == 'mscMerit')
            DB::table('masters_merit_list')->truncate();
            

        return redirect()->back()->with('warning','Records Successfully Deleted!');
    }
}

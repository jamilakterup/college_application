<?php

namespace App\Http\Controllers;

use Auth;
use Config;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.settings:Site Settings|General';

        return view('BackEnd.setting.general', compact('title', 'breadcrumb'));
    }

    public function general_update(Request $request){
        // if(Auth::user()->cannot('access','setting.manage'))
        //     return redirect()->route('profile')->with('error','Access denied!');
        // return $request->all();
        if ($request->site_logo != '') {
            $this->uploadOne($request->file('site_logo'), $request, 'site_logo');
        }

        if ($request->site_favicon != '') {
            $this->uploadOne($request->file('site_favicon'), $request, 'site_favicon');
        }

        $type = $request->type;

        $keys = $request->except('_token', 'type', 'site_logo', 'site_favicon');

        foreach ($keys as $key => $value)
        {
            $status = Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', ucfirst($type).' General Settings Successfully Updated.');
    }

    public function social(){
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.settings:Site Settings|Social';

        return view('BackEnd.setting.social', compact('title', 'breadcrumb'));
    }

    public function social_update(Request $request){
        $keys = $request->except('_token', 'type', 'site_logo', 'site_favicon');

        foreach ($keys as $key => $value)
        {
            $status = Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success',' Social Settings Successfully Updated.');
    }

    public function instruction(){
        $title = 'Easy CollegeMate - Admission Management';
        $breadcrumb = 'admin.settings:Site Settings|Instruction';

        return view('BackEnd.setting.instruction', compact('title', 'breadcrumb'));
    }

    public function instruction_update(Request $request){
        $keys = $request->except('_token');

        foreach ($keys as $key => $value)
        {
            $status = Setting::set($key, $value);
        }

        return redirect()->route('settings.instruction')->with('success', 'Instruction Settings Successfully Updated.');
    }


    public function uploadOne(UploadedFile $file, $request, $key){

        if ($request->has($key) && $file) {
            $this->deleteOne(config('settings.'.$key));
            $name = $key.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/sites/'), $name);
            Setting::set($key, $name);
            return $name;
        }

    }

    public function deleteOne($filename){
        if ($filename != '') {
            if (file_exists(public_path('upload/sites/'.$filename))) {
                unlink(public_path('upload/sites/'.$filename));
            }
        }
    }

    public function email_env_update(Request $request){
        if(Auth::user()->cannot('access','setting.manage'))
            return redirect()->route('profile')->with('error','Access denied!');
        
        $mail_driver = str_replace(' ', '', $request->mail_driver);
        $mail_host = str_replace(' ', '', $request->mail_host);
        $mail_port = str_replace(' ', '', $request->mail_port);
        $mail_username = str_replace(' ', '', $request->mail_username);
        $mail_password = str_replace(' ', '', $request->mail_password);
        $mail_encryption = str_replace(' ', '', $request->mail_encryption);

        $email_update = [
        'MAIL_DRIVER'   => $mail_driver,
        'MAIL_HOST'   => $mail_host,
        'MAIL_PORT' => $mail_port,
        'MAIL_USERNAME' => $mail_username,
        'MAIL_PASSWORD' => $mail_password,
        'MAIL_ENCRYPTION' => $mail_encryption
        ];

        changeEnv($email_update);
    }

    public function config_edit(Request $request){

        if(!$request->ajax()){
            $title = 'Easy CollegeMate - Configurations Edit';
            $breadcrumb = 'admin.settings:Site Settings|Configurations Edit';
            return view('BackEnd.setting.configuration_edit', compact('title', 'breadcrumb'));
        }else{
            $conf = Configuration::find($request->key_id);
            return view('BackEnd.setting.particles.conf_form', \compact('conf'))->render();
        }
    }

    public function config_update(Request $request){
        $conf = Configuration::find($request->key);
        $conf->value = $request->value;
        $conf->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Configuration Updated Successfull.',
        ],Response::HTTP_OK);
    }
}

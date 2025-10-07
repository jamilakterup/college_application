<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AjaxController extends Controller
{
    public function districtChange(Request $request) {

	    if($request->ajax()){
	         $dist= $request->get('dist'); 
	         $result = DB::table('district_thana')
	                ->select('thana')
	                ->Where('district', $dist)
	                ->get();

	       foreach ($result as  $value)
	        {                          

	          echo  "<option value='{$value->thana}'>{$value->thana}</option>";               
	        }  
	        //echo json_encode($ssc_roll);    
	    }
  	}

  	public function faculty_department_dropdown(Request $request)
   {
 
       if($request->ajax())
        {
            $faculty_name=$request->get('faculty');
            
          
            $results=DB::table('faculties')->where('faculty_name', $faculty_name )->get();
            if(count($results) < 1){
                return '';
            }

            $faculty_id= $results->first()->id;

            $query = DB::table('departments')
                    ->select('dept_name')
                    ->where('faculty_id', $faculty_id);

            query_has_permissions($query, ['dept_name']);

            $options = $query->pluck('dept_name', 'dept_name');
            echo \Form::select('dept', $options, null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'dept', 'placeholder' => '--Select Department--']);

        }                    
        
    }
}

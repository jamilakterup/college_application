<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Importer;

class PreHscResultController extends Controller
{
    public function create(){
    	$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'pre_hsc_result.index:HSC Result|Dashboard';

    	return view('BackEnd.hsc_result.pre_hsc_result.create', compact('title', 'breadcrumb'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx,csv',
            'group' => 'required'
        ]);


        $dateTime = date('Ymd_His');
        $file = $request->file('file');
        $fileName = $dateTime.'_'.$file->getClientOriginalName();
        $savePath = public_path('/upload/temp/');  
        $file->move($savePath, $fileName);
        $filePath = $savePath.$fileName;

        $excel = Importer::make('Excel');
        $excel->load($savePath.$fileName);
        $collection = $excel->getCollection();

        $num_rows_group = 0;

        if ($request->group == 'Humanities') {
            return $this->import_arts_result($collection, $filePath);
        }elseif($request->group == 'Business Studies'){
            return $this->import_commerce_result($collection, $filePath);
        }elseif($request->group == 'Science'){
            return $this->import_science_result($collection, $filePath);
        }
    }

    public function import_arts_result($collection, $filePath){
        if (sizeof($collection[1]) == 23) {
            $numRows = sizeof($collection);
            $numCols = sizeof($collection[1]);
            $rows = $collection;


            for ($i=0; $i < $numRows ; $i++) { 
                try {
                    $marks_string='';
                    for ($j = 0; $j < $numCols; $j++) {
                        
                        if($i==0){
                            $header[$j]=$rows[$i][$j];
                        }
                        
                        else{
                            if($j==0)
                                $roll=$rows[$i][$j];
                            else if($j==1)
                                $groups=$rows[$i][$j];
                            else if($j==2){
                                $session=$rows[$i][$j];
                            }
                            
                            /*For Commerce*/
                            else if ($j>2 && $j<17){
                                $marks_string.=$header[$j]."/".$rows[$i][$j].";";
                            }
                            
                            else if ($j==17){
                                $total_marks=$rows[$i][$j];
                            }
                            
                            else if($j==18){ /**This is changed for just input as only gpa, not withgpa/without gpa***/
                                $gpa_with_fourth=$rows[$i][$j];
                                //$gpa_without_fourth=$rows[$i][$j];
                            }
                            
                            else if($j==19){
                                //$gpa_with_fourth=$rows[$i][$j];
                                $gpa_without_fourth=$rows[$i][$j];
                            }
                            
                            else if ($j==20){
                                $attendance=$rows[$i][$j];
                            }       
                            
                            else if ($j==21){
                                $exam_name=$rows[$i][$j];
                            }      
                                
                        }
                    }
                    if ($i != 0) {
                        DB::statement("Insert into hsc_result_student_show 
                        (roll,groups,session,marks_string,gpa_with_fourth,gpa_without_fourth,
                        total_mark_except,attendence,exam_name)

                        values('$roll','$groups','$session','$marks_string','$gpa_with_fourth',
                        '$gpa_without_fourth','$total_marks','$attendance','$exam_name')");
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error' , $e->getMessege());
                }

            }

            unlink($filePath);
            return redirect()->back()->with('success' ,'Records Successfully Updated.');

        }else{
            return redirect()->back()->with('error' ,'Please provide data in file according to sample file.');
        }
    }

    public function import_commerce_result($collection, $filePath){
        if (sizeof($collection[1]) == 16) {
            $numRows = sizeof($collection);
            $numCols = sizeof($collection[1]);
            $rows = $collection;


            for ($i=0; $i < $numRows ; $i++) { 
                try {
                    $marks_string='';
                    for ($j = 0; $j < $numCols; $j++) {
                        
                        if($i==0){
                            $header[$j]=$rows[$i][$j];
                        }
                        
                        else{
                                if($j==0)
                                    $roll=$rows[$i][$j];
                                else if($j==1)
                                    $groups=$rows[$i][$j];
                                else if($j==2){
                                    $session=$rows[$i][$j];
                                }
                                
                                /*For Commerce*/
                                else if ($j>2 && $j<10){
                                    $marks_string.=$header[$j]."/".$rows[$i][$j].";";
                                }
                                
                                else if ($j==10){
                                    $total_marks=$rows[$i][$j];
                                }
                                
                                else if($j==11){
                                    $gpa_without_fourth=$rows[$i][$j];
                                }
                                
                                else if($j==12){
                                    $gpa_with_fourth=$rows[$i][$j];
                                }
                                
                                else if ($j==13){
                                    $attendance=$rows[$i][$j];
                                }       
                                
                                else if ($j==14){
                                    $exam_name=$rows[$i][$j];
                                }       
                                
                        }
                    }
                    if ($i != 0) {
                        DB::statement("Insert into hsc_result_student_show 
                        (roll,groups,session,marks_string,gpa_with_fourth,gpa_without_fourth,
                        total_mark_except,attendence,exam_name)

                        values('$roll','$groups','$session','$marks_string','$gpa_with_fourth',
                        '$gpa_without_fourth','$total_marks','$attendance','$exam_name')");
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error' , $e->getMessege());
                }

            }

            unlink($filePath);
            return redirect()->back()->with('success' ,'Records Successfully Updated.');

        }else{
            return redirect()->back()->with('error' ,'Please provide data in file according to sample file.');
        }
    }

    public function import_science_result($collection, $filePath){
        if (sizeof($collection[1]) == 17) {
            $numRows = sizeof($collection);
            $numCols = sizeof($collection[1]);
            $rows = $collection;


            for ($i=0; $i < $numRows ; $i++) { 
                try {
                    $marks_string='';
                    for ($j = 0; $j < $numCols; $j++) {
                        
                        if($i==0){
                            $header[$j]=$rows[$i][$j];
                        }
                        
                        else{
                            if($j==0)
                                $roll=$rows[$i][$j];
                            else if($j==1)
                                $groups=$rows[$i][$j];
                            else if($j==2){
                                $session=$rows[$i][$j];
                            }
                            
                            /*For Science*/
                            else if ($j>2 && $j<11){
                                $marks_string.=$header[$j]."/".$rows[$i][$j].";";
                            }
                            
                            else if ($j==11){
                                $total_marks=$rows[$i][$j];
                            }
                            
                            else if($j==12){
                                $gpa_without_fourth=$rows[$i][$j];
                            }
                            
                            else if($j==13){
                                $gpa_with_fourth=$rows[$i][$j];
                            }
                            
                            else if ($j==14){
                                $attendance=$rows[$i][$j];
                            }       
                            
                            else if ($j==15){
                                $exam_name=$rows[$i][$j];
                            }       
                                
                        }
                    }
                    if ($i != 0) {
                        DB::statement("Insert into hsc_result_student_show 
                        (roll,groups,session,marks_string,gpa_with_fourth,gpa_without_fourth,
                        total_mark_except,attendence,exam_name)

                        values('$roll','$groups','$session','$marks_string','$gpa_with_fourth',
                        '$gpa_without_fourth','$total_marks','$attendance','$exam_name')");
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error' , $e->getMessege());
                }

            }

            unlink($filePath);
            return redirect()->back()->with('success' ,'Records Successfully Updated.');

        }else{
            return redirect()->back()->with('error' ,'Please provide data in file according to sample file.');
        }
    }

    public function hsc_result_show(Request $request){
        return view('hsc_result.pre_result');
    }
}

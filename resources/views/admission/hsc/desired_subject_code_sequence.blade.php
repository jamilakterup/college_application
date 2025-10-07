<?php 


	$student =  DB::table('hsc_admitted_students')->where('auto_id',$id)->get();


	foreach ($student as $value) {
		$hsc_group = $value->hsc_group;
		$compulsory = $value->compulsory;
		$selective = $value->selective;
		$optional = $value->optional;
	}


    $courses =  DB::table('course_hsc_new')->where('groups', strtolower($hsc_group))->get();
    
	$cods = array();
	foreach($courses as $course){
		if (strpos($course->subjects, ',') !== FALSE) { 
			$subjects = explode(",", $course->subjects);
			$codes = explode(",", $course->codes);
			foreach ($subjects as $key => $subject) {
				$cods[$codes[$key]] = $subject;
			}
		} else {
			$cods[$course->codes] = $course->subjects;
		}
	}

	$compulsory = explode(",", $compulsory);
	$selective = explode(",", $selective);
	$optional = explode(",", $optional);

	$compulsory_string = '';
	$selective_string = '';
	$optional_string = '';

	foreach ($compulsory as $value) {
		$compulsory_string .= $cods[$value]."(".$value."),";
	}
	$compulsory_string=rtrim($compulsory_string,",");

	foreach ($selective as $value) {
		$selective_string .= $cods[$value]."(".$value."),";
	}
	$selective_string=rtrim($selective_string,",");

	foreach ($optional as $value) {
		$optional_string .= $cods[$value]."(".$value."),";
	}
	$optional_string=rtrim($optional_string,",");
	
	$compulsory_string = str_replace("-", ",", $compulsory_string);
	$selective_string = str_replace("-", ",", $selective_string);

	$optional_string = str_replace("-", ",", $optional_string);

	Session::put('compulsory_string',$compulsory_string);
    Session::put('selective_string',$selective_string);
     Session::put('optional_string',$optional_string);

	if ($status == 0) {
		$all_string = $compulsory_string.",".$selective_string.",".$optional_string;
		echo "$all_string";
	} elseif ($status == 1) {
		echo "<table class='table table-bordered table-hover'>";
        echo "<tr class='warning'>";
        echo "<td>Subject Type</td>";
        echo "<td>Subjects</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Compulsory</td>";
        echo "<td>$compulsory_string</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Selective</td>";
        echo "<td>$selective_string</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Optional</td>";
        echo "<td>$optional_string</td>";
        echo "</tr>";
        echo "</table>";
	}
?>
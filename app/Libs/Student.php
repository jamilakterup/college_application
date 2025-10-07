<?php

class Student{
    
    public static $course_table='';
    public static $form_fillup_table='';
	public static $result_table='';
    public static $condition_with_attr=array();
	public static $level_study='';
	public static $exam_year='';
	public static $values=array();
	public static $student_info_table='';
	public static $exam_name='';
	
	
	
	function __constructor(){
		
		
	}
	
	public static function roll_generate(){
	
	$fp=fopen("class_roll.txt","r");
	$class_roll=fgets($fp);
	$class_roll2=$class_roll+1;
 	$fp=fopen("class_roll.txt","w");
 	fputs($fp,$class_roll2);
 	return $class_roll2;
		
	}
	
	
	public static function id_generate($session,$catagory,$class_roll){
	$id=$session.$catagory.$class_roll;
	$id=str_replace('-','',$id);
	return substr($id,4,8);
	

}
		
		
		// for Honours  students		
		
	public static function roll_generate_honours($session,$subject,$catagory,$prefix){
		global $database;
			$id_table_subject=$prefix.$subject;
			
			//echo $id_table_subject;
			
			
	$query="select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'";
	$results=$database->get_all_by_sql($query);
	//convert 1 as 001 for 3 digit roll
	foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
		
	$query="select dept_code from department where dept_name='$subject'";
	$results=$database->get_all_by_sql($query);
	foreach($results as $result){ $dept_code=$result->dept_code; break; }
	$dept_code=substr($dept_code,0,2); // take first two digit of the department code
	
	$class_roll=$catagory.$dept_code.$digit;
	
 	return $class_roll;
		
	}
	
	public static function roll_generate_degree($session,$groups,$catagory){
		global $database;

		$groups=trim($groups);
		if($groups=='arts')  $dept_code=1;
		if($groups=='social_science')  $dept_code=2;
		if($groups=='commerce')  $dept_code=3;
		if($groups=='science')  $dept_code=4;
		
		
		$groups='degree_'.$groups;  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
	
	$query="select last_digit_used from id_roll where session='$session' and dept_name='$groups'";
	$results=$database->get_all_by_sql($query);
	//convert 1 as 001 for 3 digit roll
	foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'4','0',STR_PAD_LEFT); break; }
	
	
	
	$class_roll=$catagory.$dept_code.$digit;
	
 	return $class_roll;
		
	}
	
	
	
	
	public static function roll_generate_hsc($session,$groups,$catagory){
		global $database;
		
		$groups='hsc_'.$groups;  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
	$query="select last_digit_used from id_roll where session='$session' and dept_name='$groups'";
	$results=$database->get_all_by_sql($query);
	//convert 1 as 001 for 3 digit roll
	foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'5','0',STR_PAD_LEFT); break; }
	$class_roll=$catagory.$digit;
	
 	return $class_roll;
		
	}
	
	
	
	
	// for honours students
	public static function id_generate_honours($session,$class_roll){
	
	$session=substr($session,0,4);  // take session as first year of the session(ex: 2012-2013 , session is 2012)
	return $id=$session.$class_roll;
	

}
		
		
		
	
	public static function session_generate(){
	
			$year=date("Y"); 
			$m=1;
			$session=array();
			for($i=-5;$i<=5;$i++)                
			{
		
			    $a=$year+$i;	
			    $b= $year+($i+1); 
			   $session[$m]=$a.'-'.$b;
			   if($i==0)
			   {
			   $session_current=$session[$m];
			   }
			   $m++;
			  			
			    
			}
			return $session;
		
	}
	
	
	public static function current_session_generate(){
	
			$current_year=date("Y");
			$current_year2=$current_year+1;
			$current_session=$current_year.'-'.$current_year2;		
			
			return $current_session;
		
	}
	
	
    public static function result_form(){
        global $database;
       
        $condition=$database->get_condition(self::$condition_with_attr);
        //remove firsr 'and' from the condition
        $len=strlen($condition);
        $condition=substr($condition,4,$len);
        
        $table_name=self::$course_table;
        $course_query="select * from {$table_name} where".$condition;       
        $results=$database->get_all_by_sql($course_query);
        foreach($results as $result){
            $all_courses[]=$result->course_code; // all course of searched level and group
            $marks[]=$result->mark;              // mark of each course
        }
		
		$no_course=count($all_courses);
		
		
		
		
        $table_name=self::$form_fillup_table;
		$xam_year=self::$exam_year;
        $student_query="select * from {$table_name} where ".$condition."and exam_year='$xam_year'";
		
        $results=$database->get_all_by_sql($student_query);
		echo "<table>\n";
		// Heading of the result process with course code
		echo "<tr>";
				echo "<td>";  echo "ID"; echo "</td>";
				
				 for($i=0;$i<$no_course;$i++){echo "<td>"; echo $all_courses[$i];  echo "</td>";}
				
			echo "</tr>";


	           foreach($results as $result){  
			$id=$result->id;      //  id of result process from forfill up table 
            $student_course=$result->course;
			//make the student's from form_fillip table in array
			$student_course=explode(',',$student_course);
			//find if the student has given improve xam.then need to extract obtain field in array with obtaion number.
			
			$last_xam_mark=Student::get_obtain_mark($id,$condition);
			
			echo "<tr id='hide_{$id}'>\n";
				//for id 
				echo "<td>\n";
					echo $id;
					$level=self::$level_study; // study level for which result is processing
				echo "<input type=\"hidden\"  value=\"{$level}\" id=\"level_{$id}\"  />\n";
					$table=self::$result_table;
				echo "<input type=\"hidden\"  value=\"{$table}\" id=\"table_{$id}\"  />\n";
					
				
				echo "</td>\n";
				
				//for mark field
				echo "<form>\n";

				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="obtain_".$id;
				//if each course is in the student course, then it will be enable other wise input field will disable
				if(in_array($all_courses[$i],$student_course)) $disable=""; 
				// and also if this course is not exists in student course and also no obtain mark 
				//then value set to 'N/A' for detecting the original course and mark during result processing
				else {$disable="disabled"; if($last_xam_mark[$all_courses[$i]]=='') $last_xam_mark[$all_courses[$i]]='N/A'; }
				
				echo "<input size='4'  {$disable} value=\"{$last_xam_mark[$all_courses[$i]]}\"  type=\"text\" class=\"{$class_name}\"  />\n";
				echo "</td>\n";
				}
				
				
				// for all hidden input for original marks
				echo "<td>\n";
				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="mark_".$id;
				echo "<input type=\"hidden\" class=\"{$class_name}\" value=\"{$marks[$i]}\"  />\n";
				echo "</td>\n";
				}
				// hidden input for coures
				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="course_".$id;
				echo "<input type=\"hidden\" class=\"{$class_name}\" value=\"{$all_courses[$i]}\"  />\n";
				echo "</td>\n";
				}
				
				
				// displaying cgpa div
				$cgpa_div="cgpa_".$id;
				echo "<td>\n";
				echo "<div id=\"{$cgpa_div}\" ></div>\n";
				echo "</td>\n";
				
				//resutl processing button
				echo "<td>\n";
				echo "<input type=\"button\" class=\"button_sub\" value=\"submit\" id=\"{$id}\" />\n";
				echo "</td>\n";
				
				
				
				//Hide Button
				echo "<td>\n";
				echo "<input type=\"button\" class=\"hide_button\" value=\"Hide\" id=\"hidediv_{$id}\" />\n";
				echo "</td>\n";
				
				

				echo "</form>\n";

				
			echo "</tr>\n"; 
			echo "\n";
			
		}
        		
		echo "</table>\n";


		
        
    }
 public static function certificate($id,$certificate_id)
	 {
	    global $database;
	    $student_type=preg_replace("/[^a-z]+/","",$certificate_id);
	    
	    if($student_type=='hsc')
	    {
	    	$table='student_info_hsc';
	    	$table2='result_hsc';
	    	$final_level='HSC 2nd Year';
	 	  	$values=array("id","name","father_name","mother_name","session","nu_roll","class_roll",'groups',"registration_id","current_level",'permanent_village','permanent_po','permanent_ps','permanent_dist');    	
	    }

		else if($student_type=='degree')
	    {
	    	$table='student_info_degree';
	    	$table2='result_degree';
	    	$final_level='Degree 3rd Year';
	    	$values=array("id","name","father_name","mother_name","session","nu_roll","class_roll",'groups',"registration_id","current_level",'permanent_village','permanent_po','permanent_ps','permanent_dist');

	    }

	    else 
	    {
	    	$table='student_info_hons';
	    	
	    	if($student_type=='honours')  
	    		{
	    			$table2='result_hons';
	    			$final_level='Honours 4th Year';
	    		}
	    	else 
	    		{
	    			$table2='result_masters';
	    			$final_level='Masters 2nd Year';
	    		}

	    	$values=array("id","name","father_name","mother_name","session","nu_roll","class_roll",'dept_name',"registration_id","current_level",'permanent_village','permanent_po','permanent_ps','permanent_dist');

	    }

	    $query="select * from {$table} where id={$id} ";
		$num_field=count($values);
		$results=$database->query($query);
		
		while($row=mysql_fetch_array($results))
	    {   		
		     
			for($i=0;$i<$num_field;$i++)
			{
				$data[$i]=$row[$values[$i]];		
			}        
			 $name=$data[1];
			 $father=$data[2];
			 $mother=$data[3];		
			 $session=$data[4];
			 $nu_roll=$data[5];
			 $class_roll=$data[6];

			
			 if($student_type=='honours' || $student_type=='masters')
			 $subject=$data[7];
			 else
			 $group=$data[7];

			 $registration_no=$data[8];

			 $class=$data[9];
			 $explode=explode(' ',$class);
			 $level=$explode[0];
			 $study_year=$explode[1];

			 $village=$data[10];
			 $post=$data[11];
			 $upazilla=$data[12];
			 $district=$data[13];		
			 					
	    }
	    $date=date('d-m-Y');

	    if($student_type!='degree')
	    {
	    	$query = "select avg(cgpa) from $table2 where processed=1 and id=$id"; 	
			$results=$database->query($query);
			
			while($row=mysql_fetch_array($results))
		    {   		
				$result=$row['avg(cgpa)'];	
				 					
		    }

	    }
	  

	    $query = "select session from $table2 where processed=1 and id=$id and level_study='$final_level'"; 	
		$results=$database->query($query);	

		while($row=mysql_fetch_array($results))
	    {   		
			$session=$row['session'];
			 					
	    }

	    $explode=explode('-',$session);
	    $exam_year=$explode[1];
	    

	    $query="select * from config_certificate where certificate_id='$certificate_id'";
	    $results=$database->query($query);
		
		while($row=mysql_fetch_array($results))
	    {   		
		    $text=$row['content'];	   	 					
	    }
	 	//$text=stripslashes($text);
		// eval("$text= \"$text\";");   
	 	//  echo $text;
	 	echo '<div style="padding:0 0 0 15px;font-family:verdana" >';
	    print eval("return<<<END\n$text\nEND;\n");
	    echo '<div>';
	  }
	
	
	public static function get_obtain_mark($id,$condition){
		global $database;
		$last_xam_mark=array();
		$table_name=self::$result_table;
			if($condition)
			$query="select * from {$table_name} where id='$id' and ".$condition;
			else
			$query="select * from {$table_name} where id='$id'";
			
			
			$result_results=$database->get_all_by_sql($query);
			$obtain_mark=''; //set to empty string otherwise it will be array after explode operation bellow
			foreach($result_results as $result_result){
				$obtain_mark=$result_result->obtain_mark;
			}
			// if not empty then split into two dimentional array with index course 
			if($obtain_mark!=''){
				
				$obtain_mark=str_replace('/',',',$obtain_mark); // replace / with ,
				$obtain_mark=explode(',',$obtain_mark);
				$no_mark=count($obtain_mark);
				
				
				
				
				for($i=0;$i<$no_mark-1;$i+=2)
				$last_xam_mark[$obtain_mark[$i]]=$obtain_mark[$i+1];	
				}
				
			else $last_xam_mark=array();
			
			return $last_xam_mark;
	}
	
	/*Internal Exam*/
	
	public static function result_form_internal(){
        global $database;
       
        $condition=$database->get_condition(self::$condition_with_attr);
        //remove firsr 'and' from the condition
        $len=strlen($condition);
        $condition=substr($condition,4,$len);
        
        $table_name=self::$course_table;
        $course_query="select * from {$table_name} where".$condition;       
        $results=$database->get_all_by_sql($course_query);
        foreach($results as $result){
            $all_courses[]=$result->course_code; // all course of searched level and group
            $marks[]=$result->mark;              // mark of each course
        }
		
		$no_course=count($all_courses);
		
		
		
		
        $table_name=self::$student_info_table;
		$xam_year=self::$exam_year;
		$condition=str_replace('level_study','current_level',$condition);
		$level_study=self::$level_study;
        $student_query="select id,(select course from student_course where {$table_name}.id=student_course.id and level_study='$level_study') as course from {$table_name} where ".$condition." and session='$xam_year'";
		
		
		
        $results=$database->get_all_by_sql($student_query);
		echo "<table>\n";
		// Heading of the result process with course code
		echo "<tr>";
				echo "<td>";  echo "ID"; echo "</td>";
				
				 for($i=0;$i<$no_course;$i++){echo "<td>"; echo $all_courses[$i];  echo "</td>";}
				
			echo "</tr>";


	           foreach($results as $result){  
			$id=$result->id;      //  id of result process from forfill up table 
            $student_course=$result->course;
			//make the student's from form_fillip table in array
			$student_course=explode(',',$student_course);

			
			//find if the student has given improve xam.then need to extract obtain field in array with obtaion number.
			
			$condition=str_replace('current_level','level_study',$condition);
			
			$last_xam_mark=Student::get_obtain_mark_internal($id,$condition);
			
			echo "<tr id='hide_{$id}'>\n";
				//for id 
				echo "<td>\n";
					echo $id;
					$level=self::$level_study; // study level for which result is processing
				echo "<input type=\"hidden\"  value=\"{$level}\" id=\"level_{$id}\"  />\n";
					$table=self::$result_table;
				echo "<input type=\"hidden\"  value=\"{$table}\" id=\"table_{$id}\"  />\n";
					//hidden Exam name Field
					
					$exam_name=self::$exam_name;
				echo "<input type=\"hidden\"  value=\"{$exam_name}\" id=\"exam_name_{$id}\"  />\n";
					
					
				
				echo "</td>\n";
				
				//for mark field
				echo "<form>\n";

				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="obtain_".$id;
				//if each course is in the student course, then it will be enable other wise input field will disable
				if(in_array($all_courses[$i],$student_course)) $disable=""; 
				// and also if this course is not exists in student course and also no obtain mark 
				//then value set to 'N/A' for detecting the original course and mark during result processing
				else {$disable="disabled"; if($last_xam_mark[$all_courses[$i]]=='') $last_xam_mark[$all_courses[$i]]='N/A'; }
				
				echo "<input size='4'  {$disable} value=\"{$last_xam_mark[$all_courses[$i]]}\"  type=\"text\" class=\"{$class_name}\"  />\n";
				echo "</td>\n";
				}
				
				
				// for all hidden input for original marks
				echo "<td>\n";
				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="mark_".$id;
				echo "<input type=\"hidden\" class=\"{$class_name}\" value=\"{$marks[$i]}\"  />\n";
				echo "</td>\n";
				}
				// hidden input for coures
				for($i=0;$i<$no_course;$i++){
				echo "<td>\n";
				$class_name="course_".$id;
				echo "<input type=\"hidden\" class=\"{$class_name}\" value=\"{$all_courses[$i]}\"  />\n";
				echo "</td>\n";
				}
				
				
				// displaying cgpa div
				$cgpa_div="cgpa_".$id;
				echo "<td>\n";
				echo "<div id=\"{$cgpa_div}\" ></div>\n";
				echo "</td>\n";
				
				//resutl processing button
				echo "<td>\n";
				echo "<input type=\"button\" class=\"button_sub\" value=\"submit\" id=\"{$id}\" />\n";
				echo "</td>\n";
				
				
				
				//Hide Button
				echo "<td>\n";
				echo "<input type=\"button\" class=\"hide_button\" value=\"Hide\" id=\"hidediv_{$id}\" />\n";
				echo "</td>\n";
				
			
				
				

				echo "</form>\n";

				
			echo "</tr>\n"; 
			echo "\n";
			
		}
        		
		echo "</table>\n";


		
        
    }
	
	
	public static function get_obtain_mark_internal($id,$condition){
		global $database;
		$last_xam_mark=array();
		$table_name=self::$result_table;
		$exam_name=self::$exam_name;
			if($condition)
			$query="select * from {$table_name} where id='$id' and exam_name='$exam_name' and ".$condition;
			else
			$query="select * from {$table_name} where id='$id'";
			
			$result_results=$database->get_all_by_sql($query);
			$obtain_mark=''; //set to empty string otherwise it will be array after explode operation bellow
			foreach($result_results as $result_result){
				$obtain_mark=$result_result->obtain_mark;
			}
			// if not empty then split into two dimentional array with index course 
			if($obtain_mark!=''){
				
				$obtain_mark=str_replace('/',',',$obtain_mark); // replace / with ,
				$obtain_mark=explode(',',$obtain_mark);
				$no_mark=count($obtain_mark);
				
				
				
				
				for($i=0;$i<$no_mark-1;$i+=2)
				$last_xam_mark[$obtain_mark[$i]]=$obtain_mark[$i+1];	
				}
				
			else $last_xam_mark=array();
			
			return $last_xam_mark;
	}
	
	
	
	/*End of internal exam*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public static function get_gp_hsc($mark){
		if($mark>=80 && $mark<=100) $gp=5;
		else if($mark>=70 && $mark<=79) $gp=4;
		else if($mark>=60 && $mark<=69) $gp=3.5;
		else if($mark>=50 && $mark<=59) $gp=3;
		else if($mark>=40 && $mark<=49) $gp=2;
		else if($mark>=33 && $mark<=39) $gp=1;
		
		else $gp=0;
		
		return $gp;
		
		
	}
	
	
	public static function get_gp_honours($mark){
	if($mark>=0 && $mark<40) $gp=0;
   
   else if($mark>=40 && $mark<45) $gp=2;
   
   else if($mark>=45 && $mark<50) $gp=2.25;
   
   else if($mark>=50 && $mark<55) $gp=2.5;
   
   else if($mark>=55 && $mark<60) $gp=2.75;
   
   else if($mark>=60 && $mark<65) $gp=3;
   
   else if($mark>=65 && $mark<70) $gp=3.25;
   
   else if($mark>=70 && $mark<75) $gp=3.5;
   
   else if($mark>=75 && $mark<80) $gp=3.75;
   
   else if($mark>=80 && $mark<=100) $gp=4;
   else
    $gp=0;   
	
	return $gp;

	}
	
	
	
public static function get_mark_string($obtain_mark,$courses){
	
	$no_course=count($courses);
	$mark_string='';
	for($i=0;$i<$no_course;$i++){
		if($obtain_mark[$i]!='N/A' && $obtain_mark[$i]!='')
		$mark_string=$mark_string.$courses[$i]."/".$obtain_mark[$i].",";
	}
	
	$mark_string=rtrim($mark_string,',');
	
	
	return $mark_string;
	
}
	
 public static function testimonial($query){
    global $database;
	$value=self::$values;
	$result_table=self::$result_table;
	$num_field=count($value);
	$result=$database->query($query);
	
	while($row=mysql_fetch_array($result))
    {   		
	     
		for($i=0;$i<$num_field;$i++)
		{
			$data[$i]=$row[$value[$i]];		
		}
         $id=$data[0];
		 $name=$data[1];
		 $father_name=$data[2];
		 $mother_name=$data[3];
		 $subject=$data[4];
		 $session=$data[5];
		 $nu_roll=$data[6];
		 $regi_no=$data[7];
		 $level_study=$data[8];
		 					
    }
	if($level_study=='Degree 1st Year' || $level_study=='Degree 2nd Year' || $level_study=='Degree 3rd Year' )
	$result_row_name='class';
	else
	$result_row_name='cgpa';
	
  $query="select {$result_row_name} from {$result_table} where id='$id' and level_study='$level_study' ";
  $result=$database->query($query);	
  while($row=mysql_fetch_array($result))
    {  
	  if($level_study=='Degree 1st Year' || $level_study=='Degree 2nd Year' || $level_study=='Degree 3rd Year' )		
	  $cgpa=$row[$temp];
	  else
	  $cgpa=$row[cgpa];
	}		
	?>	
	
 <table width="600" border="1">
  <tr>
    <td><table  width="600" border="0">
  <tr>
    <td align="center">Goverment of the People's Republic of Bangladesh</td>
  </tr>
  <tr>
    <td align="center"><?php echo COLLEGE_NAME; ?></td>
  </tr>
  <tr>
    <td align="center"><img src="images/logo.png" alt="logo" height="80" width="100" /></td>
  </tr>
  <tr>
    <td align="center">Established: 1879 A.D</td>
  </tr>
  <tr>
    <td align="left">Serial No:</td>
  </tr>
  <tr>
    <td align="center"><h3><u>TESTIMONIAL</u></h3></td>
  </tr>
  <tr>
    <?php if($result_row_name=='cgpa')
	$text="Department";
	else
	$text="Group";
	
	
	if($level_study=='HSC 1st Year' || $level_study=='HSC 2nd Year')
	$text="Group";
	
	 echo "<td>This is certify that <b>$name</b> son/daughter of <b>$father_name</b> and <b>$mother_name</b> has been 
	regular/irregular student in the <b>$text of $subject</b>  of this institute during Session:<b>$session</b> bearing <b>
	Roll No: $nu_roll</b>  and <b>Registration No: $regi_no</b> .He/She took his <b>$level_study examination</b> of <b>2008</b> 
	held in <b>2012</b> and obtained "; ?>
	<?php   if($result_row_name!='class') echo "CGPA- "; echo $cgpa; ?>
	<b></td>
  </tr>
  <tr>
    <td><br/>To the best of my knowledge,he/she did not took part in any activity subversive either of the institute or of the 
	state during study in this college.</td>
  </tr>
  <tr>
    <td><br/>He/She bears a good moral character.<br/>I wish him/her a bright future.</td>
  </tr>
  <tr>
    <td>Date:</td>
  </tr>
  <tr>
    <td align="right">Principal<br/><?php echo COLLEGE_NAME; ?></td>
  </tr>
  <tr>
    <td>Written by:</td>
  </tr>
  <tr>
    <td>Checked by:</td>
  </tr>
  </table>
</td>
  </tr>
</table>

<?php   }  	
  
 
 public static function tc($query){
    global $database;
	$value=self::$values;
	$num_field=count($value);
	$result=$database->query($query);
	
	while($row=mysql_fetch_array($result))
    {   		
	     
		for($i=0;$i<$num_field;$i++)
		{
			$data[$i]=$row[$value[$i]];		
		}
         $id=$data[0];
		 $name=$data[1];
		 $father_name=$data[2];
		 $mother_name=$data[3];
		 $subject=$data[4];
		 $session=$data[5];
		 $nu_roll=$data[6];
		 $regi_no=$data[7];
		 $level_study=$data[8];
		
		 					
    }
	if($level_study=='Degree 1st Year' || $level_study=='Degree 2nd Year' || $level_study=='Degree 3rd Year' )
	$result_row_name='class';
	else
	$result_row_name='cgpa';
	
  	?>	
	
 <table width="600" border="1">
  <tr>
    <td><table  width="600" border="0">
  <tr>
    <td align="center">Goverment of the People's Republic of Bangladesh</td>
  </tr>
  <tr>
    <td align="center"><?php echo COLLEGE_NAME; ?></td>
  </tr>
  <tr>
    <td align="center"><img src="images/logo.png" alt="logo" height="80" width="100" /></td>
  </tr>
  <tr>
    <td align="center">Established: 1879 A.D</td>
  </tr>
  <tr>
    <td align="left">Serial No:</td>
  </tr>
  <tr>
    <td align="center"><h3><u>TRANSFER CERTIFICATE</u></h3></td>
  </tr>
  <tr>
    <?php 
	if($result_row_name=='cgpa')
	$text="Department";
	else
	$text="Group";
	
	if($level_study=='HSC 1st Year' || $level_study=='HSC 2nd Year')
	$text="Group";
	
	echo "<td>This is certify that <b>$name</b> son/daughter of <b>$father_name</b> and <b>$mother_name</b> has been 
	regular/irregular student in the <b>$text of $subject</b>  of this institute during Session:<b>$session</b> bearing <b>
	Roll No: $nu_roll</b>  and <b>Registration No: $regi_no</b> .I am giving his/her Transfer Certificate. </td>"; ?>
  </tr>
  <tr>
    <td><br/>To the best of my knowledge,he/she did not took part in any activity subversive either of the institute or of the 
	state during study in this college.</td>
  </tr>
  <tr>
    <td><br/>He/She bears a good moral character.<br/>I wish him/her a bright future.</td>
  </tr>
  <tr>
    <td>Date:</td>
  </tr>
  <tr>
    <td align="right">Principal<br/><?php echo COLLEGE_NAME; ?></td>
  </tr>
  <tr>
    <td>Written by:</td>
  </tr>
  <tr>
    <td>Checked by:</td>
  </tr>
  </table>
</td>
  </tr>
</table>

<?php   }  	
  	
}


?>

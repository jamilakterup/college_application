<style>
	.set{
		text-align:center;
		margin: 0 auto;
		width:650px;
		background:#fff;
		padding-left:25px;
		padding-right:25px;
		border:1px solid #ccc;
		background:#fff;
	}



	.set h3
	{
		font-size:18px;
		color:#fff;
		font-weight:bold;
		background:#398180;
		margin-bottom: 0;
		padding:8px;
	}

	.set h3 a
	{
		text-decoration:none;
		color:#fff;
	}


	input,select
	{
		padding: 7px;
		border-radius:5px;
		border:1px solid #ccc;
		width:300px !important;
	}

	.custom,.custom2
	{
		border-collapse:collapse;
		margin-top:0 !important;

	}

	.custom td
	{
		border:1px solid #ccc;
		font-size:13px;
		font-weight:bold;
	}

	.custom th
	{
		border:1px solid #ccc;
		font-size:13px;
		font-weight:bold;
	}

	.custom2 td
	{
		text-align:left !important;	
		padding-left:10px  !important;
		border:1px solid #ccc;
		font-size:15px;
		font-weight:bold;
	}

	.custom2 th
	{
		text-align:right !important;
		padding-right:10px  !important;
		border:1px solid #ccc;
		border-right:3px solid #fff;
	}

	.left
	{
		text-align:left !important;
		padding-left:10px !important;
	}

	.header
	{
		background:#398180;
		color:#fff;
		font-size:18px;
	}

	th
	{
		background:#6CBEBC!important;
		border-top:1px solid #ccc !important;
		font-weight:bold !important;
	}

	
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
	
}	
	
</style>
<link rel="stylesheet" type="text/css" href="{{ asset('css/pre_style.css') }}" />
<script>
function downloadFunction() {
    window.print();
}
</script>
	
<?php 	
	if(isset($_POST['submit'])){
		$roll=mysql_escape($_POST['roll']);
		$session=mysql_escape($_POST['session']);
		$exam_name=mysql_escape($_POST['exam_name']);
		$group=mysql_escape($_POST['groups']);
		
		if($roll=='' || $session=='' || $exam_name=='' || $group==''){
			echo "<center style='color:red'><h3>All fields are required.</h3></center>";
		}
		
		else{
			 $results = DB::select("select hsc_result_student_show.*,student_info_hsc.id as st_id,
			 		 name,father_name,mother_name,birth_date,gender,
					student_info_hsc.id from hsc_result_student_show,student_info_hsc
					where CAST(`class_roll` AS SIGNED)=hsc_result_student_show.roll
									and
				student_info_hsc.session=hsc_result_student_show.session
									and 
				hsc_result_student_show.roll='$roll' and hsc_result_student_show.session='$session'
									and 
				hsc_result_student_show.exam_name='$exam_name'");

				if(count($results)<=0)
					{
						echo "<center style='color:red'><h3>No student found.</h3></center>";
					}
					
				else{
				foreach($results as $result){
					$name=$result->name;
					$father_name=$result->father_name;
					$mother_name=$result->mother_name;
					$birth_date=$result->birth_date;
					$gender=$result->gender;
					$student_id=$result->st_id;
					$ses=$result->session;
					$marks_string=$result->marks_string;
					
					$gpa_with_fourth=$result->gpa_with_fourth;
					if($gpa_with_fourth>5)
						$gpa_with_fourth=5;
					$gpa_without_fourth=$result->gpa_without_fourth;
				}

				
		echo "<div class='set header'>";
				
				echo '<h2>Rajshahi College, Rajshahi <br/></h2>';
				echo '<h3>'.$exam_name.'-'.date('Y').'</h3>';
				

		echo "</div>";
		echo "<div class='set'>";
			echo "<center><h3>Basic Information</h3></center>";
		/**General Iformation**/
			echo "<table class='pagination custom2' align='center' width='100%'>";
				echo "<tr>";
					echo "<th>Roll</th>";
					echo "<td>{$roll}</td>";
				echo "</tr>";					
				echo "<tr>";					
					echo "<th>Student ID</th>";
					echo "<td>{$student_id}</td>";
				echo "</tr>";					
				echo "<tr>";
					echo "<th>Name</th>";
					echo "<td>{$name}</td>";
				echo "</tr>";					
				echo "<tr>";		
					echo "<th>Father's Name</th>";
					echo "<td>{$father_name}</td>";
				echo "</tr>";					
				echo "<tr>";		
					echo "<th>Mother's Name</th>";
					echo "<td>{$mother_name}</td>";	
				echo "</tr>";					
				echo "<tr>";				
					echo "<th>Session</th>";
					echo "<td>{$ses}</td>";
				echo "</tr>";					
				echo "<tr>";
				if ($group=='commerce')
					$group='Business Studies';
				else if ($group=='arts')
					$group='Humanities';
					echo "<th>Group</th>";
					echo "<td>{$group}</td>";
				echo "</tr>";
			echo "</table>";			
	
			/***Marks Information***/
				echo "<center><h3>Result in Detail</h3></center>";
				echo "<table class='pagination custom' align='center' width='100%'>";
				/**Explede the string of individual subject and gpa like (Bangla/80)**/
				$subject_mark=explode(";",$marks_string);
				echo "<tr>";
					echo "<th class='left'>Name of the Subject</th>";
					echo "<th>Grade Obtained</th>";
				echo "</tr>";
				
				foreach($subject_mark as $sub_mark){
				/**Explode subject name and obtained gpa**/
					$mark_info=explode("/",$sub_mark);
					
					if(isset($mark_info[1]) && $mark_info[1]!=''){
						echo "<tr>";
							echo "<td class='left'>{$mark_info[0]}</td>";
							echo "<td>";
							if(isset($mark_info[1]) && $mark_info[0]!=''){
								echo $mark_info[1];
							}
							echo "</td>";
						echo "</tr>";
					}	
				}
				
			echo "<table>";
			$remark="Pass";
			if($gpa_with_fourth<=0) $remark="Fail";
			echo "<center><h3>GPA: ".number_format((float)$gpa_with_fourth, 2, '.', '')."<br/>Remark: {$remark}</h3></center>";
		echo "</div>";


			echo "<br/><center> <a href='' class='ff green no-print' style='width:100px !important;cursor:pointer'><b>Search Again</b></a> </center><br/>";?>
<button class="no-print ff green" onclick="downloadFunction()">Print Result</button>
<?php
			exit;
		}
		
	}
		
	}
	?> <?php
	echo "<fieldset class='set'>";
	echo "<legend>Search Panel</legend>";
	
	echo "<table class='' align='center' width='100%'>";
	
	echo "<form method='POST' action='".route('hsc_result_show')."'>";
	echo csrf_field();
	
	/**Distinct Exam name,group,session query for search option**/
	$results = DB::select("select distinct exam_name from hsc_result_student_show");
	
	echo "<tr>";
		echo "<td>Roll No.</td>";
		
		echo "<td>";
			echo "<input name='roll' type='text' placeholder='Enter Roll No.' />";
		echo "</td>";
		
	echo "<tr>";
	
	echo "<tr>";
		echo "<td>Group</td>";
		echo "<td>";
			echo "<select name='groups'>";
				echo "<option value=''>Select Group</option>";
				echo "<option value='science'>Science</option>";
				echo "<option value='commerce'>Commerce</option>";
				echo "<option value='arts'>Arts</option>";
			echo "</select>";
		echo "</td>";
		
	echo "<tr>";
	
	echo "<tr>";
		echo "<td>Exam Name</td>";
		
		echo "<td>";
			echo "<select name='exam_name'>";
				echo "<option value=''>Select Exam Name</option>";
				foreach($results as $result){
					echo "<option value='{$result->exam_name}'>{$result->exam_name}</option>";
				}
			echo "</select>";
		echo "</td>";
	echo "<tr>";
	
	echo "<tr>";
		echo "<td>Session</td>";
		
		echo "<td>";
		$results = DB::select("select distinct session from hsc_result_student_show");
		
		echo "<select name='session'>";
			echo "<option value=''>Select Session</option>";
			foreach($results as $result){
				echo "<option value='{$result->session}'>{$result->session}</option>";
			}
		echo "</select>";
		echo "</td>";
		
	echo "<tr>";
	
	
	echo "<tr>";
		echo "<td></td>";
		echo "<td>";
			echo "<input name='submit' class='ff blue' style='width:100px !important;cursor:pointer' value='Search' type='submit'/>";
		echo "</td>";
	echo "</tr>";		
	echo "</form>";
	
	echo "</table>";
	echo "</fieldset>";
?>

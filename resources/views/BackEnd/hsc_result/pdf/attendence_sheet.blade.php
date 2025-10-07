<?php ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style>


table, th, td {
	 
    border: 1px solid #000;
    border-collapse: collapse;
}
th, td {
	border: 1px solid #000;
    padding: 5px;
    text-align: left;    
}
.text-center{text-align: center;}
</style>
</head>


<body>
 <div style="text-align: center; width:100%; float: left; margin: 10px 0;"> <h2>Students Attendance Sheet </h2>    

<h2>{{$exam_name->name}}</h2>

<h3>Exam Date: {{$exam_date}}</h3>

<h3>Room No.: {{$room_no}}</h3>
<h3>Subject: {{$subject_name}}</h3>
</div>
		<table class='table table-bordered' width="100%">

			<tr>
                <th width="10%" class="text-center">Sl No.</th>
				<th width="15%" class="text-center" >Roll</th>
				<th width="50%" class="text-center">Name</th>
				 <!--th width="15%" class="text-center">Image</th-->				
				<th width="30%" class="text-center">Signature</th>				
			
			</tr>
                        <?php $i = 1; ?>
			@foreach($student_info_hsc as $attendance_info)

				<tr>
                                        <td class="text-center">{{$i}}</td>					
                                        <td class="text-center">{{ $attendance_info->class_roll }}</td>
					<td class="text-center">{{ $attendance_info->name}}</td>
					<!--td class="text-center">

<img width="40" height="40" src="{{ URL::to('/') }}/upload/college/hsc/{{$attendance_info->image}}" alt="..." >
</td-->
					 <td height="50"> </td>
				</tr>	
                           <?php $i++; ?>
			@endforeach

		</table>

</body>

</html>

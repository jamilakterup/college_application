<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{{config('settings.college_name')}}</title>
</head>

<body>
<style>
html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}

table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
body {font-family: 'FreeSerif',sans-serif;}
td.title{ font-size:30px; line-height:36px; color:#000;}
td.subtitle{ font-size:24px; line-height:30px; color:#000;}
td.mintitle{ font-size:20px; line-height:24px; color:#000;}
.table-bordered tr, .table-bordered td{ border:1px solid #cecece; padding:5px 10px;}
.table-bordered tr, .table-bordered th{ border:1px solid #cecece; padding:5px 10px;}
</style>
    <div class="invoice-table">
		<table class='table table-bordered'>

			<tr>
				<th>Student ID</th>
				<th>SSC Roll</th>			
				<th>Session</th>				
				<th>Class Roll</th>		
				<th>Name</th>				
				<th>Groups</th>	
				<th>Current Level</th>	
				<th>Contact Number</th>	
				<th>Guardian Number</th>	



			</tr>

			@foreach($hscstudents as $college)
                <?php $admited_student = DB::table('hsc_admitted_students_tc')->where('auto_id',$college->refference_id)->first();?>
				<tr class="">
					<td>{{ $college->id }}</td>
					<td>{{ $college->ssc_roll }}</td>
						
					<td>{{ $college->session }}</td>					
					<td>{{ $college->class_roll }}</td>					
					<td>{{ $college->name }}</td>					
					<td>{{ $college->groups }}</td>					
					<td>{{ $college->current_level }}</td>	
					<td>{{ $college->contact_no}}</td>	
					<td>{{$admited_student->guardian_phone}}</td>					

				</tr>	

			@endforeach

		</table>

   </div>

</body>
</html>

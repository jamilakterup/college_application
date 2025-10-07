@php
	use App\Models\StudentInfoHsc;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Top Result</title>

	<style type="text/css">
		table{
			border-collapse: collapse;
		}
		table th, td{
			font-size: 10px;
			padding: 2px;
			border: 1px solid #ddd;
		}
	</style>
</head>
<body>

	<p style="font-weight: bold;font-size: 16px;text-transform: uppercase; text-align: center; margin-bottom: 0;">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</p>

	<p style="margin: 0px;font-weight: bold;font-size: 13px;text-transform: uppercase; text-align: center;">
		{{$exam->name}} - {{$exam_year}}
	</p>

	<p style="text-align: center;">Result Summery</p>
	@php
        $details = '';
        $passDetails = '';
        $failDetails = '';
        $totalStuDetails = '';
        $presentStuDetails = '';
        $absentStuDetails = '';
        $passPercentDetails = '';
        $gradeWiseRows = [];
        $totalPass = 0;
        $totalFail = 0;
        $totalExaminee = StudentInfoHsc::where('session', $session)->count() ?? 0;

        foreach($summary as $group => $val){
            $passCount = count(array_filter($data[$group] ?? [], fn($student) => $student['grade'] != 'F'));
            $failCount = count(array_filter($data[$group] ?? [], fn($student) => $student['grade'] == 'F'));
            
            $details .= "<th>".($group ?? '')."</th>";
            
            $totalPass += $passCount;
            $passDetails .= "<td align='center'>{$passCount}</td>";

			$totalFail += $failCount;
            $failDetails .= "<td align='center'>{$failCount}</td>";
            
            $groupTotalStu = StudentInfoHsc::where('groups', $group)
                ->where('session', $session)
                ->count() ?? 0;
            
            $totalStuDetails .= "<td align='center'>{$groupTotalStu}</td>";
            $presentStuDetails .= "<td align='center'>".(count($data[$group] ?? []))."</td>";
            $absentStuDetails .= "<td align='center'>".($groupTotalStu - count($data[$group] ?? []))."</td>";
            
            $totalStudents = $passCount + $failCount;
            $passPercentage = $totalStudents > 0 ? ($passCount / $totalStudents) * 100 : 0;
            $passPercentDetails .= "<td align='center'>".number_format($passPercentage, 2)."%</td>";
        }

        foreach ($scales as $letter) {
            foreach (array_keys($summary) as $group) {
                $totalLetterStudent = count(array_filter($data[$group] ?? [], 
                    fn($student) => $student['grade'] == $letter
                ));
                $gradeWiseRows[$letter][$group] = $totalLetterStudent;
            }
        }

	@endphp
	<table width="100%">
		<thead>
			<tr>
				<th>Details</th>
				{!!$details!!}
				<th align='center'>Total</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>Total Examinees</td>
				{!!$totalStuDetails!!}
				<td align='center'>{{$totalExaminee}}</td>
			</tr>
			<tr>
				<td>Present Examinees</td>
				{!!$presentStuDetails!!}
				<td align='center'>{{$totalPass+$totalFail}}</td>
			</tr>
			<tr>
				<td>Absent Examinees</td>
				{!!$absentStuDetails!!}
				<td align='center'>{{$totalExaminee-($totalPass+$totalFail)}}</td>
			</tr>

			<tr>
				<td>No. of Examinees (Pass)</td>
				{!!$passDetails!!}
				<td align='center'>{{$totalPass}}</td>
			</tr>
			<tr>
				<td>No. of Examinees (Fail)</td>
				{!!$failDetails!!}
				<td align='center'>{{$totalFail}}</td>
			</tr>

			@foreach($gradeWiseRows as $letter => $groupData)
				<tr>
					<td>{{$letter}}</td>
					@foreach($groupData as $val)
						<td align='center'>{{$val}}</td>
					@endforeach
					<td align='center'>{{array_sum(array_values($groupData))}}</td>
				</tr>
			@endforeach

			<tr>
				@php
					$totalStudents = $totalPass + $totalFail;
					if ($totalStudents > 0) {
					    $passPercentage = ($totalPass / $totalStudents) * 100;
					} else {
					    $passPercentage = 0;
					}
					$totalPassPercent = number_format($passPercentage, 2);
				@endphp

				<td>Pass Percent</td>
				{!!$passPercentDetails!!}
				<td align="center">
					{{$totalPassPercent}}%
				</td>
			</tr>
		</tbody>
	</table>

	<h3 style="text-align: center; margin-top: 5px;margin-bottom: 0;">Top: {{$total_position}}</h3>
	@foreach($data as $group => $studentIds)
		@php
            $numberOfPresentPassStudents = array_filter($studentIds ?? [], fn($student) => $student['grade'] != 'F');
        @endphp
        <h4 style="text-align: center;margin-top: 2px; margin-bottom: 1px;text-decoration: underline;">
            Group: {{$group ?? ''}}
        </h4>
	    <table width="100%">
	    	<thead>
	    		<tr>
		    		<th>SL</th>
		    		<th>Name</th>
		    		<th>Roll</th>
		    		<th>Contact No</th>
		    		<th>Total Mark</th>
		    		<th>GPA</th>
		    		<th>Position</th>
	    		</tr>
	    	</thead>
		    <tbody>
                @foreach(array_slice($numberOfPresentPassStudents, 0, $total_position) as $key => $student)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$student['name'] ?? ''}}</td>
                        <td align="center" width="15%">{{$student['student_id'] ?? ''}}</td>
                        <td align="center">{{$student['contact_no'] ?? ''}}</td>
                        <td align="center" width="10%">{{$student['total_mark'] ?? '0'}}</td>
                        <td align="center">{{$student['cgpa'] ?? '0'}}</td>
                        <td align="center">{{$student['position'] ?? '0'}}</td>
                    </tr>
                @endforeach
            </tbody>
	    </table>
	@endforeach
	
</body>
</html>
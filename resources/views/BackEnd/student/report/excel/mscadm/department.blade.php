@php
    $ids = $data->pluck('id')->toArray();
    $student_ids = [];
    foreach($ids as $id) $student_ids[] = (string) $id;

    $department = DB::table('student_info_masters')->whereIn('id',$student_ids)->where('session', $request->session)->groupBy('dept_name')->get();

	$departments = $department->pluck('dept_name')->toArray();

	$head_colspan = 9;

	$grand_total_student = 0;
	$grand_total_amount = 0;
@endphp

<table border="1">

	<thead>
		<tr>
			<th style="text-align: center; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</th>
		</tr>
		<tr>
			<td style="text-align: center; color: #C00000; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">{{$request->current_level ?? 'Masters'}} Admission {{$request->session}}</td>
		</tr>
		<tr>
			<td style="text-align: center; background-color: #FFFF00; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">Department Wise Statement of {{$request->current_level}} Admission {{$request->session}}</td>
		</tr>

		<tr>
			<td style="background-color: #A9D08E;font-weight: bold;" colspan="6">Date of Duration: {{$request->from_date != '' ? $request->from_date.' To '.$request->to_date :''}}</td>
		</tr>

		<tr>
			<td colspan="{{$head_colspan}}" style="text-align: right;font-weight: bold;">Print Date: {{date('d-m-Y')}}</td>
		</tr>

	</thead>
	<tbody border="1">
		<tr>
			<td style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">SI No</td>
			<td colspan="3" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Department</td>		
			<td colspan="3" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Total Number of Students</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Total Amount</td>		
		</tr>

		@foreach ($departments as $key =>  $dept)
			@php
				$dept_students = DB::table('student_info_masters')->leftJoin('admission_students','student_info_masters.id','=','admission_students.id')->whereIn('student_info_masters.id', $student_ids)->where('student_info_masters.session', $request->session)->where('student_info_masters.dept_name', $dept)->select('student_info_masters.id','admission_students.total_amount','admission_students.transaction_id','admission_students.date','student_info_masters.merit_status')->get();

				$grand_total_student += count($dept_students);
				$grand_total_amount += $dept_students->sum('total_amount');
			@endphp
			<tr>
				<td style="border: 1px solid #ddd;text-align:center;">{{$key+1}}</td>
				<td colspan="3" style="border: 1px solid #ddd; background-color: #FCE4D6;">{{$dept}}</td>
				<td colspan="3" style="border: 1px solid #ddd;text-align:center;">{{count($dept_students)}}</td>
				<td colspan="2" style="border: 1px solid #ddd;text-align:center;">{{$dept_students->sum('total_amount')}}</td>
			</tr>
		@endforeach

		<tr>
			<td colspan="4" style="border: 1px solid #ddd; background-color: #e17055; text-align: center; font-weight: bold;">Total</td>
			<td colspan="3" style="border: 1px solid #ddd; background-color: #e17055; text-align: center; font-weight: bold;">{{$grand_total_student}}</td>
			<td colspan="2" style="border: 1px solid #ddd; background-color: #e17055; text-align: center; font-weight: bold;">{{$grand_total_amount}}</td>
		</tr>

		{{-- signature section --}}
		<tr></tr>
		<tr></tr>
		<tr></tr>
		@php
			$signatures = ['Dealing assistant','Cashiar','Accountant','Principal'];
			$colspan_sig = intval(($head_colspan)/count($signatures));
		@endphp
		<tr>
			@foreach ($signatures as $sig)
				<td colspan="{{$colspan_sig}}" style="text-align: right;">{{$sig}}</td>
			@endforeach
		</tr>
		{{-- end signature section --}}
	</tbody>
</table>
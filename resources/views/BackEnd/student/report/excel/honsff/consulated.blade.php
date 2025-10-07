@php
    $student_ids = $data->pluck('id')->toArray();
	//start checking dept
	$department = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->groupBy('dept_name');

	$departments = $department->pluck('dept_name')->toArray();

	$head_colspan = count($departments)+4;

	$grand_total_student = 0;
	$grand_total_amount = 0;
	$grand_total_dept_student = [];

	$slip_groups = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->groupBy('total_amount','slip_name')->orderBy('total_amount', 'asc')->get();
@endphp

<table border="1">

	<thead>
		<tr>
			<th style="text-align: center; font-size: 15px;font-weight: bold;" colspan="{{$head_colspan}}">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</th>
		</tr>
		<tr>
			<td style="text-align: center; color: #C00000; font-size: 15px;font-weight: bold;" colspan="{{$head_colspan}}">{{$request->current_level}} From Fillup {{$request->exam_year}}</td>
		</tr>
		<tr>
			<td style="text-align: center; background-color: #FFC000; font-size: 15px;font-weight: bold;" colspan="{{$head_colspan}}">Consulated Statement of {{$request->current_level}} Form Fillup {{$request->exam_year}}</td>
		</tr>

		<tr>
			<td style="background-color: #A9D08E;font-weight: bold;" colspan="5">Date of Duration: {{$request->from_date != '' ? $request->from_date.' To '.$request->to_date :''}}</td>
		</tr>

		<tr>
			<td style="text-align: right; font-size: 14px;font-weight: bold;" colspan="{{$head_colspan}}">Print Date: {{date('d-m-Y')}}</td>
		</tr>

	</thead>
	<tbody border="1">

		{{-- general student report --}}
		{{-- table header --}}
		<tr>
			<td rowspan="2" style="background-color: #FFC000; border: 1px solid #ddd; font-weight: bold;">Slip Name/Type</td>
			<td rowspan="2" style="background-color: #FFC000; border: 1px solid #ddd; font-weight: bold;">Taka</td>
			<td colspan="{{count($departments)}}" style="text-align: center;background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Number of Students</td>
			
			<td rowspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Total Student</td>
			<td rowspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Total Taka</td>
		</tr>

		<tr>
			@foreach ($departments as $dept)
				@php
					$grand_total_dept_student[$dept] = 0;
				@endphp
				<td style="background-color: #B7DEE8; border: 1px solid #ddd;">{{$dept}}</td>
			@endforeach
		</tr>

		{{-- end table header --}}


		{{-- start looping formfillup students report --}}

		@foreach($slip_groups as $slip){
			<tr>
				{{-- first 2 column --}}
				<td style="text-align: center; background-color: #FFC000; border: 1px solid #ddd;">{{$slip->slip_name}}</td>
				<td style="text-align: center; background-color: #FFC000; border: 1px solid #ddd;">{{$slip->total_amount}}</td>

				{{-- start department student number --}}
				@php
					$total_number_of_student = 0;
					$sub_total_types_amount = 0
				@endphp
				@foreach ($departments as $dept)
					@php
						$dept_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $dept)->where('total_amount', $slip->total_amount)->where('slip_name', $slip->slip_name)->get();

						$total_number_of_student += count($dept_students);
						$grand_total_dept_student[$dept] += count($dept_students);
						$sub_total_types_amount += $dept_students->sum('total_amount');
					@endphp
					{{-- number of dept students --}}
					<td style="border: 1px solid #ddd;">{{count($dept_students) > 0 ? count($dept_students): '' }}</td>
				@endforeach

				@php 
					$grand_total_student +=$total_number_of_student;
					$grand_total_amount += $sub_total_types_amount;
				@endphp

					{{-- number of total dept students --}}
					<td style="border: 1px solid #ddd;">{{$total_number_of_student}}</td>

					{{-- multiply with amounts and total dept students --}}
					<td style="border: 1px solid #ddd;">{{($sub_total_types_amount)}}</td>
			</tr>

		@endforeach

		{{-- end student reports --}}
		
		{{-- g total row --}}
		<tr>
			<td colspan="2" style="background-color: #e17055; border: 1px solid #ddd; font-weight: bold;">Grand Total</td>
			@foreach ($departments as $dept)
				<td style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold;">{{$grand_total_dept_student[$dept]}}</td>
			@endforeach
			<td style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold;">{{$grand_total_student}}</td>
			<td style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold;">{{$grand_total_amount}}</td>
		</tr>
		{{-- end g total row --}}


		{{-- signature section --}}
		<tr></tr>
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
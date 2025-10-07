@php
    $student_ids = $data->pluck('id')->toArray();

	$head_colspan = 9;

	$grand_total_student = 0;
	$grand_total_amount = 0;
	$sn = 1;

	$slip_groups = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->groupBy('total_amount','slip_name')->orderBy('total_amount', 'asc')->get();
	
@endphp

<table border="1">

	<thead>
		<tr>
			<th style="text-align: center; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</th>
		</tr>
		<tr>
			<td style="text-align: center; color: #C00000; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">{{$request->current_level}} From Fillup {{$request->exam_year}}</td>
		</tr>
		<tr>
			<td style="text-align: center; background-color: #FFFF00; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">Statement of {{$request->dept_name}}</td>
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
			<td colspan="{{$head_colspan}}" style="text-align: center;background-color: #fdcb6e; border: 1px solid #ddd;">Number of General student</td>
		</tr>

		<tr>
			<td style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">SI No</td>
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Slip Name/Type</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Taka</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Number of Students</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Total Amount</td>	
		</tr>

		{{-- start looping formfillup types --}}

		@foreach($slip_groups as $slip){
			<tr>
				<td style="text-align: center; border: 1px solid #ddd;">{{$sn}}</td>
				<td colspan="2" style="text-align: center; background-color: #FFF2CC; border: 1px solid #ddd;">{{$slip->slip_name}}</td>
				<td colspan="2" style="text-align: center; border: 1px solid #ddd;">{{$slip->total_amount}}</td>

				{{-- start groups student number --}}
				@php
					$total_number_of_student = 0;
					$sub_total_types_amount = 0;
					$group_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('total_amount', $slip->total_amount)->where('slip_name', $slip->slip_name)->get();

					$total_number_of_student += count($group_students);
					$sub_total_types_amount += $group_students->sum('total_amount');

					$grand_total_student +=$total_number_of_student;
					$grand_total_amount += $sub_total_types_amount;
					$sn++;
				@endphp

					{{-- number of total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{$total_number_of_student}}</td>

					{{-- multiply with amounts and total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{($sub_total_types_amount)}}</td>
			</tr>

		@endforeach

		{{-- end student reports --}}

		{{-- start grand total section --}}
		<tr>
			<td style="background-color: #e17055; border: 1px solid #ddd;"></td>
			<td colspan="2" style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold;">Total</td>
			<td colspan="2" style="background-color: #e17055; border: 1px solid #ddd;"></td>
			<td colspan="2" style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold; text-align: center;">{{$grand_total_student}}</td>
			<td colspan="2" style="background-color: #e17055; border: 1px solid #ddd;font-weight: bold;text-align: center;">{{$grand_total_amount}}</td>
		</tr>
		{{-- end grand total section --}}

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
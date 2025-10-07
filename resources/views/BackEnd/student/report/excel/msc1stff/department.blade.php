@php
    $student_ids = $data->pluck('id')->toArray();
	//start checking dept
	$ff_group = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->groupBy('dept_name');
    // check permission
    query_has_permissions($ff_group, ['dept_name', 'level_study','session', 'exam_year']);

    $ff_groups = $ff_group->pluck('dept_name')->toArray();

    $departments = DB::table('departments')->whereIn('dept_name', $ff_groups)->get();


	$head_colspan = 9;

	// start checking Configurations
    $config = Configurations()::where('details->session', $request->session)->where('details->current_level', $request->current_level)->first();

    $config_details = json_decode($config->details);

    $general_min_sub = $config_details->general->min_length;
    $special_min_sub = $config_details->special->min_length;

	$grand_total_student = 0;
	$grand_total_amount = 0;
	$sn = 1;

	$general_student_papers = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('dept_name', $request->dept_name)->where('exam_year', $request->exam_year)->where('total_papers' , '<=', $general_min_sub)->where('student_type', 'general')->where('pay_type', 'paper')->orderBy('total_papers', 'asc')->groupBy('total_papers')->pluck('total_papers');

	$generals_type_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'general')->where('pay_type', 'general')->groupBy('total_amount')->orderBy('total_amount', 'asc')->get();

	$special_student_papers = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('total_papers' , '<=', $special_min_sub)->where('student_type', 'special')->where('pay_type', 'paper')->orderBy('total_papers', 'asc')->groupBy('total_papers')->pluck('total_papers');

	$special_type_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'special')->where('pay_type', 'general')->groupBy('total_amount')->orderBy('total_amount', 'asc')->get();


@endphp

<table border="1">

	<thead>
		<tr>
			<th style="text-align: center; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">Govt. M M College, Jashore</th>
		</tr>
		<tr>
			<td style="text-align: center; color: #C00000; font-size: 15px; font-weight: bold;" colspan="{{$head_colspan}}">Preliminary to Masters From Fillup {{$request->exam_year}}</td>
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
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;">Category/Type</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Taka</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Number of Students</td>		
			<td colspan="2" style="background-color: #B7DEE8; border: 1px solid #ddd; font-weight: bold;text-align:center;">Total Amount</td>	
		</tr>

		{{-- start looping papers --}}
		@foreach ($general_student_papers as $paper)
			@php

				$paper_amount = $config_details->general->paper_amount->$paper;
				$total_number_of_paper_student = 0;
				$sub_total_papers_amount = 0;
			@endphp

			<tr>
				{{-- first 2 column --}}
				<td style="text-align: center; border: 1px solid #ddd;">{{$sn}}</td>
				<td colspan="2" style="text-align: center; background-color: #FFF2CC; border: 1px solid #ddd;">{{$paper}} Paper</td>
				<td colspan="2" style="text-align: center; border: 1px solid #ddd;">{{$paper_amount}}</td>
				@php
					$dept_paper_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'general')->where('pay_type', 'paper')->where('total_papers', $paper)->get();

					$total_number_of_paper_student += count($dept_paper_students);
					$sub_total_papers_amount += $dept_paper_students->sum('total_amount');

					$grand_total_student +=$total_number_of_paper_student;
					$grand_total_amount += $sub_total_papers_amount;
					$sn++;
				@endphp
					{{-- number of total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{$total_number_of_paper_student}}</td>

					{{-- multiply with paper_amount and total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{($sub_total_papers_amount)}}</td>
			</tr>

		@endforeach
		{{-- end looping papers --}}

		{{-- start looping formfillup types --}}

		@foreach($generals_type_students as $type){
			<tr>
				<td style="text-align: center; border: 1px solid #ddd;">{{$sn}}</td>
				<td colspan="2" style="text-align: center; background-color: #FFF2CC; border: 1px solid #ddd;">{{ucfirst($type->formfillup_type)}}</td>
				<td colspan="2" style="text-align: center; border: 1px solid #ddd;">{{$type->total_amount}}</td>

				{{-- start department student number --}}
				@php
					$total_number_of_student = 0;
					$sub_total_types_amount = 0;
					$dept_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'general')->where('pay_type', 'general')->where('total_amount', $type->total_amount)->get();

					$total_number_of_student += count($dept_students);
					$sub_total_types_amount += $dept_students->sum('total_amount');

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

		{{-- end looping formfillup types --}}

		{{-- end general student reports --}}

		{{-- start special students --}}

		@if(count($special_student_papers) > 0 || count($special_type_students) > 0)
			{{-- table header --}}
			<tr>
				<td colspan="{{$head_colspan}}" style="text-align: center;background-color: #fdcb6e; border: 1px solid #ddd;">Number of Special Student</td>
			</tr>

			{{-- end table header --}}
		@endif

		{{-- start looping papers --}}
		@foreach ($special_student_papers as  $paper)
			@php

				$paper_amount = $config_details->general->paper_amount->$paper;
				$total_number_of_paper_student = 0;
				$sub_total_papers_amount = 0;
			@endphp

			<tr>
				{{-- first 2 column --}}
				<td style="text-align: center; border: 1px solid #ddd;">{{$sn}}</td>
				<td colspan="2" style="text-align: center; background-color: #FFF2CC; border: 1px solid #ddd;">{{$paper}} Paper</td>
				<td colspan="2" style="text-align: center; border: 1px solid #ddd;">{{$paper_amount}}</td>
				@php
					$dept_paper_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'special')->where('pay_type', 'paper')->where('total_papers', $paper)->get();

					$total_number_of_paper_student += count($dept_paper_students);
					$sub_total_papers_amount += $dept_paper_students->sum('total_amount');

					$grand_total_student +=$total_number_of_paper_student;
					$grand_total_amount += $sub_total_papers_amount;
					$sn++;
				@endphp
					{{-- number of total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{$total_number_of_paper_student}}</td>

					{{-- multiply with paper_amount and total dept students --}}
					<td colspan="2" style="border: 1px solid #ddd;text-align: center;">{{($sub_total_papers_amount)}}</td>
			</tr>

		@endforeach
		{{-- end looping papers --}}

		{{-- start looping formfillup types --}}

		@foreach($special_type_students as $type){
			<tr>
				<td style="text-align: center; border: 1px solid #ddd;">{{$sn}}</td>
				<td colspan="2" style="text-align: center; background-color: #FFF2CC; border: 1px solid #ddd;">{{ucfirst($type->formfillup_type)}}</td>
				<td colspan="2" style="text-align: center; border: 1px solid #ddd;">{{$type->total_amount}}</td>

				{{-- start department student number --}}
				@php
					$total_number_of_student = 0;
					$sub_total_types_amount = 0;
					$dept_students = DB::table('form_fillup')->whereIn('id', $student_ids)->where('level_study', $request->current_level)->where('exam_year', $request->exam_year)->where('dept_name', $request->dept_name)->where('student_type', 'special')->where('pay_type', 'general')->where('total_amount', $type->total_amount)->get();

					$total_number_of_student += count($dept_students);
					$sub_total_types_amount += $dept_students->sum('total_amount');

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

		{{-- end looping formfillup types --}}

		{{-- end special student reports --}}

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
@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

				@can('hsc.admission.index')
        	<div class="d-flex justify-content-between">
        		<button class="btn btn-primary" data-target="#filter-modal" data-toggle="modal" type="button"><i class="fas fa-filter"></i> Filter HSC 2nd Year Admission Report</button>
        		{!! Form::open(['route' => 'report.hsc2nd.admission.generate', 'method'=> 'post', 'target' => '_blank']) !!}
						{!! Form::hidden('session', $session) !!}
						{!! Form::hidden('groups', $groups) !!}
						{!! Form::hidden('current_level', $current_level) !!}
						{!! Form::hidden('exam_year', $exam_year) !!}
						{!! Form::hidden('from_date', $from_date) !!}
						{!! Form::hidden('to_date', $to_date) !!}
					{{-- <button class="btn btn-primary" type="submit" value><i class="fas fa-file-pdf"></i> Generate Report</button> --}}

					<div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" id="reportDropdown"
              data-toggle="dropdown" aria-expanded="false">
              Download Report
            </button>
            <div class="dropdown-menu" aria-labelledby="reportDropdown" role="menu">
              <button class="dropdown-item" name="type" type="submit" value="pdf"><i class="fas fa-file-pdf"></i> Generate PDF</button>
              <button class="dropdown-item" name="type" type="submit" value="csv"><i class="fas fa-file-csv"></i> Generate CSV</button>
              <button class="dropdown-item" name="type" type="submit" value="csv_dept_report"><i class="fas fa-file-csv"></i> Generate Departmental</button>
            </div>
          </div>
				{!! Form::close() !!}
        </div>
			<br>

			@if($student_id != '' || $groups !='' || $current_level!='' || $session!='' || $from_date !='' || $to_date !='')
			<table class="table input-mark mb-0">
				<caption class="mb-0">
					Student Id: <span>{{ $student_id }}</span> 
					Group: <span>{{ $groups }}</span> 
					Current Level: <span>{{ $current_level }}</span> 					
					Session: <span>{{ $session }}</span>
					Exam Year: <span>{{ $exam_year }}</span>
					From Date: <span>{{ $from_date }}</span>
					To Date: <span>{{ $to_date }}</span>
				</caption>
			</table>
			@endif

			@if($num_rows>0)
			 <h3> Total Number Of Student: {{$num_rows}}</h3> 
			@endif

			@if ($total_amount > 0)
				<strong style="font-size: 16px;">Total Amount : {{$total_amount}}</strong><br/>
			@endif

      	<table class="table table-hover defDTable table-striped w-full cell-border">
            <thead>
              <tr>
								<th>Student ID</th>
								<th>Student Name</th>
								<th>Session</th>
								<th>Level Of Study</th>				
								<th>Faculty</th>				
								<th>Department</th>	
								<th>Course</th>		
								<th>Total Amount</th>					
								<th>Exam Year</th>	
								<th>Paid Date</th>	
							</tr>
            </thead>
            
            <tbody>

				@foreach($hscstudents as $college)

					<tr class="">
						<tr class="{{ Study::updatedRow('id', $college->id) }}">
						<td>{{ $college->id }}</td>
						<td>{{ $college->name }}</td>
						<td>{{ $college->session }}</td>
						<td>{{ $college->level_study }}</td>					
						<td>{{ $college->groups }}</td>									
						<td>{{ $college->dept_name }}</td>	
						<td>{{ $college->course }}</td>				
						<td>{{ $college->total_amount }}</td>					
						<td>{{ $college->exam_year }}</td>	
						<td>{{ $college->date }}</td>
					</tr>	
						
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $hscstudents->appends(Request::except('page'))->links() }}

          {{-- end hsc_admission_index permission--}}
          @endcan
          
        </div>
      </div>

{{-- modal --}}
<div class="modal fade" id="filter-modal" aria-hidden="true" aria-labelledby="examplePositionSidebar" role="dialog" tabindex="-1">
  <div class="modal-dialog modal-simple">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Filter HSC 2nd Year Admission Report</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route'=> 'report.hsc2nd.admission', 'method'=> 'post', 'class' => 'form-horizontal']) !!}
		  		<div class="form-group">
				    {!! Form::text('id', $student_id, ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('groups', selective_hsc_groups(), $groups, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('groups')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('current_level', selective_multiple_hsc_level(), $current_level, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('current_level')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('session')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year, ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('exam_year')!!}
				  </div>

				  <div class="form-group">
					  <div class="input-daterange" data-plugin="datepicker">
              <div class="input-group mb-2">
                <span class="input-group-addon">
                  <i class="icon wb-calendar" aria-hidden="true"></i>
                </span>
                {!! Form::text('from_date', $from_date, ['class'=> 'form-control', 'placeholder' => 'From Date', 'autocomplete'=> 'off']) !!}
              </div>
              <div class="input-group">
                <span class="input-group-addon">to</span>
                {!! Form::text('to_date', $to_date, ['class'=> 'form-control', 'placeholder' => 'To Date', 'autocomplete'=> 'off']) !!}
              </div>
            </div>
				  </div>

		  <button type="submit" class="btn btn-info btn-block">Search</button>
		  <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>
		{!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
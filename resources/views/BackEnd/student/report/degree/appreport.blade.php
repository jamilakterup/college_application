@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Degree Application Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

				@can('degree.admission.index')
        	<div class="d-flex justify-content-between">
        		<button class="btn btn-primary" data-target="#filter-modal" data-toggle="modal" type="button"><i class="fas fa-filter"></i> Filter Degree Application Report</button>
        		{!! Form::open(['route' => 'report.degree.application.generate', 'method'=> 'post', 'target' => '_blank']) !!}
						{!! Form::hidden('session', $session) !!}
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
              <button class="dropdown-item" type="submit" name="type" value="pdf"><i class="fas fa-file-pdf"></i> Generate PDF</button>
              <button class="dropdown-item" type="submit" name="type" value="csv"><i class="fas fa-file-csv"></i> Generate CSV</button>
            </div>
          </div>
				{!! Form::close() !!}
        </div>
			<br>

			@if($exam_year!='' || $session!='' || $from_date !='' || $to_date !='' || $admission_roll!='')
			<table class="table input-mark mb-0">
				<caption class="mb-0">
					Admission Roll: <span>{{ $admission_roll }}</span>
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
								<th>Admission Roll</th>
								<th>Student Name</th>
								<th>Contact No</th>
								<th>Father Name</th>
								<th>Mother Name</th>
								<th>Session</th>
								<th>Total Amount</th>					
								<th>Exam Year</th>	
								<th>Paid Date</th>
							</tr>
            </thead>
            
            <tbody>

						@foreach($applications as $application)

							<tr class="{{ Study::updatedRow('id', $application->id) }}">
								<td>{{ $application->admission_roll }}</td>
								<td>{{ $application->name }}</td>
								<td>{{ $application->contact_no }}</td>
								<td>{{ $application->father_name }}</td>
								<td>{{ $application->mother_name }}</td>
								<td>{{ $application->session }}</td>			
								<td>{{ $application->total_amount }}</td>					
								<td>{{ $application->exam_year }}</td>	
								<td>{{ $application->date }}</td>
							</tr>	

						@endforeach
            </tbody>
          </table>
          {{ $applications->appends(Request::except('page'))->links() }}

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
        <h4 class="modal-title">Filter Degree Admission Report</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route'=> 'report.degree.application', 'method'=> 'post', 'class' => 'form-horizontal']) !!}
		  		<div class="form-group">
				    {!! Form::text('admission_roll', $admission_roll, ['class'=> 'form-control', 'placeholder' => 'Admission Roll']) !!}
				  </div>

				  <div class="form-group">
					    {!! Form::select('dept_name', selective_degree_subjects(), $dept_name, ['class'=>'form-control dept', 'autocomplete'=> 'off']) !!}
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
@include('BackEnd.common.drop_down_js')
	<script>
		
	</script>
@endpush
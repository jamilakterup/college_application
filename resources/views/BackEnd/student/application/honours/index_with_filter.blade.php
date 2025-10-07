@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Form Fillup List')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

        	<div class="d-flex justify-content-between">
        		<button class="btn btn-primary" data-target="#filter-modal" data-toggle="modal" type="button"><i class="fas fa-filter"></i> Filter</button>
        		{!! Form::open(['route' => 'student.formfillup.honreport', 'method'=> 'post', 'target' => '_blank']) !!}
					{!! Form::hidden('session', $session) !!}
					{!! Form::hidden('dept_name', $dept_name) !!}
					{!! Form::hidden('exam_year', $exam_year) !!}
					{!! Form::hidden('level', $level) !!}
					{!! Form::hidden('id', $student_id) !!}
					<button class="btn btn-primary" type="submit"><i class="fas fa-file-pdf"></i> Generate Report</button>
				{!! Form::close() !!}
        	</div>
			<br>
			<table class="table input-mark mb-0">
				<caption class="mb-0">
					Student Id: <span>{{ $student_id }}</span> 
					Group: <span>{{ $dept_name }}</span> 
					Current Level: <span>{{ $level }}</span> 					
					Session: <span>{{ $session }}</span> 
					Exam Year: <span>{{ $exam_year }}</span>
				</caption>
			</table>

			<div class="d-flex justify-content-between">
				<div>
					@if ($num_rows > 0)
						<strong>Total Number Of Student : {{$num_rows}}</strong><br/>
						<strong>Total Amount: {{$total_amount}}</strong>
					@endif
				</div>
				<form class="form-inline">
					<div class="form-group">
						{!! Form::label('search', 'Search:' , ['class' => 'form-control-label']) !!}
					    {!! Form::text('search', null, ['class'=>'form-control dt-search', 'autocomplete'=> 'off', 'placeholder'=> 'Search']) !!}
					  </div>
				</form>
			</div>
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

				@foreach($form_fillup as $form)

					<tr class="{{ Study::updatedRow('id', $form->id) }}">
						<td>{{ $form->id }}</td>
						<td>{{ $form->name }}</td>
						<td>{{ $form->session }}</td>
						<td>{{ $form->level_study }}</td>					
						<td>{{ $form->groups }}</td>									
						<td>{{ $form->dept_name }}</td>	
						<td>{{ $form->course }}</td>				
						<td>{{ $form->total_amount }}</td>					
						<td>{{ $form->exam_year }}</td>	
						<td>{{ $form->date }}</td>					
						
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $form_fillup->links() }}
        </div>
      </div>

{{-- modal --}}
<div class="modal fade" id="filter-modal" aria-hidden="true" aria-labelledby="examplePositionSidebar" role="dialog" tabindex="-1">
  <div class="modal-dialog modal-simple modal-sidebar modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Filter Honours Form Fillup</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route'=> 'student.formfillup.honours', 'method'=> 'post', 'class' => 'form-horizontal']) !!}
		  <div class="form-group">
		    {!! Form::text('student_id', $student_id, ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
		  </div>

		  <div class="form-group">
		    {!! Form::select('dept_name', create_option_array('departments', 'dept_name', 'dept_name', 'Department'), $dept_name, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
					{!!invalid_feedback('dept_name')!!}
		  </div>

		  <div class="form-group">
		    {!! Form::select('level', selective_multiple_honours_level(), $level, ['class'=>'form-control level', 'autocomplete'=> 'off']) !!}
					{!!invalid_feedback('level')!!}
		  </div>

		  <div class="form-group">
		    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
					{!!invalid_feedback('session')!!}
		  </div>

		  <div class="form-group">
		    {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year, ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
					{!!invalid_feedback('exam_year')!!}
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
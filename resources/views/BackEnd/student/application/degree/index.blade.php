@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Degree Application List')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

        	<div class="col-md-12 d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.application.degree', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::text('admission_roll', $admission_roll, ['class'=> 'form-control', 'placeholder' => 'Admission Roll']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', selective_degree_subjects(), $dept_name, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('dept_name')!!}
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
				    {!! Form::select('registration_type', [''=>'--Select One--','Application'=> 'Application', 'Registration' => 'Registration'], $registration_type, ['class'=>'form-control registration_type', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('registration_type')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::text('date', $date, ['class'=> 'form-control date', 'placeholder' => 'Date']) !!}
				  </div>

				  <button type="submit" class="btn btn-info">Search</button>
				{!! Form::close() !!}

			</div>
			<br>
			<div class="d-flex justify-content-between">
				
				<div>
					@if ($num_rows > 0)
						<strong>Total Number Of Student : {{$num_rows}}</strong><br/>
						<strong>Total Amount: {{$total_amount}}</strong>
					@endif
				</div>

				{!! Form::open(['route' => 'student.application.degreeAppreport', 'method'=> 'post', 'target' => '_blank']) !!}
					{!! Form::hidden('session', $session) !!}
					{!! Form::hidden('dept_name', $dept_name) !!}
					{!! Form::hidden('exam_year', $exam_year) !!}
					{!! Form::hidden('level', $level) !!}
					{!! Form::hidden('admission_roll', $admission_roll) !!}
					{!! Form::hidden('registration_type', $registration_type) !!}
					<button class="btn btn-primary" type="submit" name="type" value="pdf"><i class="fas fa-file-pdf"></i> Generate PDF</button>
					<button class="btn btn-primary" type="submit" name="type" value="csv"></i><i class="fas fa-file-csv"></i> Generate CSV</button>
				{!! Form::close() !!}
			</div>
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
								<th>Admission Roll</th>
								<th>Student Name</th>
								<th>Deptartment Name</th>
								<th>Contact No</th>
								<th>Session</th>
								<th>Exam Year</th>
								<th>Registration Type</th>		
								<th>Total Amount</th>					
								<th>Paid Date</th>
							</tr>
            </thead>
            
            <tbody>

				@foreach($applications as $application)

					<tr class="{{ Study::updatedRow('id', $application->id) }}">
						<td>{{ $application->admission_roll }}</td>
						<td>{{ $application->name }}</td>
						<td>{{ $application->dept_name }}</td>
						<td>{{ $application->contact_no }}</td>
						<td>{{ $application->session }}</td>			
						<td>{{ $application->exam_year }}</td>	
						<td>{{ $application->registration_type }}</td>	
						<td>{{ $application->total_amount }}</td>					
						<td>{{ $application->date }}</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $applications->appends(Request::except('page'))->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
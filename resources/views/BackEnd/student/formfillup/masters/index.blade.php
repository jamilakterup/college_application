@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Masters Form Fillup List')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

        	<div class="col-md-12 d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.formfillup.masters', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::text('student_id', $student_id, ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', selective_departments(), $dept_name, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('dept_name')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('level', selective_multiple_masters_level(), $level, ['class'=>'form-control level', 'autocomplete'=> 'off']) !!}
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

				{!! Form::open(['route' => 'student.formfillup.mastersffreport', 'method'=> 'post', 'target' => '_blank']) !!}
					{!! Form::hidden('session', $session) !!}
					{!! Form::hidden('dept_name', $dept_name) !!}
					{!! Form::hidden('exam_year', $exam_year) !!}
					{!! Form::hidden('level', $level) !!}
					{!! Form::hidden('id', $student_id) !!}
					<button class="btn btn-primary mb-1" type="submit"><i class="fas fa-file-pdf"></i> Generate Report</button>
				{!! Form::close() !!}
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

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
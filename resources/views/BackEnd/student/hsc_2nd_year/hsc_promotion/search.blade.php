@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc 2nd Year Promotion Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <h3 class="panel-title">Hsc 2nd Year Student List</h3>
        </header>
        <div class="panel-body">

        	<div class="col-md-12 d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.hsc.2nd.promotion.search', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::text('id', session('id'), ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', selective_multiple_study_group(), session('dept_name'), ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('dept_name')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('exam_year', selective_multiple_exam_year(), session('exam_year'), ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('exam_year')!!}
				  </div>


				  <button type="submit" class="btn btn-info">Search</button>
				{!! Form::close() !!}

			</div>
			<br>
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

				@foreach($form_fillup as $college)

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
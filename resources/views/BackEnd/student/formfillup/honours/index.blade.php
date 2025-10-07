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

        	@can('honours.formfillup.manage')
			{!! Form::open(['route'=> 'student.formfillup.honours', 'method'=> 'post', 'class' => 'form-inline d-flex justify-content-center']) !!}

        <div class="form-group">
          {!! Form::text('id', $id, ['class'=> 'form-control form-control-sm', 'placeholder' => 'Enter Your ID']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('dept_name', selective_multiple_subject(), $dept_name, ['class'=>'form-control form-control-sm dept_name selectize', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('dept_name')!!}
        </div>

        <div class="form-group">
				    {!! Form::select('current_level', selective_multiple_honours_level(), $level, ['class'=>'form-control form-control-sm current_level selectize', 'autocomplete'=> 'off', 'required' => true]) !!}
        </div>

        <div class="form-group">
          {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control form-control-sm session selectize', 'autocomplete'=> 'off', 'data-placeholder'=> '>--Select Session--<']) !!}
							{!!invalid_feedback('session')!!}
        </div>

        <div class="form-group">
          {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year, ['class'=>'form-control form-control-sm exam_year selectize', 'autocomplete'=> 'off', 'required'=> true]) !!}
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

        <div class="form-group">
            {!! Form::submit('Search', ['class' => 'btn btn-sm btn-primary']) !!}
        </div>
      {!! Form::close() !!}

			<div class="d-flex justify-content-between">
				<div>
						@if($num_rows>0)
						 <h3> Total Number Of Student: {{$num_rows}}</h3> 
						@endif

						@if ($total_amount > 0)
							<strong style="font-size: 16px;">Total Amount : {{$total_amount}}</strong><br/>
						@endif
				</div>

        </div>

      	<table class="table table-hover defDTable table-striped w-full cell-border">
            <thead>
              <tr>
								<th>ID</th>
								<th>Student Name</th>
								<th>Session</th>
								<th>Level Of Study</th>				
								<th>Faculty</th>
								<th>Subject</th>
								<th>Reg Type</th>		
								<th>Total Amount</th>					
								<th>Exam Year</th>	
								<th>Trx ID</th>	
								<th>Paid Date</th>
							</tr>
            </thead>
            
            <tbody>

							@foreach($students as $student)
								<tr class="{{ Study::updatedRow('id', $student->id) }}">
									<td>{{ $student->id }}</td>
									<td>{{ $student->name }}</td>
									<td>{{ $student->session }}</td>
									<td>{{ $student->level_study }}</td>					
									<td>{{ $student->groups }}</td>			
									<td>{{ $student->dept_name }}</td>			
									<td>{{ $student->formfillup_type }}</td>				
									<td>{{ $student->total_amount }}</td>					
									<td>{{ $student->exam_year }}</td>	
									<td>{{ $student->transaction_id }}</td>
									<td>{{ $student->date }}</td>
								</tr>
							@endforeach
            </tbody>
          </table>
          {{ $students->appends(Request::except('page'))->links() }}

          {{-- end hsc_admission_index permission--}}
          @endcan
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
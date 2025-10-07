@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Application Student List')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

        	<div class="d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.application.honours', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::text('admission_roll', $admission_roll, ['class'=> 'form-control', 'placeholder' => 'Admission Roll']) !!}
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
				    {!! Form::text('date', $date, ['class'=> 'form-control date', 'placeholder' => 'Payment Date', 'autocomplete'=> 'off']) !!}
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

				{!! Form::open(['route' => 'student.application.honappreport', 'method'=> 'post', 'target' => '_blank']) !!}
					{!! Form::hidden('session', $session) !!}
					{!! Form::hidden('exam_year', $exam_year) !!}
					{!! Form::hidden('date', $date) !!}
					<button class="btn btn-primary" type="submit" name="type" value="pdf"><i class="fas fa-file-pdf"></i> Generate PDF</button>
            
          <button class="btn btn-primary" type="submit" name="type" value="csv"></i><i class="fas fa-file-csv"></i> Generate CSV</button>
				{!! Form::close() !!}

			</div>
          <table class="table table-hover defDTable w-full cell-border">
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
								<th>Downloads</th>
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
						<td>
								@if($application->admission_form !='')
                	<a href="{{route('student.applicaton.download',['admission_form',$application->id])}}" style="margin-bottom: 2px;" target="__blank">Admission Form</a><br>
                @endif
                @if($application->hsc_transcript !='')
                	<a href="{{route('student.applicaton.download',['hsc_transcript',$application->id])}}" style="margin-bottom: 2px;" target="__blank">HSC Transcript</a>
                @endif
            </td>
						
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
@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Admission Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<div class="panel-body">

		<div class="col-md-12 d-flex justify-content-center search-filter">

			{!! Form::open(['route'=> 'students.honours.regstudent', 'method'=> 'post', 'class' => 'form-inline']) !!}

			<div class="form-group">
				{!! Form::text('id', $id, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'id','placeholder' => 'Refference ID']) !!}
				{!!invalid_feedback('id')!!}
			</div>

			<div class="form-group">
				{!! Form::text('adm_roll', $adm_roll, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'adm_roll', 'placeholder' => 'Admission Roll']) !!}
				{!!invalid_feedback('adm_roll')!!}
			</div>

			<div class="form-group">
				{!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off' , 'session' => 'session']) !!}
				{!!invalid_feedback('session')!!}
			</div>

			{!! Form::submit('Search', ['class' => 'btn btn-default']) !!}
			{!! Form::close() !!}

		</div>

		<table class="table table-hover defDTable w-full cell-border">
			<thead>
				<tr>
					<th>Ref. ID</th>
					<th>Name</th>	
					<th>Father's Name</th>	
					<th>Admission Roll</th>
					<th>Password</th>
					<th>Contact No</th>			
					<th>Reg. Time</th>				
					<th>Session</th>				
					<th>Subject</th>
					<th>Print</th>
				</tr>
			</thead>

			<tbody>

				@foreach($students as $student)

					<tr class="">
						<td>{{str_pad(str_pad($student->auto_id,4,'0',STR_PAD_LEFT),6,'33' ,STR_PAD_LEFT) }}</td>
						<td>{{ $student->name }}</td>
						<td>{{ $student->father_name }}</td>					
						<td>{{ $student->admission_roll }}</td>
						<td>{{ $student->password }}</td>
						<td>{{ $student->contact_no }}</td>								
						<td>{{ $student->entry_time }}</td>					
						<td>{{ $student->session }}</td>	

						<td>{{ $student->subject }}</td>
						
						<td><a  class='View' id="{{ $student->id }}" style="cursor: pointer;"><i class='fa fa-file-text-o'></i></a>
						</td>
					</tr>	

				@endforeach
			</tbody>
		</table>

		{{ $students->appends(Request::except('page'))->links() }}

	</div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
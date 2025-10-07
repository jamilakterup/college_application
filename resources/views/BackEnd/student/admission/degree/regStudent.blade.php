@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Degree Reg List Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">


        	<div class="col-md-12 d-flex justify-content-center search-filter">

				{!! Form::open(['route'=> 'students.degree.regstudent', 'method'=> 'post', 'class' => 'form-inline']) !!}

				<div class="form-group">
				    {!! Form::text('id', $ref_id, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'id','placeholder' => 'Refference ID']) !!}
					{!!invalid_feedback('id')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::text('admission_roll', $admission_roll, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'admission_roll', 'placeholder' => 'Admission Roll']) !!}
					{!!invalid_feedback('groups')!!}
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
					<th>Password</th>
					<th>Admission Roll</th>
					<th>Reg. Time</th>				
					<th>Session</th>				
					<th>Name</th>	
					<th>Father's Name</th>	
					<th>Groups</th>
					<th>Contact No</th>												
					<th>Print</th>
				</tr>
            </thead>
            
            <tbody>

				@foreach($students as $college)

					<tr class="">
						<td>{{@auto_id_deg($college->auto_id) }}</td>
						<td>{{ $college->password }}</td>
						<td>{{ $college->admission_roll }}</td>
						<td>{{ $college->entry_time }}</td>					
						<td>{{ $college->admission_session }}</td>					
										
						<td>{{ $college->name }}</td>
						<td>{{ $college->father_name }}</td>					

						<td>{{ $college->faculty }}</td>

						<td>{{ $college->permanent_mobile }}</td>								
						
						<td><a  class='View' id="{{ $college->id }}" style="cursor: pointer;"><i class='fa fa-file-text-o'></i></a>
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
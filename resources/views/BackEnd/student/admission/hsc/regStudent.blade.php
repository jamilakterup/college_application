@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc Reg List Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">


        	<div class="col-md-12 d-flex justify-content-center search-filter">

				{!! Form::open(['route'=> 'students.hsc.regstudent', 'method'=> 'post', 'class' => 'form-inline']) !!}

				<div class="form-group">
				    {!! Form::text('id', $ref_id, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'id','placeholder' => 'Refference ID']) !!}
					{!!invalid_feedback('id')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::text('ssc_roll', $ssc_roll, ['class'=>'form-control', 'autocomplete'=> 'off', 'id' => 'ssc_roll', 'placeholder' => 'SSC Roll']) !!}
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
					<th>SSC Roll</th>
					<th>Reg. Time</th>				
					<th>Session</th>				
					<th>Name</th>	
					<th>Father's Name</th>	
					<th>HSC Groups</th>
					<th>SSC Groups</th>
					<th>Contact No</th>												
					<th>Print</th>
				</tr>
            </thead>
            
            <tbody>

				@foreach($hscstudents as $college)

					<tr class="">
						<td>{{str_pad(str_pad($college->auto_id,4,'0',STR_PAD_LEFT),6,'11' ,STR_PAD_LEFT) }}</td>
						<td>{{ $college->password }}</td>
						<td>{{ $college->ssc_roll }}</td>
						<td>{{ $college->entry_time }}</td>					
						<td>{{ $college->admission_session }}</td>					
										
						<td>{{ $college->name }}</td>
						<td>{{ $college->fathers_name }}</td>					

						<td>{{ $college->hsc_group }}</td>
						<td>{{ $college->ssc_group }}</td>	

						<td>{{ $college->mobile }}</td>								
						
						<td><a  class='View' id="{{ $college->id }}" style="cursor: pointer;"><i class='fa fa-file-text-o'></i></a>
						</td>
						
					</tr>	

				@endforeach
            </tbody>
          </table>

          {{ $hscstudents->appends(Request::except('page'))->links() }}

        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
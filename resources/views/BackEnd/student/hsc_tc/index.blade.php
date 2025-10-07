@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'HSC TC Student')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

        	<div class="d-flex justify-content-center">

				{!! Form::open(['route'=> 'students.hsc.hsctcstudents', 'method'=> 'post', 'class' => 'form-inline filter-form']) !!}
				    {!! Form::text('id', $from_student_id, ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				    {!! Form::text('ssc_roll', $from_ssc_roll, ['class'=> 'form-control', 'placeholder' => 'SSC Roll']) !!}
				    {!! Form::select('groups', selective_multiple_study_group(), $from_groups, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
				    {!! Form::select('current_level', selective_multiple_hsc_level(), $form_level, ['class'=>'form-control level', 'autocomplete'=> 'off']) !!}
				    {!! Form::select('session', selective_multiple_session(), $form_session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}

				  <button type="submit" class="btn btn-info">Search</button>
				{!! Form::close() !!}

			</div>
			<br>
			<div class="d-flex justify-content-between">
				
				<div>
					@if ($num_rows > 0)
						<strong>Total Number Of Student : {{$num_rows}}</strong><br/>
					@endif
				</div>

				{!! Form::open(['route' => 'students.hsc.tc_student_pdf', 'method'=> 'post', 'target' => '_blank', 'class' => 'form-inline filter-form']) !!}
					{{ Form::hidden('from_student_id', $from_student_id) }}
					{{ Form::hidden('from_ssc_roll', $from_ssc_roll) }}
					{{ Form::hidden('from_groups', $from_groups) }}
					{{ Form::hidden('form_leve', $form_level) }}
					{{ Form::hidden('form_session', $form_session) }}
					<button class="btn btn-primary mb-1" type="submit"><i class="fas fa-file-pdf"></i> Generate Report</button>
				{!! Form::close() !!}
			</div>
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Student ID</th>
					<th>SSC Roll</th>
					<th>Ref. ID</th>				
					<th>Session</th>				
					<th>Class Roll</th>		
					<th>Name</th>				
					<th>Groups</th>	
					<th>Current Level</th>	
					<th>Status</th>	
				</tr>
            </thead>
            
            <tbody>

				@foreach($hscstudents as $college)

					<tr class="text-center">
						<td>{{ $college->id }}</td>
						<td>{{ $college->ssc_roll }}</td>
						<td>{{ str_pad(str_pad($college->refference_id,4,'0',STR_PAD_LEFT),6,'11' ,STR_PAD_LEFT)}}</td>					
						<td>{{ $college->session }}</td>					
						<td>{{ $college->class_roll }}</td>					
						<td>{{ $college->name }}</td>					
						<td>{{ $college->groups }}</td>					
						<td>{{ $college->current_level }}</td>	
						<td>TC</td>					

					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $hscstudents->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
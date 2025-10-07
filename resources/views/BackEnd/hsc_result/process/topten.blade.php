@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Download Top Records</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.process.top-ten-download', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	<div class="form-group row">
				    {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
				    <div class="col-md-10">
				  
				      {{ Form::select('session', selective_multiple_session(), NULL, ['class' => 'form-control', 'id' => 'session']) }}
				  
				      {!!invalid_feedback('session')!!}
				  
				    </div>
				  </div>
				  
				  <div class="form-group row">
				    {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2 form-control-label']) }}
				    <div class="col-md-10">
				  
				      {{ Form::select('exam_year', selective_multiple_exam_year(), NULL, ['class' => 'form-control', 'id' => 'exam_year']) }}
				  
				      {!!invalid_feedback('exam_year')!!}
				  
				    </div>
				  </div>
				  
				  <div class="form-group row">
				    {{ Form::label('exam_id', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
				    <div class="col-md-10">
				  
				      {{ Form::select('exam_id', $exam_lists, NULL, ['class' => 'form-control exam', 'id' => 'exam']) }}
				  
				      {!!invalid_feedback('exam_id')!!}
				  
				    </div>
				  </div>

				  <div class="form-group row">
				    {{ Form::label('total_position', 'Total Position', ['class' => 'col-md-2 form-control-label']) }}
				    <div class="col-md-10">
				      {{ Form::text('total_position', NULL, ['class' => 'form-control']) }}
				      {!!invalid_feedback('total_position')!!}
				  
				    </div>
				  </div>

				  <div class="form-group row">
				    <div class="col-md-10 offset-md-2">
				      {!! Form::submit('Download', ['class'=> 'btn btn-primary']) !!}
				      <a href="{{ route('hsc_result.process.index') }}" class="btn btn-warning btn-outline">Back</a>
				    </div>
				  </div>
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
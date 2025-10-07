@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.class.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Generate Student Subject Wise Data</h3>
	</header>

	<div class="panel-body">

		<div class="col-md-8 offset-md-2">
		  	{{ Form::open(['route' => 'hsc_result.download_student_sub_data', 'method' => 'post', 'class'=> 'form-horizontal']) }}

	          	<div class="form-group row">
				  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
				  <div class="col-md-10">

				    {{ Form::select('session', selective_multiple_session(),$session, ['class' => 'form-control', 'readonly' => 'true']) }}

				    {!!invalid_feedback('session')!!}

				  </div>
				</div>

				<div class="form-group row">
				  {{ Form::label('current_level', 'Current Level', ['class' => 'col-md-2 form-control-label']) }}
				  <div class="col-md-10">

				    {{ Form::select('current_level', selective_multiple_hsc_level(),$current_level, ['class' => 'form-control', 'readonly' => 'true']) }}

				    {!!invalid_feedback('current_level')!!}

				  </div>
				</div>

				<div class="form-group row">
				  <div class="col-md-10 offset-md-2">
				    {{ Form::submit('Download Zip', ['class' => 'btn btn-primary']) }}
				    <a href="{{ route('hsc_result.subject_info.index') }}" class="btn btn-warning btn-outline">Back</a>
				  </div>
				</div>
	          	
	        {!! Form::close() !!}
        </div>
	</div>
</div>

@endsection
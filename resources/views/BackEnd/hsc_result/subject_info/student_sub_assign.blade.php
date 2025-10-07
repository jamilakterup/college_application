@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Subject Info Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.subject_info.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Student Subject Assign</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.student_subject.assign.update', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	<div class="form-group row">
					  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control']) !!}

					    {!!invalid_feedback('session')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('group', 'Group', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('group', selective_multiple_study_group(), null, ['class' => 'form-control']) !!}

					    {!!invalid_feedback('group')!!}

					  </div>
					</div>

					<div class="form-group row">
					  <div class="col-md-10 offset-md-2">
					    {!! Form::submit('Generate', ['class'=> 'btn btn-primary']) !!}
					    <a href="{{ route('hsc_result.student_subject.assign') }}" class="btn btn-warning btn-outline">Back</a>
					  </div>
					</div>
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
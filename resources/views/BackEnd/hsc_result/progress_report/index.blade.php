@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Progress Reporting')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
		<h3 class="panel-title">Generate Progress Report</h3>
	</header>

	<div class="panel-body">
		<div class="row">
			<div class="col-md-8 m-auto">
				{{ Form::open(['route' => 'hsc_result.progress_report.generate', 'method' => 'post', 'class'=> 'form-horizontal', 'target'=> '_blank']) }}

				<div class="form-group row">
					{{ Form::label('student_id', 'Student ID', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('student_id', NULL, ['class' => 'form-control', 'id' => 'student_id', 'placeholder'=> '(Optional)']) }}

						{!!invalid_feedback('student_id')!!}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::select('session', selective_multiple_session(), NULL, ['class' => 'form-control', 'id' => 'session']) }}

						{!!invalid_feedback('session')!!}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('current_year', 'Current Year', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::select('current_year', selective_multiple_hsc_level(), NULL, ['class' => 'form-control year']) }}

						{!!invalid_feedback('current_year')!!}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('group', 'Group', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::select('group', selective_multiple_study_group(), NULL, ['class' => 'form-control group']) }}

						{!!invalid_feedback('group')!!}

					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-10 offset-md-2">
						{!! Form::submit('Generate', ['class'=> 'btn btn-primary']) !!}
					</div>
				</div>

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection
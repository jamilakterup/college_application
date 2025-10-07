@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
		<h3 class="panel-title">Indivisual Student Result Processing</h3>
	</header>

	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				{{ Form::open(['route' => 'hsc_result.process.indivisual.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

				<input type="hidden" name="processing_id" value="{{$result->id}}">

				<div class="form-group row">
					{{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('session', $result->session, ['class' => 'form-control exam', 'id' => 'exam', 'disabled'=> true]) }}
					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('exam_year', $result->exam_year, ['class' => 'form-control exam', 'id' => 'exam', 'disabled'=> true]) }}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('current_level', 'Current Year', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('current_level', $result->classe->name ?? null, ['class' => 'form-control exam', 'id' => 'exam', 'disabled'=> true]) }}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('group_id', 'Group', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('group_id', $result->group->name ?? null, ['class' => 'form-control exam', 'id' => 'exam', 'disabled'=> true]) }}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('exam_id', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('exam_id', $result->exam->name ?? null, ['class' => 'form-control exam', 'id' => 'exam', 'disabled'=> true]) }}

					</div>
				</div>

				<div class="form-group row">
					{{ Form::label('student_id', 'Student ID', ['class' => 'col-md-2 form-control-label']) }}
					<div class="col-md-10">

						{{ Form::text('student_id', null, ['class' => 'form-control exam', 'id' => 'student_id']) }}

						{!! invalid_feedback('student_id') !!}

					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-10 offset-md-2">
						{!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
						<a href="{{ route('hsc_result.process.index') }}" class="btn btn-warning btn-outline">Back</a>
					</div>
				</div>

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection
@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Honours Migration Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Migration List</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($migration_student, ['route' => ['students.migration.list.edited', $migration_student->id], 'method' => 'post', 'class'=> 'form-horizontal'])}}

					{{ Form::hidden('id', $id, array('id' => 'invisible_id')) }}

	          		<div class="form-group row">
					  {{ Form::label('admission_roll', 'Admission Roll', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('admission_roll', NULL, ['class' => 'form-control']) }}

					    {!!invalid_feedback('admission_roll')!!}

					  </div>
					</div>

					<div class="form-group row">

					  {{ Form::label('admission_session', 'Admission Session', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::select('admission_session',$session_lists, null, ['class' => 'form-control']) }}

					    {!!invalid_feedback('admission_session')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('admitted_subject', 'Admitted Subject', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('admitted_subject', $dept_list, null, ['class'=>'form-control select2']) !!}

					    {!!invalid_feedback('admitted_subject')!!}
					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('faculty', 'Faculty', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('faculty', $faculty_list, null, ['class'=>'form-control']) !!}

					    {!!invalid_feedback('faculty')!!}
					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('changed_subject', 'Changed Subject', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('changed_subject', $dept_list, null, ['class'=>'form-control select2']) !!}

					    {!!invalid_feedback('changed_subject')!!}
					  </div>
					</div>

					<div class="form-group row">
					  <div class="col-md-10 offset-md-2">
					    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
					  </div>
					</div>

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
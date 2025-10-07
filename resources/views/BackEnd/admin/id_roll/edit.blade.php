@extends('BackEnd.admin.layouts.master')
@section('page-title', 'ID Roll Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit ID Roll</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($id_roll, ['route' => ['admin.id_roll.edit', $id_roll->id], 'method' => 'post', 'class'=> 'form-horizontal'])}}

	          		<div class="form-group row">
					  {{ Form::label('dept_name', 'Department Name', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('dept_name', NULL, ['class' => 'form-control', 'readonly' => true]) }}

					    {!!invalid_feedback('dept_name')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('session', NULL, ['class' => 'form-control', 'readonly' => true]) }}

					    {!!invalid_feedback('session')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('start_digit', 'Starting Roll', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('start_digit', NULL, ['class' => 'form-control', 'placeholder' => 'Start Digit']) }}

					    {!!invalid_feedback('start_digit')!!}

					  </div>
					</div>

	                <div class="form-group row">
					  {{ Form::label('last_digit_used', 'Last Digit Used', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('last_digit_used', NULL, ['class' => 'form-control', 'placeholder' => 'Last Digit']) }}

					    {!!invalid_feedback('last_digit_used')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('end_digit', 'Ending Roll', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::text('end_digit', NULL, ['class' => 'form-control', 'placeholder' => 'End Digit']) }}

					    {!!invalid_feedback('end_digit')!!}

					  </div>
					</div>

					<div class="form-group row">
					  <div class="col-md-10 offset-md-2">
					    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
					    <a href="{{ route('admin.id_roll.create') }}" class="btn btn-warning btn-outline">Back</a>
					  </div>
					</div>

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
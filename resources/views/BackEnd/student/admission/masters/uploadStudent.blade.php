@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours EX-Student Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Upload Honours EX-Student From CSV</h3>
	</header>

	<div class="panel-body">

		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'students.honours.upload.ext', 'method' => 'post', 'class'=> 'form-horizontal','files'=> true])}}

	                <div class="form-group row">
					  <div class="col md-2">
					  {{ Form::label('upload_csv', 'Honours Student CSV Path', ['class' => 'form-control-label']) }}
					  	<p class='margin-0'><a href="{{ URL::route('students.honours.format') }}">Download CSV Format</a></p>	
					  </div>
					  <div class="col-md-10">
					    {{ Form::file('upload_csv', NULL, ['class' => 'form-control']) }}

					    {!!invalid_feedback('upload_csv')!!}

					  </div>
					</div>

					<div class="form-group row">
					  <div class="col-md-10 offset-md-2">
					    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
					    <a href="{{ url()->previous() }}" class="btn btn-warning btn-outline">Back</a>
					  </div>
					</div>

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
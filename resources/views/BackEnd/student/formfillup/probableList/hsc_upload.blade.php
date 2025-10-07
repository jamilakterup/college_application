@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'HSC Probable List Student Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Upload HSC Probable List From CSV</h3>
	</header>

	<div class="panel-body">

		<div class="header-menu mb-5 d-flex justify-content-start">
			{!! Form::open(['route'=> 'truncate.table', 'method'=> 'post']) !!}
			{!! Form::hidden('type', 'hscff') !!}
			<button class="btn btn-danger delete mr-1" type="submit"><i class="fas fa-trash"></i> Delete</button>
			{!! Form::close() !!}
			
    		<a href="{{route('download.csv.format', ['type'=> 'hscff'])}}" class="btn btn-info"><i class="fas fa-file-csv"></i> Download Format</a>

    	</div>

		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => ['student.prblist.upload.exe',['type'=> 'hsc']], 'method' => 'post', 'class'=> 'form-horizontal','files'=> true])}}

	                <div class="form-group row">
					  {{ Form::label('title', 'Material CSV Path', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">
					    {{ Form::file('csv', NULL, ['class' => 'form-control']) }}
					    {!!invalid_feedback('csv')!!}
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
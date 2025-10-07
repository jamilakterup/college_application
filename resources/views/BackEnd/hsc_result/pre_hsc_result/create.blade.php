@php
use App\Libs\Study;
@endphp

@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Student Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
		<header class="panel-heading">
			<div class="panel-actions"><a href="{{ route('hsc_result_show') }}" class="btn btn-sm btn-default" target="__blank"><i class="fal fa-search"></i> Search Result</a></div>

		  <h3 class="panel-title">HSC Result Upload</h3>
		</header>
        <div class="panel-body">
        	<div class="submenu-item-inline mb-5">
	        	<a href="{{ url('csv/mark_value/marks_value_arts.xlsx') }}" class="btn btn-success">marks_value_arts.xlsx</a>
	        	<a href="{{ url('csv/mark_value/marks_value_commerce.xlsx') }}" class="btn btn-success">marks_value_commerce.xlsx</a>
	        	<a href="{{ url('csv/mark_value/marks_value_science.xlsx') }}" class="btn btn-success">marks_value_science.xlsx</a>
        	</div>
        	<div class="col-md-8 offset-md-2">
        		
        		{!! Form::open(['route'=> 'pre_hsc_result.store', 'files'=> true]) !!}

	        		<div class="form-group row">
					  {{ Form::label('file', 'Upload XLSX', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::file('file', ['class'=> 'form-control']) !!}

					    {!!invalid_feedback('file')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('group', 'Select Hsc Group', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('group', selective_multiple_study_group(), null, ['class'=>'form-control']) !!}

					    {!!invalid_feedback('group')!!}

					  </div>
					</div>
	        	
	        		
	        	
	        		<button type="submit" class="btn btn-primary">Upload</button>
        		{!! Form::close() !!}
        	</div>
        </div>
      </div>

@endsection

@push('scripts')
	
@endpush
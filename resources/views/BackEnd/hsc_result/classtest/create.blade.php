@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.exam.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Create Class Test</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.class_test.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.classtest.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
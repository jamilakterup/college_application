@extends('BackEnd.teacher.layouts.master')
@section('page-title', 'Teacher Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New Teacher</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'teacher.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.teacher.teacher.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
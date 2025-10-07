@extends('BackEnd.hall.layouts.master')
@section('page-title', 'Hall Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Create Hall</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hall.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hall.hall.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
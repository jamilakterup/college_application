@extends('BackEnd.library.layouts.master')
@section('page-title', 'Material Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New Material</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				
			  	{{ Form::open(['route' => 'library.material.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.library.material.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
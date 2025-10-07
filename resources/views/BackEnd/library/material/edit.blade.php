@extends('BackEnd.library.layouts.master')
@section('page-title', 'Material Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Material</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				@if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
			  	{{ Form::model($material, ['route' => ['library.material.update', $maccession->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.library.material.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
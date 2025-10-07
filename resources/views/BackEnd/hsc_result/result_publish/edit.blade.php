@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Result Publish Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Result Publish</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($class, ['route' => ['hsc_result.result_publish.update', $class->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.result_publish.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Program Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Program</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($program, ['route' => ['admin.program.update', $program->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.admin.program.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
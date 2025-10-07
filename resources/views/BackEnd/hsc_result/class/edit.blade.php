@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Class Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.class.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Class</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($class, ['route' => ['hsc_result.class.update', $class->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.class.particles.form-edit')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
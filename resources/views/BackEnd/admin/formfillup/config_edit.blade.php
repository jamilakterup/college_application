@extends('BackEnd.admin.layouts.master')
@section('page-title', ucfirst($course)." Admission Config Management")

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit {{ucfirst($course)}} Config</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($config,['route' => ['admin.admission.config.update', $config->id], 'method' => 'put', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.admission.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
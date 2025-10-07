@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Certificate Requirement Management')

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
	  <h3 class="panel-title">Edit Certificate Requirement</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($requirement,['route' => ['admin.requirement.update', $requirement->id], 'method' => 'put', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.requirement.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
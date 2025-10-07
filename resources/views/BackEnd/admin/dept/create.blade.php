@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Department Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.dept.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New Department</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.dept.store', 'method' => 'post', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.dept.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
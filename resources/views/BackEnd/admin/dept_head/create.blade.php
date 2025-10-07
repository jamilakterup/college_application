@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New Department Head')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.admin.dept_head.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New Department Head</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.dept_head.store', 'method' => 'post', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.dept_head.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Edit Faculty Head')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.admin.fac_head.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Faculty Head</h3>
	  @if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($faculty_head, ['route' => ['admin.fac_head.update', $faculty_head->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.admin.fac_head.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
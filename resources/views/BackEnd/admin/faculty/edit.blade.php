@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Edit Faculty')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.admin.faculty.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Faculty</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($faculty, ['route' => ['admin.faculty.update', $faculty->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.admin.faculty.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
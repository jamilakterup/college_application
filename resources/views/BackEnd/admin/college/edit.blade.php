@extends('BackEnd.admin.layouts.master')
@section('page-title', 'College Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit College</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($college,['route' => ['admin.college.update', $college->id], 'method' => 'put', 'class'=> 'form-horizontal', 'files'=>true])}}

	                @include('BackEnd.admin.college.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
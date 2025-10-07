@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Item Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New College</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.college.store', 'method' => 'post', 'class'=> 'form-horizontal', 'files'=>true])}}

	                @include('BackEnd.admin.college.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
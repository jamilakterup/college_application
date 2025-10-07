@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New ID Roll')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New ID Roll</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.id_roll.new_store', 'method' => 'post', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.id_roll.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
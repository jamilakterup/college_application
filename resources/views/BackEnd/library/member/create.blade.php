@extends('BackEnd.library.layouts.master')
@section('page-title', 'Member Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New Member</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				
			  	{{ Form::open(['route' => 'library.member.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.library.member.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
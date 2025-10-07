@extends('BackEnd.library.layouts.master')
@section('page-title', 'Member Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Member</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				
			  	{{ Form::model($libmember, ['route' => ['library.member.update', $libmember->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.library.member.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
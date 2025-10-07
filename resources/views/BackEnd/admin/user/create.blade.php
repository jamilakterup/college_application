@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New User')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.user.store', 'method' => 'post', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.user.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
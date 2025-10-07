@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Edit User')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($user,['route' => ['admin.user.update', $user->id], 'method' => 'put', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.user.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
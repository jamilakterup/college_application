@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Edit Subject')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.subject.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Assign Class Subject</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => ['hsc_result.assign_subject.update',$class_id, $department_id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.assign_subject.particles.form-edit')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.exam.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Exam</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($exam, ['route' => ['hsc_result.exam.update', $exam->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.exam.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Date Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Assign Class Subject</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.exam_date.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.exam_date.particles.form-edit')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
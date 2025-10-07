@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Generator Management')

@push('styles')
<style type="text/css">
	table tr th.bg-type-a {
	    background: #bbdef5;
	    color: #4e5e6a;
	}
</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Add New PaySlip Generator</h3>
	</header>

	<div class="panel-body">

		<form>
  
</form>
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::open(['route' => 'admin.payslip_generator.store', 'method' => 'post', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.payslip_generator.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
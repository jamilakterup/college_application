@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Item Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit PaySlip Item</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  <div class="col-md-12">

	          	{{ Form::model($payslip_item, ['route' => ['admin.payslip_item.update', $payslip_item->id], 'method' => 'put', 'class'=> 'form-horizontal'])}}

	                @include('BackEnd.admin.payslip_item.particles.form')

	            {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection
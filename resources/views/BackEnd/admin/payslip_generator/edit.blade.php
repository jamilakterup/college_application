@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Header Management')

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
	  <h3 class="panel-title">Edit PaySlip Header</h3>
	</header>

	<div class="panel-body">

      	{{ Form::model($payslip_title, ['route' => ['admin.payslip_generator.update', $payslip_title->id], 'method' => 'put', 'class'=> 'form-horizontal'])}}

            @include('BackEnd.admin.payslip_generator.particles.form-edit')

        {!! Form::close() !!}
	</div>
</div>

@endsection
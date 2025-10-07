@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'College Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
	<div class="panel-body">
	  <p class="admission-menu">
	  		@can('hsc_admission_config.manage')
			<a href="{{ route('admin.admission.config') }}" class='btn btn-primary'><i class='fal fa-plus'></i> Admission Configuration Open/Close </a>
			@endcan

		</p>
	</div>
</div>

@endsection
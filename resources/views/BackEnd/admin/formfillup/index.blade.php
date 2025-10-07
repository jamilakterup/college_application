@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Formfillup Configs Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<div class="panel-body">
	  <p class="admission-menu">
			@can('hsc_admission_config.manage')
			<a href="{{ route('admin.formfillup.config') }}" class='btn btn-primary'><i class='fal fa-plus'></i> FormFillup Configuration Open/Close </a>
			@endcan
		</p>
	</div>
</div>

@endsection
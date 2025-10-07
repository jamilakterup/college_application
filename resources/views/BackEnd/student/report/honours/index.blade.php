@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <div class="panel-body">

  	<p class="header-menu">
			{{ link_to_route('report.honours.admission', 'Honours Admission Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.honours.application', 'Honours Application Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.honours.ff', 'Honours Formfillup Report', NULL, ['class' => 'btn btn-info']) }}
		</p>

  </div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
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
			{{ link_to_route('report.hsc.admission', 'HSC Admission Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.hsc2nd.admission', 'HSC 2nd Year Admission Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.hsc.ff', 'HSC Form Fillup Report', NULL, ['class' => 'btn btn-info']) }}
		</p>

  </div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
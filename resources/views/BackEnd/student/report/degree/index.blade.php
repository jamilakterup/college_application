@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Degree Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <div class="panel-body">

  	<p class="header-menu">
			{{ link_to_route('report.degree.admission', 'Degree Admission Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.degree.application', 'Degree Application Report', NULL, ['class' => 'btn btn-info']) }}
			{{ link_to_route('report.degree.ff', 'Degree Formfillup Report', NULL, ['class' => 'btn btn-info']) }}
		</p>

  </div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
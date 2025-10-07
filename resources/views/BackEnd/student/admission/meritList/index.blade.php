@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Merit List Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <div class="panel-body">

  	<p class="header-menu">
		@can('merit.list.honours')
			<a href="{{ route('student.meritlist.honours') }}" class="btn btn-info">Honours Merit List</a>
		@endcan
		@can('merit.list.masters')
			<a href="{{ route('student.meritlist.masters') }}" class="btn btn-info">Masters Merit List</a>
		@endcan
		@can('merit.list.degree')
			<a href="{{ route('student.meritlist.degree') }}" class="btn btn-info">Degree Merit List</a>
		@endcan
		@can('merit.list.hsc')
			<a href="{{ route('student.meritlist.hsc') }}" class="btn btn-info">HSC Merit List</a>
		@endcan
	</p>

  </div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
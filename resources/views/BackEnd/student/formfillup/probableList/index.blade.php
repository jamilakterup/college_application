@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Probable List Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <div class="panel-body">

  	<p class="header-menu">
		<a href="{{ route('student.prblist.honours') }}" class="btn btn-info">Honours Form Fillup Probable List</a>
		<a href="{{ route('student.prblist.masters') }}" class="btn btn-info">Masters Form Fillup Probable List</a>
		<a href="{{ route('student.prblist.degree') }}" class="btn btn-info">Degree Form Fillup Probable List</a>
		<a href="{{ route('student.prblist.hsc') }}" class="btn btn-info">HSC Form Fillup Probable List</a>
	</p>

  </div>
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush
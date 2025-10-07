@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'New Student Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<div class="panel-body">
	  <p class="admission-menu">
			<a href="{{ route('admin.newstudent.hscnewstudent') }}" class='btn btn-primary'><i class='fa fa-plus'></i> Add HSC New Student </a>	
			<a href="{{ route('admin.newstudent.honnewstudent') }}" class='btn btn-primary'><i class='fa fa-plus'></i> Add Honours New Student </a>				
			<a href="{{ route('admin.newstudent.masnewstudent') }}" class='btn btn-primary'><i class='fa fa-plus'></i> Add Masters New Student </a>			
			<a href="{{ route('admin.newstudent.degnewstudent') }}" class='btn btn-primary'><i class='fa fa-plus'></i> Add Degree New Student </a>
		</p>
	</div>
</div>

@endsection
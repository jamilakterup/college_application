@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Student Migration Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <h3 class="panel-title">Honours Student Migration</h3>
        </header>
        <div class="panel-body">

        	<p class="header-menu">
						@include('BackEnd.student.honours.migration.particles.subMenu')
					</p>

        </div>
      </div>

@endsection

@push('scripts')
@endpush
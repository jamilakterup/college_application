@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Student Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">
          <img src="{{ URL::to('/') }}/img/rc.jpg" alt='' class="img-fluid" />
          {{\Config::get('app.SMS_PASSWORD')}}a
        </div>
      </div>

@endsection

@push('scripts')
	
@endpush
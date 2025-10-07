@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Student Migration Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title">Student Migration</h3>
  </header>
  <div class="panel-body">

    <div class="col-md-12 d-flex justify-content-center">
      {!! Form::open(['route'=> 'students.migration.search', 'method'=> 'post', 'class' => 'form-inline']) !!}
        <div class="form-group">
          {!! Form::text('id', $id, ['class' => 'form-control m-left-0','placeholder' => 'Student ID']) !!}
        </div>

        <div class="form-group">
          {!! Form::text('adm_roll', $adm_roll, ['class' => 'form-control', 'placeholder' => 'Admission Roll']) !!}
        </div>

        <div class="form-group">
          {{ Form::select('session', $session_lists, $session, ['class' => 'form-control m-left-0']) }} 
        </div>

        <div class="form-group">
          {{ Form::select('course', $course_lists, $course, ['class' => 'form-control m-left-0']) }} 
        </div>
        
        <button type="submit" class="btn btn-info">Search</button>
      {!! Form::close() !!}
    </div>

    <table class="table table-hover defDTable w-full cell-border">
      <thead>
        <th>Name</th>       
        <th>Admission Roll</th> 
        <th>Previous Student ID</th>
        <th>Present Student ID</th>
        <th>session</th>
        <th>Previous Subject</th>
        <th>Present Subject</th>
        <th>Course</th>
        <th>Previous Amount</th>
        <th>Present Amount</th>
        <th>Payment Difference</th>
        <th>Payment Status</th>
      </thead>

      <tbody>
        @foreach($migrated_students as $college)

            <tr class="">
              <td>{{ $college->name }}</td>
              <td>{{ $college->admission_roll }}</td>
              <td>{{ $college->previous_id }}</td>
              <td>{{ $college->present_id }}</td>
              <td>{{ $college->session }}</td>
              <td>{{ $college->previous_subject }}</td>
              <td>{{ $college->present_subject }}</td>
              <td>{{ $college->course }}</td>
              <td>{{ $college->previous_paid_amount }}</td>
              <td>{{ $college->present_paid_amount }}</td>
              <td>{{ $college->payment_diff }}</td>
              <td>
                @if ($college->payment_status == 'Paid')
                  <span class="badge badge-success">Paid</span>
                @elseif($college->payment_status == 'Refundable')
                  <span class="badge badge-info">Refundable</span>
                @else
                  <span class="badge badge-danger">{{$college->payment_status}}</span>
                @endif
              </td>
            </tr>

        @endforeach
      </tbody>
    </table>

    {{ $migrated_students->appends(Request::except('page'))->links() }}


  </div>
</div>

@endsection

@push('scripts')
@endpush
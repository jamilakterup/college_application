@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Masters Probable List Management')

@push('styles')
<style type="text/css">
  .table{
    margin: 0 auto;
    width: 100%;
    clear: both;
    word-wrap:break-word;
    border-collapse: collapse;
    table-layout: fixed;
  }
</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions">
            <a href="{{ route('student.prblist.upload', ['type'=> 'masters']) }}" class="btn btn-sm btn-info" data-label="Masters Probale Student"><i class="fas fa-upload"></i> Upload Batch Probable List</a>
            <a href="{{ route('student.prblist.create', ['type'=> 'masters']) }}" class="btn btn-sm btn-primary add_new" data-label="Masters Probable Student"><i class="fal fa-plus"></i> Add New Probable Student</a>
          </div>
          <h3 class="panel-title">Probable Lists</h3>
        </header>
        <div class="panel-body">

          <form class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::text('student_id', null , ['class'=> 'form-control form-control-sm filter', 'id' => 'student_id', 'placeholder'=> '<--Search ID-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('session', filter_empty_array(selective_multiple_session()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'session', 'data-placeholder' => '<--Session-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('faculty_name', filter_empty_array(selective_faculties()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'faculty_name', 'data-placeholder' => '<--Faculty-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('dept_name', filter_empty_array(selective_multiple_subject()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'dept_name', 'data-placeholder' => '<--Department-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('level', filter_empty_array(selective_multiple_masters_level()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'current_level', 'data-placeholder' => '<--Level-->']) !!}
            </div>

            <div class="form-group">
                {!! Form::select('registration_type', filter_empty_array(selective_formfillup_type()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'registration_type', 'data-placeholder' => '<--Registration Type-->']) !!}
            </div>

          </form>
          <table class="table table-hover w-full cell-border text-center table-sm" id="datatable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Student Id</th>
                <th>Name</th>
                <th>Session</th>
                <th>Department</th>
								<th>Level</th>
								<th>Registration Type</th>
								<th>Student Type</th>
                <th>Total Amount</th>
								<th>Action</th>
              </tr>
            </thead>
            
            <tbody id="tdata">

            </tbody>
          </table>
        </div>
      </div>

      {{ajax_modal()}}

@endsection

@push('scripts')

<script>
$(document).ready(function() {
  datatable = $('#datatable').DataTable({
      ajax:{
        url: "{{ route('student.prblist.masters.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.student_id = $('#student_id').val(),
          d.session = $('#session').val(),
          d.faculty_name = $('#faculty_name').val(),
          d.dept_name = $('#dept_name').val(),
          d.current_level = $('#current_level').val(),
          d.registration_type = $('#registration_type').val()
        },
        dataType: "json",
        type: "POST",
        cache: true
      },
      columns: [
        { data: "auto_id", 'visible':false},
        { data: "id"},
        { data: "name"},
        { data: "session"},
        { data: "dept_name"},
        { data: "current_level"},
        { data: "registration_type"},
        { data: "student_type"},
        { data: "total_amount"},
        { data: "actions", orderable: false, searchable: false,width: "100px"}
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      scrollY: '70vh',
      sDom: 'rtlipf',
      responsive: true,
      order: [[ 0, "desc" ]],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
      bAutoWidth: true,
    });

  datatable.columns.adjust().draw();

  $(document).on('change','.filter', function(){
    datatable.draw();
  });

  $(document).on('input','.filter', function(){
    datatable.draw();
  });
  
});
  
</script>
@endpush
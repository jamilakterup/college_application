@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Merit List Management')

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
            <a href="{{ route('student.meritlist.upload', ['type'=> 'honours']) }}" class="btn btn-sm btn-info" data-label="Honours Merit Student"><i class="fas fa-upload"></i> Upload Batch Merit List</a>
            <a href="{{ route('student.meritlist.create', ['type'=> 'honours']) }}" class="btn btn-sm btn-primary add_new" data-label="Honours Merit Student"><i class="fal fa-plus"></i> Add New Merit Student</a>
          </div>
          <h3 class="panel-title">Honours Merit Lists</h3>
        </header>
        <div class="panel-body">

          <form class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::text('admission_roll', null , ['class'=> 'form-control form-control-sm filter', 'id' => 'admission_roll', 'placeholder'=> '<--Search Roll-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('session', filter_empty_array(selective_multiple_session()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'session', 'data-placeholder' => '<--Session-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('faculty', filter_empty_array(selective_faculties()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'faculty', 'data-placeholder' => '<--Faculty-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('subject', filter_empty_array(selective_multiple_subject()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'subject', 'data-placeholder' => '<--Department-->']) !!}
            </div>

            <div class="form-group">
                {!! Form::select('admission_status' ,selective_admission_status(),null, ['class'=> 'form-control form-control-sm selectize filter','id' => 'admission_status', 'data-placeholder' => '<--Admission Status-->']) !!}
            </div>

          </form>
          <table class="table table-hover w-full cell-border text-center table-sm" id="datatable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Admission Roll</th>
                <th>Name</th>
                <th>Department</th>
								<th>Status</th>
								<th>Merit Status</th>
								<th>Merit Position</th>
                <th>Session</th>
								<th>Password</th>
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
        url: "{{ route('student.meritlist.honours.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.admission_roll = $('#admission_roll').val(),
          d.session = $('#session').val(),
          d.faculty = $('#faculty').val(),
          d.subject = $('#subject').val(),
          d.admission_status = $('#admission_status').val()
        },
        dataType: "json",
        type: "POST",
        cache: true
      },
      columns: [
        { data: "id", 'visible':false},
        { data: "admission_roll"},
        { data: "name"},
        { data: "subject"},
        { data: "admission_status"},
        { data: "merit_status"},
        { data: "merit_pos"},
        { data: "session"},
        { data: "password"},
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
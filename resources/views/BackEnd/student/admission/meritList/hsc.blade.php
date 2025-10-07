@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'HSC Merit List Management')

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
            <a href="{{ route('student.meritlist.upload', ['type'=> 'hsc']) }}" class="btn btn-sm btn-info" data-label="HSC Merit Student"><i class="fas fa-upload"></i> Upload Batch Merit List</a>
            <a href="{{ route('student.meritlist.create', ['type'=> 'hsc']) }}" class="btn btn-sm btn-primary add_new" data-label="HSC Merit Student"><i class="fal fa-plus"></i> Add New Merit Student</a>
          </div>
          <h3 class="panel-title">HSC Merit Lists</h3>
        </header>
        <div class="panel-body">

          <form class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::text('ssc_roll', null , ['class'=> 'form-control form-control-sm filter', 'id' => 'ssc_roll', 'placeholder'=> '<--Search SSC Roll-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('ssc_group', filter_empty_array(selective_hsc_groups()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'ssc_group', 'data-placeholder' => '<--SSC Group-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('ssc_board', filter_empty_array(selective_boards()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'ssc_board', 'data-placeholder' => '<--SSC Board-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('session', filter_empty_array(selective_multiple_session()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'session', 'data-placeholder' => '<--Session-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('quota', [''=> '>--Select--<', 1=> 'Quota',0 => 'Non Quota'] ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'quota', 'data-placeholder' => '<--Quota-->']) !!}
            </div>

            <div class="form-group">
                {!! Form::select('admission_status' ,selective_admission_status(),null, ['class'=> 'form-control form-control-sm selectize filter','id' => 'admission_status', 'data-placeholder' => '<--Admission Status-->']) !!}
            </div>

          </form>
          <table class="table table-hover w-full cell-border text-center table-sm" id="datatable">
            <thead>
              <tr>
                <th>ID</th>
                <th>SSC Roll</th>
                <th>Name</th>
                <th>Groups</th>
                <th>Board</th>
                <th>Passing Year</th>
								<th>Status</th>
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
        url: "{{ route('student.meritlist.hsc.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.ssc_roll = $('#ssc_roll').val(),
          d.ssc_group = $('#ssc_group').val(),
          d.ssc_board = $('#ssc_board').val(),
          d.session = $('#session').val(),
          d.groups = $('#groups').val(),
          d.quota = $('#quota').val(),
          d.admission_status = $('#admission_status').val()
        },
        dataType: "json",
        type: "POST",
        cache: true
      },
      columns: [
        { data: "id", 'visible':false},
        { data: "ssc_roll"},
        { data: "name"},
        { data: "ssc_group"},
        { data: "ssc_board"},
        { data: "passing_year"},
        { data: "admission_status"},
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
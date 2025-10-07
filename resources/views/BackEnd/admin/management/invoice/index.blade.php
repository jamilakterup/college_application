@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Invoice Management')

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
  .list-group-item{
      border: 1px solid rgba(0,0,0,.125) !important;
  }
</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions">
            <button class="btn btn-sm btn-danger d-none" id="deleteAllBtn">Delete All</button>
            <a href="{{ route('admin.invoice.create') }}" class="btn btn-sm btn-primary add_new" data-label="Invoice"><i class="fal fa-plus"></i> Add New Invoice</a>
          </div>
          <h3 class="panel-title">Invoice Lists</h3>
        </header>
        <div class="panel-body">

          <form class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::text('roll', null , ['class'=> 'form-control form-control-sm filter', 'id' => 'roll', 'placeholder'=> '<--Search Roll-->']) !!}
            </div>

            <div class="form-group">
                {!! Form::select('type', filter_empty_array(getEnumValues('invoices', 'type')) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'type', 'data-placeholder' => '<--Type-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('pro_group', filter_empty_array(selective_faculties()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'pro_group', 'data-placeholder' => '<--Faculty-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('subject', filter_empty_array(selective_multiple_subject()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'subject', 'data-placeholder' => '<--Subject-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('level', filter_empty_array(selective_multiple_level()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'level', 'data-placeholder' => '<--Level-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('session', filter_empty_array(selective_multiple_session()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'session', 'data-placeholder' => '<--Session-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('exam_year', filter_empty_array(selective_multiple_exam_year()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'exam_year', 'data-placeholder' => '<--Exam Year-->']) !!}
            </div>
          </form>
          <table class="table table-hover w-full cell-border text-center table-sm" id="datatable">
            <thead>
              <tr>
                <th></th>
                <th>ID</th>
                <th><input type="checkbox" name="main_checkbox"><label></label></th>
                <th>Name</th>
                <th>Roll</th>
                <th>Type</th>
                <th>Level</th>
                <th>Pro Group</th>
                <th>Subject</th>
                <th>Total Amount</th>
                <th>Session</th>
                <th>Exam Year</th>
                <th>Start Date</th>
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
        url: "{{ route('admin.invoice.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.roll = $('#roll').val(),
          d.pro_group = $('#pro_group').val(),
          d.group_dept = $('#group_dept').val(),
          d.subject = $('#subject').val(),
          d.level = $('#level').val(),
          d.session = $('#session').val(),
          d.exam_year = $('#exam_year').val(),
          d.type = $('#type').val()
        },
        dataType: "json",
        type: "POST",
        cache: true
      },
      columns: [
        { data: "details",orderable: false, searchable: false,width : '25px'},
        { data: "id", visible:false},
        { data: "checkbox",orderable: false, searchable: false, width : '50px'},
        { data: "name"},
        { data: "roll"},
        { data: "type"},
        { data: "level"},
        { data: "pro_group"},
        { data: "subject"},
        { data: "total_amount"},
        { data: "admission_session"},
        { data: "passing_year"},
        { data: "date_start"},
        { data: "actions", orderable: false, searchable: false,width: "100px"}
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      scrollY: '70vh',
      sDom: 'rtlipf',
      responsive: true,
      order: [[ 1, "desc" ]],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
      bAutoWidth: true,
    });

  datatable.columns.adjust().draw();
  table = datatable;

  $(document).on('change','.filter', function(){
    datatable.draw();
  });

  $(document).on('input','.filter', function(){
    datatable.draw();
  });
  
});


$(function () {
    $(document).on('click','.invoice_generate', function() {
      event.preventDefault();
      trigger_ajax_proccessing($(this));
    });
  });
  
</script>

{{ajax_crud_setup_dtable()}}

@include('BackEnd.common.checkbox_delete_action', ['route'=> route('admin.invoice.delete.all')]);
@endpush
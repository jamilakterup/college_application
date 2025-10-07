@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Header Management')

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
            <a href="{{ route('admin.payslip_header.create') }}" class="btn btn-sm btn-primary add_new" data-label="Payslip Header"><i class="fal fa-plus"></i> Add PaySlip Header</a>
          </div>
          <h3 class="panel-title">PaySlip Header Lists</h3>
        </header>
        <div class="panel-body">

          <form class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::select('pro_group', filter_empty_array(selective_multiple_group()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'pro_group', 'data-placeholder'=> '<--Group-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('group_dept', filter_empty_array(selective_faculties()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'group_dept', 'data-placeholder' => '<--Faculty-->']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('subject', filter_empty_array(selective_multiple_subject()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'subject', 'data-placeholder' => '<--Department-->']) !!}
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

            <div class="form-group">
                {!! Form::select('type', filter_empty_array(selective_multiple_type()) ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'type', 'data-placeholder' => '<--Type-->']) !!}
            </div>
          </form>
          <table class="table table-hover w-full cell-border text-center table-sm" id="datatable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Group</th>
                <th>Faculty</th>
                <th>Department</th>
                <th>Level</th>
                <th>Session</th>
                <th>Exam Year</th>
                <th>Header Type</th>
                <th>Formfillup Type</th>
                <th>Action</th>
                <th>Operations</th>
              </tr>
            </thead>
            
            <tbody id="tdata">

            </tbody>
          </table>
        </div>
      </div>

      {{ajax_modal()}}
      {{ajax_basic_modal()}}

@endsection

@push('scripts')

<script>
$(document).ready(function() {
  datatable = $('#datatable').DataTable({
      ajax:{
        url: "{{ route('admin.payslip_header.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
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
      },
      columns: [
        { data: "id", 'visible':false},
        { data: "title"},
        { data: "pro_group"},
        { data: "group_dept"},
        { data: "subject"},
        { data: "level", width:"50px"},
        { data: "session"},
        { data: "exam_year"},
        { data: "type"},
        { data: "formfillup_type"},
        { data: "actions", orderable: false, searchable: false,width: "100px"},
        { data: "operations", orderable: false, searchable: false }
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      // searchDelay: 500,
      scrollY: '70vh',
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      // sDom: 'Lfrtlip',
      sDom: 'rtlipf',
      responsive: true,
      // aaSorting: [],
      // aaSorting: [ 0, "desc" ],
      order: [[ 0, "desc" ]],

    //   aoColumns: [{
    //     "bSortable": true,
    //     "mData": 0
    // }],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      bAutoWidth: true,
      // columnDefs: [
      //   // { orderable: false, targets: 4 }
      //   // { "width": "10%", "targets": [1,4] }
      // ],
      // columnDefs: [
      //       { width: "200px", targets: 10 }
      //   ]



    });

  datatable.columns.adjust().draw();

  $(document).on('change','.filter', function(){
    datatable.draw();
  });

  $(document).on('keyup','.filter', function(){
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
@endpush
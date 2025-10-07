@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', " FormFillup Config Management")

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
    <header class="panel-heading">
      <h3 class="panel-title">Formfillup Configs</h3>
    </header>
    <div class="panel-body">
      <table class="d-flex justify-content-center w-full">
       <tr>

          <td>
           {!! Form::select('course', student_course_list(), null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'course', 'data-placeholder' => '<--Select Course-->']) !!}
         </td>

         <td>
           {!! Form::select('current_level', selective_multiple_level() ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'current_level', 'data-placeholder' => '<--Select Level-->']) !!}
         </td>

         <td>
           {!! Form::select('open', [''=> '', '1' => 'Open', '0'=> 'Closed'] , null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'open', 'data-placeholder' => '<--Change Status-->']) !!}
         </td>
         
       </tr>
     </table>
      <table class="table table-hover w-full cell-border" id="datatable">
        <thead>
          <tr>
          	  <th>ID</th>
              <th>Current Level</th>
              <th>Session</th>
              <th>Open</th>
              <th>Exam Year</th>
              <th>Opening Date</th>
              <th>Clossing Date</th>
              <th>Edit</th>
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
        url: "{{ route('admin.formfillup.config.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.course = $('#course').val()
          d.open = $('#open').val(),
          d.current_level = $('#current_level').val()
        },
        dataType: "json",
        type: "POST",
      },
      columns: [
        { data: "id"},
        { data: "current_level"},
        { data: "session"},
        { data: "open"},
        { data: "exam_year"},
        { data: "opening_date"},
        { data: "clossing_date"},
        { data: "actions",className:"dt_options", orderable: false, searchable: false }
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      scrollY: '50vh',
      sDom: 'rtlipf',
      responsive: true,
      order: [[ 0, "desc" ]],
      aaSorting: [],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
    });

  datatable.columns.adjust().draw();

  $(document).on('change','.filter', function(){
    datatable.draw();
  });

  $(document).on('keyup','.filter', function(){
    datatable.draw();
  });

});
  
</script>
@endpush
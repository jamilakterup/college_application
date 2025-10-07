@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Student Management')

@push('styles')
<style type="text/css">
</style>
@endpush

@section('content')

@can('honours.admission.index')
<div class="panel">
  <div class="panel-body">

      <div class="row d-flex justify-content-center form-inline">
        <div class="form-group">
          {!! Form::text('student_id', null, ['class'=> 'form-control filter', 'placeholder' => 'Student ID','id'=> 'student_id']) !!}
        </div>

        <div class="form-group">
          {!! Form::text('admission_roll', null, ['class'=> 'form-control filter', 'placeholder' => 'Admission Roll', 'size'=> '12', 'id'=> 'admission_roll']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('faculty', selective_multiple_faculty(), null ,['class'=> 'form-control filter selectize get_options', 'data-placeholder'=>'--Select Faculty--', 'data-options-for'=> '#department', 'data-options-url'=> url("api/get_deptartment_options"), 'id'=> 'faculty']) !!}
      </div>
  
      <div class="form-group">
          {!! Form::select('department', [], null ,['class'=> 'form-control filter selectize', 'id'=>'department','data-placeholder'=>'--Select Department--']) !!}
      </div>

        <div class="form-group">
          {!! Form::select('current_level', selective_multiple_honours_level(), $current_level ?? null, ['class'=>'form-control group filter selectize', 'autocomplete'=> 'off', 'id'=> 'current_level', 'data-option'=> '--Select Level--']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control filter session selectize', 'autocomplete'=> 'off' , 'id' => 'session','data-placeholder'=> '--Select Session--']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('merit_status',DB::table('student_info_hons')->groupBy('merit_status')->pluck('merit_status', 'merit_status')->toArray(), $merit_status ?? null, ['class'=>'form-control filter merit_status selectize', 'autocomplete'=> 'off' , 'id' => 'merit_status','data-placeholder'=> '--Select Merit Status--']) !!}
        </div>
      </div>

  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between bg-light">
    <h3>Honours Student List</h3>
    <div class="mt-3">
      <button class="btn btn-warning d-none" id="checkboxAllBtn" data-action="Force Promotion">Action</button>
      @include('BackEnd.student.admission.honours.particles.subMenu')
      @can('student.honours.create')
        <a href="{{route('students.honours.create')}}" class="btn btn-primary add_new" data-label="Honours Student">Add A New Student</a>
      @endcan
    </div>

  </div>
  <div class="card-body">
    <table class="table table-hover w-full cell-border" id="datatable">
          <thead>
            <tr>
              <th><input type="checkbox" name="main_checkbox"><label></label></th>
              <th>Image</th>
              <th>Student ID</th>
              <th>Class Roll</th>
              <th>Admission Roll</th>
              <th>Name</th>
              <th>Faculty</th>
              <th>Department</th>
              <th>Current Level</th>
              <th>Merit Status</th>
              <th>Father Name</th>
              <th>Mother Name</th>
              <th>Guardian</th>
              <th>Birth Date</th>
              <th>Blood Group</th>
              <th>Contact No</th>
              <th>Address</th>
              <th>Gender</th>
              <th>Religion</th>
              <th>Session</th>
              <th>SSC Reg</th>
              <th>HSC Reg</th>
              <th>Total Amount</th>
              <th>Payment Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          
    </table>
  </div>
</div>

@endcan

{{ajax_modal()}}
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
      datatable = $('#datatable').DataTable({
          ajax:{
            url: "{{ route('students.honours.datasource') }}",
            data: function (d) {
              d._token = "{{ csrf_token() }}",
              d.student_id = $("#student_id").val(),
              d.admission_roll = $("#admission_roll").val(),
              d.faculty = $("#faculty").val(),
              d.department = $("#department").val(),
              d.session = $("#session").val(),
              d.current_level = $("#current_level").val()
            },
            dataType: "json",
            type: "POST",
          },
          columns: [
            { data: "checkbox",orderable: false, searchable: false, width : '50px'},
            { data: "image",orderable: false, searchable: false},
            { data: "id", visible: true},
            { data: "class_roll", 'visible': false},
            { data: "admission_roll"},
            { data: "name"},
            { data: "faculty_name"},
            { data: "dept_name"},
            { data: "current_level"},
            { data: "merit_status", visible: false},
            { data: "father_name", visible: false},
            { data: "mother_name", visible: false},
            { data: "guardian", visible: false},
            { data: "birth_date", visible: false},
            { data: "blood_group", visible: false},
            { data: "contact_no", visible: false},
            { data: "address", visible:false, searchable: false, orderable: false},
            { data: "gender", visible: false},
            { data: "religion", visible: false},
            { data: "session"},
            { data: "hsc_reg", visible: false},
            { data: "ssc_reg", visible: false},
            { data: "total_amount", visible: false},
            { data: "payment_date", visible: false},
            { data: "actions", orderable: false, searchable: false},
          ],
          autoWidth : true,
          processing: true,
          serverSide: true,
          searching : true,
          deferRender: true,
        //   scrollCollapse: true,
          scrollY: '70vh',
        //   sDom: 'rtlipf',
          responsive: true,
          // order: [[ 0, "desc" ]],
          aaSorting: [],
          lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
          iDisplayLength: 50,
          dom: 'lBfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
                },
                {
                  extend:    'csvHtml5',
                  exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                      columns: ':visible'
                    },
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    customize: function(doc) {
                      doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                    }
                },
                'colvis'
           ],
          initComplete: function () {
              var btns = $('.dt-button');
              btns.addClass('btn btn-info btn-sm');
              btns.removeClass('dt-button');
          }
        });
    
      datatable.columns.adjust().draw();
    
    });

    $(document).on('.change','.filter', function(){
      datatable.draw();
    });
  
    $(document).on('keyup','.filter', function(){
      datatable.draw();
    });

    $(document).on('blur','.filter', function(){
      datatable.draw();
    });
    
</script>

@include('BackEnd.common.checkbox_row_action', ['route'=> route('students.honours.force_promotion')]);
@endpush
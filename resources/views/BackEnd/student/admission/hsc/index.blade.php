@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'HSC Student Management')

@push('styles')
<style type="text/css">
</style>
@endpush

@section('content')

@can('hsc.admission.index')
<div class="panel">
  <div class="panel-body">

      <div class="row d-flex justify-content-center form-inline">
        <div class="form-group">
          {!! Form::text('student_id', null, ['class'=> 'form-control filter', 'placeholder' => 'Student ID','id'=> 'student_id']) !!}
        </div>

        <div class="form-group">
          {!! Form::text('ssc_roll', null, ['class'=> 'form-control filter', 'placeholder' => 'SSC Roll', 'size'=> '12', 'id'=> 'ssc_roll']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('groups', selective_multiple_study_group(), null ,['class'=> 'form-control filter selectize', 'data-placeholder'=>'--Select Group--', 'id'=> 'groups']) !!}
      </div>

        <div class="form-group">
          {!! Form::select('current_level', selective_multiple_hsc_level(), $current_level ?? null, ['class'=>'form-control group filter selectize', 'autocomplete'=> 'off', 'id'=> 'current_level', 'data-option'=> '--Select Level--']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control filter session selectize', 'autocomplete'=> 'off' , 'id' => 'session','data-placeholder'=> '--Select Session--']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('gender', selective_gender_list(), $gender ?? null, ['class'=>'form-control filter gender selectize', 'id' => 'gender','data-placeholder'=> '--Select gender--']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('merit_status',DB::table('student_info_hsc')->groupBy('merit_status')->pluck('merit_status', 'merit_status')->toArray(), $merit_status ?? null, ['class'=>'form-control filter merit_status selectize' ,'id' => 'merit_status','data-placeholder'=> '--Select Merit Status--']) !!}
        </div>
      </div>

  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between bg-light">
    <h3>HSC Student List</h3>
    <div class="mt-3">
      <button class="btn btn-warning d-none" id="checkboxAllBtn" data-action="Force Promotion">Action</button>
      @include('BackEnd.student.admission.hsc.particles.subMenu')
      @can('student.hsc.create')
        <a href="{{route('students.hsc.create')}}" class="btn btn-primary add_new" data-label="HSC Student">Add A New Student</a>
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
              <th>SSC Roll</th>
              <th>Name</th>
              <th>Groups</th>
              <th>Current Level</th>
              <th>Father Name</th>
              <th>Mother Name</th>
              <th>Guardian Name</th>
              <th>Guardian Phone</th>
              <th>Birth Date</th>
              <th>Blood Group</th>
              <th>Contact No</th>
              <th>Address</th>
              <th>Gender</th>
              <th>Religion</th>
              <th>Session</th>
              <th>SSC Reg</th>
              <th>SSC Session</th>
              <th>SSC Passing Year</th>
              <th>Subject Info</th>
              <th>Total Amount</th>
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
            url: "{{ route('students.hsc.datasource') }}",
            data: function (d) {
              d._token = "{{ csrf_token() }}",
              d.student_id = $("#student_id").val(),
              d.ssc_roll = $("#ssc_roll").val(),
              d.groups = $("#groups").val(),
              d.session = $("#session").val(),
              d.current_level = $("#current_level").val(),
              d.merit_status = $("#merit_status").val(),
              d.gender = $("#gender").val()
            },
            dataType: "json",
            type: "POST",
          },
          columns: [
            { data: "checkbox",orderable: false, searchable: false, width : '50px'},
            { data: "image",orderable: false, searchable: false},
            { data: "id", visible: true},
            { data: "class_roll", 'visible': false},
            { data: "ssc_roll"},
            { data: "name"},
            { data: "groups"},
            { data: "current_level"},
            { data: "father_name", visible: false},
            { data: "mother_name", visible: false},
            { data: "guardian_name", visible: false,'searchable': false, orderable: false},
            { data: "guardian_phone", visible: false,'searchable': false, orderable: false},
            { data: "birth_date", visible: false},
            { data: "blood_group", visible: false, 'searchable': false, orderable: false},
            { data: "contact_no", visible: false},
            { data: "address", visible:false, searchable: false, orderable: false},
            { data: "gender", visible: false},
            { data: "religion", visible: false},
            { data: "session"},
            { data: "ssc_reg_no", visible: false},
            { data: "ssc_session", visible: false},
            { data: "ssc_passing_year", visible: false},
            { data: "hsc_subjects_info", visible: false},
            { data: "total_amount", visible: false},
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

@include('BackEnd.common.checkbox_row_action', ['route'=> route('students.hsc.force_promotion')]);
@endpush
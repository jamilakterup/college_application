@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Permission Management')
@push('vendor_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/selectize/selectize.bootstrap4.css') }}">
@endpush

@push('styles')
<style type="text/css">


</style>
@endpush

@section('content')

<div class="panel">
    <header class="panel-heading">
      <div class="panel-actions">
      	<a href="{{route('admin.permission.create')}}" class="btn btn-sm btn-primary add_new"><i class="fal fa-plus"></i> Add New Permission</a>
      </div>
      <h3 class="panel-title">Permission Lists</h3>
    </header>
    <div class="panel-body">
      <table class="d-flex justify-content-center w-full">
        @php
          $permission_group_names = App\Models\Permission::groupBy('group_name')->pluck('group_name', 'group_name')->toArray();
          $permission_parent_group_names = App\Models\Permission::groupBy('parent_group_name')->pluck('parent_group_name', 'parent_group_name')->toArray();
        @endphp
       <tr>

        <td>
           {!! Form::text('name', null , ['class'=> 'form-control form-control-sm filter', 'id' => 'permission_name', 'placeholder' => 'Search Name']) !!}
         </td>

         <td>
           {!! Form::select('group_name', [''=>'Select']+ $permission_group_names ,null , ['class'=> 'form-controlselectize selectize filter', 'id' => 'group_name', 'data-placeholder' => '--Group Name--']) !!}
         </td>
         <td>
           {!! Form::select('parent_group_name',[''=>'Select']+$permission_parent_group_names ,null , ['class'=> 'form-control form-control-sm selectize filter', 'id' => 'parent_group_name', 'data-placeholder' => '-- Parent Group--']) !!}
         </td>
         
       </tr>
     </table>

      <table class="table table-hover w-full cell-border text-center" id="datatable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
						<th>Group Name</th>
						<th>Parent Group Name</th>
						<th>Guard Name</th>
						<th class="text-center">Actions</th>
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
        url: "{{ route('admin.permission.datasource') }}",
        data: function (d) {
          d._token = "{{ csrf_token() }}",
          d.name = $('#permission_name').val()
          d.group_name = $('#group_name').val(),
          d.parent_group_name = $('#parent_group_name').val()
        },
        dataType: "json",
        type: "POST",
      },
      columns: [
        { data: "id", 'visible':false},
        { data: "name"},
        { data: "group_name"},
        { data: "parent_group_name"},
        { data: "guard_name"},
        { data: "actions",className:"dt_options", orderable: false, searchable: false }
      ],
      autoWidth : true,
      processing: true,
      serverSide: true,
      searching : false,
      scrollY: '50vh',
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      // sDom: 'Lfrtlip',
      sDom: 'rtlipf',
      responsive: true,
      // aaSorting: [ 0, "desc" ],
      order: [[ 0, "desc" ]],
      aaSorting: [],
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      iDisplayLength: 50,
      // dom: '<"top"i>rt<"bottom"flp><"clear">',
      // bAutoWidth: false,
      columnDefs: [
        // { orderable: false, targets: 4 }
        // { "width": "10%", "targets": [1,4] }
      ],



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
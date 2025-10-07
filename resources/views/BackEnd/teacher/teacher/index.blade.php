@extends('BackEnd.teacher.layouts.master')
@section('page-title', 'Teacher Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
  <header class="panel-heading">
    <div class="panel-actions"><a href="{{ route('teacher.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Teacher</a></div>
    <h3 class="panel-title">Teacher Lists</h3>
  </header>
  <div class="panel-body">
    <table class="table table-hover w-full cell-border" id="teacherDatatable">
      <thead>
        <tr>
          <th scope="col">Teacher ID</th>
          <th scope="col">Name</th>
          <th scope="col">Home District</th>
          <th scope="col">Contact No.</th>
          <th scope="col">Status</th>
          <th scope="col">Details</th>
          <th scope="col">Download PDS</th>
          <th scope="col">Release</th>
          <th scope="col">Joining Letter</th>
          <th scope="col">Release Letter</th>
          <th scope="col">Edit</th>
          <th scope="col">Delete</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection

@php
    $datatable = 'teacherDatatable';
    $table_columns = [
        ['data'=> 'id'],
        ['data'=> 'name','className'=> 'text-center'],
        ['data'=> 'home_district'], 
        ['data'=> 'personal_mobile'],
        ['data'=> 'status'],
        ['data'=> 'details', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'pds', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'release', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'joining_letter', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'release_letter', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'edit', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
        ['data'=> 'delete', 'orderable'=> false, 'searchable'=> false,'className'=> 'text-center'],
    ];

    $datatableProperty = [
        'scrollCollapse' => false,
        'scrollY' => '50vh'
    ];
@endphp

@includeIf('common.datatable', [
    'datatable'=> $datatable,
    'url'=> route('teacher.datasource'),
    'table_columns'=> $table_columns ?? [],
    'properties' =>$datatableProperty ?? []
])
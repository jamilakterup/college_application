@extends('BackEnd.teacher.layouts.master')
@section('page-title', 'Teacher Management')

@section('content')
<div class="col-12">
    <div class="card">
        <div class="card-header align-items-center d-flex justify-content-between bg-light">
            <h4 class="card-title mb-0 flex-grow-1"><i data-feather="list" class="icon-md"></i>University Lists</h4>

            <button data-href="{{ route('teacher.university-list.create') }}" data-action="create" onclick="getAjaxModalData(this, 'Add New University')" class="btn btn-primary">
                <div class="d-flex align-items-center">
                    <i class="fa fa-plus mr-1"></i> Add New University
                </div>
            </button>
        </div><!-- end card header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover nowrap table-striped align-middle mb-0" id="datatable">
                    <thead>
                        <tr class="bg-soft-primary">
                            <th scope="col">SL</th>
                            <th scope="col">Name</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!--end col-->
</div>

@endsection

@php
    $datatable = 'datatable';
    $table_columns = [
        ['data'=> 'id', 'visible'=>false], 
        ['data'=> 'name', 'className'=> 'text-center'],
        ['data'=> 'actions', 'orderable'=> false, 'searchable'=> false]
    ];
    $datatableProperty = [
        'scrollY' => '50vh',
        'scrollCollapse' => false
    ];
@endphp

@includeIf('common.datatable', [
    'datatable'=> $datatable,
    'url'=> route('teacher.university-list.datasource'),
    'table_columns'=> $table_columns ?? [],
    // 'data' => $filterData ?? [],
    'properties' =>$datatableProperty ?? []
])
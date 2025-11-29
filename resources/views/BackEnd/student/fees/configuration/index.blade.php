@extends('BackEnd.student.layouts.master')
@section('page-title', 'Fees Configuration Management')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="card-title" id="total-configs">-</h3>
                    <p class="card-text">Total Configurations</p>
                    <i class="fas fa-cog fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="card-title" id="active-configs">-</h3>
                    <p class="card-text">Active</p>
                    <i class="fas fa-check-circle fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="card-title" id="open-configs">-</h3>
                    <p class="card-text">Currently Open</p>
                    <i class="fas fa-door-open fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="card-title" id="courses-count">-</h3>
                    <p class="card-text">Courses</p>
                    <i class="fas fa-graduation-cap fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('student.fees-configuration.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Add New Configuration
                </a>
                <button type="button" class="btn btn-info" id="refresh-table">
                    <i class="fas fa-sync mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i> Filters
            </h3>
            <div class="ml-auto">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="course-filter">Course</label>
                            <select class="form-control" id="course-filter" name="course">
                                <option value="">All Courses</option>
                                <option value="App\Models\StudentInfoHsc">HSC</option>
                                <option value="App\Models\StudentInfoHons">Honours</option>
                                <option value="App\Models\StudentInfoDegree">Degree</option>
                                <option value="App\Models\StudentInfoMasters">Masters</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status-filter">Status</label>
                            <select class="form-control" id="status-filter" name="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="d-none d-sm-block">&nbsp;</label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <i class="fas fa-search mr-1"></i> Apply
                                </button>
                                <button type="button" class="btn btn-secondary" id="reset-filters">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table mr-2"></i> Configurations
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="configuration-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Course</th>
                            <th>Level</th>
                            <th>Session</th>
                            <th>Date Range</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End container-fluid -->
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1rem;
    }
    .position-absolute {
        position: absolute;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Load summary statistics
    loadSummary();

    // Initialize DataTable
    const table = $('#configuration-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('student.fees-configuration.data') }}",
            data: function (d) {
                d.course = $('#course-filter').val();
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'type_name', name: 'type', searchable: true },
            { data: 'course_name', name: 'course_name' },
            { data: 'level', name: 'level', defaultContent: 'N/A' },
            { data: 'academic_session', name: 'academic_session', defaultContent: 'N/A' },
            { data: 'date_range', name: 'date_range', orderable: false },
            { data: 'status_badge', name: 'status_badge', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Apply filters
    $('#apply-filters').on('click', function() {
        table.ajax.reload();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        table.ajax.reload();
    });

    // Refresh table
    $('#refresh-table').on('click', function() {
        table.ajax.reload();
        loadSummary();
    });

    // Toggle status
    $(document).on('click', '.toggle-status', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        
        if (confirm('Are you sure you want to change the status?')) {
            $.ajax({
                url: `/students/fees-configuration/${id}/status`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                        loadSummary();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error updating status');
                }
            });
        }
    });

    // Delete configuration
    $(document).on('click', '.delete-config', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this configuration? This action cannot be undone.')) {
            $.ajax({
                url: `/students/fees-configuration/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                        loadSummary();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error deleting configuration');
                }
            });
        }
    });

    // Load summary statistics
    function loadSummary() {
        $.ajax({
            url: "{{ route('student.fees-configuration.summary') }}",
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#total-configs').text(response.data.total);
                    $('#active-configs').text(response.data.active);
                    $('#open-configs').text(response.data.open);
                    $('#courses-count').text(response.data.courses);
                }
            },
            error: function(xhr) {
                console.error('Error loading summary');
            }
        });
    }
});
</script>
@endpush

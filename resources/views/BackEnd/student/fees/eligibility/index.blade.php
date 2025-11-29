@extends('BackEnd.student.layouts.master')
@section('page-title', 'Fees Eligibility Management')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="card-title" id="total-eligible">-</h3>
                        <p class="card-text">Total Eligible</p>
                        <i class="fas fa-users fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="card-title" id="active-eligible">-</h3>
                        <p class="card-text">Active Eligible</p>
                        <i class="fas fa-check-circle fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3 class="card-title" id="inactive-eligible">-</h3>
                        <p class="card-text">Inactive Eligible</p>
                        <i class="fas fa-times-circle fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="card-title" id="sessions-count">-</h3>
                        <p class="card-text">Sessions</p>
                        <i class="fas fa-calendar fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEligibilityModal">
                        <i class="fas fa-plus mr-1"></i> Add Eligibility
                    </button>
                    <a href="{{ route('student.fees-eligibility.csv-upload') }}" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> CSV Upload
                    </a>
                    <button type="button" class="btn btn-info" id="refresh-table">
                        <i class="fas fa-sync mr-1"></i> Refresh
                    </button>
                </div>
                
                <!-- Batch Action Buttons (Hidden initially) -->
                <div class="btn-group ml-2" role="group" id="batch-actions" style="display: none;">
                    <button type="button" class="btn btn-success" id="batch-enable">
                        <i class="fas fa-check mr-1"></i> Enable Selected (<span id="selected-count">0</span>)
                    </button>
                    <button type="button" class="btn btn-warning" id="batch-disable">
                        <i class="fas fa-times mr-1"></i> Disable Selected
                    </button>
                    <button type="button" class="btn btn-danger" id="batch-delete">
                        <i class="fas fa-trash mr-1"></i> Delete Selected
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
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="student-type-filter">Student Type</label>
                                <select class="form-control" id="student-type-filter" name="student_type">
                                    <option value="">All Types</option>
                                    <option value="App\Models\StudentInfoHsc">HSC</option>
                                    <option value="App\Models\StudentInfoHons">Honours</option>
                                    <option value="App\Models\StudentInfoDegree">Degree</option>
                                    <option value="App\Models\StudentInfoMasters">Masters</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="session-filter">Session</label>
                                {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control selectize', 'id'=> 'session-filter', 'data-placeholder' => '--Select Session--']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="status-filter">Status</label>
                                <select class="form-control" id="status-filter" name="status">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
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
                    <i class="fas fa-table mr-2"></i> Eligibility Records
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="eligibility-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="select-all"></th>
                                <th>ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Student Type</th>
                                <th>Session</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
</div>
<!-- End container-fluid -->

<!-- Add Eligibility Modal -->
<div class="modal fade" id="addEligibilityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i> Add Student Eligibility
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="eligibility-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="student_type">Student Type</label>
                                <select class="form-control" id="student_type" name="student_type">
                                    <option value="">Select Type (Optional)</option>
                                    <option value="App\Models\StudentInfoHsc">HSC</option>
                                    <option value="App\Models\StudentInfoHons">Honours</option>
                                    <option value="App\Models\StudentInfoDegree">Degree</option>
                                    <option value="App\Models\StudentInfoMasters">Masters</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="session">Session <span class="text-danger">*</span></label>
                                {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control selectize', 'id'=> 'session', 'required' => true, 'data-placeholder' => '--Select Session--']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="academic_year">Academic Year</label>
                                <input type="text" class="form-control" id="academic_year" name="academic_year" placeholder="e.g., 2024">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="level">Level</label>
                                <input type="text" class="form-control" id="level" name="level" placeholder="e.g., HSC 1st Year">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="student_ids">Student IDs <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="student_ids" name="student_ids" rows="4" required placeholder="Enter student IDs separated by commas (e.g., 1, 2, 3, 4)"></textarea>
                                <small class="form-text text-muted">Enter multiple student IDs separated by commas</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="is_active">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Eligibility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Eligibility Modal -->
<div class="modal fade" id="editEligibilityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Student Eligibility
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="edit-eligibility-form">
                <input type="hidden" id="edit_eligibility_id" name="eligibility_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_student_type">Student Type</label>
                                <select class="form-control" id="edit_student_type" name="student_type">
                                    <option value="">Select Type (Optional)</option>
                                    <option value="App\Models\StudentInfoHsc">HSC</option>
                                    <option value="App\Models\StudentInfoHons">Honours</option>
                                    <option value="App\Models\StudentInfoDegree">Degree</option>
                                    <option value="App\Models\StudentInfoMasters">Masters</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_session">Session <span class="text-danger">*</span></label>
                                {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control selectize', 'id'=> 'edit_session', 'required' => true, 'data-placeholder' => '--Select Session--']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_student_id">Student ID <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_student_id" name="student_id" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_academic_year">Academic Year</label>
                                <input type="text" class="form-control" id="edit_academic_year" name="academic_year" placeholder="e.g., 2024">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_level">Level</label>
                                <input type="text" class="form-control" id="edit_level" name="level" placeholder="e.g., HSC 1st Year">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="edit_is_active">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_is_active" name="is_active" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Eligibility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">
<style>
.card-title h3 {
    font-size: 1.8rem;
    font-weight: bold;
    margin: 0;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}

.table td, .table th {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 0;
}

.custom-switch .custom-control-label::before {
    left: -2.25rem;
    width: 1.75rem;
    pointer-events: all;
    border-radius: 0.5rem;
}

.custom-switch .custom-control-label::after {
    top: calc(0.25rem + 2px);
    left: calc(-2.25rem + 2px);
    width: calc(1rem - 4px);
    height: calc(1rem - 4px);
    border-radius: 0.5rem;
}
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#eligibility-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("student.fees-eligibility.data") }}',
            data: function(d) {
                d.student_type = $('#student-type-filter').val();
                d.session = $('#session-filter').val();
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="row-checkbox" data-id="' + row.id + '">';
                }
            },
            { data: 'id', name: 'id' },
            { data: 'student_id', name: 'student_id' },
            { data: 'student_info', name: 'student_info', orderable: false },
            { data: 'student_type', name: 'student_type' },
            { data: 'session', name: 'session' },
            { data: 'level', name: 'level' },
            { data: 'status', name: 'is_active', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
            emptyTable: 'No eligibility records found',
            zeroRecords: 'No matching records found'
        }
    });

    // Apply filters
    $('#apply-filters').click(function() {
        table.ajax.reload();
        loadSummary();
    });

    // Reset filters
    $('#reset-filters').click(function() {
        $('#filter-form')[0].reset();
        table.ajax.reload();
        loadSummary();
    });

    // Refresh table
    $('#refresh-table').click(function() {
        table.ajax.reload();
        loadSummary();
    });

    // Handle eligibility form submission
    $('#eligibility-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('[type="submit"]');
        var originalText = $submitBtn.html();
        
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...').prop('disabled', true);

        $.ajax({
            url: '{{ route("student.fees-eligibility.store") }}',
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addEligibilityModal').modal('hide');
                    $form[0].reset();
                    table.ajax.reload();
                    loadSummary();
                    showNotification('Eligibility records created successfully!', 'success');
                } else {
                    showNotification(response.message || 'Error creating eligibility records', 'error');
                }
            },
            error: function(xhr) {
                var message = 'Error creating eligibility records';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification(message, 'error');
            },
            complete: function() {
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Toggle status
    $(document).on('click', '.toggle-status', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var status = $btn.data('status');
        var originalText = $btn.html();

        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        // Convert string to boolean/integer
        var isActive = (status === 'true' || status === true || status === 1 || status === '1') ? 1 : 0;

        $.ajax({
            url: '{{ route("student.fees-eligibility.update-status", ":id") }}'.replace(':id', id),
            method: 'PATCH',
            data: {
                is_active: isActive
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                    loadSummary();
                    showNotification('Status updated successfully!', 'success');
                } else {
                    showNotification(response.message || 'Error updating status', 'error');
                }
            },
            error: function(xhr) {
                var message = 'Error updating status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification(message, 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Edit eligibility
    $(document).on('click', '.edit-eligibility', function() {
        var id = $(this).data('id');
        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("student.fees-eligibility.edit", ":id") }}'.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;
                    
                    // Populate the edit form
                    $('#edit_eligibility_id').val(data.id);
                    $('#edit_student_type').val(data.student_type);
                    $('#edit_student_id').val(data.student_id);
                    $('#edit_academic_year').val(data.academic_year);
                    $('#edit_level').val(data.level);
                    $('#edit_is_active').val(data.is_active ? 1 : 0);
                    
                    // Initialize or refresh selectize for session
                    if ($('#edit_session')[0].selectize) {
                        $('#edit_session')[0].selectize.setValue(data.session);
                    } else {
                        $('#edit_session').val(data.session);
                    }
                    
                    // Show the modal
                    $('#editEligibilityModal').modal('show');
                } else {
                    showNotification(response.message || 'Error fetching eligibility data', 'error');
                }
            },
            error: function(xhr) {
                var message = 'Error fetching eligibility data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification(message, 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Handle edit eligibility form submission
    $('#edit-eligibility-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('[type="submit"]');
        var originalText = $submitBtn.html();
        var eligibilityId = $('#edit_eligibility_id').val();
        
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...').prop('disabled', true);

        // Prepare form data
        var formData = $form.serializeArray();
        var dataObject = {};
        $.each(formData, function(i, field) {
            dataObject[field.name] = field.value;
        });

        $.ajax({
            url: '{{ route("student.fees-eligibility.update", ":id") }}'.replace(':id', eligibilityId),
            method: 'PUT',
            data: dataObject,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editEligibilityModal').modal('hide');
                    $form[0].reset();
                    table.ajax.reload();
                    loadSummary();
                    showNotification('Eligibility record updated successfully!', 'success');
                } else {
                    showNotification(response.message || 'Error updating eligibility record', 'error');
                }
            },
            error: function(xhr) {
                var message = 'Error updating eligibility record';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showNotification(message, 'error');
            },
            complete: function() {
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Delete eligibility
    $(document).on('click', '.delete-eligibility', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this eligibility record?')) {
            $.ajax({
                url: '{{ route("student.fees-eligibility.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        loadSummary();
                        showNotification('Eligibility record deleted successfully!', 'success');
                    } else {
                        showNotification(response.message || 'Error deleting record', 'error');
                    }
                },
                error: function(xhr) {
                    var message = 'Error deleting record';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showNotification(message, 'error');
                }
            });
        }
    });

    // Load summary data
    function loadSummary() {
        $.ajax({
            url: '{{ route("student.fees-eligibility.summary") }}',
            method: 'GET',
            data: {
                student_type: $('#student-type-filter').val(),
                session: $('#session-filter').val()
            },
            success: function(response) {
                if (response.success && response.data) {
                    $('#total-eligible').text(response.data.total_eligible);
                    $('#active-eligible').text(response.data.active_eligible);
                    $('#inactive-eligible').text(response.data.inactive_eligible);
                    $('#sessions-count').text(response.data.sessions_count);
                } else {
                    $('#total-eligible').text('0');
                    $('#active-eligible').text('0');
                    $('#inactive-eligible').text('0');
                    $('#sessions-count').text('0');
                }
            },
            error: function() {
                $('#total-eligible').text('Error');
                $('#active-eligible').text('Error');
                $('#inactive-eligible').text('Error');
                $('#sessions-count').text('Error');
            }
        });
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const alertClass = {
            info: 'alert-info',
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning'
        }[type] || 'alert-info';

        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(notification);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Select All checkbox
    $('#select-all').on('click', function() {
        var checked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', checked);
        updateBatchButtons();
    });

    // Individual checkbox
    $(document).on('change', '.row-checkbox', function() {
        updateBatchButtons();
        
        // Update select-all checkbox
        var totalCheckboxes = $('.row-checkbox').length;
        var checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update batch button visibility and count
    function updateBatchButtons() {
        var checkedCount = $('.row-checkbox:checked').length;
        $('#selected-count').text(checkedCount);
        
        if (checkedCount > 0) {
            $('#batch-actions').show();
        } else {
            $('#batch-actions').hide();
        }
    }

    // Get selected IDs
    function getSelectedIds() {
        var ids = [];
        $('.row-checkbox:checked').each(function() {
            ids.push($(this).data('id'));
        });
        return ids;
    }

    // Batch Enable
    $('#batch-enable').on('click', function() {
        var ids = getSelectedIds();
        
        if (ids.length === 0) {
            showNotification('Please select at least one record', 'warning');
            return;
        }
        
        if (!confirm('Are you sure you want to enable ' + ids.length + ' record(s)?')) {
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Enabling...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("student.fees-eligibility.batch-enable") }}',
            method: 'POST',
            data: {
                ids: ids,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    table.ajax.reload();
                    $('#select-all').prop('checked', false);
                    updateBatchButtons();
                    loadSummary();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error enabling records', 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Batch Disable
    $('#batch-disable').on('click', function() {
        var ids = getSelectedIds();
        
        if (ids.length === 0) {
            showNotification('Please select at least one record', 'warning');
            return;
        }
        
        if (!confirm('Are you sure you want to disable ' + ids.length + ' record(s)?')) {
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Disabling...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("student.fees-eligibility.batch-disable") }}',
            method: 'POST',
            data: {
                ids: ids,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    table.ajax.reload();
                    $('#select-all').prop('checked', false);
                    updateBatchButtons();
                    loadSummary();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error disabling records', 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Batch Delete
    $('#batch-delete').on('click', function() {
        var ids = getSelectedIds();
        
        if (ids.length === 0) {
            showNotification('Please select at least one record', 'warning');
            return;
        }
        
        if (!confirm('Are you sure you want to delete ' + ids.length + ' record(s)? This action cannot be undone!')) {
            return;
        }
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Deleting...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("student.fees-eligibility.batch-delete") }}',
            method: 'POST',
            data: {
                ids: ids,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    table.ajax.reload();
                    $('#select-all').prop('checked', false);
                    updateBatchButtons();
                    loadSummary();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error deleting records', 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Initial load
    loadSummary();
});
</script>
@endpush

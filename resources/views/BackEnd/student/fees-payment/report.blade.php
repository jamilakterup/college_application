@extends('BackEnd.student.layouts.master')
@section('page-title', 'Fees Payment List')

@section('content')
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row" id="summary-cards">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h3 class="card-title" id="total-applications">-</h3>
                            <p class="card-text">Total Applications</p>
                            <i class="fas fa-file-alt fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h3 class="card-title" id="paid-applications">-</h3>
                            <p class="card-text">Paid Applications</p>
                            <i class="fas fa-check-circle fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h3 class="card-title" id="pending-applications">-</h3>
                            <p class="card-text">Pending Applications</p>
                            <i class="fas fa-clock fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h3 class="card-title" id="total-revenue">-</h3>
                            <p class="card-text">Total Revenue</p>
                            <i class="fas fa-money-bill-wave fa-2x position-absolute" style="right: 15px; top: 15px; opacity: 0.7;"></i>
                        </div>
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
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="status-filter">Status</label>
                                    <select class="form-control" id="status-filter" name="status" aria-label="Filter by status">
                                        <option value="">All Status</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="date-from">Date From</label>
                                    <input type="text" class="form-control datepickr" id="date-from" name="date_from" aria-label="Filter by start date">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="date-to">Date To</label>
                                    <input type="text" class="form-control datepickr" id="date-to" name="date_to" aria-label="Filter by end date">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="level-filter">Level</label>
                                    <select class="form-control selectize" id="level-filter" name="level" aria-label="Filter by level">
                                        @foreach(selective_multiple_level() as $key => $level)
                                            <option value="{{$key}}">{{$level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="group-filter">Group</label>
                                    <select class="form-control" id="group-filter" name="group_dept" aria-label="Filter by group">
                                        @foreach(selective_multiple_study_group() as $key => $level)
                                            <option value="{{$key}}">{{$level}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="department-filter">Department</label>
                                    <select class="form-control" id="department-filter" name="dept" aria-label="Filter by department">
                                        @foreach(selective_departments() as $key => $level)
                                            <option value="{{$key}}">{{$level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="session-filter">Session</label>
                                    {!! Form::select('academic_session', selective_multiple_session(), null, ['class' => 'form-control selectize', 'id'=> 'session-filter', 'data-placeholder' => '--Select Session--' ,'aria-label'=> 'Filter by Session']) !!}
                                </div>
                            </div>

                            <div class="col-md-9 col-sm-12 mb-3">
                                <div class="form-group">
                                    <label class="d-none d-sm-block">&nbsp;</label>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary" id="apply-filters">
                                            <i class="fas fa-search mr-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="reset-filters">
                                            <i class="fas fa-undo mr-1"></i> Reset
                                        </button>
                                        <button type="button" class="btn btn-success" id="export-btn">
                                            <i class="fas fa-download mr-1"></i> Export CSV
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
                        <i class="fas fa-table mr-2"></i> Fees Payment Applications
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="fees-payment-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Registration ID</th>
                                    <th>Academic Session</th>
                                    <th>Level</th>
                                    <th>Group/Dept</th>
                                    <th>Mobile</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>TRX Id</th>
                                    <th>Application Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Application Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">
                    <i class="fas fa-info-circle mr-2"></i> Application Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-content">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')

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

.info-card {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 0.25rem;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
}

.fee-breakdown-table {
    margin-top: 15px;
}

.fee-breakdown-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.status-paid {
    color: #28a745;
    font-weight: bold;
}

.status-pending {
    color: #ffc107;
    font-weight: bold;
}

.status-failed {
    color: #dc3545;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')

<script>
$(document).ready(function() {
    // Initialize DataTable with Bootstrap 4 styling
    var table = $('#fees-payment-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("student.fees-payment-report.data") }}',
            data: function(d) {
                d.status = $('#status-filter').val();
                d.date_from = $('#date-from').val();
                d.date_to = $('#date-to').val();
                d.level = $('#level-filter').val();
                d.academic_session = $('#session-filter').val();
                d.group_dept = $('#group-filter').val();
                d.dept = $('#department-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'registration_id', name: 'registration_id' },
            { data: 'academic_session', name: 'academic_session' },
            { data: 'current_level', name: 'current_level' },
            { data: 'group_dept', name: 'group_dept' },
            { data: 'mobile', name: 'mobile' },
            { data: 'total_amount', name: 'total_amount', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'txnid', name: 'txnid', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
            emptyTable: 'No fees payment applications found',
            zeroRecords: 'No matching records found'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });

    // Debounce function to limit AJAX calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Load summary data
    const loadSummary = debounce(function() {
        $.ajax({
            url: '{{ route("student.fees-payment-report.summary") }}',
            method: 'GET',
            data: {
                status: $('#status-filter').val(),
                date_from: $('#date-from').val(),
                date_to: $('#date-to').val(),
                level: $('#level-filter').val(),
                academic_session: $('#session-filter').val(),
                group_dept: $('#group-filter').val(),
                dept: $('#department-filter').val(),
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#total-applications').text(data.total_applications || 0);
                    $('#paid-applications').text(data.paid_applications || 0);
                    $('#pending-applications').text(data.pending_applications || 0);
                    $('#total-revenue').text('৳' + formatNumber(data.total_revenue || 0));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading summary:', error);
                $('#total-applications').text('0');
                $('#paid-applications').text('0');
                $('#pending-applications').text('1');
                $('#total-revenue').text('৳0');
            }
        });
    }, 500);

    // Initial load of summary
    loadSummary();

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

    // Export functionality
    $('#export-btn').click(function() {
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Exporting...').prop('disabled', true);

        var params = new URLSearchParams({
            status: $('#status-filter').val(),
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val(),
            level: $('#level-filter').val(),
            academic_session: $('#session-filter').val(),
            group_dept: $('#group-filter').val(),
            dept: $('#department-filter').val(),
        });

        $.ajax({
            url: '{{ route("student.fees-payment-report.export") }}?' + params.toString(),
            method: 'GET',
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        }).done(function() {
            window.location.href = '{{ route("student.fees-payment-report.export") }}?' + params.toString();
        }).fail(function(xhr, status, error) {
            showNotification('Error exporting data. Please try again.', 'error');
        });
    });

    // View details modal
    $(document).on('click', '.view-details', function() {
        var applicationId = $(this).data('id');
        $('#detailsModal').modal('show');
        $('#modal-content').html(`
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("student.fees-payment-report.details") }}',
            method: 'GET',
            data: { id: applicationId },
            success: function(response) {
                if (response.success) {
                    displayApplicationDetails(response.data);
                } else {
                    $('#modal-content').html(`
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${response.message || 'Error loading application details'}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#modal-content').html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error loading application details. Please try again.
                    </div>
                `);
            }
        });
    });

    // Function to display application details in modal
    function displayApplicationDetails(data) {
        var statusClass = getStatusClass(data.status);
        var invoiceSection = '';

        if (data.invoice) {
            invoiceSection = `
                <div class="info-card">
                    <h6 class="mb-3"><i class="fas fa-receipt mr-2"></i> Payment Information</h6>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <span class="detail-label">Total Amount:</span>
                            <span class="detail-value">৳${data.invoice.total_amount}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="detail-label">Payment Date:</span>
                            <span class="detail-value">${data.invoice.payment_date}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="detail-label">Header Title:</span>
                            <span class="detail-value">${data.invoice.header_title}</span>
                        </div>
                    </div>
                </div>
            `;

            if (data.fee_breakdown && data.fee_breakdown.length > 0) {
                invoiceSection += `
                    <div class="fee-breakdown-table">
                        <h6 class="mb-3"><i class="fas fa-list mr-2"></i> Fee Breakdown</h6>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.fee_breakdown.forEach(function(fee) {
                    invoiceSection += `
                        <tr>
                            <td>${fee.title}</td>
                            <td class="text-right">৳${fee.fees}</td>
                        </tr>
                    `;
                });

                invoiceSection += `
                            </tbody>
                        </table>
                    </div>
                `;
            }
        }

        var modalContent = `
            <div class="info-card">
                <h6 class="mb-3"><i class="fas fa-user mr-2"></i> Personal Information</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">${data.name}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Father's Name:</span>
                        <span class="detail-value">${data.father_name || 'N/A'}</span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <span class="detail-label">Mother's Name:</span>
                        <span class="detail-value">${data.mother_name || 'N/A'}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Date of Birth:</span>
                        <span class="detail-value">${data.date_of_birth}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value">${data.gender || 'N/A'}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Mobile:</span>
                        <span class="detail-value">${data.mobile || 'N/A'}</span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h6 class="mb-3"><i class="fas fa-graduation-cap mr-2"></i> Academic Information</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <span class="detail-label">Registration ID:</span>
                        <span class="detail-value">${data.registration_id}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Academic Session:</span>
                        <span class="detail-value">${data.academic_session}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="detail-label">Current Level:</span>
                        <span class="detail-value">${data.current_level}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Group/Department:</span>
                        <span class="detail-value">${data.group_dept || 'N/A'}</span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i> Application Status</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value ${statusClass}">${data.status}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Application ID:</span>
                        <span class="detail-value">#${data.id}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="detail-label">Applied On:</span>
                        <span class="detail-value">${data.created_at}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Last Updated:</span>
                        <span class="detail-value">${data.updated_at}</span>
                    </div>
                </div>
            </div>

            ${invoiceSection}
        `;

        $('#modal-content').html(modalContent);
    }

    // Function to get status CSS class
    function getStatusClass(status) {
        switch(status) {
            case 'Paid':
                return 'status-paid text-success';
            case 'Pending':
                return 'status-pending text-warning';
            case 'Failed':
                return 'status-failed text-danger';
            default:
                return '';
        }
    }

    // Function to format numbers with commas
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Auto-refresh summary every 30 seconds
    setInterval(loadSummary, 30000);

    // Handle filter changes with debounce
    $('#status-filter, #date-from, #date-to, #level-filter, #group-filter', '#department-filter', '#session-filter').on('change', debounce(function() {
        table.ajax.reload();
        loadSummary();
    }, 500));

    // Handle Enter key in filter inputs
    $('#filter-form input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#apply-filters').click();
        }
    });

    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();

    // Handle modal close event
    $('#detailsModal').on('hidden.bs.modal', function() {
        $('#modal-content').html(`
            <div class="text-center">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading...</p>
            </div>
        `);
    });

    // Handle table responsive
    $(window).on('resize', function() {
        table.columns.adjust().responsive.recalc();
    });

    // Custom search functionality
    $('#fees-payment-table_filter input').on('keyup', debounce(function(e) {
        var searchTerm = $(this).val();
        table.search(searchTerm).draw();
    }, 500));

    // Print functionality
    $(document).on('click', '.print-details', function() {
        var applicationId = $(this).data('id');
        window.open('{{ route("fees-payment.download-slip") }}?application_id=' + applicationId, '_blank');
    });

    // Notification function
    function showNotification(message, type = 'info') {
        const alertClass = {
            info: 'alert-info',
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning'
        }[type] || 'alert-info';

        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('body').append(notification);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            loadSummary();
        }
    });
});

// Global function to refresh table
function refreshTable() {
    $('#fees-payment-table').DataTable().ajax.reload(null, false);
    loadSummary();
}
</script>
@endpush
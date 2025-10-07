@extends('BackEnd.student.layouts.master')

@section('title', 'Transfer Certificate Management')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-certificate"></i> Transfer Certificate Management
                <div class="pull-right">
                    <a href="{{ route('certificates.transfer.upload') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-upload"></i> Bulk Upload
                    </a>
                    <a href="{{ route('certificates.transfer.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Add New Transfer Certificate
                    </a>
                </div>
            </h3>
        </div>
        
        <div class="panel-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {!! nl2br(e(session('success'))) !!}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {!! nl2br(e(session('warning'))) !!}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Bulk Operations -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form id="bulkForm" method="POST">
                        @csrf
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm" onclick="bulkDownloadPdf()">
                                <i class="fa fa-download"></i> Bulk Download PDF
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="bulkStatusUpdate('active')">
                                <i class="fa fa-check"></i> Mark Active
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="bulkStatusUpdate('inactive')">
                                <i class="fa fa-pause"></i> Mark Inactive
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                                <i class="fa fa-trash"></i> Bulk Delete
                            </button>
                        </div>
                        <span class="text-muted ml-2" id="selectedCount">0 selected</span>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="transferTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>SL</th>
                            <th>TC No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Roll</th>
                            <th>Session</th>
                            <th>Leaving Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $key => $certificate)
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="{{ $certificate->id }}"></td>
                            <td>{{ $certificates->firstItem() + $key }}</td>
                            <td>{{ $certificate->tc_no }}</td>
                            <td>{{ $certificate->student_name }}</td>
                            <td>{{ $certificate->last_attended_class }}</td>
                            <td>{{ $certificate->roll_no }}</td>
                            <td>{{ $certificate->session }}</td>
                            <td>{{ $certificate->leaving_date->format('d-m-Y') }}</td>
                            <td>{{ Str::limit($certificate->reason_for_leaving, 30) }}</td>
                            <td>
                                <span class="badge badge-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($certificate->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('certificates.transfer.show', $certificate->id) }}" 
                                       class="btn btn-info btn-sm" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('certificates.transfer.pdf', $certificate->id) }}" 
                                       class="btn btn-primary btn-sm" title="Download PDF" target="_blank">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <a href="{{ route('certificates.transfer.edit', $certificate->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('certificates.transfer.destroy', $certificate->id) }}" 
                                          method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this transfer certificate?')" 
                                                title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">No transfer certificates found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                {{ $certificates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#transferTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false
    });

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox change
    $('.row-checkbox').change(function() {
        updateSelectedCount();
        
        // Update select all checkbox
        var totalCheckboxes = $('.row-checkbox').length;
        var checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateSelectedCount() {
        var count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count + ' selected');
    }
});

function getSelectedIds() {
    var ids = [];
    $('.row-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    return ids;
}

function bulkDownloadPdf() {
    var ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one transfer certificate.');
        return;
    }
    
    var form = $('#bulkForm');
    form.attr('action', '{{ route("certificates.transfer.bulk-pdf") }}');
    form.find('input[name="ids[]"]').remove();
    
    $.each(ids, function(index, id) {
        form.append('<input type="hidden" name="ids[]" value="' + id + '">');
    });
    
    form.submit();
}

function bulkStatusUpdate(status) {
    var ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one transfer certificate.');
        return;
    }
    
    if (confirm('Are you sure you want to update the status of selected transfer certificates?')) {
        var form = $('#bulkForm');
        form.attr('action', '{{ route("certificates.transfer.bulk-status") }}');
        form.find('input[name="ids[]"]').remove();
        form.find('input[name="status"]').remove();
        
        $.each(ids, function(index, id) {
            form.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });
        form.append('<input type="hidden" name="status" value="' + status + '">');
        
        form.submit();
    }
}

function bulkDelete() {
    var ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one transfer certificate.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected transfer certificates? This action cannot be undone.')) {
        var form = $('#bulkForm');
        form.attr('action', '{{ route("certificates.transfer.bulk-delete") }}');
        form.find('input[name="ids[]"]').remove();
        
        $.each(ids, function(index, id) {
            form.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });
        
        form.submit();
    }
}
</script>
@endpush
{{-- views/BackEnd/hsc_result/mark_input/list.blade.php --}}
@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Mark Input Management')

@section('content')
    <div class="panel">
        <header class="panel-heading">
            <h3 class="panel-title">Mark Input List</h3>
        </header>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @php
                        $groupName = App\Models\Group::find($group)->name;
                        $examName = App\Models\Exam::find($exam_id)->name;
                        $subject = App\Models\Subject::find($subject_id);
                    @endphp

                    {{-- Session Info Table --}}
                    <table class="table input-mark mb-4">
                        <caption>
                            <strong>Session:</strong> {{ $session }} |
                            <strong>Group:</strong> {{ $groupName }} |
                            <strong>Level:</strong> {{ $curr_level->name }} |
                            <strong>Exam:</strong> {{ $examName }} |
                            <strong>Subject:</strong> {{ $subject->name }} ({{ $subject->code }}) |
                            <strong>Year:</strong> {{ $exam_year }}
                        </caption>
                    </table>

                    {{-- Instructions --}}
                    <div class="alert alert-info mb-4">
                        <h5 class="mb-2"><strong>Instructions:</strong></h5>
                        <ul class="mb-0">
                            <li>If a student was <strong>absent</strong>, input mark as 'A' or 'Absent'</li>
                            <li>Marks are automatically saved after typing</li>
                            <li>Yellow background indicates saving in progress</li>
                            <li>Green background indicates successful save</li>
                            <li>Red background indicates an error in saving or invalid mark</li>
                        </ul>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mb-4">
                        <a href="{{ route('hsc_result.mark_input.mark_pdf', [
                            $session,
                            $group,
                            $curr_level->id,
                            $exam_id,
                            $subject_id,
                            $exam_test_id,
                            $exam_year,
                        ]) }}"
                            target="_blank" class="btn btn-danger">
                            <i class="fa fa-download"></i> Download Marks
                        </a>

                        <button type="button" class="btn btn-success" id="save-all">
                            <i class="fa fa-save"></i> Save All Changes
                        </button>

                        <div class="float-right">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="auto-save" checked
                                    {{ $isDisabled ? 'disabled' : null }}>
                                <label class="form-check-label" for="auto-save">Enable Auto-Save</label>
                            </div>
                        </div>
                    </div>

                    {{-- Status Message --}}
                    <div id="status-message" class="alert" style="display: none;"></div>

                    <div class="mb-3 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input type="checkbox" id="check-all" checked {{ $isDisabled ? 'disabled' : null }}>
                                    <label class="form-check-label fw-semibold" for="check-all">
                                        Select All Students
                                    </label>
                                </div>
                                <span id="selected-count" class="badge bg-primary rounded-pill ml-3">
                                    <i class="bi bi-people-fill me-1"></i>
                                    <span class="count">0</span> selected
                                </span>
                            </div>
                        </div>
                    </div>


                    {{-- Marks Input Table --}}
                    <div class="table-responsive">


                        <table class="table table-lg table-hover table-bordered" id="marks-table" width="100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Table Styles */
        .table {
            background: #fff;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        /* Mark Input Styles */
        .mark-input {
            width: 80px !important;
            display: inline-block !important;
            margin-right: 5px;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .mark-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            border-color: #80bdff;
        }

        .mark-input.saving {
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .mark-input.saved {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .mark-input.error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }


        /* Status Message */
        #status-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: none;
            min-width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .mark-input {
                width: 60px !important;
            }

            .table-responsive {
                border: 0;
                margin-bottom: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let autoSaveEnabled = true;
            let pendingChanges = new Set();
            const configExamParticles = @json($config_exam_particles);
            let columns = [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'student_id',
                    title: 'Student ID',
                    name: 'student_id'
                },
                {
                    data: 'student_roll',
                    title: 'Class Roll',
                    name: 'student_roll'
                },
                {
                    data: 'student_name',
                    title: 'Student Name',
                    name: 'student_name'
                }
            ];

            configExamParticles.forEach(particle => {
                console.log(particle);
                columns.push({
                    data: 'particle_' + particle.xmparticle_id,
                    name: 'particle_' + particle.xmparticle_id,
                    title: `${particle.xmparticle.name}<br/><small>(Max: ${particle.total})</small>`,
                    width: '120px',
                    orderable: false,
                    searchable: false
                });
            });



            // Initialize DataTable
            let table = $('#marks-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.session = '{{ $session }}';
                        d.group = '{{ $group }}';
                        d.current_year = '{{ $current_level }}';
                        d.exam_id = '{{ $exam_id }}';
                        d.subject_id = '{{ $subject_id }}';
                        d.exam_test = '{{ $exam_test_id }}';
                        d.exam_year = '{{ $exam_year }}';
                    }
                },
                columns: columns,

                lengthMenu: [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, "All"]
                ],
                iDisplayLength: 50,
                order: [
                    [1, 'asc']
                ],
                scrollY: '70vh',
                drawCallback: function() {
                    initializeMarkInputs();
                    updateSelectedCount();
                }
            });

            // Checkbox management functions
            function initializeCheckboxHandlers() {
                // Handle "Check All" checkbox
                $('#check-all').on('change', function() {
                    const isChecked = $(this).prop('checked');
                    $('.student-checkbox').prop('checked', isChecked);
                    updateSelectedCount();
                });

                // Handle individual checkboxes
                $(document).on('change', '.student-checkbox', function() {
                    updateCheckAllState();
                    updateSelectedCount();
                });
            }

            // Update the "Check All" checkbox state
            function updateCheckAllState() {
                const totalCheckboxes = $('.student-checkbox').length;
                const checkedCheckboxes = $('.student-checkbox:checked').length;

                $('#check-all').prop({
                    'checked': checkedCheckboxes === totalCheckboxes,
                    'indeterminate': checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes
                });
            }

            // Update selected count display
            function updateSelectedCount() {
                const checkedCount = $('.student-checkbox:checked').length;
                $('#selected-count').text(`${checkedCount} selected`);
            }

            // Get selected student IDs
            function getSelectedStudentIds() {
                return $('.student-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            // Initialize checkbox handlers
            initializeCheckboxHandlers();

            // Auto-save toggle

            $('#auto-save').on('change', function() {
                autoSaveEnabled = $(this).prop('checked');
                showStatus(
                    autoSaveEnabled ? 'Auto-save enabled' : 'Auto-save disabled',
                    autoSaveEnabled ? 'success' : 'warning'
                );

                // If enabling auto-save, save all pending changes
                if (autoSaveEnabled && pendingChanges.size > 0) {
                    saveAllPendingChanges();
                }
            });

            // Save all button
            $('#save-all').on('click', function() {
                saveAllPendingChanges();
            });


            function initializeMarkInputs() {
                let saveTimeout;

                // Helper function to handle mark updates
                const handleMarkUpdate = ($input, mark) => {
                    const data = {
                        input: $input,
                        mark,
                        particle_id: $input.data('particle'),
                        student_id: $input.data('student')
                    };

                    pendingChanges.add(data);
                    if (autoSaveEnabled) {
                        saveMark($input, mark, data.particle_id, data.student_id);
                    }
                };

                // Handle input/paste value processing
                const processValue = ($input, value) => {
                    if (value.toUpperCase() === 'A') {
                        return 'A';
                    }
                    const numValue = parseInt(value, 10);
                    const maxValue = parseInt($input.attr('max'), 10);
                    return !isNaN(numValue) ? Math.min(numValue, maxValue).toString() : '';
                };

                $('.mark-input')
                    .on('keypress', isNumberOrA)
                    .on('input', function() {
                        const $input = $(this);
                        if (validateMark(this)) {
                            clearTimeout(saveTimeout);
                            saveTimeout = setTimeout(() => {
                                handleMarkUpdate($input, $input.val());
                            }, 500);
                        }
                    });
            }

            function isNumberOrA(evt) {
                const charCode = evt.which || evt.keyCode; // Cross-browser support for keyCode

                // Allow 'A' or 'a' (65 for 'A', 97 for 'a')
                if (charCode === 65 || charCode === 97) {
                    return true; // Allow default behavior
                }

                // Allow decimal point (46 is the charCode for '.')
                if (charCode === 46) {
                    const input = $(evt.target);
                    // Allow only one decimal point
                    if (input.val().includes('.')) {
                        evt.preventDefault(); // Prevent additional decimal points
                        return false;
                    }
                    return true; // Allow default behavior
                }

                // Allow numbers (48–57 are the charCodes for '0' to '9')
                if (charCode >= 48 && charCode <= 57) {
                    return true; // Allow default behavior
                }

                // Block all other characters
                evt.preventDefault();
                return false;
            }

            function validateMark(input) {
                const $input = $(input);
                let value = $input.val();

                // Clear previous validation state
                $input.removeClass('error');

                // Handle empty input
                if (!value) {
                    return true;
                }

                // Handle 'A' for absent
                if (value.toUpperCase() === 'A') {
                    $input.val('A');
                    return true;
                }

                // Remove any non-numeric characters
                value = value.replace(/[^0-9]/g, '');

                // Convert to integer and validate
                const numValue = parseInt(value, 10);
                const maxValue = parseInt($input.attr('max'), 10);

                if (isNaN(numValue)) {
                    $input.addClass('error');
                    showStatus?.('Please enter a valid number or A for absent', 'danger');
                    return false;
                }

                // Check maximum value
                if (numValue > maxValue) {
                    $input.val(maxValue);
                    showStatus?.('Maximum mark is ' + maxValue, 'warning');
                    return true;
                }

                // Update input with cleaned value
                $input.val(numValue);
                return true;
            }

            function saveMark($input, mark, particle_id, student_id) {
                $input.addClass('saving');

                $.ajax({
                    url: '{{ route('hsc_result.mark_input.save_mark') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mark: mark,
                        particle_id: particle_id,
                        student_id: student_id,
                        exam_id: '{{ $exam_id }}',
                        subject_id: '{{ $subject_id }}',
                        exam_test_id: '{{ $exam_test_id }}',
                        exam_year: '{{ $exam_year }}',
                        session: '{{ $session }}',
                        group_id: '{{ $group }}',
                        current_level: '{{ $current_level }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $input.removeClass('saving error').addClass('saved');
                            pendingChanges.forEach(change => {
                                if (change.student_id === student_id &&
                                    change.particle_id === particle_id &&
                                    change.mark === mark) {
                                    pendingChanges.delete(change);
                                }
                            });
                            setTimeout(() => $input.removeClass('saved'), 1000);
                            showStatus('Mark saved successfully', 'success');
                        } else {
                            handleError($input, response.message);
                        }

                    },
                    error: function(xhr) {
                        handleError($input, xhr.responseJSON?.message || 'Error saving mark');
                    }
                });
            }


            function saveAllPendingChanges() {
                const selectedIds = getSelectedStudentIds();

                if (pendingChanges.size === 0) {
                    showStatus('No pending changes to save', 'info');
                    return;
                }

                const filteredChanges = Array.from(pendingChanges).filter(change =>
                    selectedIds.includes(change.student_id.toString())
                );

                if (filteredChanges.length === 0) {
                    showStatus('No selected students have pending changes', 'info');
                    return;
                }

                $.LoadingOverlay("show");

                const promises = filteredChanges.map(change =>
                    saveMark(
                        change.input,
                        change.mark,
                        change.particle_id,
                        change.student_id
                    )
                );

                Promise.all(promises)
                    .then(() => {
                        showStatus(`Saved changes for ${filteredChanges.length} students`, 'success');
                    })
                    .catch(error => {
                        showStatus('Error saving some changes', 'error');
                        console.error('Save all error:', error);
                    })
                    .finally(() => {
                        $.LoadingOverlay("hide");
                    });
            }

            function handleError(input, message) {
                input.removeClass('saving').addClass('error');
                setTimeout(() => input.removeClass('error'), 1000);
                showStatus(message || 'Error saving mark', 'danger');
            }

            function showStatus(message, type) {
                const iconMap = {
                    'success': 'success',
                    'danger': 'error',
                    'warning': 'warning',
                    'info': 'info'
                };

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: iconMap[type] || 'info',
                    title: message
                });
            }

            // Warn about unsaved changes when leaving page
            $(window).on('beforeunload', function() {
                if (pendingChanges.size > 0) {
                    return "You have unsaved changes. Are you sure you want to leave?";
                }
            });
        });
    </script>

    <script></script>
@endpush

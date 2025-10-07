{{-- @can('honours.admission.upload_student')
<a href="{{ route('students.honours.student.upload') }}" class="btn btn-info"><i class='fa fa-plus'></i> Upload Student</a>
@endcan --}}

@can('honours.admission.registered_student')
{{ link_to_route('students.honours.regstudent', 'Registered Student', NULL, ['class' => 'btn btn-info']) }}
@endcan
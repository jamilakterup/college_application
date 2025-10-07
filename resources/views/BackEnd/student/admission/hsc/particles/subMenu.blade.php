{{-- @can('hsc.admission.upload_student')
<a href="{{ route('students.hsc.student.upload') }}" class='btn btn-info'><i class='fa fa-upload'></i> Upload Student </a>
@endcan --}}
{{-- 
@can('hsc.admission.meritlist')
{{ link_to_route('students.hsc.meritlist', 'Merit List', NULL, ['class' => 'btn btn-info']) }}
@endcan --}}

@can('hsc.admission.totlist')
{{ link_to_route('students.hsc.totlist', 'Tot List', NULL, ['class' => 'btn btn-info']) }}
@endcan

@can('hsc.admission.registered_student')
{{ link_to_route('students.hsc.regstudent', 'Registered Student', NULL, ['class' => 'btn btn-info']) }}
@endcan
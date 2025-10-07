{{ link_to_route('hsc_result.subject_info.index', 'Student Subject Info', NULL, ['class' => 'btn btn-info']) }}
{{ link_to_route('hsc_result.student_subject.assign', 'Student Subject Assign', NULL, ['class' => 'btn btn-info']) }}
{{ link_to_route('hsc_result.assign_subject_from_mark', 'Student Subject Assign From Mark', NULL, ['class' => 'btn btn-info']) }}

<a href="{{route('hsc_result.download_student_sub_data')}}" class='btn btn-info'>Generate Subject Wise Data</a>
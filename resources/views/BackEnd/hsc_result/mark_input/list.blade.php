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

                {{ Form::open(['route' => 'hsc_result.mark_input.store', 'method' => 'post']) }}
                
                {{-- Hidden Fields --}}
                @foreach(['current_level' => $curr_level->id, 'session' => $session, 
                         'group_id' => $group, 'exam_id' => $exam_id, 
                         'subject_id' => $subject_id, 'exam_test_id' => $exam_test_id,
                         'exam_year' => $exam_year] as $field => $value)
                    {!! Form::hidden($field, $value) !!}
                @endforeach

                <div class="form-group">
                    <div class="col-sm-12">
                        {{-- Instructions --}}
                        <p class="para-type-b">
                            <span>Instructions:</span><br/>
                            i) If a student was <span>absent</span>, then input mark A or Absent
                        </p>

                        {{-- Download Link --}}
                        <a href="{{ route('hsc_result.mark_input.mark_pdf', [$session, $group, $curr_level->id, $exam_id, $subject_id, $exam_test_id, $exam_year]) }}" 
                           target="_blank" 
                           class="btn btn-danger mb-3">
                            Marks Download
                        </a>

                        {{-- Pagination Info --}}
                        <h5 class="text-right text-success">
                            Showing {{ $student_info->firstItem() }} to {{ $student_info->lastItem() }} 
                            of {{ $student_info->total() }}
                        </h5>

                        {{-- Session Info Table --}}
                        <table class="table input-mark">
                            <caption>
                                <strong>Session:</strong> {{ $session }} |
                                <strong>Group:</strong> {{ $groupName }} |
                                <strong>Level:</strong> {{ $curr_level->name }} |
                                <strong>Exam:</strong> {{ $examName }} |
                                <strong>Subject:</strong> {{ $subject->name }} ({{ $subject->code }}) |
                                <strong>Year:</strong> {{ $exam_year }}
                            </caption>
                        </table>

                        {{-- Marks Input Table --}}
                        <table class="table table-hover dataTable w-full cell-border">
                            <thead>
                                <tr>
                                    <th class="w-1/10">
                                        <i class="i-type-a">ALL</i><br/>
                                        <input type="checkbox" onchange="checkAll(this)" 
                                               name="toggleCheck" id="toggle-check" checked/>
                                    </th>
                                    <th class="w-1/10">Roll</th>
                                    <th class="w-1/4">Name</th>
                                    @foreach($config_exam_particles as $particle)
                                        <th>
                                            {{ $particle->xmparticle->name }}<br/>
                                            {{ $particle->total }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($student_info as $info)
                                    @php
                                        $student = DB::table("student_info_hsc")
                                                    ->where("id", $info->student_id)
                                                    ->first();
                                    @endphp
                                    
                                    @if($student)
                                        <tr>
                                            <td>
                                                {!! Form::checkbox("info-{$info->student_id}", 
                                                    $info->student_id, true, 
                                                    ['class' => 'action-type-a']) 
                                                !!}
                                            </td>
                                            <td>{{ $info->student_id }}</td>
                                            <td>{{ $student->name }}</td>

                                            @foreach($config_exam_particles as $particle)
                                                @php
                                                    $markQuery = $exam_test_id == 0 
                                                        ? App\Models\Mark::query()
                                                        : App\Models\ClassTestMark::query();
                                                    
                                                    $mark = $markQuery
                                                        ->where([
                                                            'student_id' => $info->student_id,
                                                            'exam_year' => $exam_year,
                                                            'group_id' => $group,
                                                            'exam_id' => $exam_id,
                                                            'subject_id' => $subject_id,
                                                            'particle_id' => $particle->xmparticle_id,
                                                        ])
                                                        ->when($exam_test_id != 0, function($q) use ($exam_test_id) {
                                                            return $q->where('class_test_id', $exam_test_id);
                                                        })
                                                        ->first();
                                                @endphp

                                                <td>
                                                    {!! Form::text(
                                                        "{$particle->xmparticle->short_name}-{$particle->xmparticle->id}-{$info->student_id}", 
                                                        $mark?->mark, 
                                                        [
                                                            'class' => 'form-control',
                                                            'placeholder' => $particle->xmparticle->name
                                                        ]
                                                    ) !!}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination Links --}}
                        <nav>
                            {{ $student_info->appends(Request::except('page'))->links() }}
                        </nav>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkAll(source) {
    const checkboxes = document.getElementsByClassName('action-type-a');
    for(let checkbox of checkboxes) {
        checkbox.checked = source.checked;
    }
}
</script>
@endpush

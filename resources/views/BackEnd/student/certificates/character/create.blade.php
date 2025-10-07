@extends('BackEnd.student.layouts.master')

@section('title', 'Create Character Certificate')

@section('content')
    <div class="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-plus"></i> Create New Character Certificate (প্রত্যয়ন পত্র)
                    <a href="{{ route('certificates.character.index') }}" class="btn btn-default btn-sm pull-right">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </h3>
            </div>

            <div class="panel-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('certificates.character.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_id">Select Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-control select2" required>
                                    <option value="">Select Student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} (Roll: {{ $student->class_roll }}) -
                                            {{ $student->session }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="issue_date">Issue Date <span class="text-danger">*</span></label>
                                <input type="date" name="issue_date" id="issue_date" class="form-control"
                                    value="{{ old('issue_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="academic_year">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" id="academic_year" class="form-control"
                                    value="{{ old('academic_year') }}" placeholder="e.g., 2023-2024" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_name">Class <span class="text-danger">*</span></label>
                                <input type="text" name="class_name" id="class_name" class="form-control"
                                    value="{{ old('class_name') }}" placeholder="e.g., HSC 1st Year" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="registration_no">Registration No</label>
                                <input type="text" name="registration_no" id="registration_no" class="form-control"
                                    value="{{ old('registration_no') }}" placeholder="Registration Number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="character_rating">Character Rating <span class="text-danger">*</span></label>
                                <select name="character_rating" id="character_rating" class="form-control" required>
                                    <option value="">Select Rating</option>
                                    <option value="excellent"
                                        {{ old('character_rating') == 'excellent' ? 'selected' : '' }}>Excellent (অত্যন্ত
                                        উত্তম)</option>
                                    <option value="very_good"
                                        {{ old('character_rating') == 'very_good' ? 'selected' : '' }}>Very Good (অতি
                                        উত্তম)</option>
                                    <option value="good" {{ old('character_rating') == 'good' ? 'selected' : '' }}>Good
                                        (উত্তম)</option>
                                    <option value="satisfactory"
                                        {{ old('character_rating') == 'satisfactory' ? 'selected' : '' }}>Satisfactory
                                        (সন্তোষজনক)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="study_period_from">Study Period From</label>
                                <input type="date" name="study_period_from" id="study_period_from" class="form-control"
                                    value="{{ old('study_period_from') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="study_period_to">Study Period To</label>
                                <input type="date" name="study_period_to" id="study_period_to" class="form-control"
                                    value="{{ old('study_period_to') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="moral_character">Moral Character <span class="text-danger">*</span></label>
                        <textarea name="moral_character" id="moral_character" class="form-control" rows="3"
                            placeholder="Details about student's moral character" required>{{ old('moral_character') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="conduct_behavior">Conduct & Behavior <span class="text-danger">*</span></label>
                        <textarea name="conduct_behavior" id="conduct_behavior" class="form-control" rows="3"
                            placeholder="Student's conduct and behavior details" required>{{ old('conduct_behavior') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="academic_performance">Academic Performance</label>
                        <textarea name="academic_performance" id="academic_performance" class="form-control" rows="3"
                            placeholder="Academic performance details">{{ old('academic_performance') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="attendance_record">Attendance Record</label>
                        <input type="text" name="attendance_record" id="attendance_record" class="form-control"
                            value="{{ old('attendance_record') }}" placeholder="e.g., 95% or Regular">
                    </div>

                    <div class="form-group">
                        <label for="extracurricular_activities">Extracurricular Activities</label>
                        <textarea name="extracurricular_activities" id="extracurricular_activities" class="form-control" rows="3"
                            placeholder="Participation in extracurricular activities">{{ old('extracurricular_activities') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="special_qualities">Special Qualities</label>
                        <textarea name="special_qualities" id="special_qualities" class="form-control" rows="3"
                            placeholder="Any special qualities or talents">{{ old('special_qualities') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="recommendations">Recommendations</label>
                        <textarea name="recommendations" id="recommendations" class="form-control" rows="3"
                            placeholder="Recommendations for the student">{{ old('recommendations') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Additional remarks">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="reset" class="btn btn-default">Reset</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Create Certificate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Student",
                allowClear: true
            });
        });
    </script>
@endsection

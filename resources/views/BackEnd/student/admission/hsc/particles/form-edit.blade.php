@php
    $quota_list = [
        '' => '--Select Quota (if any)--',
        'Education' => 'Education',
        'Freedom Fighter' => 'Freedom Fighter',
    ];
    $query_present_ps = isset($student) ? ['district=' => $student->present_dist] : null;
    $query_permanent_ps = isset($student) ? ['district=' => $student->permanent_dist] : null;
    $selectiv_sub = explode(',', $student->admitted_student->selective);
@endphp

{!! Form::open([
    'route' => 'students.hsc.store',
    'method' => 'post',
    'files' => true,
    'data-form' => 'postForm',
]) !!}
<h4>Personal Info</h4>
<div class="row mb-3">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="name" class="col-form-label">Name</label>
            {!! Form::text('student_name', @$student->name ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Student Name',
                'required' => true,
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="student_name_bn" class="col-form-label">Name (In Bangla)</label>
            {!! Form::text('student_name_bn', @$student->admitted_student->bangla_name ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Student Name (In Bangla)',
                'required' => true,
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="session" class="col-form-label">Admission Session</label>
            {!! Form::select('session', selective_multiple_session(), $student->session ?? null, [
                'class' => 'form-control',
                'id' => 'Admission session',
                'required' => true,
                'data-placeholder' => '--Select Session--',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="photo" class="col-form-label">Your picture</label>
            <input type="file" name="photo" class="form-control" id="photo">
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="father_name" class="col-form-label">Father's Name</label>
            {!! Form::text('father_name', $student->father_name ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Father\'s Name',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="father_nid" class="col-form-label">Father's NID</label>
            {!! Form::text('father_nid', $student->admitted_student->fathers_nid ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Father\'s NID',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="mother_name" class="col-form-label">Mother's Name</label>
            {!! Form::text('mother_name', $student->mother_name ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Mother\'s Name',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="mother_nid" class="col-form-label">Mother's NID</label>
            {!! Form::text('mother_nid', $student->admitted_student->mothers_nid ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Mother\'s NID',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="birth_date" class="col-form-label">Date Of Birth</label>
            {!! Form::text('birth_date', $student->birth_date ?? null, [
                'class' => 'form-control date',
                'placeholder' => 'Date of Birth',
                'required' => true,
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="birth_reg_no" class="col-form-label">Birth Registration Number</label>
            {!! Form::text('birth_reg_no', $student->admitted_student->birth_reg_no ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Birth Registration Number',
                'maxlength' => '11',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="student_mobile" class="col-form-label">Student Mobile Number</label>
            {!! Form::text('student_mobile', $student->contact_no ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Student Mobile',
                'maxlength' => '11',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="blood_group" class="col-form-label">Blood Group</label>
            {!! Form::select('blood_group', selective_blood_lists(), $student->admitted_student->blood_group ?? null, [
                'class' => 'form-control selectize',
                'id' => 'blood_group',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="gender" class="col-form-label">Gender</label>
            {!! Form::select('gender', selective_gender_list(), $student->admitted_student->sex ?? null, [
                'class' => 'form-control selectize',
                'id' => 'gender',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="religion" class="col-form-label">Religion</label>
            {!! Form::select('religion', selective_religion_list(), $student->admitted_student->religion ?? null, [
                'class' => 'form-control selectize',
                'id' => 'gender',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="guardian_name" class="col-form-label">Guardian Name</label>
            {!! Form::text('guardian_name', $student->admitted_student->guardian_name ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Guardian Name',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="guardian_phone" class="col-form-label">Guardian Mobile Number</label>
            {!! Form::text('guardian_phone', $student->admitted_student->guardian_phone ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Guardian Mobile Number',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="relation" class="col-form-label">Guardian Relation</label>
            {!! Form::text('relation', $student->admitted_student->relation ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Guardian Relation',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="occupation" class="col-form-label">Guardian Occupation</label>
            {!! Form::text('occupation', $student->admitted_student->occupation ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Guardian Occupation',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="income" class="col-form-label">Guardian's Yearly Income
            </label>
            {!! Form::select('income', selective_income(), $student->admitted_student->income ?? null, [
                'class' => 'form-control',
                'id' => 'income',
            ]) !!}
            <div class="help-block"></div>
        </div>

        <div class="form-group">
            <label for="quota" class="col-form-label">Quota (If Any)</label>
            {!! Form::select('quota', $quota_list, $student->admitted_student->quota ?? null, [
                'class' => 'form-control',
                'id' => 'quota',
            ]) !!}
            <div class="help-block"></div>
        </div>

        <div class="form-group">
            <label for="form_current_level" class="col-form-label">Current Level </label>
            {!! Form::select('form_current_level', selective_multiple_hsc_level(), $student->current_level ?? null, [
                'class' => 'form-control selectize',
                'data-placeholder' => '--Select Current Level--',
                'id' => 'form_current_level',
                'required' => true,
            ]) !!}
            {!! invalid_feedback('form_current_level') !!}
        </div>
    </div>
</div>

<h4>Address Info</h4>
<div class="row">
    <div class="col-lg-6">
        <label></label>
        <div class="form-group">
            <label for="present_village" class="col-form-label">Present Village</label>
            {!! Form::text('present_village', $student->present_village ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Present Village',
                'id' => 'present_village',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="present_po" class="col-form-label">Present Post Office</label>
            {!! Form::text('present_po', $student->present_po ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Present Post Office',
                'id' => 'present_po',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="present_dist" class="col-form-label">Present District</label>
            {!! Form::select(
                'present_dist',
                create_option_array('district_thana', 'district', 'district', 'District'),
                $student->present_dist ?? null,
                [
                    'class' => 'form-control',
                    'onchange' => 'getThanaOption(this);',
                    'data-for' => 'present_ps',
                    'id' => 'present_dist',
                ],
            ) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="present_ps" class="col-form-label">Present Thana</label>
            {!! Form::select(
                'present_ps',
                create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_present_ps),
                $student->present_ps ?? null,
                ['class' => 'form-control', 'id' => 'present_ps', 'data-placeholder' => '--Select Thana--'],
            ) !!}
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-lg-6">
        <input type="checkbox" name="same_as_present" id="same_as_present"> <label for="same_as_present">Same as
            Present
            Address</label>
        <div class="form-group">
            <label for="permanent_village" class="col-form-label">Permanent Village</label>
            {!! Form::text('permanent_village', $student->permanent_village ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Permanent Village',
                'id' => 'permanent_village',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <label for="permanent_po" class="col-form-label">Permanent Post Office</label>
            {!! Form::text('permanent_po', $student->permanent_po ?? null, [
                'class' => 'form-control',
                'placeholder' => 'Permanent Post Office',
                'id' => 'permanent_po',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="permanent_dist" class="col-form-label">Permanent District</label>
            {!! Form::select(
                'permanent_dist',
                create_option_array('district_thana', 'district', 'district', 'District'),
                $student->permanent_dist ?? null,
                [
                    'class' => 'form-control',
                    'onchange' => 'getThanaOption(this);',
                    'data-for' => 'permanent_ps',
                    'id' => 'permanent_dist',
                ],
            ) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="permanent_ps" class="col-form-label">Permanent Thana</label>
            {!! Form::select(
                'permanent_ps',
                create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_permanent_ps),
                $student->permanent_ps ?? null,
                ['class' => 'form-control', 'id' => 'permanent_ps', 'data-placeholder' => '--Select Thana--'],
            ) !!}
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>

<h4>SSC Info</h4>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="ssc_registration" class="col-form-label">SSC Registration</label>
            {!! Form::text('ssc_registration', $student->ssc_reg_no ?? null, [
                'class' => 'form-control',
                'placeholder' => 'SSC Registration',
                'required' => true,
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="ssc_roll" class="col-form-label">SSC Roll</label>
            {!! Form::text('ssc_roll', $student->ssc_roll ?? null, [
                'class' => 'form-control',
                'placeholder' => 'SSC Roll',
                'required' => true,
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>


        <div class="form-group">
            <label for="ssc_gpa" class="col-form-label">SSC GPA</label>
            {!! Form::text('ssc_gpa', $student->gpa ?? null, ['class' => 'form-control', 'placeholder' => 'SSC GPA']) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="ssc_passing_year" class="col-form-label">SSC Passing Year</label>
            {!! Form::select('ssc_passing_year', selective_multiple_passing_year(), $student->ssc_passing_year ?? null, [
                'class' => 'form-control selectize get_options',
                'data-placeholder' => '--Select Passing Year--',
                'id' => 'passing_year',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="ssc_institute" class="col-form-label">SSC Institute</label>
            {!! Form::text('ssc_institute', $student->admitted_student->ssc_institution ?? null, [
                'class' => 'form-control',
                'placeholder' => 'SSC Institute',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="ssc_group" class="col-form-label">SSC Group</label>
            {!! Form::select('ssc_group', selective_multiple_study_group(), $student->ssc_group ?? null, [
                'class' => 'form-control selectize get_options',
                'data-placeholder' => '--Select SSC Group--',
                'id' => 'ssc_group',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="ssc_board" class="col-form-label">SSC Board</label>
            {!! Form::select('ssc_board', selective_boards(), $student->ssc_board ?? null, [
                'class' => 'form-control selectize get_options',
                'data-placeholder' => '--Select SSC Board--',
                'id' => 'ssc_board',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label for="ssc_session" class="col-form-label">SSC Session</label>
            {!! Form::select('ssc_session', selective_multiple_session(), $student->ssc_session ?? null, [
                'class' => 'form-control selectize',
                'id' => 'ssc_session',
                'data-placeholder' => '--Select Session--',
            ]) !!}
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>

<h4 class="mt-4">Admission Info</h4>
<div class="row">
    <div class="col-12">
        <table class='table table-bordered'>
            <tr>
                <td>Subject4</td>
                <td>
                    <input class='form-control' type='text' value='{{ @$selectiv_sub[0] }}' name='sub_4'
                        id='sub4' />
                </td>

                <td>Subject5</td>
                <td>
                    <input class='form-control' type='text' value='{{ @$selectiv_sub[1] }}' name='sub_5'
                        id='sub5' />
                </td>

            </tr>
            <tr>
                <td>Subject6</td>
                <td>
                    <input class='form-control' type='text' value='{{ @$selectiv_sub[2] }}' name='sub_6'
                        id='sub6' />
                </td>
                <td>4th Subject</td>
                <td>
                    <input class='form-control' type='text'
                        value='{{ $student->admitted_student->optional ?? null }}' name='sub_4th' id='sub4th' />
                </td>
                <input type="hidden" value="{{ $student->groups ?? null }}" name="groups" id="groups" />
                <input type="hidden" value="{{ $student->admitted_student->compulsory ?? null }}" name="compulsory"
                    id="compulsory" />
                <input type="hidden" value="{{ $student->refference_id ?? null }}" name="auto_id" id="auto_id" />
            </tr>
        </table>
    </div>
</div>

<div class="float-right clear mb-2">
    {!! Form::submit('Save Data', ['class' => 'btn btn-primary', 'data-value' => 'create', 'data-button' => 'save']) !!}
</div>
{!! Form::close() !!}

<!DOCTYPE html>
<html>

<head>
    <title>HSC Online Admission</title>
    <link rel="shortcut icon" href="{{ asset('upload/sites/' . config('settings.site_favicon')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
</head>

<body>

    @php
        $ssc_group = Session::get('ssc_group');
        $ssc_board = Session::get('ssc_board');
        $name = Session::get('name');
        $passing_year = Session::get('passing_year');
        $admission_session = Session::get('session');
        $quota_list = [
            '' => '--Select Quota (if any)--',
            'Education' => 'Education',
            'Freedom Fighter' => 'Freedom Fighter',
        ];
    @endphp
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">{{ config('settings.college_name') }} HSC Online Admission</a>
            </div>
            <a href="<?php echo url('/') . '/Admission/HSC'; ?>" class="btn btn-danger navbar-btn">Admission</a>
            <a href="<?php echo url('/') . '/Admission/HSC/signin'; ?>" class="btn btn-danger navbar-btn">Login</a>
        </div>
    </nav>

    <div class="container">

        <div class="panel-group">

            <div class="row mb">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">গুরত্বপূর্ণ নির্দেশাবলীঃ</div>
                        <div class="panel-body">ভবিষ্যতে লগইনের জন্য নিচে আপনার পছন্দমত পাসওয়ার্ড দিন। পাসওয়ার্ডটি
                            সংরক্ষন করুন, কারন ভর্তি পরবর্তী সময়ে এই সাইটে আপনার আবার লগইন করার প্রয়োজন হবে। </div>
                    </div>
                </div>
            </div>
            <?php   if( Session::get('tracking_id')!='' && Session::get('password')!='') {

      ?>
            <div class="row mb">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">Ref Id and Password</div>
                        <div class="panel-body bg-success" style="font-size:18px;">
                            <p>Your Ref Id : <span style="color: red;">{{ session::get('tracking_id') }}</span> And
                                Password: <span style="color: red;">{{ Session::get('password') }}</span> Please
                                remember your Ref Id and password for login. You need to login several time for
                                admission purpose</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <form action="{{ route('student.hsc.admission.hscInformationSubmit') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row mb">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Password</div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::password('password', [
                                                'data-minlength' => '3',
                                                'id' => 'inputPassword',
                                                'class' => 'form-control input-lg',
                                                'placeholder' => 'Password',
                                                'required' => true,
                                            ]) !!}
                                            <div class="help-block">{!! invalid_feedback('password') !!}</div>
                                            <div class="help-block">Minimum of 3 characters</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::password('password', [
                                                'data-match' => '#inputPassword',
                                                'id' => 'inputPasswordConfirm',
                                                'class' => 'form-control input-lg',
                                                'placeholder' => 'Confirm Password',
                                                'required' => true,
                                                'data-match-error' => "Password don't match",
                                            ]) !!}
                                            <div class="controls">
                                                <div class="confirm_password_error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb">
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Persional Information</div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Name</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('student_name', $name, [
                                            'class' => 'form-control',
                                            'id' => 'name',
                                            'required' => true,
                                            'readonly' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="photo" class="col-sm-4 control-label">Your picture</label>
                                    <div class="col-sm-8">
                                        <div class="img-thumbnail mb-1" id="student_img_image_pre_area"
                                            style="display: none; width: 80px;">
                                            <img style="height: 100%; width: 100%;" src=""
                                                id="student_img_image_pre" alt="Not Set Yet">
                                        </div>
                                        {!! Form::file('photo', [
                                            'class' => 'form-control image_data',
                                            'id' => 'photo',
                                            'required' => true,
                                            'data-type' => 'student_img',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name_bangla" class="col-sm-4 control-label">Name (In Bangla)</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('name_bangla', null, [
                                            'class' => 'form-control',
                                            'id' => 'name_bangla',
                                            'required' => true,
                                            'placeholder' => 'Name in Bangla',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="father_name" class="col-sm-4 control-label">Father's Name</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('father_name', null, [
                                            'class' => 'form-control',
                                            'id' => 'father_name',
                                            'required' => true,
                                            'placeholder' => "Father's Name",
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('father_name') !!}</div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="fathers_nid" class="col-sm-4 control-label">Father's NID</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('fathers_nid', null, [
                                            'class' => 'form-control',
                                            'id' => 'fathers_nid',
                                            'required' => true,
                                            'placeholder' => "Father's NID",
                                        ]) !!}
                                        <div class="help-block">{!! invalid_feedback('fathers_nid') !!}</div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="mother_name" class="col-sm-4 control-label">Mother's Name</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('mother_name', null, [
                                            'class' => 'form-control',
                                            'placeholder' => "Mother's Name",
                                            'required' => true,
                                            'id' => 'mother_name',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('mother_name') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="mothers_nid" class="col-sm-4 control-label">Mother's NID</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('mothers_nid', null, [
                                            'class' => 'form-control',
                                            'placeholder' => "Mother's NID",
                                            'required' => true,
                                            'id' => 'mothers_nid',
                                        ]) !!}
                                        <div class="help-block">{!! invalid_feedback('mothers_nid') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="student_mobile" class="col-sm-4 control-label">Student Mobile
                                        Number</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('student_mobile', null, [
                                            'class' => 'form-control',
                                            'placeholder' => '01xxxxxxxxx',
                                            'required' => true,
                                            'id' => 'student_mobile',
                                            'maxlength' => '11',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('student_mobile') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="student_mobile_re" class="col-sm-4 control-label">Retype Mobile
                                        Number</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('student_mobile_re', null, [
                                            'class' => 'form-control',
                                            'placeholder' => '01xxxxxxxxx',
                                            'required' => true,
                                            'id' => 'student_mobile_re',
                                            'maxlength' => '11',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="controls">
                                        <div class="mobile_number_confirmation_error"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="birth_date" class="col-sm-4 control-label">Date Of Birth</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('birth_date', null, [
                                            'class' => 'form-control date',
                                            'placeholder' => 'Date Of Birth',
                                            'required' => true,
                                            'id' => 'birth_date',
                                            'autocomplete' => 'off',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="birth_reg_no" class="col-sm-4 control-label">Birth Registration
                                        No</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('birth_reg_no', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Birth Reg No',
                                            'required' => true,
                                            'id' => 'birth_reg_no',
                                            'autocomplete' => 'off',
                                        ]) !!}
                                        <div class="help-block">{!! invalid_feedback('birth_reg_no') !!}</div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="blood_group" class="col-sm-4 control-label">Blood Group</label>
                                    <div class="col-sm-8">
                                        {!! Form::select('blood_group', selective_blood_lists(), null, [
                                            'class' => 'form-control',
                                            'id' => 'blood_group',
                                            'required' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="gender" class="col-sm-4 control-label">Gender</label>
                                    <div class="col-sm-8">
                                        {!! Form::select('gender', selective_gender_list(), null, [
                                            'class' => 'form-control',
                                            'id' => 'gender',
                                            'required' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="religion" class="col-sm-4 control-label">Religion</label>
                                    <div class="col-sm-8">
                                        {!! Form::select('religion', selective_religion_list(), null, [
                                            'class' => 'form-control',
                                            'id' => 'religion',
                                            'required' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Guardian Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="guardian_info" class="col-sm-4 control-label">Guardian Info</label>
                                    <div class="col-sm-8">
                                        <input type="radio" name="guardian_info"
                                            value="Father">&nbsp;&nbsp;Father&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="radio" name="guardian_info"
                                            value="Mother">&nbsp;&nbsp;Mother&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="radio" name="guardian_info" value="Other">&nbsp;&nbsp;Other

                                        <div class="help-block"></div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="guardian_name" class="col-sm-4 control-label">Guardian Name</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('guardian_name', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Guardian Name',
                                            'required' => true,
                                            'id' => 'guardian_name',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('guardian_name') !!}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="guardian_relation" class="col-sm-4 control-label">Guardian
                                        Relation</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('guardian_relation', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Guardian Relation',
                                            'required' => true,
                                            'id' => 'guardian_relation',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="guardian_phone" class="col-sm-4 control-label">Guardian Phone</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('guardian_phone', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Guardian Phone',
                                            'required' => true,
                                            'id' => 'guardian_phone',
                                        ]) !!}
                                        <div class="help-block">{!! invalid_feedback('guardian_phone') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="occupation" class="col-sm-4 control-label">Guardian Occupation</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('occupation', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Guardian Occupation',
                                            'required' => true,
                                            'id' => 'occupation',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('occupation') !!}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="religion" class="col-sm-4 control-label">Guardian's Yearly Income
                                    </label>
                                    <div class="col-sm-8">
                                        {!! Form::select('income', selective_income(), null, [
                                            'class' => 'form-control',
                                            'required' => true,
                                            'id' => 'income',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="quota" class="col-sm-4 control-label">Quota (If Any)</label>
                                        <div class="col-sm-8">
                                            {!! Form::select('quota', $quota_list, null, ['class' => 'form-control', 'id' => 'quota']) !!}
                                            <div class="help-block"></div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mb">
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Present Address</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="present_village" class="col-sm-4 control-label">Village</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('present_village', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Village',
                                            'required' => true,
                                            'id' => 'present_village',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('present_village') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="present_po" class="col-sm-4 control-label">Post Office</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('present_po', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Post Office',
                                            'required' => true,
                                            'id' => 'present_po',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('present_po') !!}</div>
                                    </div>
                                </div>
                                <?php $dist = DB::table('district_thana')
                                    ->distinct()
                                    ->get(['district']); ?>
                                <div class="form-group">
                                    <label for="present_dist" class="col-sm-4 control-label">District</label>
                                    <div class="col-sm-8">
                                        <select required="required" name="present_dist" id="present_dist"
                                            class="form-control">
                                            <option>--Select--</option>
                                            <?php foreach($dist as $value)  {?>
                                            <option value="<?php echo $value->district; ?>"><?php echo $value->district; ?></option>
                                            <?php  } ?>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="present_thana" class="col-sm-4 control-label">Thana</label>
                                    <div class="col-sm-8">
                                        <select required="required" name="present_thana" id="present_thana"
                                            class="form-control">
                                            <option value="">--Select--</option>

                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Permanent Address</div>
                            <div class="controls">
                                <input style="margin-bottom:1em; margin-left: 1em;" type="checkbox" name="same"
                                    id="same"> Same as present address
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="permanent_village" class="col-sm-4 control-label">Village</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('permanent_village', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Permanent Village',
                                            'required' => true,
                                            'id' => 'permanent_village',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('permanent_village') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="permanent_post_office" class="col-sm-4 control-label">Post</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('permanent_post_office', null, [
                                            'class' => 'form-control',
                                            'placeholder' => 'Permanent Post Office',
                                            'required' => true,
                                            'id' => 'permanent_post_office',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('permanent_post_office') !!}</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="permanent_district" class="col-sm-4 control-label">District</label>
                                    <div class="col-sm-8">
                                        <select required="required" name="permanent_district" id="permanent_district"
                                            class="form-control">
                                            <option>--Select--</option>
                                            <?php foreach($dist as $value1)  {?>
                                            <option value="<?php echo $value1->district; ?>"><?php echo $value1->district; ?></option>
                                            <?php  } ?>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="permanent_thana" class="col-sm-4 control-label">Thana</label>
                                    <div class="col-sm-8">
                                        <select required="required" name="permanent_thana" id="permanent_thana"
                                            class="form-control">
                                            <option value="">--Select--</option>


                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mb">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">SSC Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="ssc_roll" class="col-sm-4 control-label">SSC Roll No:</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('ssc_roll', session()->get('ssc_roll'), [
                                            'class' => 'form-control',
                                            'id' => 'ssc_roll',
                                            'disabled' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="ssc_registration" class="col-sm-4 control-label">SSC Registration
                                        No:</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('ssc_registration', null, [
                                            'class' => 'form-control',
                                            'id' => 'ssc_registration',
                                            'required' => true,
                                            'placeholder' => 'Enter SSC Registration',
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('ssc_registration') !!}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ssc_gpa" class="col-sm-4 control-label">SSC GPA :</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('ssc_gpa', null, [
                                            'class' => 'form-control',
                                            'id' => 'ssc_gpa',
                                            'required' => true,
                                            'placeholder' => 'Enter SSC GPA',
                                        ]) !!}
                                        <div class="help-block">{!! invalid_feedback('ssc_gpa') !!}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ssc_institute" class="col-sm-4 control-label">SSC Institute :</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('ssc_institute', null, [
                                            'class' => 'form-control',
                                            'id' => 'ssc_institute',
                                            'placeholder' => 'Enter SSC Institute',
                                            'required' => true,
                                        ]) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('ssc_institute') !!}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ssc_group" class="col-sm-4 control-label">SSC Group :</label>
                                    <div class="col-sm-8">
                                        {!! Form::select('ssc_group', selective_multiple_study_group(), $ssc_group, [
                                            'class' => 'form-control',
                                            'required' => true,
                                            'id' => 'ssc_group',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>
{{-- todo --}}
                                <div class="form-group">
                                    <label for="ssc_session" class="col-sm-4 control-label">SSC Session :</label>
                                    <div class="col-sm-8">
                                        {!! Form::select('ssc_session', selective_multiple_session(), null, [
                                            'class' => 'form-control',
                                            'required' => true,
                                            'id' => 'ssc_session',
                                        ]) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="ssc_total_mark" class="col-sm-4 control-label">SSC Total Mark
                                        :</label>
                                    <div class="col-sm-8">
                                        {!! Form::text('ssc_total_mark', null, ['class' => 'form-control', 'required' => true, 'id' => 'ssc_session']) !!}
                                        <div class="help-block"></div>
                                        <div class="help-block">{!! invalid_feedback('ssc_total_mark') !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Admission Information</div>
                            <div class="panel-body">

                                <div class="form-group">
                                    <label for="college_hostle" class="col-sm-4 control-label">Hostel Facility
                                    </label>
                                    <div class="col-sm-8">
                                        {!! Form::select(
                                            'college_hostle',
                                            ['' => '--Select Hostel Facility--', 'Residential' => 'Residential', 'Non Residential' => 'Non Residential'],
                                            null,
                                            ['class' => 'form-control', 'required' => true, 'id' => 'college_hostle'],
                                        ) !!}
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="hsc_group" class="col-sm-4 control-label">HSC Group :</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $admission_group = Session::get('hsc_group');
                                        $admission_group = trim($admission_group);
                                        
                                        ?>
                                        <select name="hsc_group" id="hsc_group" class="form-control">
                                            <option value=""><--Select--></option>
                                            <option value="<?php echo ucwords($admission_group); ?>"><?php echo ucwords($admission_group); ?></option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="compulsory_course_codes" class="col-sm-4 control-label">Compulsory
                                        Course Info :</label>
                                    <div class="col-sm-8">
                                        <div class="compulsory_course_codes">
                                            <table class="table table-bordered table-striped">
                                                <!-- <caption >Compulsory Courses &amp; Codes</caption> -->
                                                <thead>
                                                    <tr style="background:#fffddd">
                                                        <th>Course Code</th>
                                                        <th>Course Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>ex: 101-102</td>
                                                        <td>ex: Bangla</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selective_course_codes" class="col-sm-4 control-label">Selective
                                        Course Info :</label>
                                    <div class="col-sm-8">
                                        <div class="selective_course_codes">
                                            <table class="table table-bordered table-striped">
                                                <!-- <caption>Selective Courses &amp; Codes</caption> -->
                                                <thead>
                                                    <tr style="background:#fffddd">
                                                        <th>Course Code</th>
                                                        <th>Course Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>ex: 174-175</td>
                                                        <td>ex: Physics</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="optional_course_codes" class="col-sm-4 control-label">Optional Course
                                        Info :</label>
                                    <div class="col-sm-8">
                                        <div class="optional_course_codes">
                                            <table class="table table-bordered table-striped">
                                                <!-- <caption>Optional Courses &amp; Codes</caption> -->
                                                <thead>
                                                    <tr style="background:#fffddd">
                                                        <th>Course Code</th>
                                                        <th>Course Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>ex: 178-179</td>
                                                        <td>ex: Biology</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="admission_session" value="<?php $admission_session = Session::get('session');
                                echo $admission_session; ?>">
                                <input type="hidden" name="ssc_board" value="<?php $ssc_board = Session::get('ssc_board');
                                echo $ssc_board; ?>">
                                <input type="hidden" name="ssc_passing_year" value="<?php $ssc_passing_year = Session::get('passing_year');
                                echo $ssc_passing_year; ?>">
                                <input type="hidden" name="ssc_roll" value="<?php $ssc_roll = Session::get('ssc_roll');
                                echo $ssc_roll; ?>">
                                <input type="hidden" name="exam_name" value="<?php $exam_name = Session::get('exam_name');
                                echo $exam_name; ?>">
                                <button style="float: right;" type="submit"
                                    class="btn btn-danger navbar-btn">Submit</button>
            </form>
        </div>
    </div>
    </div>
    </div>


    </div>

    </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/drop_down.js') }}"></script>
    <script src="{{ asset('fjs/hsc_admission.js') }}"></script>
    <script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>


    @include('common.message')

</body>


</html>

<script type="text/javascript">
    function preview_image_url(input) {
        var type = input.dataset.type;

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Check if the image size is greater than 15KB
                if (input.files[0].size > 50000) { // 15KB in bytes
                    // If image size is greater than 50KB, display an error alert
                    alert('Image size exceeds 50KB. Please select a smaller image.');
                    input.value = ''; // Clear the input
                    $('#' + type + '_image_pre_area').hide(); // Hide the preview area
                } else {
                    // Show the preview area and set the image source
                    $('#' + type + '_image_pre_area').show();
                    $('#' + type + '_image_pre').attr('src', e.target.result);
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".image_data").change(function() {
        preview_image_url(this);
    });


    $('form').on('submit', function() {
        var compulsory_course_codes = '';
        var selective_course_codes = '';
        var optional_course_codes = '';

        var val = $("#hsc_group").val();
        var prev = '';

        if ((val == 'Science') || (val == 'Business Studies')) {

            var selective_course_code = $(".selective_course_codes select").val();

            if (selective_course_code == '') {
                alert("Selective Courses must be selected properly");
                return false;
            }


            var optional_course_code = $(".optional_course_codes select").val();
            if (optional_course_code == '') {
                alert("optional Course must be selected properly");
                return false;
            }

            $(".selective_course_codes select > option").each(function() {
                if (this.value != '') {
                    var s = $('#selective_course_codes').val();
                    s = s.replace("," + this.value, "");
                    $('#selective_course_codes').val(s);
                }
            });

            if (!($('#selective_course_codes').val().indexOf(selective_course_code) > (-1))) {
                $('#selective_course_codes').val($('#selective_course_codes').val() + "," +
                    selective_course_code);
            }

            compulsory_course_codes = $('#compulsory_course_codes').val();
            selective_course_codes = $('#selective_course_codes').val();
            optional_course_codes = $('#optional_course_codes').val();
            //alert(selective_course_codes);
        } else if (val == 'Humanities') {
            var count = $('.selective_course_codes input[type=checkbox]:checked').length;
            if (count < 3) {
                alert("At-least 3 of the selective subjects must be chosen");
                return false;
            } else {
                var selective_course_codes = "";
                var a = [];
                $('.selective_course_codes input[type=checkbox]:checked').each(function() {
                    var id = $(this).attr('id').replace(/check/, '');
                    var option = $("#text" + id).val();
                    a.push(option);
                });
                selective_course_codes = a.join(',');
                $('#selective_course_codes').val(selective_course_codes);
            }

            var optional_course_code = $(".optional_course_codes select").val();
            if (optional_course_code == '') {
                alert("Optional Course must be selected");
                return;
            }

            compulsory_course_codes = $('#compulsory_course_codes').val();
            selective_course_codes = $('#selective_course_codes').val();
            optional_course_codes = $('#optional_course_codes').val();
        }
        var mobile = $('#student_mobile').val();
        var mobile_number_confirmation = $('#student_mobile_re').val();
        var password = $('#inputPassword').val();
        var password_confirmation = $('#inputPasswordConfirm').val();

        if (mobile != mobile_number_confirmation) {
            $('.mobile_number_confirmation_error').html(
                '<span style="color:red;">Mobile Number  Mismatch</span>');
            return false;
        } else {
            $('.mobile_number_confirmation_error').html('');
        }

        if (password != password_confirmation) {
            $('.confirm_password_error').html('<span style="color:red;">Password  Mismatch</span>');
            return false;
        } else {
            $('.confirm_password_error').html('');
        }


        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        })
    });


    $('#birth_date').datepicker({
        format: 'yyyy-mm-dd',
        endDate: new Date()

    });
    var same_address = 0;
    $('#same').click(function() {
        if ($(this).is(':checked')) {
            //alert($('#present_po').val());
            $('#permanent_village').attr('readonly', 'readonly');
            $('#permanent_village').val($('#present_village').val()).change();
            $('#permanent_post_office').attr('readonly', 'readonly');
            $('#permanent_post_office').val($('#present_po').val()).change();
            $('#permanent_district').val($('#present_dist').val()).change();
            $('#permanent_thana').val($('#present_thana').val()).change();
            $('#permanent_thana').attr('disabled', 'disabled');
            $('#permanent_mobile_no').attr('readonly', 'readonly');
            $('#permanent_mobile_no').val($('#student_mobile').val()).change();

            same_address = 1;
        } else {
            $('#permanent_village').removeAttr('readonly');
            $('#permanent_post_office').removeAttr('readonly');
            $('#permanent_district').removeAttr('disabled');
            $('#permanent_thana').removeAttr('disabled');
            $('#permanent_mobile_no').removeAttr('readonly');
            same_address = 0;
        }
    });



    $("#hsc_group").change(function() {
        var val = $("#hsc_group").val();
        if ((val == 'Science') || (val == 'Business Studies')) {
            $(".selective_course_codes select").off();
            $(document).on('change', '.selective_course_codes select', function() {
                var option = $(this).val();
                var id = $(this).attr('id').replace(/select/, '');
                //alert(id);
                $("input[type=text]#text" + id).attr('value', option);
                if (option == '') {
                    option = "Select";
                    $(".optional_course_codes select").val('');
                    $(".optional_course_codes select option").removeAttr('disabled');
                    $(".optional_course_codes select").attr('disabled', 'disabled');
                } else {
                    $(".optional_course_codes select").val('');
                    $(".optional_course_codes select option").removeAttr('disabled');
                    $(".optional_course_codes select").removeAttr('disabled');
                    $(".optional_course_codes select option[value='" + option + "']").attr('disabled',
                        'true');
                }
                $("input[type=text]#text" + id).val(option);
            });
            $(document).on('change', '.optional_course_codes #selecting', function() {
                //alert('arts');
                var option = $(this).val();
                $("#optional_course_codes").attr('value', option);
                if (option == '') option = "Select";
                $("#texting").val(option);
            });
        } else if (val == 'Humanities') {
            //alert('arts');
            $(".selective_course_codes select").off();
            $(document).on('change', '.selective_course_codes select', function() {
                var option = $(this).val();
                if (option == '') option = "Select";
                var id = $(this).attr('id').replace(/select/, '');
                $("input[type=text]#text" + id).val(option).change();
                $("input[type='checkbox']#text" + id).val(option).change();
            });
            $(document).on('change', '.optional_course_codes #selecting', function() {
                var option = $(this).val();
                $("#optional_course_codes").attr('value', option);
                if (option == '') option = "Select";
                $("#texting").val(option);
            });
            $(document).on('change', '.selective_course_codes input[type=checkbox]', function() {
                var count = $('.selective_course_codes input[type=checkbox]:checked').length;
                if (count == 4) {
                    alert("You can only select 3 of these selective subjects");
                    $(this).prop('checked', false);
                    return;
                }
                if (count == 3) $(".optional_course_codes #selecting").removeAttr('disabled');
                else {
                    $(".optional_course_codes #selecting").prop("selectedIndex", 0);
                    $(".optional_course_codes #selecting").attr('disabled', 'disabled');
                }
                var id = $(this).attr('id').replace(/check/, '');
                var option = $(this).val();

                if (option == "Select") {
                    alert("Please select a subject 1st, and then; tick the related checkbox");
                    $(this).prop('checked', false);
                } else {
                    if ($(this).prop('checked') == true) {

                        var intId = id.match(/\d+/);

                        $("#select" + intId).attr('disabled', 'true');
                        $("#select" + intId + " > option").each(function() {
                            $(".optional_course_codes select option[value='" + this.value +
                                "']").attr('disabled', 'true');
                        });

                        $(".optional_course_codes select option[value='" + option + "']").attr(
                            'disabled', 'true');
                    } else {
                        var intId = id.match(/\d+/);
                        $("#select" + intId).removeAttr('disabled');
                        $("#select" + intId + " > option").each(function() {

                            $(".optional_course_codes select option[value='" + this.value +
                                "']").removeAttr('disabled');
                        });

                        $(".optional_course_codes select option[value='" + option + "']").removeAttr(
                            'disabled');
                    }
                }
            });
        }
    });
</script>

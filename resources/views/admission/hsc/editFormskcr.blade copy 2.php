<!DOCTYPE html>
<html>

<head>
    <title>Online Admission</title>
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/') ?>/css/bootstrap.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/') ?>/css/font-awesome.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/') ?>/css/bootstrap-datepicker3.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/') ?>/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">

</head>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">{{config('settings.college_name')}} Online Admission</a>
            </div>
            <a href="<?php echo url('/') . '/Admission/HSC' ?>" class="btn btn-danger navbar-btn">Admission</a>
            <a href="<?php echo url('/') . '/Admission/HSC/logout' ?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
    </nav>

    @php

    $auto_id = Session::get('auto_id');

    $hsc_admitted = DB::table("hsc_admitted_students")->where('auto_id', $auto_id)->first();

    @endphp


    <div class="container">
        <div class="row mb">
            <div class="panel panel-info">
                <div class="panel-heading"> Edit Admission Form Information</div>
                <div class="panel-body">

                    <div class="col-md-8">

                        {!! Form::open(['route'=> 'student.hsc.admission.updateForm', 'method'=> 'post', 'files'=> true]) !!}
                        <div class="form-group">
                            <label for="photo">Your picture</label>

                            <div class="img-thumbnail mb-1" id="student_img_image_pre_area" style="display: none; width: 80px;">
                                <img style="height: 100%; width: 100%;" src="" id="student_img_image_pre" alt="Not Set Yet">
                            </div>

                            {!! Form::file('photo', ['class'=> 'form-control image_data', 'id'=> 'photo', 'required'=> true, 'data-type' =>'student_img',]) !!}

                            {!!invalid_feedback('photo')!!}
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{route('student.hsc.admission.HscConfirmation')}}" class="btn btn-warning">Back</a>
                        {!! Form::close() !!}
                    </div>

                    <div class="col-md-4">
                        @php
                        if(\File::exists(public_path('upload/college/hsc/draft/'.$hsc_admitted->photo))){
                        $photo_url = url('upload/college/hsc/draft/'.$hsc_admitted->photo);
                        }else{
                        $photo_url = url('upload/college/hsc/'.$hsc_admitted->admission_session.'/'.$hsc_admitted->photo);
                        }
                        @endphp
                        <img class="user_pic_view img-polaroid" src="{{$photo_url}}" alt="User Photo" />

                    </div>


                </div>
            </div>
        </div>
    </div>



    <script src="<?php echo url('/') ?>/js/jquery.min.js"></script>
    <script src="<?php echo url('/') ?>/js/bootstrap.min.js"></script>

    <script src="<?php echo url('/') ?>/js/drop_down.js"></script>
    <script src="<?php echo url('/') ?>/fjs/hsc_admission.js"></script>
    <script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
    <script src="{{ asset('js/loadingoverlay.min.js') }}"></script>

    <script>
        function preview_image_url(input) {
            type = input.dataset.type;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + type + '_image_pre_area').show();
                    $('#' + type + '_image_pre').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".image_data").change(function() {
            preview_image_url(this);
        });
    </script>
    @include('common.message')
</body>


</html>
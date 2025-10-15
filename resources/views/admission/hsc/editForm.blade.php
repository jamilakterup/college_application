<!DOCTYPE html>
<html>

<head>
    <title>Online Admission</title>
    <link rel="shortcut icon" href="{{ asset('upload/sites/' . config('settings.site_favicon')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/'); ?>/css/bootstrap.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/'); ?>/css/font-awesome.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/'); ?>/css/bootstrap-datepicker3.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="<?php echo url('/'); ?>/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
</head>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">{{ config('settings.college_name') }} Online Admission</a>
            </div>
            <a href="<?php echo url('/') . '/Admission/HSC'; ?>" class="btn btn-danger navbar-btn">Admission</a>
            <a href="<?php echo url('/') . '/Admission/HSC/logout'; ?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
    </nav>

    @php

        $auto_id = Session::get('auto_id');

        $hsc_admitted = DB::table('hsc_admitted_students')->where('auto_id', $auto_id)->first();
        $student = DB::table('student_info_hsc')->where('refference_id', $auto_id)->first();

        $query_present_ps = !is_null($student) ? ['district=' => $student->present_dist] : null;
        $query_permanent_ps = !is_null($student) ? ['district=' => $student->permanent_dist] : null;
    @endphp


    <div class="container">
        <div class="row">
            <div class="panel panel-danger">
                <div class="panel-heading text-danger">
                    <h5>Correction for Admission Form Information | শিক্ষার্থীর ভর্তির তথ্যাদি সংশোধন</h5>
                </div>
            </div>
        </div>
        {!! Form::open(['route' => 'student.hsc.admission.updateForm', 'method' => 'post', 'files' => true]) !!}
        <div class="row mb">
            <div class="panel panel-info">
                <div class="panel-heading"> Personal Information</div>
                <div class="panel-body">
                    <p class="text-danger"></p>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="student_name">Student Name (In English)</label>

                            {!! Form::text('student_name', $hsc_admitted->name ?? null, [
                                'class' => 'form-control',
                                'required' => true,
                                'readonly' => true,
                            ]) !!}

                            {!! invalid_feedback('student_name') !!}
                        </div>

                        <div class="form-group">
                            <label for="photo">Your picture</label>

                            <div class="img-thumbnail mb-1" id="student_img_image_pre_area"
                                style="display: none; width: 80px;">
                                <img style="height: 100%; width: 100%;" src="" id="student_img_image_pre"
                                    alt="Not Set Yet">
                            </div>

                            {!! Form::file('photo', ['class' => 'form-control image_data', 'id' => 'photo', 'data-type' => 'student_img']) !!}

                            {!! invalid_feedback('photo') !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        @php
                            if (\File::exists(public_path('upload/college/hsc/draft/' . $hsc_admitted->photo))) {
                                $photo_url = url('upload/college/hsc/draft/' . $hsc_admitted->photo);
                            } else {
                                $photo_url = url(
                                    'upload/college/hsc/' .
                                        $hsc_admitted->admission_session .
                                        '/' .
                                        $hsc_admitted->photo,
                                );
                            }
                        @endphp
                        <img class="user_pic_view img-polaroid" src="{{ $photo_url }}" alt="User Photo" />

                    </div>


                </div>
            </div>

        </div>

        <div class="row mb">

            <div class="panel panel-info">
                <div class="panel-heading"> Admission Information Subject Change <span style="color:red;"> (if you want
                        to change your subject) - Select HSC Group otherwise leave blank</span></div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="hsc_group" class="col-sm-4 control-label">HSC Group :</label>
                        <div class="col-sm-8">
                            <?php
                            $admission_group = $hsc_admitted->hsc_group;
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
                        <label for="compulsory_course_codes" class="col-sm-4 control-label">Compulsory Course Info
                            :</label>
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
                        <label for="selective_course_codes" class="col-sm-4 control-label">Selective Course Info
                            :</label>
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
                        <label for="optional_course_codes" class="col-sm-4 control-label">Optional Course Info :</label>
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
                </div>
            </div>

        </div>

        <div class="row mb" style="margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('student.hsc.admission.HscConfirmation') }}" class="btn btn-warning">Back</a>
        </div>

    </div>
    {!! Form::close() !!}


    <script src="<?php echo url('/'); ?>/js/jquery.min.js"></script>
    <script src="<?php echo url('/'); ?>/js/bootstrap.min.js"></script>

    <script src="<?php echo url('/'); ?>/js/drop_down.js"></script>
    <script src="<?php echo url('/'); ?>/fjs/hsc_admission.js"></script>
    <script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
    <script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('#birth_date').datepicker({
            format: 'yyyy-mm-dd',
            endDate: new Date()

        });

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

        $(document).on('click', '#same_as_present', function(e) {
            var same_address = 0;
            if ($(this).is(':checked')) {
                present_ps = $('#present_ps').val();
                $('#permanent_village').attr('readonly', 'readonly');
                $('#permanent_village').val($('#present_village').val()).change();
                $('#permanent_po').attr('readonly', 'readonly');
                $('#permanent_po').val($('#present_po').val()).change();
                $('#permanent_dist').attr('disabled', 'disabled');
                $('#permanent_dist').val($('#present_dist').val()).change();
                $('#permanent_ps').val(present_ps).change();
                $('#permanent_ps').attr('disabled', 'disabled');
                same_address = 1;
            } else {
                $('#permanent_village').removeAttr('readonly');
                $('#permanent_po').removeAttr('readonly');
                $('#permanent_dist').removeAttr('disabled');
                $('#permanent_ps').removeAttr('disabled');
                same_address = 0;
            }
        });

        function getThanaOption(district) {
            value = district.value;
            if (value == '') {
                value = 'empty';
            }
            data_for = $(district).attr('data-for');
            $.ajax({
                type: "POST",
                url: "{{ url('api/get_thana_options') }}/" + value,
                success: function(result) {
                    var my_data = result.data;
                    if (my_data != undefined) {
                        if (my_data) {
                            $(`#${data_for}`).html(my_data);
                        } else {
                            $(`#${data_for}`).html('');
                        }
                    }
                    if (present_ps != undefined && present_ps != '') {
                        $('#permanent_ps').val(present_ps).change();
                        present_ps = '';
                    }
                },
                error: function(error) {
                    trigger_ajax_swal_msg(error);
                }
            });
        }

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
    @include('common.message')
</body>


</html>

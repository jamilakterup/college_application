<!doctype html>
<html lang="en">

<head>
  <title>Get Admit Card | EasyCollegeMate</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
    <section class="section admission-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 pt-4">
                    <div class="card">
                        <div class="card-header text-center bg-dark bg-gradient">
                            <h2 class="text-white">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</h2>
                            <h3 class="text-primary">Admit Card</h3>
                            <p class="text-danger">Please complete all field to download your Admit Card</p>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-6 p-3">
                                    @if(session()->get('error'))
                                        <p class="alert alert-danger text-center">{{session()->get('error')}}</p>
                                    @endif
                                    {!! Form::open(['route'=> 'get-admit-card', 'method'=> 'post']) !!}
                                        <div class="mb-3">
                                            <label for="student_id" class="form-label">Student ID</label>
                                            {!! Form::text('student_id', null, ['class'=> 'form-control', 'placeholder'=> 'Enter your Student ID']) !!}
                                            {!!invalid_feedback('student_id')!!}
                                        </div>
                                        <div class="mb-3">
                                            <label for="level" class="form-label">HSC level</label>
                                            {!! Form::select('level', create_option_array('classes', 'id', 'name', 'HSC Level'), null, ['class'=> 'form-select', 'id'=> 'level']) !!}
                                            {!!invalid_feedback('level')!!}
                                        </div>
                                        <div class="mb-3">
                                            <label for="exam" class="form-label">Exam Name</label>
                                            {!! Form::select('exam', ['' => '--Select Exam--'], null, ['class'=> 'form-select' , 'id' => 'exam']) !!}
                                            {!!invalid_feedback('exam')!!}
                                        </div>
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    {!! Form::close() !!}
                                </div>
                              </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                        <div class="card-footer text-muted">
                            <div class="d-flex justify-content-between">
                                <p>© 2020 EasyCollegeMate</p>
                                <p>Developed & Maintained By <a target="_blank" href="https://rajit.net">RajIT Solutions LTD</a></p>
                            </div>
                          </div>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
    </section>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>

<script>
    $(document).on('change', '#level', function(){
        var class_id = $(this).val();
        getExam(class_id);
    });

    getExam({{old('level')}});

    function getExam(class_id = null){
        if(class_id){
            $.ajax({
                type: "post",
                url: `{{url('api/get-exam-options')}}/${class_id}`,
                success: function (response) {
                    if(response.status == 'success'){
                        $('#exam').html(response.html);
                        @if(old('exam'))
                            $('#exam').val({{old('exam')}}).change();
                        @endif
                    }
                    
                }
            });
        }
    }

    $(document).on('click', '#exam', function(){
        if(!$('#level').val()){
            alert('Please Select HSC Level First');
        }
    });
  </script>
</body>

</html>
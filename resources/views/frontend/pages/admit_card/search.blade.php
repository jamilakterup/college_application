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
                            <h4 class="text-primary">{{$publish->exam->name}} - {{$publish->exam_year}}</h4>
                        </div><!-- end card header -->
                        <div class="card-body form-steps">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered" width="100%">
                                                <tr>
                                                    <td width="30%">Class Roll</td>
                                                    <td width="70%">{{$student->class_roll}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Student Name</td>
                                                    
                                                    <td>{{ucfirst($student->name)}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Fathers Name</td>
                                                    <td>{{$student->father_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mothers Name</td>
                                                    <td>{{$student->mother_name}}</td>
                                                </tr>      
                                                <tr>
                                                    <td>Session</td>
                                                    <td>{{$student->session}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Current Level</td>
                                                    <td>{{$student->current_level}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Group</td>
                                                    <td>{{$student->groups}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            @php  $sub_info= App\Models\StudentSubInfo::whereStudent_id($student->id)->whereCurrent_level($student->current_level)->get(); @endphp
                                            <table class="table table-bordered" width="100%">
                                                <tr>
                                                    <td width="16%">{{$sub_info[0]->sub1->code}}</td>
                                                    <td width="84%">{{$sub_info[0]->sub1->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{$sub_info[0]->sub2->code}}</td>
                                                    <td>{{$sub_info[0]->sub2->name}}</td>
                                                </tr>
                                                <tr>
                                                    @if($sub_info[0]->sub3_id!=0)
                                                    <td>{{$sub_info[0]->sub3->code}}</td>
                                                    <td>{{$sub_info[0]->sub3->name}}</td>
                                                    @else
                                                    <td>{{$sub_info[0]->sub4->code}}</td>
                                                    <td>{{$sub_info[0]->sub4->name}}</td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    @if($sub_info[0]->sub3_id!=0)    
                                                    <td>{{$sub_info[0]->sub4->code}}</td>
                                                    <td>{{$sub_info[0]->sub4->name}}</td>
                                                    @else
                                                    <td>{{$sub_info[0]->sub5->code}}</td>
                                                    <td>{{$sub_info[0]->sub5->name}}</td>
                                                    @endif    
                                                </tr>
                                                <tr>
                                                    @if($sub_info[0]->sub3_id!=0)    
                                                    <td>{{$sub_info[0]->sub5->code}}</td>
                                                    <td>{{$sub_info[0]->sub5->name}}</td>
                                                    @else
                                                    <td>{{$sub_info[0]->sub6->code}}</td>
                                                    <td>{{$sub_info[0]->sub6->name}}</td>
                                                    @endif    
                                                </tr>
                                                <tr>
                                                    @if($sub_info[0]->sub3_id!=0)     
                                                    <td>{{$sub_info[0]->sub6->code}}</td>
                                                    <td>{{$sub_info[0]->sub6->name}}</td>
                                                    @else
                                                    <td>{{$sub_info[0]->fourth->code}}</td>
                                                    <td>{{$sub_info[0]->fourth->name}} (4th)</td>
                                                    @endif    
                                                </tr>
                                                @if($sub_info[0]->sub3_id!=0) 
                                                <tr>       
                                                    <td>{{$sub_info[0]->fourth->code}}</td>
                                                    <td>{{$sub_info[0]->fourth->name}} (4th)</td>          
                                                </tr> 
                                                @endif        
                                            </table>
                                        </div>
                                    </div>
                              </div>

                                <!-- end tab content -->
                                <div class="d-flex justify-content-center my-3">
                                    {!! Form::open(['route' => 'download-admit-card', 'method'=> 'post']) !!}
                                        {!! Form::hidden('exam_id', $publish->exam->id) !!}
                                        {!! Form::hidden('student_id', $student->id) !!}
                                        {!! Form::hidden('session', $student->session) !!}
                                        {!! Form::hidden('current_level', $publish->level) !!}
                                        <button type="submit" class="btn btn-primary text-nowrap">Download</button>
                                        <a href="{{route('get-admit-card')}}" class="btn btn-default text-nowrap">Search Again</a>
                                    {!! Form::close() !!}
                                </div>
                            </div>
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
</body>

</html>
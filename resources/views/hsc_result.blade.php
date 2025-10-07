<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>HSC Result</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
	<style type="text/css">
		.form-group {
		    margin-bottom: 0px;
		}
		.help-block {
			color: red;
		}
	</style>
</head>
<body>

  <div class="row col-sm-8 col-sm-offset-2">
		<div class="panel panel-primary">
	        <div class="panel-heading">
	          <h2 class="panel-title text-center">
	            HSC Result
	          </h2>
	        </div>
	        <div class="panel-body">
	    	@if(Session::get('error') != '')
		    	<div class="alert alert-danger text-center">
		    		{!! Session::get('error') !!}
		    	</div>
	    	@endif

	    	@if(!$show_transcript)

	          <form action="{{route('hsc_result.search')}}" method="POST" role="form" class="form-horizontal">
	          	@csrf
				  <div class="form-group">
				    <label for="Class Roll" class="col-sm-2 control-label">Student ID</label>
				    <div class="col-sm-10">
				      {!! Form::text('student_id', $student_id, ['class'=> 'form-control input-sm', 'placeholder' => 'Enter Student ID']) !!}
				      <div class="help-block">{!!invalid_feedback('student_id')!!}</div>
				    </div>
				  </div>


				  <div class="form-group">
				    <label for="level" class="col-sm-2 control-label">Level</label>
				    <div class="col-sm-10">
				      {!! Form::select('level', $current_yr_lists , $current_level, ['class' => 'form-control input-sm']) !!}
				      <div class="help-block">{!!invalid_feedback('level')!!}</div>
				    </div>
				  </div>

				  <div class="form-group">
				    <label for="exam" class="col-sm-2 control-label">Exam</label>
				    <div class="col-sm-10">
				      {!! Form::select('exam_id', $exam_lists, $exam_id, ['class' => 'form-control input-sm']) !!}
				      <div class="help-block">{!!invalid_feedback('exam_id')!!}</div>
				    </div>
				  </div>
				  
				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-10">
				      <button type="submit" class="btn btn-primary">Search</button>
				    </div>
				  </div>
				</form>
	    	@else

	    	<?php   


		        $exam=App\Models\Exam::find($exam_id);
		        $info=App\Models\StudentInfoHsc::find($student_id);
		    ?>

		    <div class='col-sm-12'>
		        <table class='table table-bordered' style="text-align: left;">
		         <tr>
		                <td><strong>Exam</strong></td>                
		            <td >{{$exam->name}}-{{$exam_year}}</td>
		            </tr>
		        <tr>        
		            <td><strong>Student's Name</strong></td>                
		            <td >{{$info->name}}</td>
		        </tr>
		        <tr>        
		            <td><strong>Father's Name</strong></td>                
		            <td>{{$info->father_name}}</td>
		        </tr>
		        <tr>        
		            <td><strong>Mother's Name</strong></td>               
		            <td>{{$info->mother_name}}</td>
		        </tr>
		        <tr>        
	                <td><strong>Student ID</strong></td>
	                <td>{{$info->id}}</td>
	            </tr>
		        <tr>        
		            <td><strong>Session</strong></td>                
		            <td>{{$info->session}}</td>
		        </tr>
		         <tr>        
		            <td><strong>Group</strong></td>               
		            <td> {{$info->groups}}</td>
		        </tr>                      
		        </table>
		    </div>

		    <div class='col-sm-12' >
		        <table class='table table-bordered'>
		            <tr>
		                <th>Code</th>
		                <th>Subject Name</th>
		                
		                <th>CQ</th>
		                <th>MCQ</th>
		                <th>PR</th>
		                
		                
		                {{-- <th>MCQ</th>
		                <th>PR</th> --}}
		                {{-- <th></th> --}}
		                <th>Total Marks</th>
		                <th>Grade</th>
		                <th>Grade Point</th>
		                <th>GPA (Without 4th)</th>
		                <th>CGPA</th>               
		            </tr>
		      
		            <?php $sub_gpa= App\Models\StudentSubMarkGp::whereStudent_id($info->id)->whereSession($info->session)->where('exam_year', $exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
		            //dd($sub_gpa);
		            $k=1; 

		            $cgpa_tot= App\Models\HscGpa::whereStudent_id($info->id)->whereSession($info->session)->where('exam_year', $exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
		            ?>  
		         
		          
		           @foreach($sub_gpa as $sub)
		            <tr>
		                <?php   $particle_mark= App\Models\Mark::whereStudent_id($info->id)->whereSession($info->session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($sub->subject->id)->where('exam_year', $exam_year)->get();?>
		                <td style="text-align: center;">{{$sub->subject->code}}</td>
		                @if($sub->fourth!=1)
		                <td>{{$sub->subject->name}}</td>
		                @else
		                    <td>{{$sub->subject->name}}</td>
		                @endif
		                    <td style="text-align: center;">{{$particle_mark[0]->converted_mark}}</td>
		                @if(count($particle_mark)==2)
		                    <td style="text-align: center;">{{$particle_mark[1]->converted_mark}}</td>
		                    <td style="text-align: center;">-</td>          
		                   
		                @elseif(count($particle_mark)==3)
		                    <td style="text-align: center;">{{$particle_mark[1]->converted_mark}}</td>
		                    <td style="text-align: center;">{{$particle_mark[2]->converted_mark}}</td>
		                @else
		                    <td style="text-align: center;">-</td>
		                    <td style="text-align: center;">-</td>    
		                @endif
		                @if($sub->absent!=1)
		                <td style="text-align: center;">{{$sub->total_mark}}</td>
		                @else
		                <td style="text-align: center;">Absent</td>
		                @endif
		                <td style="text-align: center;">{{$sub->grade}}</td>
		                <td style="text-align: center;">{{$sub->point}}</td>
		                @if($k==1)
		                <td style="text-align: center;" rowspan="{{$sub_gpa->count()}}">{{$cgpa_tot[0]->without_4th}}</td>
		                <td style="text-align: center;" rowspan="{{$sub_gpa->count()-1}}">{{$cgpa_tot[0]->cgpa}}</td>

		                @endif
		                @if($k==$sub_gpa->count())
		                    <td>                       
		                        <!--<table  class='table table-bordered'>-->
		                        <!--    <tr><th>Above 2</th></tr>-->
		                        <!--    <?php $zero=0; ?>-->
		                        <!--    @if($sub->absent!=1 && $sub->point>2)   -->
		                        <!--    <tr><td >{{$sub->point-2}}</td></tr>-->
		                        <!--    @elseif($sub->absent!=1 && $sub->point<3)-->
		                        <!--    <tr><td >{{$zero}}</td></tr>-->
		                        <!--    @elseif($sub->absent1=1) -->
		                        <!--    <tr><td>{{$zero}}</td> </tr>      -->
		                        <!--    @endif    -->
		                        <!--</table>                            -->
		                    </td>
		                @endif
		            </tr>
		            <?php $k++;?>
		           @endforeach             
		        </table>      
		    </div>

		    	<div>
			        <div class='col-sm-6' style="text-align: left">
		               {!! Form::open(['route' => 'hsc_result.result-pdf', 'method' => 'post']) !!}
		               {!! Form::hidden('student_id', $student_id, []) !!}
		               {!! Form::hidden('exam_id', $exam_id, []) !!}
		               {!! Form::hidden('group_id', $group_id, []) !!}
		               {!! Form::hidden('exam_year', $exam_year) !!}
		               {!! Form::hidden('publish_id', $publish_id, []) !!}
		               	<button class="btn btn-danger" type="submit"> Download Transcript</button>
		               {!! Form::close() !!}
			        </div>
			        <div class='col-sm-6' style="text-align: right ;margin-bottom: 10px">
			            {{ link_to_route('hsc_result.result', 'Search Again', NULL, ['class' => 'btn btn-success']) }}
			        </div>
			    </div>

	    	@endif

	        </div>
	    </div>
  </div>
</body>
</html>
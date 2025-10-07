@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Subject Info Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item sub-menu">
	@include('BackEnd.hsc_result.subject_info.particles.subMenu')
</div>

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Student Subject Assign From Mark</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.assign_hsc_subject_from_marks.exe', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	<div class="form-group row">
					  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control']) !!}

					    {!!invalid_feedback('session')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('current_year', 'Current Year', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::select('current_year', $current_yr_lists, NULL, ['class' => 'form-control year']) }}

					    {!!invalid_feedback('current_year')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('exam_id', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::select('exam_id', $exam_lists, NULL, ['class' => 'form-control exam', 'id' => 'exam']) }}

					    {!!invalid_feedback('exam_id')!!}

					  </div>
					</div>

					<div class="form-group row">
					  {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2 form-control-label']) }}
					  <div class="col-md-10">

					    {{ Form::select('exam_year', selective_multiple_exam_year(), NULL, ['class' => 'form-control', 'id' => 'exam_year']) }}

					    {!!invalid_feedback('exam_year')!!}

					  </div>
					</div>

					<div class="form-group row">
					  <div class="col-md-10 offset-md-2">
					    {!! Form::submit('Generate', ['class'=> 'btn btn-primary']) !!}
					    <a href="{{ route('hsc_result.student_subject.assign') }}" class="btn btn-warning btn-outline">Back</a>
					  </div>
					</div>
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">			
	$(document).ready(function() {
		$(document.body).on('change','.year', function() {	
									 
            $('.loaderImage').show();            
             var group = $(this).val();
             var exam = $('#exam').empty();
             var subject = $('#subject').empty();
			 var exam_test = $('#exam_test').empty();
             
            if(group == '') 
            {
	            $('.loaderImage').hide();
	            $('<option/>', {
	                value:'',
	                text:'Select Exam'
	            }).appendTo(exam);

	             $('<option/>', {
			                        value:'',
			                        text:'Select Subject'
			                }).appendTo(subject);
							
				$('<option/>', {
			                    value:'',
			                    text:'Select class test'
			                }).appendTo(exam_test);			
	        }
               
            $.get('{{URL::to("hsc_result/mark_input/load-exam")}}/'+$(this).val(), function(response){
            	
	                if(response.success)
	                {  

	                    $('.loaderImage').hide();
		                    $('<option/>', {
			                        value:'',
			                        text:'Select Exam'
			                }).appendTo(exam);

			                $('<option/>', {
			                        value:'',
			                        text:'Select Subject'
			                }).appendTo(subject);
							$('<option/>', {
			                    value:'',
			                    text:'Select class test'
			                }).appendTo(exam_test);
							
		                    $.each(response.exam_arr, function(id, name){ 
		                        $('<option/>', {
		                            value:id,
		                            text:name
		                        }).appendTo(exam);
	                        });
	                }
                }, 'json'); 
		});

		$(document.body).on('change','.group', function() {	
									 
            $('.loaderImage').show();            
             var group = $(this).val();
             var subject = $('#subject').empty();
             var year = $('.year').val();

            
            if(group == '') 
            {
	            $('.loaderImage').hide();
	            $('<option/>', {
	                value:'',
	                text:'Select Subject'
	            }).appendTo(subject);
	        }
               
            $.get('{{URL::to("hsc_result/mark_input/load-subject")}}/'+year+'/'+$(this).val(), function(response){
            	
	                if(response.success)
	                {  

	                    $('.loaderImage').hide();
		                    $('<option/>', {
			                        value:'',
			                        text:'Select Subject'
			                }).appendTo(subject);

		                    $.each(response.sub_arr, function(id, name){ 
		                        $('<option/>', {
		                            value:id,
		                            text:name
		                        }).appendTo(subject);
	                        });
	                }
                }, 'json'); 
		});


		$(document.body).on('change','.exam', function() {	
								 
            $('.loaderImage').show();            
             var group = $(this).val();
             var examid = $('.exam').val();
             var exam_test = $('#exam_test').empty();
            $.get('{{URL::to("hsc_result/mark_input/load-classtest")}}/'+examid, function(response){
            	
	                if(response.success)
	                {  
	                    $('.loaderImage').hide();
		                    $.each(response.sub_arr, function(id, name){ 
		                        $('<option/>', {
		                            value:id,
		                            text:name
		                        }).appendTo(exam_test);
	                        });
	                }
                }, 'json'); 
		});


		
    });
</script>
@endpush
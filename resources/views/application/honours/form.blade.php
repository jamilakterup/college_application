<!DOCTYPE html>
<html>
<head>
	<title>Online Honours Final Application Form</title>
	<meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
    <style type="text/css">
    	.row{
    		margin-bottom: 5px;
    	}
    </style>
</head>

<body>
  

  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Honours Online Application</a>
    </div>
    <a href="<?php echo url('/').'/Application/Honours' ?>" class="btn btn-danger navbar-btn">Application</a>
  </div>
</nav>

<div class="container">
	<div class="panel-group">

		{!! Form::open(['route'=> 'student.honours.application.honAppInformationSubmit', 'method' => 'post', 'files' => true, 'class'=> 'form-horizontal']) !!}

		<div class="row mb">
	    	<div class="col-md-10 col-md-offset-1">
			    <div class="panel panel-primary">
			    	<div class="panel-heading">Student Data</div>
			      	<div class="panel-body">
			       		<div class="form-group">
			       			<label for="student_name" class="col-sm-2 control-label">Full Name</label>
			       			<div class="col-sm-10">
				       			{!! Form::text('student_name', null, ['class' => 'form-control', 'id'=> 'student_name', 'placeholder'=> 'Please Enter Your Full Name', 'required'=> true]) !!}
				       			{!!invalid_feedback('student_name')!!}
				       		</div>
			       		</div>

			       		<div class="form-group">
			       			<label for="father_name" class="col-sm-2 control-label">Father Name</label>
			       			<div class="col-sm-10">
				       			{!! Form::text('father_name', null, ['class' => 'form-control', 'id'=> 'father_name', 'placeholder'=> 'Please Enter Father Name', 'required'=> true]) !!}
				       			{!!invalid_feedback('father_name')!!}
				       		</div>
			       		</div>

			       		<div class="form-group">
			       			<label for="contact_no" class="col-sm-2 control-label">Contact No</label>
			       			<div class="col-sm-10">
			       				{!! Form::text('contact_no', null, ['class' => 'form-control', 'id'=> 'contact_no', 'placeholder'=> 'Contact No', 'required'=> true, 'maxlength' => '11']) !!}
			       				{!!invalid_feedback('contact_no')!!}
			       			</div>
			       		</div>

			       		<div class="form-group">
			       			<label for="admission_form" class="col-sm-2 control-label">NU Admission Form</label>
			       			<div class="col-sm-10">
			       				{!! Form::file('admission_form', ['class' => 'form-control', 'id'=> 'admission_form','required'=> true]) !!}
			       				{!!invalid_feedback('admission_form')!!}
			       			</div>
			       		</div>

			       		<div class="form-group">
			       			<label for="hsc_transcript" class="col-sm-2 control-label">HSC Transcript</label>
			       			<div class="col-sm-10">
			       				{!! Form::file('hsc_transcript', ['class' => 'form-control', 'id'=> 'hsc_transcript','required'=> true]) !!}
			       				{!!invalid_feedback('hsc_transcript')!!}
			       			</div>
			       		</div>

			       		<div class="form-group">
			       			<label for="name" class="col-sm-2 control-label"></label>
			       			<div class="col-sm-10">
			       				<button type="submit" class="btn btn-danger navbar-btn" >Submit</button>	
			       			</div>
			       		</div>
			       		
			      	</div>
			    </div>
		    </div>
	    </div>

	    	<input type="hidden" name="admission_roll" value="<?php  echo Session::get('admission_roll'); ?>">
	    	<input type="hidden" name="invoice_id" value="<?php  echo Session::get('invoice_id'); ?>">
						  	
			
		{!! Form::close() !!}
	</div>
</div>

	<script src="{{ asset('fjs/jquery.min.js') }}"></script>
  	<script src="{{ asset('fjs/bootstrap.min.js') }}"></script>
  	<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
  	<script src="{{ asset('fjs/scripts.js') }}"></script>
  	<script src="{{ asset('fjs/masters_application.js') }}"></script>
  	<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
   
   

  	@include('common.dropdown_js')
	@include('common.message')
</body>


</html>

<script type="text/javascript">
$("document").ready(function(){
$('#birth_date').datepicker({
	format: 'yyyy-mm-dd',
endDate: new Date() 
    
});
$('form').on('submit', function() {	


 }); 


</script>


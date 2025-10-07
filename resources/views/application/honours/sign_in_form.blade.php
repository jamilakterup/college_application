<!DOCTYPE html>
<html>
<head>
    <title>Online Honours Application</title>
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
</head>

<body>
  

  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Honours Online Application</a>
    </div>
    <a href="<?php echo url('/').'/Application/Honours' ?>" class="btn btn-danger navbar-btn">Application</a>
    <a href="<?php echo url('/').'/Application/Honours/signin'?>" class="btn btn-danger navbar-btn">Signin</a>
  </div>
</nav>

<div class="container">
  
  <div class="panel-group">


       <form action="" method="POST" enctype="multipart/form-data">
            <div class="row" id="passage_information">
             <center>
                <div class="col-md-6" style="float: none;">
                <div class="panel panel-primary "  >
                    <div class="panel-heading">Login</div>
                  <div class="panel-body">

                        <div class="form-group">
                        <div class="col-sm-12">                       
                          <div class="form-group">
                            <label>Ref. ID</label>
                            <input class="form-control tracking_id"  id="text1" type="text" value="" required name="text1">
                            <div class="help-block"></div>
                          </div>
                        </div>   
                        <div class="col-sm-12">               
                         <div class="form-group">
                        <label>Password</label>
                            <input class="form-control password"  id="text1" type="password" value="">
                            <div class="help-block"></div>
                            </div>
                            </div>
                        </div> 
                            <div>
                            <input type="button" id="forgot_password" class="btn btn-info btn-large pull-left" value="Forgot Ref.ID/Password" />
                            <input type="button" id="sign_in" class="btn btn-info btn-large pull-right" value="Login" />
                            </div>                        
                  </div>
                </div>
                </div>
               </center> 

            </div>        
              
            <div class="row" id="next_step_form">
             <center>
                <div class="col-md-6" style="float: none;">
                <div class="panel panel-primary "  >
                    <div class="panel-heading">Recover Ref.ID/Passsword</div>
                  <div class="panel-body">

                        <div class="form-group">
                        <div class="col-sm-12">                       
                          <div class="form-group">
                            <label>Admission Roll</label>
                            <input class="form-control admission_roll"   id="text1" type="text"  placeholder="Admission Roll" required>
                            <div class="help-block"></div>
                          </div>
                        </div>   
                        <div class="col-sm-12">               
                         <div class="form-group">
                        <label>HSC Roll</label>
                            <input type="text" id="text1"  class="form-control hsc_roll"   placeholder="Type your HSC roll">
                            <div class="help-block"></div>
                            </div>
                            </div>
                        </div> 
                            <div>
                            <input type="button" id="retrieve_passsword" class="btn btn-info btn-large pull-right" value="Recover" />
                            </div>                        
                  </div>
                </div>
                </div>
               </center> 

            </div> 


       </form>
       <br/><div style="margin:0 auto;font-size:18px; text-align:center;" id="retrieve_error">
       <br /> <div style="margin:0 auto;font-size:18px; text-align:center;" id="sign_in_error"></div>    
       </div>
    </div>
</div>
            

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('fjs/masters_application.js') }}"></script>

</body>


</html>

    <script type="text/javascript">

      $('document').ready(function(){
            $('#next_step_form').hide();

            $('#forgot_password').click(function(){
                $('#next_step_form').show();
                $('#passage_information').hide();
            });

            $('#retrieve_passsword').click(function(){
                var admission_roll =$('.admission_roll').val();
                var hsc_roll =$('.hsc_roll').val();

                $.ajax({
                    type:'POST',
                    url:'retrievepass',
                    data:{admission_roll:admission_roll,hsc_roll:hsc_roll},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    
                    success:function(response){
                        // alert(response);
                        var res = JSON.parse(response);
                        //console.log(res);
                        var password = res[0];
                        var auto_id = res[1];
                        if (password == '') {
                            $('#retrieve_error').html("<span style='color:red;font-size:25px;'>These was some problem. Please try again.</span>");
                        }
                        else {
                            $('#retrieve_error').html("<span style='color:green;font-size:25px;'>Your Ref.ID is: <b>" + auto_id + "</b></span><br><br><span style='color:green;font-size:25px;'>Your password is: <b>" + password + "</b></span>");
                        }
                    }
                });
            });
      });
  </script>



<script type="text/javascript">
    
    $('document').ready(function(){
          $('#sign_in').click(function(){
            $('#sign_in_Modal').modal('show');


                        var tracking_id = $('.tracking_id').val();
                        var password = $('.password').val();
                        $.ajax({
                                type:'POST',
                                url :'mastersStudentSignin',
                                dataType:'json',
                                data:{tracking_id:tracking_id,password:password},
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                success:function(response){
                                    $('#sign_in_Modal').modal('hide');
                                    var sign_in_status =response;
                                    
                                    if(sign_in_status==1){

                                        var url ='dbblapplication';
                                        window.location=url;
                                    }
                                    if(sign_in_status==2){
                                        $('#sign_in_error').html('<span style="color:red;">আপনার ট্রাকিং আইডি অথবা পাসওয়ার্ড ভুল হয়েছে। সঠিকভাবে পূরণ করুন।');
                                    }

                                    if(sign_in_status==5){
                                        $('#sign_in_error').html('<span style="color:red;">Your Invoice is not genereated. Please contact to college।');
                                    }

                                    if(sign_in_status==3){
                                        $('#sign_in_error').html('<span style="color:red;">You are not in merit list।');
                                    }

                                    if(sign_in_status==4){
                                        $('#sign_in_error').html('<span style="color:red;">Admission not open।');
                                    }
                                }
                        });//end of ajax
            });//end of sign in
          
    });
</script>        
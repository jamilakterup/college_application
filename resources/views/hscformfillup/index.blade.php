<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HSC Online Form Fillup</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply"> 


    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">

    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/animate.min.css') }}">
    <style type="text/css">
    </style>

</head>
<body >
    <nav class="navbar navbar-trans navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapsible">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <p class="navbar-brand" href="#section1"><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>HSC Online Form Fillup</span></p>
            </div>
            <div class="navbar-collapse collapse" id="navbar-collapsible">
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="#section2">HSC Form Fillup</a></li>
                <!-- <li><a href="#section3">Product</a></li>
                <li><a href="#section4">Contact</a></li>
                <li><a href="#section5">More</a></li>
                <li>&nbsp;</li> -->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li></li>
            </ul>
        </div>
    </div>
</nav>

<section class="container-fluid one" id="section1">
    <div class="v-center">
        <h1 class="text-center"><span style='color:   #cf5713   ;'>Online Form Fillup</span><span> For HSC Student</span></h1>
    </div>
</section>

<section class="container-fluid two" id="section2">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInLeft animate">
                            <div class="panel-heading">
                                <h3>{{config('settings.college_name')}}</h3></div>
                                <div class="panel-body">
                                    <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="" class="footer-logo">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 text-center">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="panel panel-primary slideInUp animate">
                                <div class="panel-heading">
                                    <h3>গুরত্বপূর্ণ নির্দেশাবলীঃ</h3></div>
                                    <div class="panel-body">
                                        <h4>এই ওয়েবসাইটের মাধ্যমে Form Fillup সম্পন্ন করার জন্য নিম্নের তথ্যগুলি অত্যাবশ্যকঃ </h4>
                                        <hr>
                                        <h4>Registration ID</h4>
                                        <h4>Formfillup Level</h4>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <p class="text-center">
                        <br>
                        <a href="#" class="btn btn-blue btn-lg btn-huge lato" data-toggle="modal" data-target="#myModal">Click Here</a>
                    </p>
                </div>

            </div>
            <!--/container-->
        </section>







        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 column">
                        <!-- <h4>Information</h4> -->
                        <p>All Right Reserved &copy; 2016</p>
                <!-- <ul class="nav">
                    <li><a href="about-us.html">Products</a></li>
                </ul> -->
            </div>
            <div class="col-xs-6 col-md-6 column">
                <div class="fr">
                    <p>Powered By &nbsp;&nbsp;&nbsp;</p>
                    <img src=" {{url('/img/ritlogo.png')}}" alt="" class="footer-logo">
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="scroll-up">
    <a href="#"><i class="fa fa-angle-up"></i></a>
</div>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="text-center"><img src="" class="img-circle"><br>HSC Form Fillup</h2>
            </div>
            <div class="modal-body row">
                <h6 class="text-center">COMPLETE THESE FIELDS TO FORM FILLUP</h6>
                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0">
                    <div style="margin:5px 5px;font-size:18px;color:red;text-align: center;" id="next_step_error"></div>

                    <form action="{{url('HSC/formfillup/check')}}" method="post" id="formCheck">
                        @csrf
                        <div class="form-group">
                            <input type="text" id="registration_id" class="form-control input-lg" placeholder="Registration ID" name="registration_id">
                        </div>

                        @if(get_config('hsc_ff_promotion_checking'))

                            <div class="form-group">
                                <input type="text" id="student_id" class="form-control input-lg" placeholder="Student ID/Class Roll" name="student_id">
                                <span class="text-warning mt-1">দয়া করে সঠিক আইডি/রোল ইনপুট করুন অথবা ফরমপূরণ গ্রহণযোগ্য হবেনা ।</span>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            @php
                            $config_levels = DB::table('form_fillup_config')->where('course', 'hsc')->where('open', 1)->pluck('current_level', 'current_level');
                            @endphp
                            {!! Form::select('current_level', $config_levels, null, ['placeholder'=>'Select Formfillup Level', 'class'=>'form-control' ,'required'=>true, 'id'=> 'current_level', 'name'=> 'current_level']) !!}
                        </div>                    

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger btn-lg btn-block">Next</button>
                            
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <h6 class="text-center"><a href="http://rajit.net" target="blank">raj IT</a></h6>
            </div>
        </div>
    </div>

    <div id="admission_step_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
       <div class="modal-header">

        <h3 id="myModalLabel"></h3>
    </div>
    <div class="modal-body">
        <p>Please Wait....</p>
    </div>
    <div class="modal-footer">

    </div>
</div>



</div>
<!--scripts loaded here-->

<script src="{{ asset('fjs/jquery.min.js') }}"></script>
<script src="{{ asset('fjs/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
<script src="{{ asset('fjs/scripts.js') }}"></script>
<script src="{{ asset('fjs/hscff.js') }}"></script>
@include('common.message')

<script>

    $(document).on('submit', '#formCheck', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var url = $(this).attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend: function() {
                $('#next_step_error').empty();
            },
            data: formData,
            dataType: 'json',
        })
        .done(function(response) {
            $('#admission_step_modal').modal('hide');
            var status= response.status;
            if(status==2){
                var url ='formfillup/next_step';
                window.location=url;
            }            
            if(status==1){
                var url ='formfillup/view';
                window.location=url;
            }
        })
        .fail(function(xhr, status, error) {
            var response = JSON.parse(xhr.responseText);
            if (response && response.error) {
                errorMessage = response.error;
            } else {
                errorMessage = 'An error occurred while processing your request.';
            }
            $('#next_step_error').html(errorMessage);
        });
    });


    $("#admission_step").click(function(){
       $('#admission_step_modal').modal('show'); 

       var registration_id =$('#registration_id').val();
       var current_level = $('#current_level').val();
       if(registration_id =='')
       {
           $('#next_step_error').html('<span style="color:red;">Enter Registration No');
           $('#information').hide();
       } 
       if(current_level =='')
       {
           $('#next_step_error').html('<span style="color:red;">Select Current Level ');
           $('#information').hide();
       }   

       else
         $.ajax({ 
            type:'POST',
            url:'formfillup/check',
            data:{registration_id:registration_id, current_level:current_level},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(response){
                    //alert(response);
                $('#admission_step_modal').modal('hide');
                var status= response;
                if(status==0 )
                {
                 $('#next_step_error').html('<span style="color:red;">Form Fillup Not Open');
                 $('#information').hide();

             }
             if(status==5){
               $('#next_step_error').html('<span style="color:red;">Registration ID is Wrong.');  
               $('#information').hide(); 
           }

           if(status==4){
               $('#next_step_error').html('<span style="color:red;">Date is Expired.');  
               $('#information').hide(); 
           }

           if(status==2){
            var url ='formfillup/next_step';
            window.location=url;
        }            
        if(status==1){
            var url ='formfillup/view';
            window.location=url;
        }
    }
});
 });
</script>
</body>
</html>
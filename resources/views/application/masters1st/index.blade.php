<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Online Preliminary to Masters Application</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
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
            <p class="navbar-brand" href="#section1"><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Online Preliminary to Masters Application</span></p>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="#section2">Application</a></li>
                <!--<li><a href="<?php echo url('/').'/Application/Masters1st/signin'?>" class=""><i class=''></i>Signin</a></li>
                <li><a href="#section3">Product</a></li>
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
        <h1 class="text-center"><span style='color:   #cf5713   ;'>Online Application</span><span> For Preliminary to Masters Student</span></h1>
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
                                
                                <ul class="instruction" type="square" style="text-align: left; letter-spacing: 1px;">
                                      <li>Admission Roll নং</li>
                                      <li>ডিজিটাল ছবি(অবশ্যই 100 কিলোবাইটের মধ্যে)</li> 
                                      <li style="color:red;">Security Code SMS এর মাধ্যমে প্রাপ্ত</li>
                                       <br>
                                       
                                  
                                </ul>
                                
                            
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
                <p>All Right Reserved &copy; 2018</p>
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
                <h2 class="text-center"><img src="" class="img-circle"><br>Preliminary to Masters Applicaton</h2>
            </div>
            <div class="modal-body row">
                <h6 class="text-center">COMPLETE THESE FIELDS FOR ADMISSION</h6>
                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0"  >
                    <div class="form-group">
                        {!! Form::text('admission_roll', null, ['class'=> 'form-control input-lg', 'placeholder'=> 'Admission Roll', 'required'=> true, 'id'=> 'admission_roll' ]) !!}
                    </div>
                    <div style="margin:0 auto;font-size:18px; text-align:center;" id="next_step_error"></div>

                    <div class="form-group">
                        <button class="btn btn-danger btn-lg btn-block" id="admission_step">Next</button>
                    </div>
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
  	<script src="{{ asset('fjs/scripts.js') }}"></script>
  	<script src="{{ asset('fjs/masters1st_application.js') }}"></script>
  </body>
</html>
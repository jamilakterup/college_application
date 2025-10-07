<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Online Form Fillup</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">


      <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/animate.min.css') }}">
    <style type="text/css">
        .one {
            min-height: 250px;
        }
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
            <p class="navbar-brand" href="#section1"><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Online HSC 2nd Year Admission</span></p>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="#section2">HSC 2nd Year Admission</a></li>
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
        <h1 class="text-center"><span style='color:   #cf5713   ;'>HSC 2nd Year Admission</span></h1>
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
                                <img src=" {{url('/img/logo2.png')}}" alt="" class="footer-logo">
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
               <p> এই ওয়েবসাইটের মাধ্যমে HSC 2nd Year Promotion Fee Payment সম্পন্ন করার জন্য নিম্নের তথ্যগুলি অত্যাবশ্যকঃ </p>
             
               <h4>Exam Year</h4>
               <h4>HSC Group</h4>
               <h4>Class Roll</h4>

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
                <p>All Right Reserved &copy; <?php echo date('Y'); ?></p>
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
                <h2 class="text-center"><img src="" class="img-circle"><br>HSC 2nd Year Promotion</h2>
            </div>
            <div class="modal-body row">
                <h6 class="text-center">COMPLETE THESE FIELDS TO HSC 2nd Year Promotion</h6>
                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0"  >
				 <label>Class Roll</label>
                    <div class="form-group">                            
                      <input type="text" id="text1" class="roll form-control" placeholder="Type your Class Roll here" />
                    </div>
					 <label>Group</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 
					 <div class="form-group">
						<select class="group form-control " id="group" name="group" required>
							<option value="">--Select--</option>          
							<option value="Science">SCIENCE</option>
							<option value="Business Studies">BUSINESS STUDIES</option>
							<option value="Humanities">HUMANITIES</option>

						</select>
					 </div>
					
					<label>Exam Year</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="form-group" >
                        {!! Form::select('session', selective_current_exam_year(), null, ['class' => 'session form-control', 'required'=> true, 'id'=> 'session']) !!}
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger btn-lg btn-block" id="admission_step">Next</button>
                        
                    </div>
			        <div style="margin:0 auto;font-size:18px; text-align:center;color:blue" id="information">
			            
			            অনলাইনে Payment প্রক্রিয়া শুরু করার জন্য আপনার  Class Roll,Group,Exam Year নং  পূরণ করে Next বাটনে এ ক্লিক করুন।
                        
                    </div>
                    <div style="margin:0 auto;font-size:18px; text-align:center;" id="next_step_error"></div>

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
{{-- {{ HTML::script('fjs/hscff.js') }} --}}

    <script src="{{ asset('fjs/jquery.min.js') }}"></script>
    <script src="{{ asset('fjs/bootstrap.min.js') }}"></script>
    <script src="{{ asset('fjs/scripts.js') }}"></script>
    <script src="{{ asset('fjs/hsc_promotion.js') }}"></script>

  </body>
</html>
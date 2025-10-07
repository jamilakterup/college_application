<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Masters Online Form Fillup</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">

<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/font-awesome.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/bootstrap.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/styles.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/animate.min.css">
<link href="https://fonts.googleapis.com/css?family=Bungee+Inline" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">


    
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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Masters Online Form Fillup</span></p>
      <a href="<?php echo url('/').'/Masters/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li></li>
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo Url('/')?>/Masters/formfillup">Masters Form Fillup</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php  foreach ($student_infos as $result) {
            $current_level=$result->current_level;
            $session=$result->session;
            $name = $result->name;
            $subject = $result->dept_name;
            $faculty_name = $result->faculty_name;
            $session = $result->session;
            $college_id  = $result->id;
     }
       Session::put('admission_step', 1);
       Session::put('payment_amount', $payment_amount);
       Session::put('subject', $subject);
       Session::put('faculty_name', $faculty_name);





?>


<section class="container-fluid two" id="section2"  style="margin-top: 26px;">
    <div class="container">
      <div class="row">

          <div class="col-sm-10 col-sm-offset-1">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInLeft animate">
                         
                            <div class="panel-body">
                            <h4>আপনার Form FillUp প্রক্রিয়া সফলভাবে সম্পন্ন হয়েছে । <br> নিচের লিংক থেকে কনফার্মেশন স্লিপ ডাউনলোড করতে পারেন । <br/><br/>
                     <a id="confirm_slip"  style="cursor:pointer;"><span style='color:#673AB7;'>Download Confirmation Slip</span></a></h4>
             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      </div>
        <div class="row">
       
            <div class="col-sm-10 text-center col-sm-offset-1">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInUp animate">
                            <div class="panel-heading">
                                <h4>Student Details </h4></div>
                            <div class="panel-body">
                                
                                <table class='table table-striped'>
                                    <tr >
                                        <td>Full Name</td>
                                        <td><?php echo $name ;?></td>
                                    </tr>

                                    <tr >
                                        <td>Registration ID</td>
                                        <td><?php echo $college_id; ?></td>
                                    </tr>

                                    <tr>
                                        <td>Form Fillup Level</td>
                                        <td><?php echo $current_level ;?></td>
                                    </tr>

                                    <tr>
                                        <td>Faculty</td>
                                        <td><?php echo $faculty_name ;?></td>
                                    </tr>

                                    <tr>
                                        <td>Department Name</td>
                                        <td><?php echo $subject ;?></td>
                                    </tr>
 
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    <img src="{{ URL::to('/') }}/img/ritlogo.png" alt="" class="footer-logo">
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="scroll-up">
    <a href="#"><i class="fa fa-angle-up"></i></a>
</div>


    <!--scripts loaded here-->
  
     <script src="<?php  echo url('/') ?>/fjs/jquery.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/bootstrap.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/scripts.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/mastersff.js"></script>
     <script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
    <script src="{{ asset('js/loadingoverlay.min.js') }}"></script>


<div class="modal fade" id="confirm_slip_file">
  <div class="modal-dialog" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
       <div id="download_link1"><p>Please Wait....</p></div>
      </div>
      <div class="modal-footer">
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
@include('common.message')
  </body>
</html>
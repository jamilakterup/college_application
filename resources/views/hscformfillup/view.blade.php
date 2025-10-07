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

<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/font-awesome.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/styles.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/animate.min.css">
<link href="https://fonts.googleapis.com/css?family=Bungee+Inline" rel="stylesheet">  
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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>HSC Online Form Fillup</span></p>
      <a href="<?php echo url('/').'/HSC/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li></li>
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo Url('/')?>/HSC/formfillup">HSC Form Fillup</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php
   Session::put('admission_step', 1);
   Session::put('payment_amount', $invoice->payment_amount);
   Session::put('groups', $invoice->pro_group);
?>

<section class="container-fluid two" id="section2" style="margin-top: 26px;">
    <div class="container">
    @if($invoice->status == 'Paid' && count($ff_student) > 0)
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
      @endif

        <div class="row">
            <div class="col-sm-10 text-center col-sm-offset-1">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInUp animate">
                            <div class="panel-heading">
                                <h4>Student Details </h4></div>
                            <div class="panel-body">
                                
                                <table class='table table-striped'>
                                    <tr>
                                        <td>Full Name</td>
                                        <td>{{$invoice->name}}</td>
                                    </tr>

                                    <tr>
                                        <td>Registration ID</td>
                                        <td>{{$invoice->roll}}</td>
                                    </tr>
                                    <tr>
                                        <td>Form Fillup Level</td>
                                        <td>{{$invoice->level}}</td>
                                    </tr>

                                    <tr>
                                        <td>Groups</td>
                                        <td>{{$invoice->pro_group}}</td>
                                    </tr>
                                    @if($invoice->registration_type)
                                        <tr>
                                            <td>Registration Type</td>
                                            <td>{{$invoice->registration_type}}</td>
                                        </tr>
                                    @endif
 
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->status == 'Pending' && count($ff_student) < 1)
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
              @php
                  $biller_id = config('settings.college_biller_id');
                  $college_name_bn = config('settings.college_name_bn');
                  $student_id = $invoice->roll;
                  $payment_guideline = get_config('hsc_ff_payment_guideline');

                  if($payment_guideline){
                    echo @configTempleteToBody($payment_guideline,['student_id'=> $invoice->roll, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>round($invoice->total_amount)]);
                  }
              @endphp

            <div class="row text-center">
                <button class="btn btn-primary text-center" onClick="window.location.reload();">Confirm To Download Confirmation Slip</button>
            </div>
                
            </div>
        </div>
        @endif

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
     <script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
     <script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
     <script src="<?php  echo url('/') ?>/fjs/scripts.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/hscff.js"></script>
     @include('common.message')


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
  </body>
</html>
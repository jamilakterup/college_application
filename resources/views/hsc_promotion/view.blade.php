<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Online Form Fillup</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/font-awesome.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/fcss/bootstrap.min.css">
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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Online Form Fillup</span></p>
			<a href="<?php echo url('/').'/Hsc/promotion/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li></li>
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo Url('/')?>/Hsc/promotion">Form Fillup</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php  foreach ($student_infos as $student_info) {
       $current_level=$student_info->current_level;
       $session=$student_info->session;
       $name=$student_info->name;
       $fathers_name=$student_info->father_name;
       $mothers_name=$student_info->mother_name;
       $subject=$student_info->groups;
       $gender=$student_info->gender;

       $religion=$student_info->religion;
       $birth_date=$student_info->birth_date;
       $class_roll=$student_info->class_roll;
       $college_id=$student_info->id;
     }
       Session::put('admission_step', 1);
       Session::put('payment_amount', $payment_amount);

      if($subject=='Science')
      $sub='Science';
        else  if($subject=='Humanities')
      $sub='HUMANITIES';
        else  if($subject=='Business Studies')
      $sub='BUSINESS STUDIES';
       else 
       $sub = $subject;

       if($payment_status=='Paid')
       {
       Session::put('student_id', $college_id);
       Session::put('current_level',$current_level);
       Session::put('subject',$subject);                    
       Session::put('name',$name);                   
       Session::put('session',$session);
       }

?>


<section class="container-fluid two" id="section2">
    <div class="container">
      <div class="row">

          <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInLeft animate">
                         
                            <div class="panel-body">
                            <?php  if($payment_status=='Pending') {
                              $payment_status = "<span style='color:red;'>Pending</span>";
                              ?>

                                <h4>আপনার, Form Fillup ফি জমা দিতে  নিচের লিংকে ক্লিক করুন । </h4>
             <a  href="">DBBL Payment</a>
                        

                         <?php   } elseif($payment_status=='Paid') { ?>
                            <h4>আপনার <big style="color:red;"> <?php echo $current_level ?> Form FillUp </big> প্রক্রিয়া সফলভাবে সম্পন্ন হয়েছে । <br> নিচের লিংক থেকে কনফার্মেশন স্লিপ ডাউনলোড করতে পারেন । <br/><br/>
                     <a id="confirm_slip"  style="cursor:pointer;"><span style='color:#673AB7;'>Download Confirmation Slip</span></a></h4>
                     <?php  } ?>
             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInLeft animate">
                            <!-- <div class="panel-heading">
                                <h3>Student Photo</h3></div> -->
                            <div class="panel-body">
                                <img src=" 
                                http://localhost:8000/img/logo2.png" alt="" class="footer-logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      </div>
        <div class="row">
       
            <div class="col-sm-6 text-center">
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
                                    <tr>
                                        <td>Father's Name</td>
                                        <td><?php echo $fathers_name ;?></td>
                                    </tr>
                                    <tr>
                                        <td>Mother's Name</td>
                                        <td><?php echo $mothers_name ;?></td>
                                    </tr>
                                    <tr>
                                        <td>Date of Birth</td>
                                        <td><?php echo $birth_date ;?></td>
                                    </tr>
                                     <tr>
                                        <td>Gender</td>
                                        <td><?php echo $gender ;?></td>
                                    </tr>
                                     <tr>
                                        <td>Religion</td>
                                        <td><?php echo $religion ;?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
               <div class="col-sm-6 text-center">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInUp animate">
                            <div class="panel-heading">
                                <h4>Academic Details </h4></div>
                            <div class="panel-body">
                                 <table class='table table-striped'>
                                    <tr >
                                        <td>Student ID</td>
                                        <td><?php echo $college_id; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Current Level</td>
                                        <td><?php echo $current_level ;?></td>
                                    </tr>
                                    <tr>
                                        <td>Class Roll</td>
                                        <td><?php echo $class_roll ;?></td>
                                    </tr>
                                    <tr>
                                        <td>Group</td>
                                        <td><?php echo $sub ;?></td>
                                    </tr>
                                     <tr>
                                        <td>Session</td>
                                        <td><?php echo $session ;?></td>
                                    </tr>
                                     <tr>
                                        <td>Payment Status</td>
                                        <td><?php echo $payment_status ;?></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <p class="text-center">
            <br>
            <a href="#" class="btn btn-blue btn-lg btn-huge lato" data-toggle="modal" data-target="#myModal">Click Here</a>
        </p> -->
    </div>
    <!--/container-->
</section>








<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 column">
                <!-- <h4>Information</h4> -->
                <p>All Right Reserved &copy; <?php echo date('Y') ?></p>
                <!-- <ul class="nav">
                    <li><a href="about-us.html">Products</a></li>
                </ul> -->
            </div>
            <div class="col-xs-6 col-md-6 column">
                <div class="fr">
                    <p>Powered By &nbsp;&nbsp;&nbsp;</p>
                    <img src=" {{url('/')}}/img/ritlogo.png" alt="" class="footer-logo">
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
     <script src="<?php  echo url('/') ?>/fjs/hsc_promotion.js"></script>


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
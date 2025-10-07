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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Online HSC 2nd Year Admission</span></p>
			<a href="<?php echo url('/').'/Hsc/promotion/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/HSC/promotion">HSC 2nd Year Admission</a></li>
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

?>


<section class="container-fluid two" id="section2">
    <div class="container">

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
                                        <td>Group</td>
                                        <td><?php echo $sub ;?></td>
                                    </tr>
                                     <tr>
                                        <td>Session</td>
                                        <td><?php echo $session ;?></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      <div class="row">

  <div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <div class="col-md-6">
            <a  href="DBBL">DBBL Payment</a>
          </div>
          <div class="col-md-6">
            <a  href="telecash" class="pull-right">DBBL Payment</a>
          </div>  
        </div>
        <div class="panel-body ">

        <div class="alert" id="con_mes" style="text-align:center;margin:0 5% 0 5%;font-size:17px;">                 
                 </div><br/>
        <br>

         <div class="alert alert-warning" style="margin:0 2% 0 2%;font-size:17px;text-align:center;">
         অনুগ্রহ করে ৩, ৪ এবং ৫ নম্বর ধাপ খুবই সতর্কতার সাথে সফলভাবে সম্পন্ন করুন। অন্যথায় আপনার ভুলের জন্য কর্তৃপক্ষ দায়ী থাকবে না।
                 </div><br/>  
                 <h3 style="color:green;padding-left:30px;text-decoration:underline;">টাকা পরিশোধের নিয়মাবলীঃ</h3>
<ul class="instruction" style="font-size:16px;">
					<ol>
            <li>যেসকল মোবাইলে DBBL মোবাইল এ্যাকাউন্ট রয়েছে,সেই মোবাইল থেকে <b>*322#</b> ডায়াল করে DBBL মোবাইল ব্যাংক মেনুতে যান
          </li>
          <li>
             পেমেন্ট <b>1</b> (One) সিলেক্ট করুন
          </li>
          
          <li>
             Other <b>2</b> (Two) সিলেক্ট করুন
          </li>
          
          <li>
             Enter Payer Mobile Number (Enter Student Mobile Number)
          </li>
          
          <li>
             Other <b>0</b> (Zero) সিলেক্ট করু
          </li>
          
          <li>
            রাজশাহী কলেজের Biller ID <b>304</b> টাইপ করুন
          </li> 
             <li>
            Bill No-এ আপনার  Class Roll <strong><?php echo $student_id ?></strong> দিন
          </li>
          <li>
						টাকার পরিমাণ <strong> <?php echo round($payment_amount) ;?> </strong> লিখুন 
					</li>	         
          <li>
            চার ডিজিটের <b>PIN</b> টাইপ করুন
          </li>
          <li>
            OK/Send বাটন চাপুন
          </li>         
          <li>            
            আপনি DBBL থেকে একটি <b>TrxID</b> পাবেন
          </li>
          <li>            
             প্রাপ্ত <b>TrxID</b> দিয়ে <b>Submit</b> বাটনে ক্লিক করুন
          </li>
          <li>
            তথ্য পাওয়ার প্রয়োজনে DBBL Help Line - <b>16216</b> নম্বরে কল করতে পারেন
          </li>
        </ol>

				</ul>			
        </div>
        <!-- <div class="panel-footer">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
             tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
             quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
             consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
             cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
             proident, sunt in culpa qui officia deserunt mollit anim id est laborum.   
        </div> -->
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
                <p>All Right Reserved &copy; 2016</p>
                <!-- <ul class="nav">
                    <li><a href="about-us.html">Products</a></li>
                </ul> -->
            </div>
            <div class="col-xs-6 col-md-6 column">
                <div class="fr">
                    <p>Powered By &nbsp;&nbsp;&nbsp;</p>
                    <img src=" http://localhost:8000/img/ritlogo.png" alt="" class="footer-logo">
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="scroll-up">
    <a href="#"><i class="fa fa-angle-up"></i></a>
</div>



    <!--scripts loaded here-->
  
     <script src="{{ asset('fjs/jquery.min.js') }}"></script>
    <script src="{{ asset('fjs/bootstrap.min.js') }}"></script>
    <script src="{{ asset('fjs/scripts.js') }}"></script>
    <script src="{{ asset('fjs/hsc_promotion.js') }}"></script>
<!-- 
     <div id="confirm_slip_file" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
             <div class="modal-header">
                
                <h3 id="myModalLabel"></h3>
            </div>
            <div class="modal-body">
                    <div id="download_link1"><p>Please Wait....</p></div>
                
            </div>
            <div class="modal-footer">
                
            </div>
        </div> -->

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
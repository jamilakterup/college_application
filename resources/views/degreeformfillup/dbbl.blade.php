<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Degree Online Form Fillup</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply"> 

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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Degree Online Form Fillup</span></p>
      <a href="<?php echo url('/').'/Degree/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/Degree/formfillup">Degree Form Fillup</a></li>
            </ul>
        </div>
    </div>
</nav>





<section class="container-fluid two" id="section2">
    <div class="container">
      <div class="row">

  <div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <div class="col-md-6">
            <a  href="telecash">DBBL Payment</a>
          </div>
          <div class="col-md-6">
            <a  href="telecash" class="pull-right">DBBL Payment</a>
          </div>  
        </div>
        <div class="panel-body ">

        <div class="alert" id="con_mes" style="text-align:center;margin:0 5% 0 5%;font-size:17px;">                 
                 </div><br/>

        <div class="alert alert-info" id="input_div" style="text-align:center;margin:0 5% 0 5%;font-size:17px;">
        <h3 style="color:green">After payment insert your DBBL transaction id </h3>

       <input type='hidden' class="form-control" disabled="yes" style='width:140px; display: inline-block;'  value='<?php echo $student_id;?>' id='studentID'/><br/>
     <input type='hidden' id="pay_am_floor" class="form-control" disabled="yes" style='width:140px; display: inline-block;'  value='<?php echo $payment_amount;?>' id='studentID'/>
     <div class="form-group">
        <div style="width: 400px;" class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
       <input  id="trxid" name="dbbl_id" placeholder="TrxID" required class="form-control"/>
     
        </div>
    <br />
    </div>
        <input type='submit' class='btn btn-info' value='Submit' id='submit_payment'/>

        </div> 
        <br>

         <div class="alert alert-warning" style="margin:0 2% 0 2%;font-size:17px;text-align:center;">
         অনুগ্রহ করে ৩, ৪ এবং ৫ নম্বর ধাপ খুবই সতর্কতার সাথে সফলভাবে সম্পন্ন করুন। অন্যথায় আপনার ভুলের জন্য কর্তৃপক্ষ দায়ী থাকবে না।
                 </div><br/>  
                 <h3 style="color:green;padding-left:30px;text-decoration:underline;">টাকা পরিশোধের নিয়মাবলীঃ</h3>

        <ul class="instruction" style="font-size:16px;">
          <ol>
            <li>যে সকল মোবাইলে DBBL মোবাইল এ্যাকাউন্ট রয়েছে, সেই মোবাইল থেকে <b>*322#</b> ডায়াল করে DBBL মোবাইল ব্যাংক মেনুতে যান
          </li>
          <li>
        পেমেন্ট <b>1</b> (One) সিলেক্ট করুন
          </li>
         
          <li>
              Rajshahi Govt. City College Biller ID <b>2363</b> টাইপ করুন
          </li> 
          
          <li>
            Bill No-এ আপনার Registration No  <strong><?php echo $student_id;?></strong> দিন
          </li>       
          <li>
            টাকার পরিমাণ <b><?php echo round($payment_amount);?></b> লিখুন 
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
            উপরের ফর্মে DBBL প্রাপ্ত <b>TrxID</b> দিয়ে <b>Submit</b> বাটনে ক্লিক করুন
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
  
     <script src="<?php  echo url('/') ?>/fjs/jquery.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/bootstrap.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/scripts.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/ff.js"></script>
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
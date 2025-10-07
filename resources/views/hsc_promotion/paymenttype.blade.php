<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Online Form Fillup</title>
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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>Shah Makhdum College, Rajshahi</span> <span style='color:   white;'>Online Form Fillup</span></p>
			<a href="<?php echo url('/').'/Hsc/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/Hsc/formfillup">Form Fillup</a></li>
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
 
        </div>
        <div class="panel-body ">
         
        <div class="alert" id="con_mes" style="text-align:center;margin:0 5% 0 5%;font-size:17px;">                 
                 </div><br/>

        <div class="alert alert-info" id="input_div" style="text-align:center;margin:0 5% 0 5%;font-size:17px;">
        <h3 style="color:green">Select Payment Type </h3>
		
			<select  id="payType" required="required" name="payType" class="form-control preferenceSelect">
			  <option value="">Please select a payment type </option>
			  <?php
				$results = DB::select("select * from payslipheaders where pro_group='hsc' and type='formfillup' and level='$current_level' and  exam_year = '$examyear' and (group_dept='$group' or group_dept='0')");
				foreach($results as $paySlip){ 
				$amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
				$total_amount = 0;
				foreach($amounts as $amount){
					$total_amount = $total_amount + $amount->fees;
				}
				?>
				   <option value="{{$paySlip->id}}">{{$paySlip->title}}<?php echo ' '.'('.' '.$total_amount .' '. 'Taka )'; ?></option>
			   <?php  } ?>
			  </select>
			   <!--h3 style="color:green">Enter Your Registration Number </h3-->
			 <input type='hidden' class="form-control" id="regNumber" name="regNumber" value='123' ></input>
       <input type='hidden' class="form-control" disabled="yes" style='width:140px; display: inline-block;'  value='<?php echo $student_id;?>' id='studentID' name='studentID'/><br/>

 

        <!--<input type='submit' class='btn btn-info' value='Next' id='submit_payment_type'/>-->
      
        </div> 
        <br>


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



    <!--scripts loaded here-->
  
     <script src="<?php  echo url('/') ?>/fjs/jquery.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/bootstrap.min.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/scripts.js"></script>
     <script src="<?php  echo url('/') ?>/fjs/hscff.js"></script>
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
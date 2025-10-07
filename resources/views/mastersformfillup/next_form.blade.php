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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>Shahid A.H.M. Kamaruzzaman Govt. Degree College</span> <span style='color:   white;'>Online Form Fillup</span></p>
			<a href="<?php echo url('/').'/Degree/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">
                <li></li>
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo Url('/')?>/Degree/formfillup">Form Fillup</a></li>
            </ul>
        </div>
    </div>
</nav>

<?php
          $registration_id = Session::get('registration_id');
          $name = Session::get('name');
          $session = Session::get('session');
          $student_type =  Session::get('student_type'); 
?>

<section class="container-fluid two" id="section2">
    <div class="container">

        <div class="row">
               <div class="col-sm-12 text-center">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="panel panel-primary slideInUp animate">
                    <center><h4>Carefully Select</u></h4></center>
                    <form class="boxCont" method="POST" style="margin:0 auto;">
                    <input type="hidden" name="registration_id" id="registration_id" value="<?php echo $registration_id ?>">
                    <input type="hidden" name="name" id="name" value="<?php echo $name ?>">
                    <input type="hidden" name="session" id="session"  value="<?php echo $session ?>">
                    <input type="hidden" name="student_type" id="student_type"  value="<?php echo $student_type ?>">


                    <?php   if($student_type!='IMPROVEMENT')  {?>                        
                        
                            <div class="form-group">
                                  <label for="sel1" class="control-label col-sm-4"> Practical  Subject</label>&nbsp;
                                  
                                  <select class="prc" id="prc" name="prc" required>
                                    
                                    <option value="">Select Subject</option>      
                                    <option value="One">One Practical Subject</option>
                                    <option value="Two">Two Practical Subject</option>
                                    <option value="No">No</option>
                                  
                                    
                                  </select>                               
                            </div>
                            <?php  } ?>                 
                     
                        <?php   if($student_type=='IMPROVEMENT')  {?>                        
                        
                            <div class="form-group">
                                  <label for="sel1" class="control-label col-sm-4"> Improvement Subject</label>&nbsp;
                                  
                                  <select class="imp" id="imp" name="imp" required>
                                          
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    
                                  </select>                               
                            </div>
                            <?php  } ?>
                            <div class="pull-right" style="margin: 20px 0 0 auto;">
                            <input type="button" id="sub_step" class="btn btn-info btn-large" value="Next" />
                            </div>
                        
                        
                      
                    </form>
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
     <script src="<?php  echo url('/') ?>/fjs/degreeff.js"></script>


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

<script type="text/javascript">

$('document').ready(function(){
                
 $("#sub_step").click(function(){	
		var student_type =$('#student_type').val();


	   if(student_type!='IMPROVEMENT')
	   {
		    var prc =$('#prc').val();
			if(prc==''){
				alert('Please select first');
				return false;
			}		   
		   var imp=0;
	   }
	   else
	   {
		   var prc ='No';
		   var imp=$('#imp').val();
	   }
	   
	   
       $.ajax({	
        type:'POST',
        url:'subcheck',
        data:{imp:imp, prc:prc},
        success:function(response){
        	//alert(response);
        	 var status= response;
			 
			if(status){
					var url ='view';
					window.location=url;
			}
        }
    });
 });

		  
 });		  
</script>
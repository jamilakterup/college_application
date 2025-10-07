<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Online Masters Part-1 Admission</title>
    <meta name="description" content="This one page example has a fixed navbar and full page height sections. Each section is vertically centered on larger screens, and then stack responsively on smaller screens. Scrollspy is used to activate the current menu item. This layout also has a contact form example. Uses animate.css, FontAwesome, Google Fonts (Lato and Bitter) and Bootstrap." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">

    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/animate.min.css') }}">
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
			
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">

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
        <h3 style="color:green">Admission Information </h3>
        <?php 
            $admission_roll = Session::get('admission_roll'); 
            $faculty = Session::get('faculty'); 
            $subject = Session::get('subject'); 
            $name = Session::get('name');
        ?>
         <div class="form-group">   
              <table class="table table-striped table-bordered table-condensed">
                <tr>
                  <td>Admission Roll:</td>
                  <td><?php echo $admission_roll;?></td>
                </tr>
                <tr>
                  <td>Name:</td>
                  <td><?php echo $name;?></td>
                </tr>
                <tr>
                  <td>Faculty:</td>
                  <td><?php echo $faculty;?></td>
                </tr>
                <tr>
                  <td>Subject:</td>
                  <td><?php echo $subject;?></td>
                </tr>
                 <tr>
                    <td>Study Level:</td>
                    <td>Masters Part-1</td>
                 </tr>


              </table>
        </div>

     <div style="font-size:15px; text-align:center;color:green;">
        উপরে বর্ণিত তথ্যসমূহ সঠিক থাকলে Next এ ক্লিক করুন।        
      </div>

        <input type='submit' class='btn btn-info' value='Next' id='showform'/>
      
        </div> 
        <br>


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
<script src="{{ asset('fjs/masters_1st_admission.js') }}"></script>


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
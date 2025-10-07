<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Honours Online Form Fillup</title>
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
            <p class="navbar-brand" ><span style='color:   #cf5713;'>{{config('settings.college_name')}}</span> <span style='color:   white;'>Honours Online Form Fillup</span></p>
      <a href="<?php echo url('/').'/Honours/formfillup/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar-collapsible">
            <ul class="nav navbar-nav navbar-left">                
                
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/Honours/formfillup">Honours Form Fillup</a></li>
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
        <h3 style="color:green">After select a payment type click next button </h3>
         <div class="form-group">   
        <input type="text" id="student_id" name ="student_id" class="form-control" readonly value="<?php echo $student_id; ?>">
        </div>

         <div class="form-group">
            @php
                $current_level = Session::get('current_level');

                $config = Configurations()::where('details->session', $session)->where('details->current_level', $current_level)->where('details->type', 'formfillup')->get();

                if(count($config) < 1){
                    dd('Configurations not set, Please Contact to Admin!');
                }

                $config_details = json_decode($config->first()->details);

                $general_min_sub = $config_details->general->min_length;

                $student = DB::table('student_info_hons_formfillup')->where('id', $student_id)->where('current_level', $current_level)->first();
                
                $query = DB::table('payslipheaders')->where('pro_group', 'honours')->where('level', $current_level)->where('type', 'formfillup')->where('exam_year', $examyear)->where(function($q) use ($faculty_name, $subject){

                    // if($faculty_name != ''){
                    //     $q->where('group_dept','LIKE','%' . $faculty_name .'%');
                    //     $q->orWhere('group_dept', '0');
                    // }

                    if($subject !=''){
                        $q->where('subject','LIKE','%' . $subject .'%');
                        $q->orWhere('subject','0');
                    }
                });

                $headers = $query->get();

            @endphp
  
          <select  id="payType" required="required" name="payType" class="form-control preferenceSelect">
            <option value="">Please select a payment type </option>

            @foreach($headers as $paySlip)
                @php
                    $total_amount = 0;
                    $amounts = DB::select("select * from payslipgenerators where payslipheader_id = $paySlip->id");
                    foreach($amounts as $amount){
                      $total_amount = $total_amount + $amount->fees;
                    }
                    if($paySlip->formfillup_type != 'others'){

                        // for honours 2nd/3rd/4th student
                        if($student->total_papers > $general_min_sub && $paySlip->total_papers != '0'){
                            continue;
                        }

                        if($current_level !='Honours 1st Year'){

                            if($student->total_papers <= $general_min_sub && $paySlip->total_papers == '0'){
                                continue;
                            }
                            if(
                                $student->registration_type == 'regular' &&
                                !in_array('regular' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                                $paySlip->total_papers == '0'
                            ){
                                continue;
                            }

                            if(
                                $student->registration_type == 'irregular' &&
                                !in_array('irregular' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                                $paySlip->total_papers == '0'
                            ){
                                continue;
                            }
                        }

                        // for honours 1st year

                        if($current_level == 'Honours 1st Year'){
                            if(
                                ($student->registration_type == 'improvement' &&
                                !in_array('improvement' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                                $paySlip->total_papers == '0') &&
                                ($student->total_papers > $general_min_sub && $paySlip->total_papers != '0')
                            ){
                                continue;
                            }

                            if(
                                ($student->registration_type == 'irregular' &&
                                !in_array('irregular' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                                $paySlip->total_papers != '0') ||

                                ($student->registration_type == 'regular' &&
                                !in_array('regular' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                                $paySlip->total_papers != '0')
                            ){
                                continue;
                            }
                        }

                        if(
                            $student->registration_type == 'private' &&
                            !in_array('private' , filter_empty_array(explode('_',$paySlip->formfillup_type))) && 
                            $paySlip->total_papers == '0'
                        ){
                            continue;
                        }

                        
                    }

                @endphp

               <option value="{{$paySlip->id}}">{{$paySlip->title}}<?php echo ' '.'('.' '.$total_amount .' '. 'Taka )'; ?></option>
            @endforeach
        </select>
        </div>

        <input type='submit' class='btn btn-info' value='Next' id='submit_payment_type'/>
      
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
     <script src="<?php  echo url('/') ?>/fjs/honoursff.js"></script>

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSC Admission Form</title>
    <style>
        .span_1_of_4{width:23.8%}
        .col{display:block;float:left;}

        * {
            margin : 0;
            padding: 0;
            box-sizing: border-box;
        }

        p{
            font-size: 13px;
        }
        .form {
            width: 90%;
            margin: 0 auto;
            background-color: #999;
            padding: 5px;
        }
        .logo{
            float: left;
            width: 13%;
        }

        .logo img {
            width: 90px;
            margin-top: -20px;
        }

        .header-content {
            float: left;
            width: 87%;
            margin-top: 10px;
            position: relative;
        }

        .header-content h2{
            font-size: 18px;
        }

        .header-content .left{
            line-height: 7px;
        }

        .clearfix {zoom: 1}
        .clearfix:after {
            content: '.';
            clear: both;
            display: block;
            height: 0;
            visibility: hidden;        
        }

        .clear {clear:both;}
        .header-content .left {
            /* background: red; */
            text-align: center;
            width: 60%;
            margin-left: 40px; 
            float: left;
            line-height: 2px;
        }
        .header-content .right {
            width: 190px;
            height: 40px;
            border-radius: 10px;
            padding: 0 5px;
            float: left;
            margin-left: -8px;
            border: 1px solid #ddd;
            text-align: center;
            line-height: 4px;
            font-weight: bold;
        }

        .personal-info {
            width: 100%;
            /* background : firebrick; */
            margin-top: 10px;
        }

        .personal-info .left{
            float: left;
            width: 50%;
        }
        .left-info tr td, .right-info tr td{
            padding: 2px 5px;
            font-size: 12px;
        }

        .personal-info .right{
            float: right;
            width: 50%;
        }

        .profile-img {
            height: 120px;
            width: 120px;
            margin-left: 11.5em;
            border: 1px solid #ddd;
            text-align: center;
            padding: 5px;
            /*border-radius: 10px;*/
        }
        .ssc-info {

        }

        .ssc-info table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .ssc-info table td {
            border: 1px solid black;
            padding: 3px 5px;
            font-size: 12px;
        }
        .hsc-info {
            
        }
        .hsc-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .hsc-info table td {
            border: 1px solid black;
            padding: 3px 5px;
            font-size: 12px;
        }

        .hsc-info table td.label {
            width: 15em;
            font-weight: bold;
        }

        section.commitment {
            margin-top: 5px;
/*            line-height: 15px;*/
        }

        .commitment h4{
            margin-top: 2px;
            margin-bottom: 0px;
            font-size: 13px;
            text-align: center;
            text-decoration: underline;
        }
        .commitment p{
            margin-top: 0px;
        }

        .signature-section {
            width: 100%;
        }

        .signature-section h4{
            margin-top: 0px;
            font-size: 13px;
        }


        .signature {
            text-align: center;
            width: 100%;
            margin-top: 35px;
        }

        .signature p {
            font-size: 13px;
        }

        .principal-sig {
            line-height: 15px;
        }

        .principal-sig span{
            font-size: 13px;
        }

        .company-section {
            /*text-align: center;*/
            margin: 0 auto;
            width: 50%;
        }
        .company-section p{
            float: left;
            margin-left: 60px;
            font-size: 12px;
        }
        .company-section .company-img {
            float: left;
            margin-top: -28px; 
            margin-left: 205px; 
            width: 40px;
        }
    
    </style>
</head>
<body>
    <header class="clearfix">
        <div class="logo">
            <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="logo" class="logo-img" style="width: 200px; margin-top: -15px;">
        </div>

        <div class="header-content clearfix">
            <div class="left">
                <p style="font-weight: bold;font-size: 15px;">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</p>
                <p>Web Address: {{config('settings.college_web_address')}}</p>
                <p>Email : {{config('settings.college_email_address')}}</p>
                <h4 class="code">EIIN No. {{config('settings.college_eiin')}}</h4>
                <h4>HSC Admission Form</h4>
                <h4>Session : {{$student->session}}</h4>
            </div>
            <div class="right">
                <p>{{$student->groups}}</p>
                <p>Class Roll: {{$student->class_roll}}</p>
            </div>
        </div>
    </header>

    <section class="personal-info clearfix" style="margin-top: -15px;">
        <div class="left">
            <table class="left-info">
                <tr>
                    <td style="font-weight: bold;">Student's Name</td>
                    <td style="font-weight: bold;">: {{$student->name}}</td>
                </tr>
                <tr>
                    <td>Student Name (In Bengali)</td>
                    <td style="font-family: 'FreeSerif',sans-serif;font-size: 15px;">: {{$admitted_student->bangla_name}}</td>
                </tr>
                <tr>
                    <td>Father’s Name</td>
                    <td>: {{$student->father_name}}</td>
                </tr>
                <tr>
                    <td>Mother's Name: </td>
                    <td>: {{$student->mother_name}}</td>
                </tr>

                <tr>
                    <td>Guardian's Name </td>
                    <td>: {{$student->guardian}}</td>
                </tr>

                <tr>
                    <td>Guardian's Contact No</td>
                    <td>: {{$admitted_student->guardian_phone}}</td>
                </tr>
                
                <tr>
                    <td>Gender</td>
                    <td>: {{$student->gender}}</td>
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td>: {{date('d-M-Y',strtotime($student->birth_date))}}</td>
                </tr>

                <tr>
                    <td>Birth Registration No</td>
                    <td>: {{$admitted_student->birth_reg_no}}</td>
                </tr>

                <tr>
                    <td>Blood Group</td>
                    <td>: {{$admitted_student->blood_group}}</td>
                </tr>

                <tr>
                    <td>Student Contact No</td>
                    <td>: {{$student->contact_no}}</td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Permanent Address</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Village/Mahalla</td>
                    <td>: {{$student->permanent_village}}</td>
                </tr>
                <tr>
                    <td>Post Office</td>
                    <td>: {{$student->permanent_po}}</td>
                </tr>
                <tr>
                    <td>Upazilla/Thana </td>
                    <td>: {{$student->permanent_ps}}</td>
                </tr>
                <tr>
                    <td>District</td>
                    <td>: {{$student->permanent_dist}}</td>
                </tr>
            </table>
        </div>
        
        <div class="right">
            <div class="profile-img">
                <img style="margin-top: 9px" class="user_pic_view" height="130px" width="130px" src="<?php echo url('/').'/upload/college/hsc/'.$student->session.'/'. $student->image;?>" alt="User Photo" />
            </div>
            <div class="content">
                <table class="right-info">
                    <tr>
                        <td>Hostel Facility</td>
                        <td>: {{$admitted_student->college_hostle}}</td>
                    </tr>

                    <tr>
                        <td>Quota</td>
                        <td>: {{$admitted_student->quota}}</td>
                    </tr>

                    <tr>
                        <td>SSC Roll</td>
                        <td>: {{$student->ssc_roll}}</td>
                    </tr>
                    <tr>
                        <td>SSC Total Mark</td>
                        <td>: {{$admitted_student->ssc_total_mark}}</td>
                    </tr>

                    <tr>
                        <td>Religion</td>
                        <td>: {{$student->religion}}</td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold;">Present Address</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Village/Mahalla</td>
                        <td>: {{$student->present_village}}</td>
                    </tr>
                    <tr>
                        <td>Post Office</td>
                        <td>: {{$student->present_po}}</td>
                    </tr>
                    <tr>
                        <td>Upazilla/Thana </td>
                        <td>: {{$student->present_ps}}</td>
                    </tr>
                    <tr>
                        <td>District</td>
                        <td>: {{$student->present_dist}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <section class="ssc-info">
        <h5  style="margin-bottom: 2px; margin-top: 8px;">SSC/ Equivalent Information: </h5>
        <table>
            <tr>
                <td>Exam Name </td>
                <td>Roll No. </td>
                <td>Reg No. </td>
                <td>Group </td>
                <td>Session </td>
                <td>Passing Year</td>
                <td>GPA</td>
                <td>Board</td>
            </tr>
            <tr>
                <td>{{strtoupper($admitted_student->exam_name)}}</td>
                <td>{{$admitted_student->ssc_roll}}</td>
                <td>{{$admitted_student->ssc_reg_no}}</td>
                <td>{{$admitted_student->ssc_group}}</td>
                <td>{{$admitted_student->ssc_session}}</td>
                <td>{{$admitted_student->ssc_passing_year}}</td>
                <td>{{$admitted_student->ssc_gpa}}</td>
                <td>{{$admitted_student->ssc_board}}</td>
            </tr>
        </table>
        <p style="margin-top: 2px;">SSC/Equivalent Level Institute: <strong>{{$admitted_student->ssc_institution}}</strong></p>
    </section>

    <section class="hsc-info">
        @php
            $results = DB::table('course_hsc_new')->where('codes', $admitted_student->optional)->get();
            foreach($results as $result){

               $sel_sub_op=$result->subjects;
            }

             $selective= explode(",",$admitted_student->selective);
             $compulsory= explode(",",$admitted_student->compulsory);

             $results = DB::table('course_hsc_new')->where('codes', $compulsory[0])->get();
            foreach($results as $result){

               $compulsory1=$result->subjects;
            }

            $results = DB::table('course_hsc_new')->where('codes', $compulsory[1])->get();
            foreach($results as $result){

               $compulsory2=$result->subjects;
            }

            $results = DB::table('course_hsc_new')->where('codes', $compulsory[2])->get();
            foreach($results as $result){

               $compulsory3=$result->subjects;
            }
                

            $results = DB::table('course_hsc_new')->where('codes', $selective[0])->get();
            foreach($results as $result){

               $sel_sub1=$result->subjects;
            }

             

            $results =  DB::table('course_hsc_new')->where('codes', $selective[1])->get();
            foreach($results as $result){

               $sel_sub2=$result->subjects;
            }

                 

            $results = DB::table('course_hsc_new')->where('codes', $selective[2])->get();
            foreach($results as $result){

               $sel_sub3=$result->subjects;
            }
               $se_sub1=$selective[0];
               $se_sub2=$selective[1];
               $se_sub3=$selective[2];

               $sel_com1 = '1. '.$compulsory1.' '.'('.$compulsory[0].')';
               $sel_com2 = '2. '.$compulsory2.' '.'('.$compulsory[1].')';
               $sel_com3 = '3. '.$compulsory3.' '.'('.$compulsory[2].')';

               $sel_sub1='4. '.$sel_sub1.' '.'('.$se_sub1.')';
               $sel_sub2='5. '.$sel_sub2.' '.'('.$se_sub2.')';
               $sel_sub3='6. '.$sel_sub3.' '.'('.$se_sub3.')';
               $sel_sub_op='7. '.$sel_sub_op.' '.'('.$admitted_student->optional.')';
        @endphp
        <h5 style="margin-top: -5px; margin-bottom: 2px;">HSC Subject Information: </h5>
        <table>
            <tr>
                <td class="label">Compulsory Subject</td>
                <td style="font-weight: bold;">
                    {{$sel_com1}}<br/>
                    {{$sel_com2}}<br/>
                    {{$sel_com3}}
                </td>
            </tr>
            <tr>
                <td  class="label">Selective Subject </td>
                <td>
                    {{$sel_sub1}}<br/>
                    {{$sel_sub2}}<br/>
                    {{$sel_sub3}}
                </td>
            </tr>

            <tr>
                <td class="label">Fourth Subject </td>
                <td>
                    {{$sel_sub_op}}
                </td>
            </tr>
        </table>
    </section>

    <section class="commitment">
        <h4 style="margin-top: 2px;">Student's Commitment</h4>
        <p>I hereby declare that the information mentioned above is true and I also promise to abide by all the internal rules of the college as well as govt. law order. </p>
    </section>

    <section class="signature-section">
        <h4>Signature Line:</h4>

        <div class="signature clearfix">
            <div class="col span_1_of_4">
                <p>Student</p>
            </div>

            <div class="col span_1_of_4">
                <p>Guardian</p>
            </div>

            <div class="col span_1_of_4">
                <p>Local  Guardian</p>
            </div>

            <div class="col span_1_of_4 principal-sig">
                <p style="margin-bottom: 0px;">Principal</p>
                <span style="margin-top: 0px;">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</span>
            </div>
        </div>

    </section>

    <section class="footer clearfix">
        <p style="color: red; text-align: center; font-size: 18px;">বি দ্রঃ অঙ্গীকারনামা প্রিন্ট কপি কলেজে জমা দিতে হবে ।</p>
    </section>


</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masters Admission Form</title>
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
            font-size: 17px;
        }

        .header-content .left{
            line-height: 8px;
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
            padding: 3px 5px;
            font-size: 13px;
        }

        .personal-info .right{
            float: right;
            width: 50%;
        }

        .profile-img {
            height: 150px;
            width: 150px;
            margin-left: 11.5em;
            border: 1px solid #ddd;
            text-align: center;
            border-radius: 10px;
        }

        .acamdemic-info {

        }

        .academic-info table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .academic-info table td {
            border: 1px solid black;
            padding: 5px 5px;
            font-size: 14px;
        }

        .academic-label td{
            font-weight: bold;
        }

        section.commitment {
            margin-top: 10px;
            line-height: 4px;
        }

        .commitment h4{
            text-align: center;
            text-decoration: underline;
        }

        .signature-section {
            width: 100%;
        }

        .signature-section h4{
            margin-top: 12px;
            font-size: 14px;
        }


        .signature {
            text-align: center;
            width: 100%;
            margin-top: 70px;
        }

        .signature p {
            font-size: 14px;
        }

        .principal-sig {
            line-height: 12px;
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
            margin-top: -29px; 
            margin-left: 206px; 
            width: 50px;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }
        .payment-info {
            margin-top: 4px;
        }
        .payment-info td{
            padding:2px;
            border:1px solid black;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="page">
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
                    <h4>Masters Final Admission Form</h4>
                    <h4>Session : {{$student->session}}</h4>
                </div>
                <div class="right">
                    <p>{{$student->dept_name}}</p>
                    <p>Class Roll: {{$student->class_roll}}</p>
                </div>
            </div>
        </header>

        <section class="personal-info clearfix" style="margin-top: -15px;">
            <div class="left">
                <table class="left-info">

                    <tr>
                        <td style="font-weight: bold;">Personal Information:</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Student name:</td>
                        <td>{{$student->name}}</td>
                    </tr>
                    <tr>
                        <td>Father’s Name:</td>
                        <td>{{$student->father_name}}</td>
                    </tr>
                    <tr>
                        <td>Mother's name: </td>
                        <td>{{$student->mother_name}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's name: </td>
                        <td>{{$student->guardian}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's Contact: </td>
                        <td>{{$admitted_student->guardian_contact}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's Relation: </td>
                        <td>{{$admitted_student->guardian_relation}}</td>
                    </tr>

                    <tr>
                        <td>Merit Position:</td>
                        <td>{{$student->merit_pos}}</td>
                    </tr>
                    <tr>
                        <td>Gender:</td>
                        <td>{{$student->gender}}</td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td>{{$student->birth_date}}</td>
                    </tr>

                    <tr>
                        <td>Blood Group:</td>
                        <td>{{$student->blood_group}}</td>
                    </tr>

                    <tr>
                        <td>Student Contact No:</td>
                        <td>{{$student->contact_no}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Permanent Address:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Village/Mahalla:</td>
                        <td>{{$student->permanent_village}}</td>
                    </tr>
                    <tr>
                        <td>Post Office:</td>
                        <td>{{$student->permanent_po}}</td>
                    </tr>
                    <tr>
                        <td>Upazilla/Thana: </td>
                        <td>{{$student->permanent_ps}}</td>
                    </tr>
                    <tr>
                        <td>District:</td>
                        <td>{{$student->permanent_dist}}</td>
                    </tr>
                </table>
            </div>
            
            <div class="right">
                <div class="profile-img">
                    <img style="margin-top: 9px" class="user_pic_view" height="130px" width="130px" src="{{ asset('upload/college/masters/'.$student->session.'/'. $student->image) }}" alt="User Photo" /> 
                </div>
                <div class="content" style="margin-top: 10px;">
                    <table class="right-info">

                        <tr>
                            <td>Religion:</td>
                            <td>{{$student->religion}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Present Address:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Village/Mahalla:</td>
                            <td>{{$student->present_village}}</td>
                        </tr>
                        <tr>
                            <td>Post Office:</td>
                            <td>{{$student->present_po}}</td>
                        </tr>
                        <tr>
                            <td>Upazilla/Thana: </td>
                            <td>{{$student->present_ps}}</td>
                        </tr>
                        <tr>
                            <td>District:</td>
                            <td>{{$student->present_dist}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <section class="academic-info">
            <h4  style="margin-bottom: 2px; margin-top: 11px;">Academic Information: </h4>
                <table class="left-info">

                    <tr>
                        <td width="30%" style="font-weight: bold;">Admission Roll</td>
                        <td>{{$student->admission_roll}}</td>

                        <td style="font-weight: bold;">Admitted Faculty</td>
                        <td>{{$student->faculty_name}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Admitted Subject</td>
                        <td>{{$student->dept_name}}</td>

                        <td style="font-weight: bold;">Masters Session</td>
                        <td>{{$student->session}}</td>
                    </tr>
                </table>
        </section>

        @if($invoice && $invoice->status=='Paid')
        <section class="payment-info">
            <table width="100%" style="margin-top: 2px;border-collapse: collapse;">
                <tr>
                    <td style="font-weight: bold;">Paid Amount</td>
                    <td>{{$invoice->total_amount}}</td>

                    <td style="font-weight: bold;">Payment Date</td>
                    <td>{{date("Y-m-d",strtotime($invoice->update_date))}}</td>
                </tr>
            </table>
        </section>

        @endif

        <section class="commitment">
            <h4 style="margin-top: 12px;">Student Commitment </h4>
            <p>I hereby declare that the information mentioned a rules of the College as well as govt. law and order. </p>
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
                    <p>Head of Department</p>
                </div>

                <div class="col span_1_of_4 principal-sig">
                    <p>Principal</p>
                    <span>{{config('settings.college_name')}}</span>
                </div>
            </div>

        </section>

        <section class="company-section clearfix" style="margin-top: 20px;">
            <p>Developed & maintain by</p>
            <div class="company-img">
            <img src="{{ asset('img/company.png') }}" alt="comapny">
            </div>
        </section>
    </div>


    {{-- 2nd page --}}

    <div class="page">
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
                    <h4>Masters Final Admission Form</h4>
                    <h4>Session : {{$student->session}}</h4>
                </div>
                <div class="right">
                    <p>{{$student->dept_name}}</p>
                    <p>Class Roll: {{$student->class_roll}}</p>
                </div>
            </div>
        </header>

        <section class="personal-info clearfix" style="margin-top: -15px;">
            <div class="left">
                <table class="left-info">

                    <tr>
                        <td style="font-weight: bold;">Personal Information:</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Student name:</td>
                        <td>{{$student->name}}</td>
                    </tr>
                    <tr>
                        <td>Father’s Name:</td>
                        <td>{{$student->father_name}}</td>
                    </tr>
                    <tr>
                        <td>Mother's name: </td>
                        <td>{{$student->mother_name}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's name: </td>
                        <td>{{$student->guardian}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's Contact: </td>
                        <td>{{$admitted_student->guardian_contact}}</td>
                    </tr>

                    <tr>
                        <td>Guardian's Relation: </td>
                        <td>{{$admitted_student->guardian_relation}}</td>
                    </tr>

                    <tr>
                        <td>Merit Position:</td>
                        <td>{{$student->merit_pos}}</td>
                    </tr>
                    <tr>
                        <td>Gender:</td>
                        <td>{{$student->gender}}</td>
                    </tr>
                    <tr>
                        <td>Date of Birth:</td>
                        <td>{{$student->birth_date}}</td>
                    </tr>

                    <tr>
                        <td>Blood Group:</td>
                        <td>{{$student->blood_group}}</td>
                    </tr>

                    <tr>
                        <td>Student Contact No:</td>
                        <td>{{$student->contact_no}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Permanent Address:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Village/Mahalla:</td>
                        <td>{{$student->permanent_village}}</td>
                    </tr>
                    <tr>
                        <td>Post Office:</td>
                        <td>{{$student->permanent_po}}</td>
                    </tr>
                    <tr>
                        <td>Upazilla/Thana: </td>
                        <td>{{$student->permanent_ps}}</td>
                    </tr>
                    <tr>
                        <td>District:</td>
                        <td>{{$student->permanent_dist}}</td>
                    </tr>
                </table>
            </div>
            
            <div class="right">
                <div class="profile-img">
                    <img style="margin-top: 9px" class="user_pic_view" height="130px" width="130px" src="{{ asset('upload/college/masters/'.$student->session.'/'. $student->image) }}" alt="User Photo" /> 
                </div>
                <div class="content" style="margin-top: 10px;">
                    <table class="right-info">

                        <tr>
                            <td>Religion:</td>
                            <td>{{$student->religion}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Present Address:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Village/Mahalla:</td>
                            <td>{{$student->present_village}}</td>
                        </tr>
                        <tr>
                            <td>Post Office:</td>
                            <td>{{$student->present_po}}</td>
                        </tr>
                        <tr>
                            <td>Upazilla/Thana: </td>
                            <td>{{$student->present_ps}}</td>
                        </tr>
                        <tr>
                            <td>District:</td>
                            <td>{{$student->present_dist}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <section class="academic-info">
            <h4  style="margin-bottom: 2px; margin-top: 11px;">Academic Information: </h4>
                <table class="left-info">

                    <tr>
                        <td width="30%" style="font-weight: bold;">Admission Roll</td>
                        <td>{{$student->admission_roll}}</td>

                        <td style="font-weight: bold;">Admitted Faculty</td>
                        <td>{{$student->faculty_name}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Admitted Subject</td>
                        <td>{{$student->dept_name}}</td>

                        <td style="font-weight: bold;">Masters Session</td>
                        <td>{{$student->session}}</td>
                    </tr>
                </table>
        </section>

        @if($invoice && $invoice->status=='Paid')
        <section class="payment-info">
            <table width="100%" style="margin-top: 2px;border-collapse: collapse;">
                <tr>
                    <td style="font-weight: bold;">Paid Amount</td>
                    <td>{{$invoice->total_amount}}</td>

                    <td style="font-weight: bold;">Payment Date</td>
                    <td>{{date("Y-m-d",strtotime($invoice->update_date))}}</td>
                </tr>
            </table>
        </section>

        @endif

        <section class="commitment">
            <h4 style="margin-top: 12px;">Student Commitment </h4>
            <p>I hereby declare that the information mentioned a rules of the College as well as govt. law and order. </p>
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
                    <p>Head of Department</p>
                </div>

                <div class="col span_1_of_4 principal-sig">
                    <p>Principal</p>
                    <span>{{config('settings.college_name')}}</span>
                </div>
            </div>

        </section>

        <section class="company-section clearfix" style="margin-top: 20px;">
            <p>Developed & maintain by</p>
            <div class="company-img">
            <img src="{{ asset('img/company.png') }}" alt="comapny">
            </div>
        </section>
    </div>
</body>
</html>
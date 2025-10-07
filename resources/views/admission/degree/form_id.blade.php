<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Degree Admission Form</title>
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('css/honours_admission_form.css') }}"> --}}
    <style>
        .span_1_of_4{width:23.8%}
        .col{display:block;float:left;}

        * {
            margin : 0;
            padding: 0;
            box-sizing: border-box;
        }

        p{
            font-size: 12px;
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
            font-size: 20px;
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
            /*border-radius: 10px;*/
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
            font-size: 13px;
        }

        .academic-label td{
            font-weight: bold;
        }

        section.commitment {
            margin-top: 10px;
            line-height: 15px;
        }

        .commitment h4{
            text-align: center;
            text-decoration: underline;
        }

        .signature-section {
            width: 100%;
        }

        .signature-section h4{
            margin-top: 15px;
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
                <h4>Degree Admission Form</h4>
                <h4>Session : {{$admitted_student->session}}</h4>
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
                    <td style="font-weight: bold;">Personal Information:</td>
                    <td></td>
                </tr>

                <tr>
                    <td>Student name:</td>
                    <td>{{$admitted_student->name}}</td>
                </tr>
                <tr>
                    <td>Father’s Name:</td>
                    <td>{{$admitted_student->father_name}}</td>
                </tr>
                <tr>
                    <td>Mother's name: </td>
                    <td>{{$admitted_student->mother_name}}</td>
                </tr>

                <tr>
                    <td>Guardian's name: </td>
                    <td>{{$admitted_student->guardian_name}}</td>
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
                    <td>Gender:</td>
                    <td>{{$admitted_student->gender}}</td>
                </tr>
                <tr>
                    <td>Date of Birth:</td>
                    <td>{{date('d-M-Y',strtotime($admitted_student->birth_date))}}</td>
                </tr>

                <tr>
                    <td>Blood Group:</td>
                    <td>{{$admitted_student->blood_group}}</td>
                </tr>

                <tr>
                    <td>Student Contact No:</td>
                    <td>{{$admitted_student->contact_no}}</td>
                </tr>
                
                <tr>
                    <td style="font-weight: bold;">Permanent Address:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Village/Mahalla:</td>
                    <td>{{$admitted_student->permanent_village}}</td>
                </tr>
                <tr>
                    <td>Post Office:</td>
                    <td>{{$admitted_student->permanent_po}}</td>
                </tr>
                <tr>
                    <td>Upazilla/Thana: </td>
                    <td>{{$admitted_student->permanent_ps}}</td>
                </tr>
                <tr>
                    <td>District:</td>
                    <td>{{$admitted_student->permanent_dist}}</td>
                </tr>
            </table>
        </div>
        
        <div class="right">
            <div class="profile-img">
                <img style="margin-top: 9px" class="user_pic_view" height="130px" width="130px" src="{{ asset('upload/college/degree/'.$admitted_student->session.'/'. $admitted_student->photo) }}" alt="User Photo" /> 
            </div>
            <div class="content" style="margin-top: 10px;">
                <table class="right-info">
                    <tr>
                        <td>Religion:</td>
                        <td>{{$admitted_student->religion}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Present Address:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Village/Mahalla:</td>
                        <td>{{$admitted_student->present_village}}</td>
                    </tr>
                    <tr>
                        <td>Post Office:</td>
                        <td>{{$admitted_student->present_po}}</td>
                    </tr>
                    <tr>
                        <td>Upazilla/Thana: </td>
                        <td>{{$admitted_student->present_ps}}</td>
                    </tr>
                    <tr>
                        <td>District:</td>
                        <td>{{$admitted_student->present_dist}}</td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold;">Admission Details:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Admission Roll:</td>
                        <td>{{$admitted_student->admission_roll}}</td>
                    </tr>
                    <tr>
                        <td>Selected Subjects:</td>
                        <td>{{$admitted_student->deg_subjects}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <section class="academic-info">
        <h4  style="margin-bottom: 2px; margin-top: 11px;">Academic Information: </h4>
        <table>
            <tr class="acamdemic-label" style="font-weight: bold;">
                <td>Exam Name </td>
                <td>Institution </td>
                <td>Board </td>
                <td>Reg No.</td>
                <td>Session</td>
                <td>Passing Year</td>
                <td>GPA</td>
            </tr>

            <tr>
                <td>SSC</td>
                <td>{{$admitted_student->ssc_institute}}</td>
                <td>{{$admitted_student->ssc_board}}</td>
                <td>{{$admitted_student->ssc_roll}}</td>
                <td>{{$admitted_student->ssc_reg}}</td>
                <td>{{$admitted_student->ssc_pass_year}}</td>
                <td>{{$admitted_student->ssc_gpa}}</td>
            </tr>

            <tr>
                <td>HSC</td>
                <td>{{$admitted_student->hsc_institute}}</td>
                <td>{{$admitted_student->hsc_board}}</td>
                <td>{{$admitted_student->hsc_roll}}</td>
                <td>{{$admitted_student->hsc_reg}}</td>
                <td>{{$admitted_student->hsc_pass_year}}</td>
                <td>{{$admitted_student->hsc_gpa}}</td>
            </tr>
            
        </table>
    </section>

    <section class="commitment">
        <h4 style="margin-top: 12px;">Student Commitment </h4>
        <p> I do hereby declare that the information mentioned is correct and I promise to abide by the rules of the College as well as government law and order.</p>
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


</body>
</html>
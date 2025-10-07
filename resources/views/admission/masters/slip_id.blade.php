<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <title>Masters Admission Confirmation Slip</title>

    <style>
        td , p{
            font-size: 17px;
        }
        * {
            margin : 0;
            padding: 0;
        }
        .slip {
            margin: 0 auto;
            padding: 10px 35px;
        }
        .logo{
            width: 110px;
            width: 110px;
            float: left;
        }

        .header-content {
            float: left;
            text-align: center;
            width: 68%;
            margin-top: 10px;
            line-height: 5px;
        }
        .code {
            text-align: center;
        }

        .clearfix {zoom: 1}
        .clearfix:after {
            content: '.';
            clear: both;
            display: block;
            height: 0;
            visibility: hidden;        
        }

        .main-content {
        }

        .main-content h2{
            text-align: center;
        }

        table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 98%;
            margin: 0 auto;
            margin-top: 8px;

        }

        table td{
            background: #C5D9F0;
            padding: 12px 20px;
            border: 1px solid black;
        }

        table tr td:first-child {
            width: 5em;
            min-width: 8em;
            max-width: 8em;
            word-break: break-all;
            font-weight: bold;
        }

        table tr td:last-child {
            padding-left: 5px;
        }

        td.info-label {
            /*width: 30%;*/
            width: 12em;
            min-width: 8em;
            max-width: 8em;
            word-break: break-all;
            font-weight: bold;
        }

        .info{
            padding-left: 20px;
        }

        section.footer {
            margin-top: 15px;
            line-height: 10px;
        }

        section.footer h3{
            float: left;
        }

        section.footer h3.company {
            float: right;
            margin-left: 430px;
        }
    </style>
</head>
<body>
    <div class="slip">
        <header class="clearfix">
            <div class="logo">
                <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="logo" class="logo">
            </div>
    
            <div class="header-content">
                <h2>{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</h2>
                <p>Web Address: {{config('settings.college_web_address')}}</p>
                <p style="margin-bottom: 25px;">Email : {{config('settings.college_email_address')}}</p>
                <h3 class="code">EIIN No. {{config('settings.college_eiin')}}</h3>
            </div>
        </header>
        <section class="main-content">
            <h2 style="margin: 2px 0;">Masters Final Admission Confirmation Slip</h2>
            <table>
                <tr>
                    <td class="info-label">Name</td>
                    <td>{{$student->name}}</td>
                </tr>

                <tr>
                    <td class="info-label">Student ID</td>
                    <td>{{$student->id}}</td>
                </tr>

                <tr>
                    <td class="info-label">Class Roll</td>
                    <td>{{$student->class_roll}}</td>
                </tr>


                <tr>
                    <td class="info-label">Current Level</td>
                    <td>{{$student->current_level}}</td>
                </tr>

                <tr>
                    <td class="info-label">Admission Roll</td>
                    <td>{{$student->admission_roll}}</td>
                </tr>
                
                <tr>
                    <td class="info-label">Department</td>
                    <td>{{$student->dept_name}}</td>
                </tr>

                <tr>
                    <td class="info-label">Session</td>
                    <td>{{$student->session}}</td>
                </tr>

                <tr>
                    <td class="info-label">Reference ID</td>
                    <td>{{$tracking_id}}</td>
                </tr>

                <tr>
                    <td class="info-label">Transaction ID</td>
                    <td>{{$invoice->trx_id}}</td>
                </tr>

                <tr>
                    <td class="info-label">Paid Amount</td>
                    <td>{{$invoice->total_amount}}</td>
                </tr>
            </table>
        </section>

        <section class="footer clearfix">
            <h3>Congratulation, Your Payment is successfully completed</h3>
            <h3 class="company">Powered by -raj IT</h3>
        </section>

    </div>

    
</body>
</html>
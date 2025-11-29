<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('upload/sites/' . config('settings.site_favicon')) }}">
    <title>Fees Payment Confirmation Slip</title>

    <style>
        td, p {
            font-size: 17px;
        }
        * {
            margin: 0;
            padding: 0;
        }
        .slip {
            margin: 0 auto;
            padding: 10px 35px;
        }
        .logo {
            width: 110px;
            height: 110px;
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
        .clearfix { zoom: 1 }
        .clearfix:after {
            content: '.';
            clear: both;
            display: block;
            height: 0;
            visibility: hidden;
        }
        .main-content h2 {
            text-align: center;
        }
        .table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 98%;
            margin: 0 auto;
            margin-top: 8px;
        }
        .table td {
            background: #C5D9F0;
            padding: 12px 20px;
            border: 1px solid black;
        }
        .table tr td:first-child {
            width: 12em;
            min-width: 8em;
            max-width: 8em;
            word-break: break-all;
            font-weight: bold;
        }
        .table tr td:last-child {
            padding-left: 5px;
        }
        td.info-label {
            width: 12em;
            min-width: 8em;
            max-width: 8em;
            word-break: break-all;
            font-weight: bold;
        }
        .info {
            padding-left: 20px;
        }
        section.footer {
            margin-top: 15px;
            line-height: 10px;
            width: 98%;
            margin: 15px auto 0;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            background: none;
            border: none;
            padding: 0;
            font-size: 17px;
        }
        .footer-table td.congratulations {
            text-align: left;
        }
        .footer-table td.company {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="slip">
        <table style="border-collapse: collapse;">
            <tr>
                <td width="20%" rowspan="4">
                    <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="logo" class="logo">
                </td>
                <td align="center">
                    <p style="font-weight: bold;font-size: 18px;">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</p>
                </td>
                <td width="10%"></td>
            </tr>
            <tr>
                <td align="center"><p>Web Address: {{config('settings.college_web_address')}}</p></td>
                <td></td>
            </tr>
            <tr>
                <td align="center"><p>Email : {{config('settings.college_email_address')}}</p></td>
                <td></td>
            </tr>
            <tr>
                <td align="center"><h3 class="code">EIIN No. {{config('settings.college_eiin')}}</h3></td>
                <td></td>
            </tr>
        </table>
        <section class="main-content">
            <h2 style="margin: 2px 0;">{{$config->home_page_title}} Confirmation Slip</h2>
            <table class="table">
                <tr>
                    <td class="info-label">Name</td>
                    <td>{{ $feesApplication->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Student ID</td>
                    <td>{{ json_decode($feesApplication->reference_data)->reference_id ?? 'N/A' }}</td>
                </tr>
                @if($feesApplication->registration_id)
                    <tr>
                        <td class="info-label">Registration ID</td>
                        <td>{{ $feesApplication->registration_id ?? 'N/A' }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="info-label">Level</td>
                    <td>{{ $config->level ?? (json_decode($feesApplication->reference_data)->current_level ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Department/Group</td>
                    <td>{{ $feesApplication->group_dept ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Academic Session</td>
                    <td>{{ $config->academic_session ?? (json_decode($feesApplication->reference_data)->academic_session ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Mobile</td>
                    <td>{{ $feesApplication->mobile ?? 'N/A' }}</td>
                </tr>
                @if($feesApplication->invoice)
                    <tr>
                        <td class="info-label">Total Amount Paid</td>
                        <td>{{ $feesApplication->invoice->total_amount ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Payment Date</td>
                        <td>{{ $feesApplication->payment_date ? date('d M Y', strtotime($feesApplication->payment_date)) : ($feesApplication->invoice->created_at ? $feesApplication->invoice->created_at->format('d M Y') : 'N/A') }}</td>
                    </tr>
                @endif
            </table>
        </section>
        <section class="footer">
            <table class="footer-table">
                <tr>
                    <td class="congratulations">
                        <h3>Congratulations, Your Payment is Successfully Completed</h3>
                    </td>
                    <td class="company">
                        <h3>Powered by rajIT Solutions Ltd.</h3>
                    </td>
                </tr>
            </table>
        </section>
    </div>
</body>
</html>
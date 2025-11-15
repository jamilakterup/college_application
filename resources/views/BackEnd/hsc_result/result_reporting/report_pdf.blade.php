<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CGPA Grade Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h2, h3 { margin: 6px 0; }
    </style>
</head>
<body>

 <table style="border: none;">
    <tr style="border: none;">
        <td style="border: none;"><img src="{{ public_path('img/banner.png') }}" alt="" class="skcr-logo"  /></td>
    </tr>
    <tr style="border: none; padding: -20px 0;">
        <td style="text-align:center;padding: -15px 0;"><h3>Airport Road, Shalbagan, Rajshahi-6203,Bangladesh</h3></td>
    </tr>
    <tr style="border: none;">
        <td style="text-align:center;"><h3>Web: <a href="https://www.rebmsc.edu.bd/" target="_blank" style="color:black; text-decoration: none;">www.rebmsc.edu.bd</a> </h3></td>
    </tr>
</table>

<h1 style="text-align: center;">Student Result Analysis</h1>


<table style="border: none;">
    <tr style="border: none;">
        <td style="border: none;">Total Students: <b>{{ $total_students }}</b></td>
        <td style="border: none;">Session: <b>{{ $session }}</b></td>
       <td style="border: none;">
    Group:
    <b>
        {{ $groups == 1 ? 'Science' : ($groups == 2 ? 'Humanities' : ($groups == 3 ? 'Business Studies' : '')) }}
    </b>
</td>

    </tr>
</table>


<table>
    <thead>
        <tr>
            <th style="text-align: center;">Grade</th>
            <th style="text-align: center;">Roll No</th>
            <th style="text-align: center;">Total</th>
            <th style="text-align: center;">Parcentage</th>
        </tr>
    </thead>

    <tbody>
        @foreach($grade_summary as $grade)
        <tr>
            <td>{{ $grade['grade'] }}</td>
            <td>
                @foreach($grade['students'] as $stu)
                    {{ $stu['Student Roll'] }},
                @endforeach
            </td>
            <td>{{ $grade['count'] }}</td>
            <td>{{ $grade['percentage'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>


  <table width="100%" style="border: none; margin-top: 300px;">
        <tr style="text-align: right; border: none;">
            <td width="70%" style="border: none;">&nbsp;</td>
            <td width="30%" style="border: none;">
                <!-- <img src="@php echo url('/');@endphp/img/principal_sign.png" alt="rebmsc logo" height="70" width="150">  -->
                <br/>
            ----------------------------
        </td>
        </tr>
        <tr style="text-align: right; border: none;">
            <td width="70%" style="border: none;">&nbsp;</td>
            <td width="30%" style="border: none;">Principal's Signature</td>
        </tr>
    </table>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>To Whom It May Concern</title>
    <style>
        body {
            font-family: 'SolaimanLipi', 'Arial', sans-serif;
            margin: 40px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 5px;
        }
        .institute {
            font-size: 22px;
            font-weight: bold;
        }
        .address {
            font-size: 15px;
        }
        .title {
            font-size: 26px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0 10px 0;
        }
        .content {
            font-size: 17px;
            text-align: justify;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }
        .principal {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .principal-institute {
            font-size: 15px;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 0;
            width: 400px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo.png') }}" class="logo" alt="Logo">
        <div class="institute">{{ config('app.institute_name', 'Shahid Buddhijibi Government College, Rajshahi') }}</div>
        <div class="address">Rajshahi</div>
    </div>
    <div class="title">To Whom It May Concern</div>
    <div class="content">
        This is to certify that <b>{{ $student->name }}</b>, Father: <b>{{ $student->father_name }}</b>, Mother: <b>{{ $student->mother_name }}</b> is a regular student of class <b>{{ $prottoyon->class_name }}</b>, in <b>{{ $prottoyon->group }}</b> group. His academic year is <b>{{ $prottoyon->academic_year }}</b> and Class Roll is <b>{{ $student->roll_no }}</b>.
        <br>
        I wish him/her every success.
    </div>
    <img src="{{ public_path('img/watermark.png') }}" class="watermark" alt="Watermark">
    <div class="footer">
        <div class="principal">
            Principal<br>
            <span class="principal-institute">{{ config('app.institute_name', 'Shahid Buddhijibi Government College, Rajshahi') }}</span>
        </div>
    </div>
</body>
</html>

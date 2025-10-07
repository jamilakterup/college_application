<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Testimonial Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            background-image: url('{{ public_path('img/testimonial_bg.jpeg') }}');
            background-image-resize: 6;
            background-position: center;
            background-repeat: no-repeat;
            background-image-opacity: 1;
        }
        body {
            font-family: 'times', serif;
            margin: 0;
            padding: 0;
            background-color: transparent;
            position: relative;
        }
        .certificate {
            width: 100%;
            position: relative;
            padding: 30px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-top: 20px;
        }
        .govt-title {
            color: #4b0082;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .college-name {
            color: #008000;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .location {
            color: #008000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .document-title {
            color: #b22222;
            font-size: 35px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .certificate-number {
            position: absolute;
            top: 100px;
            left: 80px;
            font-weight: bold;
        }
        .content {
            fontfamily: 'garamond', serif;
            padding: 0 40px;
            line-height: 1.8;
            font-size: 18px;
        }
        .signature-table {
            width: 100%;
            margin-top: 80px;
            padding: 0 40px;
        }
        .field-value{
            font-weight: bold;
            font-size: 17px;
        }
        .signature-left, .signature-right {
            text-align: center;
            width: 40%;
            color: #000;
            vertical-align: bottom;
        }
        .signature-center {
            width: 20%;
        }
        .signature-center {
            width: 30%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-img {
            max-width: 150px;
            max-height: 60px;
            margin: 0 auto;
        }
        .signature-title {
            margin-top: 5px;
            font-weight: bold;
        }
        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: white;
        }
        .qr-label {
            font-size: 12px;
            margin-top: 5px;
            color: #555;
            text-align: center;
        }
        .date {
            position: absolute;
            bottom: 100px;
            left: 50px;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-number">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $documentId ?? '900' }}</div>
        
        <div class="header">
            <div class="govt-title">GOVT. OF THE PEOPLE'S REPUBLIC OF BANGLADESH</div>
            <div class="college-name">NAOGAON GOVT. COLLEGE</div>
            <div class="location">NAOGAON</div>
            <div class="document-title">TESTIMONIAL</div>
        </div>
        
        <div class="content">
            <p>
                This is to certify that <span class="field-value">{{ $student->name }}</span> S/O, D/O <span class="field-value">{{ $student->fathers_name ?? $student->parent_name ?? 'N/A' }}</span> & <span class="field-value">{{ $student->mothers_name ?? $student->address ?? 'N/A' }}</span> Passed the B.A / B.S.S / B.Sc / B.B.A (Honours) Examination in <span class="field-value">{{ $student->subject ?? 'N/A' }}</span> in the year <span class="field-value">{{ $student->passing_year ?? date('Y') }}</span>  bearing Roll No.  <span class="field-value">{{ $student->roll ?? $student->roll_number ?? $student->class_roll ?? 'N/A' }}</span> Registration No. <span class="field-value">{{ $student->registration_no ?? $student->registration_number ?? 'N/A' }}</span> in the session <span class="field-value">{{ $student->session ?? 'N/A' }} </span> He/She obtained <span class="field-value">{{ $student->result ?? $student->cgpa ?? 'N/A' }}</span> Class under National University. 
            </p>
            <p>
                He/she bears a good moral character. To the best of my knowledge he/she did not take part in any activities subversive of the state or of discipline during his/her stay here.
            </p>
            
            <p>
                I wish him / her all success in life.
            </p>
        </div>
        
        <!-- Using table instead of flex for better mPDF compatibility -->
        <table class="signature-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="signature-left">
                    @if(isset($academic_officer_signature) && file_exists($academic_officer_signature))
                        <img src="{{ $academic_officer_signature }}" class="signature-img" alt="Academic Officer Signature">
                    @endif
                    <div class="field-value">Verified</div>
                    <div class="signature-title">Academic Officer</div>
                </td>
                <td class="signature-center">
                    <div class="qr-code">
                        <img src="{{ $qrCodePath }}" width="100" height="100" alt="Certificate QR Code">
                    </div>
                    <div class="scan-text">Scan to verify</div>
                </td>
                <td class="signature-right">
                    @if(isset($signature) && file_exists($signature))
                        <img src="{{ $signature }}" class="signature-img" alt="Principal Signature">
                    @endif
                    <div class="field-value">Principal</div>
                    <div class="signature-title field-value">Naogaon Govt. College, Naogaon.</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
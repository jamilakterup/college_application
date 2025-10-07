<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>চারিত্রিক সনদ পত্র</title>
    <style>
        /* Reset and base styles */
        body {
            font-family: bangla, sans-serif;
            margin: 0;
            padding: 0;
            background-color: transparent;
            position: relative;
            font-size: 14pt;
        }
        
        /* Page setup with background - mPDF specific approach */
        @page {
            margin: 0;
            padding: 0;
            background-image: url('{{ public_path('img/document-border.jpg') }}');
            background-image-resize: 6;
            background-position: center;
            background-repeat: no-repeat;
            background-image-opacity: 1;
        }
        
        /* Certificate container */
        .certificate {
            width: 100%;
            position: relative;
            padding: 30px;
            box-sizing: border-box;
        }
        
        /* Document number - mPDF compatible absolute positioning */
        .document-number {
            position: absolute;
            top: 100px; /* Adjust as needed */
            left: 80px; /* Adjust as needed */
            font-family: bangla;
            font-weight: bold;
            font-size: 14pt;
            z-index: 100;
        }
        
        /* Header styles */
        .header {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 20px;
            line-height: 0.8;
        }
        
        .header h1 {
            font-family: bangla;
            font-size: 20pt;
            font-weight: bold;
            margin: 15px 0;
            color: #b22222;
        }
        
        .header h2 {
            font-family: bangla;
            font-size: 18pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        /* Content area */
        .content {
            padding: 0 40px;
            line-height: 1.8;
            font-size: 14pt;
            text-align: justify;
            font-family: bangla;
        }
        
        /* Bold text styling - explicitly using bangla font */
        .field-value {
            font-family: bangla;
            font-weight: bold;
            font-size: 14pt;
        }
        
        /* Signature section */
        .signature-table {
            width: 100%;
            margin-top: 80px;
            padding: 0 40px;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .signature-left, .signature-right {
            text-align: center;
            width: 40%;
            vertical-align: bottom;
        }
        
        .signature-center {
            width: 20%;
            text-align: center;
            vertical-align: bottom;
        }
        
        .signature-img {
            max-width: 150px;
            max-height: 60px;
            margin: 0 auto;
            display: block;
        }
        
        .signature-title {
            margin-top: 5px;
            font-family: bangla;
            font-weight: bold;
            font-size: 14pt;
        }
        
        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: white;
            text-align: center;
            box-sizing: border-box;
        }

        .qr-code img {
            width: 90px; /* Explicit size for mPDF */
            height: 90px; /* Explicit size for mPDF */
            display: block; /* Remove inline spacing */
            margin: 0 auto; /* Center horizontally */
        }

        .scan-text {
            font-size: 12pt;
            margin-top: 5px;
            color: #555;
            text-align: center;
        }

        .scan-text {
            font-size: 12pt;
            margin-top: 5px;
            color: #555;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Document number with absolute positioning -->
    <div class="document-number">{{ $documentId ?? '900' }}</div>
    
    <div class="certificate">
        <div class="header">
            <h2>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h2>
            <h2>অধ্যক্ষের কার্যালয়</h2>
            <h2>নওগাঁ সরকারি কলেজ, নওগাঁ ।</h2>
            <h1>চারিত্রিক সনদ পত্র/প্রত্যয়ন পত্র</h1>
        </div>
        
        <div class="content">
            <p>এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, <span class="field-value">{{ $student->name ?? '........................' }}</span> পিতাঃ <span class="field-value">{{ $student->fathers_name_bn ?? '........................' }}</span> মাতাঃ <span class="field-value">{{ $student->mothers_name_bn ?? '.......................' }}</span> গ্রামঃ <span class="field-value">{{ $student->village ?? '.......................' }}</span> পোঃ <span class="field-value">{{ $student->post_office_bn ?? '.......................' }}</span> উপজেলাঃ <span class="field-value">{{ $student->upazila_bn ?? '...................' }}</span>জেলাঃ <span class="field-value">{{ $student->district_bn ?? '.......................' }}</span> । অত্র কলেজের <span class="field-value">{{ $student->department ?? '......................' }}</span> শিক্ষাবর্ষের <span class="field-value">{{ $student->academic_year ?? '.......................' }}</span> <span class="field-value">{{ nameToBangla($student->level) ?? '.......................' }}</span> শাখার/বিভাগের ছাত্র/ছাত্রী। সে <span class="field-value">{{ $student->details->exam_year ?? '.......................' }}</span> সালের <span class="field-value">{{ $student->details->exam_name ?? '.......................' }}</span>পরীক্ষায় <span class="field-value">{{ $student->details->result ?? '........................' }}</span> বিভাগ/শ্রেণী পেয়ে উত্তীর্ণ হয়েছে। তার রোল নং <span class="field-value">{{ numtobn($student->class_roll) ?? '......................' }}</span> রেজিস্ট্রেশন নং <span class="field-value">{{ numtobn($student->registration_no) ?? '......................' }}</span> । সে উত্তম চরিত্রের অধিকারী। আমার জানামতে সে সমাজ বা রাষ্ট্র বিরোধী কোন কাজে জড়িত নাই।</p>
            
            <p>আমি তার সকল কামনা করি।</p>
        </div>
        
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
                        <img src="{{ $qrCodePath ?? public_path('img/fallback-qrcode.png') }}" alt="Certificate QR Code" width="90" height="90">
                    </div>
                    <div class="scan-text">Scan to verify</div>
                </td>
                <td class="signature-right">
                    @if(isset($signature) && file_exists($signature))
                        <img src="{{ $signature }}" class="signature-img" alt="Principal Signature">
                    @endif
                    <div class="field-value">অধ্যক্ষ</div>
                    <div class="signature-title">নওগাঁ সরকারি কলেজ, নওগাঁ</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
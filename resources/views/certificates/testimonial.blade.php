<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Testimonial</title>
    <style>
        body {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background: url('{{ public_path('img/testimonial-bg.jpg') }}') no-repeat center top;
            background-image-resize: 6;
            background-image-resolution: from-image;
            position: relative;
            width: 100%;
            height: 100vh;
        }
        
        .content-area {
            padding: 320px 90px 120px;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }
        
        .content {
            font-size: 16px;
            text-align: justify;
            line-height: 1.7;
            margin: 5px 0 40px 0;
        }
        
        .content p {
            margin-bottom: 12px;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 1;
            width: 300px;
            height: auto;
        }
        
        .success-wish {
            margin-top: 25px;
            font-size: 15px;
            text-align: left;
        }
        
        /* Table styling for better mPDF compatibility */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        
        .layout-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        
        .serial-date-table {
            width: 100%;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: bold;
            border-collapse: collapse;
        }
        
        .serial-date-table td {
            border: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    @if(file_exists(public_path('img/watermark.png')))
    <img src="{{ public_path('img/watermark.png') }}" class="watermark" alt="Watermark">
    @endif
    
    <!-- Content Area -->
    <div class="content-area">
        
        <!-- Serial and Date Row using HTML Table -->
        <table class="serial-date-table">
            <tr>
                <td style="text-align: left;">Serial : {{ $testimonial->ref_no }}</td>
                <td style="text-align: right;">Date : {{ date('d/m/Y', strtotime($testimonial->issue_date)) }}</td>
            </tr>
        </table>
        
        <!-- Main Content -->
        <div class="content">
            <p>
                This is to certify that <strong>{{ strtoupper($testimonial->student_name) }}</strong>, 
                son/daughter of <strong>{{ strtoupper($testimonial->father_name) }}</strong> and 
                <strong>{{ strtoupper($testimonial->mother_name) }}</strong>, 
                @if($testimonial->registration_no)
                Registration no. <strong>{{ $testimonial->registration_no }}</strong>, 
                @endif
                Roll no. <strong>{{ $testimonial->roll_no }}</strong>. Session <strong>{{ $testimonial->student->session }}</strong>, 
                was a student of <strong>Rajshahi Shikkha Board Govt. Model School & College</strong> in 
                the <strong>{{ $testimonial->class_name }}</strong> 
                @if($testimonial->student && $testimonial->student->admitted_student && $testimonial->student->admitted_student->hsc_group)
                Group. He/She appeared in the <strong>HSC-{{ substr($testimonial->academic_year, 0, 4) }}</strong> examination held in <strong>{{ $testimonial->exam_year }}</strong> 
                under the Board of Intermediate & Secondary Education, Rajshahi, Bangladesh and obtained 
                GPA <strong>{{ $testimonial->gpa }}</strong>. His/Her date of birth is 
                <strong>{{ $testimonial->student->admitted_student->date_of_birth ? date('d F, Y', strtotime($testimonial->student->admitted_student->date_of_birth)) : 'N/A' }}</strong>. 
                @else
                Group. His/Her date of birth is <strong>N/A</strong>. 
                @endif
                He/She bears a good moral character. To the best of my knowledge he/she did not involve in any anti-state activities.
            </p>
        </div>

        <div class="success-wish">
            I wish his/her overall progress in life.
        </div>
    </div>
</body>
</html>
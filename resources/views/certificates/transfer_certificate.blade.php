<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Transfer Certificate</title>
    <style>
        body {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background: url('{{ public_path('img/transfer-certificate-bg.jpg') }}') no-repeat center top;
            background-image-resize: 6;
            background-image-resolution: from-image;
            position: relative;
            width: 100%;
            height: 100vh;
            line-height: 1.6;
        }

        .content-area {
            padding: 200px 90px 120px;
            min-height: 100vh;
            position: relative;
            z-index: 10;
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

        /* Serial and Date Row */
        .tc-date-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 14px;
            /* font-weight: bold; */
            border-collapse: collapse;
        }

        .tc-date-table td {
            border: none;
            padding: 0;
        }

        /* Main content styling */
        .content {
            font-size: 14px;
            text-align: justify;
            line-height: 1.7;
            margin: 120px 0 0 0;
        }

        .content p {
            margin-top: 120px;
            margin-bottom: 10x;
        }

        /* Student information styling */
        .student-details {
            margin: 20px 0;
        }

        .student-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .parent-names {
            font-weight: bold;
            text-transform: uppercase;
        }

        .academic-info {
            font-weight: bold;
        }

        /* Success wish styling */
        .success-wish {
            font-size: 14px;
            text-align: left;
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    {{-- @if (file_exists(public_path('img/watermark.png')))
    <img src="{{ public_path('img/watermark.png') }}" class="watermark" alt="Watermark">
    @endif --}}

    <!-- Content Area -->
    <div class="content-area">
        <!-- TC No and Date Row -->
        <table class="tc-date-table">
            <tr>
                <td style="text-align: left;">Reference No : {{ $certificate->tc_no }}</td>
                <td style="text-align: right;">Date : {{ date('d/m/Y', strtotime($certificate->issue_date)) }}</td>
            </tr>
        </table>

        <!-- Main Content -->
        <div class="content">
            <p style="text-indent: 50px; line-height:35px">
                This is to certify that <span class="student-name">{{ strtoupper($certificate->student_name) }}</span>,
                Father: <span style="text-transform: capitalize">{{ strtoupper($certificate->father_name) }}</span>,
                Mother: <span style="text-transform: capitalize">{{ strtoupper($certificate->mother_name) }}</span>,
                Village:
                <span
                    style="text-transform: capitalize">{{ strtoupper($certificate->student->permanent_village) }}</span>,
                Post Office:
                <span style="text-transform: capitalize">{{ strtoupper($certificate->student->permanent_po) }}</span>,
                Police Station:
                <span style="text-transform: capitalize">{{ strtoupper($certificate->student->permanent_ps) }}</span>,
                <span style="text-transform: capitalize">{{ strtoupper($certificate->student->permanent_dist) }}</span>
                District, is a regular student of class
                @if ($certificate->student->current_level == 'HSC 1st Year')
                    eleven
                @else
                    twelve
                @endif
                in
                @if ($certificate->student)
                    {{ $certificate->student->groups }}.
                @endif
                @if ($certificate->student->gender == 'male')
                    His
                @else
                    Her
                @endif
                academic
                session is
                @if ($certificate->academic_year)
                    {{ $certificate->academic_year }}
                @endif
                and class roll
                @if ($certificate->roll_no)
                    {{ $certificate->roll_no }}.
                @endif
                @if ($certificate->student->gender == 'male')
                    He
                @else
                    She
                @endif

                is reading optional subjects

                @php
                    $subjects = $certificate->student->hsc_subjects_info;

                    // Regex দিয়ে প্রতিটা subject আলাদা করা
                    preg_match_all('/([A-Za-z\s]+)\(\d+(?:,\d+)*\)/', $subjects, $matches);

                    $subjectNames = $matches[1] ?? [];

                    // যেগুলো বাদ দিতে হবে
                    $ignore = ['Bangla', 'English', 'ICT'];

                    $finalSubjects = array_values(
                        array_filter($subjectNames, function ($name) use ($ignore) {
                            return !in_array(trim($name), $ignore);
                        }),
                    );

                    // শেষ subject বের করা
                    $last = array_pop($finalSubjects);
                @endphp

                @if (!empty($finalSubjects))
                    {{ implode(', ', $finalSubjects) }} and 4th subject {{ $last }}.
                @endif



                @if ($certificate->student->gender == 'Male')
                    He
                @else
                    She
                @endif
                Paid
                tuition fees till @if ($certificate->leaving_fees_upto)
                    {{ strtolower(\Carbon\Carbon::parse($certificate->leaving_date)->format('F Y')) }}.
                @endif
            </p>
        </div>

        <!-- Success Wish -->
        <div class="success-wish" style="text-indent: 50px;">
            I wish @if ($certificate->student->gender == 'Male')
                him
            @else
                her
            @endif all the best.
        </div>
    </div>
</body>

</html>

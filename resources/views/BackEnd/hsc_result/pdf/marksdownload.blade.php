<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Report Card</title>
    <style>
        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            font-size: 12px;
        }

        /* Header styles */
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        .logo {
            margin-bottom: 10px;
        }

        .logo-img {
            max-width: 80px;
            height: auto;
        }

        .college-info {
            margin-bottom: 15px;
        }

        .college-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .college-info p {
            margin: 3px 0;
            font-size: 11px;
        }

        .college-info h4 {
            margin-top: 5px;
            color: #34495e;
        }

        /* Report info section */
        .report-info {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .info-label {
            font-weight: bold;
            color: #2c3e50;
            width: 80px;
        }

        /* Marks table styles */
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 15px;
        }

        .marks-table th {
            background: #34495e;
            color: white;
            padding: 8px 4px;
            border: 1px solid #2c3e50;
            font-size: 10px;
            text-align: center;
            vertical-align: middle;
        }

        .marks-table td {
            padding: 6px 4px;
            border: 1px solid #bdc3c7;
            text-align: center;
            vertical-align: middle;
        }

        .marks-table .roll-col {
            width: 60px;
        }

        .marks-table .name-col {
            width: 180px;
            text-align: center;
        }

        .marks-table .marks-col {
            width: 60px;
        }

        .marks-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Footer styles */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #bdc3c7;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .footer p {
            margin: 3px 0;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            @if(config('settings.site_logo'))
                <img src="{{ asset('upload/sites/' . config('settings.site_logo')) }}" alt="College Logo" class="logo-img">
            @endif
        </div>
        
        <div class="college-info">
            <h3>{{ config('settings.college_name') }}{{ !empty(config('settings.college_district')) ? ', ' . config('settings.college_district') : '' }}</h3>
            <h3>HSC Mark List</h2>
        </div>
    </header>

    <div class="report-info">
        <table class="info-table">
            <tr>
                <td class="info-label">Session:</td>
                <td>{{ $session }}</td>
                <td class="info-label">Group:</td>
                <td>{{ $group }}</td>
            </tr>
            <tr>
                <td class="info-label">Class:</td>
                <td>{{ $curr_level->name }}</td>
                <td class="info-label">Exam Year:</td>
                <td>{{ $exam_year }}</td>
            </tr>
            <tr>
                <td class="info-label">Subject:</td>
                <td colspan="3">{{ $subject_name }} {{ !empty($subject_code) ? "($subject_code)" : '' }}</td>
            </tr>
        </table>
    </div>

    <table class="marks-table">
        <thead>
            <tr>
                <th class="roll-col">Roll No.</th>
                <th class="name-col">Student Name</th>
                @foreach($config_exam_particles as $particle)
                    <th class="marks-col">
                        {{ $particle->xmparticle->name }}
                        <br>
                        <small>({{ $particle->total }})</small>
                    </th>
                @endforeach
                <th class="marks-col">Total Marks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student_info as $info)
                @if(isset($students[$info->student_id]))
                    @php
                        $totalMarks = 0;
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $info->student_id }}</td>
                        <td class="text-left">{{ $students[$info->student_id]->name }}</td>
                        @php
                            $totalMarks = 0;
                        @endphp
                        @foreach($config_exam_particles as $particle)
                            @php
                                $studentMarks = $marks[$info->student_id] ?? [];
                                $particleMarks = $studentMarks[$particle->xmparticle_id] ?? [];
                                $mark = !empty($particleMarks) ? $particleMarks[0]->mark : '';

                                $totalMarks += is_numeric($mark) ? $mark : 0;
                            @endphp
                            <td>{{ $mark }}</td>
                        @endforeach
                        <td class="font-bold">{{ $totalMarks }}</td>
                    </tr>

                @endif
            @empty
                <tr>
                    <td colspan="{{ count($config_exam_particles) + 3 }}" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <div class="footer">
        <p>Generated on: {{ date('d-m-Y h:i:s A') }}</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>

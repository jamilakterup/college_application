<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Progress Report</title>

    <style>
        * {
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
        }
        td,th{
            text-align: center;
        }
        .heading-text {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            line-height: 30px;
        }
        #markDistributeTable th,
        #markDistributeTable td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        $j = 0;
    @endphp
    @foreach($studentSubjectMarks as $info)
        <div class="container">
            <table width="100%">
                <tr>
                    <td></td>
                    <td width="50%" class="heading-text">
                        <p>{{config('settings.college_name') ?? ''}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</p>
                        <p>Higher Secondary Class</p>
                        <p>Progress Report</p>
                        <p>{{$info['studentInfo']['groups'] ?? ''}}</p>
                    </td>
                    <td></td>
                </tr>
            </table>

            <table width="100%" style="margin: 10px 20px">
                <tr>
                    <td align="left" width="25%"><strong>Name: {{$info['studentInfo']['name'] ?? ''}}</strong></td>
                    <td align="left" width="25%"><strong>Roll: {{$info['studentInfo']['id'] ?? ''}}</strong></td>
                    <td align="left" width="25%"><strong>Session: {{$info['studentInfo']['session'] ?? ''}}</strong></td>
                </tr>
            </table>

            <table width="100%" id="markDistributeTable">
                <thead>
                    <tr>
                        <th rowspan="2">Name of the Subjects</th>
                        <th rowspan="2">Subject Code</th>
                        @foreach($info['headerExamInfos'] as $exam_id => $headerInfo)
                            <th colspan="3">{{App\Models\Exam::find($exam_id)->name ?? ''}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($info['headerExamInfos'] as $exam_id => $headerInfo)
                            <th>{{$headerInfo[0] ?? ''}}</th>
                            <th>{{$headerInfo[1] ?? ''}}</th>
                            <th>{{$headerInfo[2] ?? ''}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($info['subjectsData'] as $subjectName => $data)
                        <tr>
                            <td align="left">{{$subjectName ?? ''}}</td>
                            <td>{{$data[0] ?? ''}}</td>
                            @foreach($info['headerExamInfos'] as $exam_id => $headerInfo)
                                <td>{{$data[1][$exam_id][0] ?? ''}}</td>
                                <td>{{$data[1][$exam_id][1] ?? ''}}</td>
                                <td>{{$data[1][$exam_id][2] ?? ''}}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td align="left">Remarks</td>
                        <td></td>
                        @for($i = 0; $i < $info['exam_count']; $i++)
                            <td colspan="3"></td>
                        @endfor
                    </tr>

                    <tr>
                        <td align="left">Gurdian Signature</td>
                        <td></td>
                        @for($i = 0; $i < $info['exam_count']; $i++)
                            <td colspan="3"></td>
                        @endfor
                    </tr>

                    <tr>
                        <td align="left">Principal Signature</td>
                        <td></td>
                        @for($i = 0; $i < $info['exam_count']; $i++)
                            <td colspan="3"></td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>

        @php $j++ @endphp
        @if(count($studentSubjectMarks) > $j)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>

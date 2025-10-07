<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);

use Illuminate\Support\Collection;

$info = App\Models\HscRsltProcessing::find($id);

$merits = DB::select("
    SELECT 
        t1.*, 
        t2.total_mark, 
        t3.name AS Student_name, 
        t3.groups, 
        t4.name AS Exam_name 
    FROM hsc_cgpa t1
    INNER JOIN (
        SELECT 
            SUM(mark) AS total_mark, 
            group_id, 
            session, 
            exam_id, 
            student_id
        FROM marks
        WHERE 
            group_id = '$info->group_id' AND 
            session = '$info->session' AND 
            exam_id = '$info->exam_id' AND 
            exam_year = $info->exam_year
        GROUP BY student_id
    ) t2 ON t1.student_id = t2.student_id 
        AND t1.session = t2.session 
        AND t1.group_id = t2.group_id 
        AND t1.exam_id = t2.exam_id
    INNER JOIN student_info_hsc t3 ON t1.student_id = t3.id
    INNER JOIN exams t4 ON t4.id = t1.exam_id
");
// todo
$merits = collect($merits)->sortByDesc('total_mark')->values();

$lastMark = null;
$position = 0;
$actualPosition = 1;

$merits = $merits->map(function ($item) use (&$lastMark, &$position, &$actualPosition) {
    if ($item->total_marks !== $lastMark) {
        $position = $actualPosition;
    }
    $item->merit_position = $position;
    $lastMark = $item->total_marks;
    $actualPosition++;
    return $item;
});

$merits = $merits->sortBy('student_id')->values();
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ config('settings.college_name') }} Merit List</title>
    <style>
        .page-break {
            page-break-after: always;
        }

        html,
        body,
        div,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            font-weight: inherit;
            font-style: inherit;
            font-size: 100%;
            font-family: inherit;
            vertical-align: top;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        body {
            font-family: 'FreeSerif', sans-serif;
        }

        td.title {
            font-size: 30px;
            line-height: 36px;
            color: #000;
        }

        td.subtitle {
            font-size: 24px;
            line-height: 30px;
            color: #000;
        }

        td.mintitle {
            font-size: 20px;
            line-height: 24px;
            color: #000;
        }

        .order-details tr,
        .order-details td {
            border: 1px solid #cecece;
        }
    </style>
</head>

<body>
    <div class="invoice-table">
        <?php $i = 1;
        $total_count = count($merits); ?>
        @foreach ($merits as $info)
            @if ($i % 25 == 1)
                <table width="100%" style="text-align:center; width:100%; margin-top:10px;">
                    @php
                        $group = App\Models\Group::find($info->group_id);
                        $exam = App\Models\Exam::find($info->exam_id);
                    @endphp
                    <tr>
                        <td width="10%" class="subtitle">&nbsp;</td>
                        <td width="30%" class="subtitle">Merit List</td>
                        <td width="40%" class="subtitle">{{ $exam->name }} - {{ $info->exam_year }}</td>
                        <td width="20%" class="subtitle">{{ $group->name }}</td>
                    </tr>
                </table>

                <div class="table-left" style="width:100%; float:left;">
                    <table class="order-details" border="1" width="100%"
                        style="margin-top:10px;page-break-inside:avoid;">
                        <tr>
                            <td width="14%" style="text-align: center;"><strong>Class Roll</strong></td>
                            <td width="14%" style="text-align: center;"><strong>Merit Position</strong></td>
                            <td width="33%" style="text-align: center;"><strong>Student Name</strong></td>
                            <td width="13%" style="text-align: center;"><strong>Session</strong></td>
                            <td width="12%" style="text-align: center;"><strong>Group</strong></td>
                            <td width="10%" style="text-align: center;"><strong>GPA <br><small
                                        style="font-size: 9px;">(Without 4th)</small></strong></td>
                            <td width="10%" style="text-align: center;"><strong>GPA</strong></td>
                        </tr>
            @endif

            <tr>
                <td style="text-align: center;">{{ $info->student_id }}</td>
                <td style="text-align: center;">{{ $info->merit_position }}</td>
                <td style="padding:5px;">{{ $info->Student_name }}</td>
                <td style="text-align: center;">{{ $info->session }}</td>
                <td style="text-align: center;">{{ $info->groups }}</td>
                <td style="text-align: center;">{{ round($info->without_4th, 2) }}</td>
                @if ($info->cgpa == 0)
                    <td style="text-align: center;">F</td>
                @else
                    <td style="text-align: center;">{{ round($info->cgpa, 2) }}</td>
                @endif
            </tr>

            @if ($i % 25 == 0 || $total_count == $i)
                </table>
                <div class="page-break"></div>
            @endif
            <?php $i++; ?>
        @endforeach
    </div>
</body>

</html>

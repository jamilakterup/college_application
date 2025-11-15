<?php ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>{{ config('settings.college_name') }} Total Marks</title>
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
        td,
        {
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        font-size: 13px;
        font-family: monospace;
        vertical-align: top;
        }

        :focus {
            outline: 0;
        }

        body {
            font-family: 'monospace', sans-serif;
        }

        p {
            font-size: 14px;
        }
    </style>
</head>

<body>

    @php
        $info = App\Models\HscRsltProcessing::find($id);
        $merits = DB::select("Select t1.*,t2.total_mark,t3.name Student_name,t3.groups,t4.name Exam_name from hsc_cgpa t1
INNER JOIN
(Select sum(mark) total_mark,group_id,session,exam_id,student_id from marks
where `group_id`='$info->group_id' and   `exam_id` = '$info->exam_id' and exam_year=$info->exam_year
group by student_id)
t2
on t1.student_id= t2.student_id and t1.session=t2.session and t1.group_id=t2.group_id and  t1.exam_id = t2.exam_id
INNER JOIN
student_info_hsc t3
ON t1.student_id=t3.id
INNER JOIN
exams t4
ON t4.id = t1.exam_id
order by t1.cgpa desc,t1.without_4th desc,t2.total_mark desc");
    @endphp

    <table width="100%">
        <tbody>
            <tr>
                <td width="72%" style="text-align: center;line-height: 20px;">
                    <div style="">
                        <p style="font-weight: bold;">{{ config('settings.college_name') }},
                            {{ config('settings.college_district') }}</p>
                        <p>Groups : {{ App\Models\Group::find($info->group_id)->name ?? null }} </p>
                        <p>Result of {{ App\Models\Exam::find($info->exam_id)->name ?? null }} : {{ $info->exam_year }}
                        </p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="mark-table">
        <table width="100%" style="width:100%; margin-top:10px;" class="resultTable">
            <tbody>
                <?php $i = 1; ?>
                @foreach ($merits as $info)
                    <?php if ($i % 8 == 1): ?>
                    <?php if ($i != 1): ?>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <?php endif; ?>


                        <td style="padding: 5px; font-size: 12px;">{{ $info->student_id }}[{{ round($info->cgpa, 2) }}],
                        </td>


                        <?php if ($i % 8 == 0 || $i == count($merits)): ?>
                    </tr>
                    <?php endif; ?>
                    <?php $i++; ?>
                @endforeach
            </tbody>
        </table>
    </div>


</body>

</html>

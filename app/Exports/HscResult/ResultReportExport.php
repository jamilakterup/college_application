<?php

namespace App\Exports\HscResult;

use DB;
use App\Models\StudentInfoHsc;
use App\Models\HscGpa;
use App\Models\StudentSubMarkGp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResultReportExport implements FromCollection, WithHeadings
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function headings(): array
    {
        return [
            'Student Roll',
            'Student Name',
            'Admission Session',
            'Department',
            'Group',
            'GPA',
            'Without 4th',
            'Grade Point',
            'Mark'
        ];
    }

    public function collection()
    {
        $groups = $this->params['groups'];
        $subjects = $this->params['subjects'];
        $exams = $this->params['exams'];
        $session = $this->params['session'];
        $grade_scales = $this->params['grade_scales'];

        if (!$subjects) {
            return $this->getCgpaData($session, $groups, $exams, $grade_scales);
        }

        return $this->getSubjectData($session, $groups, $exams, $subjects, $grade_scales);
    }

    private function getCgpaData($session, $groups, $exams, $grade_scales)
    {
        $query = HscGpa::select([
                'student_info_hsc.class_roll as Student Roll',
                'student_info_hsc.name as Student Name',
                'hsc_cgpa.session as Admission Session',
                DB::raw('COALESCE(student_info_hsc.groups, "") as Department'),
                'student_info_hsc.groups as Group',
                'hsc_cgpa.cgpa as GPA',
                'hsc_cgpa.without_4th as Without 4th',
                'hsc_cgpa.grade as Grade Point',
                DB::raw('sum(student_sub_mark_gp.total_mark) as Mark')
            ])
            ->join('student_sub_mark_gp', 'hsc_cgpa.student_id', '=', 'student_sub_mark_gp.student_id')
            ->join('student_info_hsc', 'hsc_cgpa.student_id', '=', 'student_info_hsc.id')
            ->when($session, fn($q) => $q->where('hsc_cgpa.session', $session))
            ->when($groups, fn($q) => $q->where('hsc_cgpa.group_id', $groups))
            ->when($exams, fn($q) => $q->where('hsc_cgpa.exam_id', $exams))
            ->when($grade_scales, function($q) use($grade_scales) {
                if(is_string($grade_scales)) {
                    $this->applyGradeScaleFilter($q, $grade_scales, 'cgpa');
                }
            })
            ->orderBy('hsc_cgpa.cgpa', 'DESC')
            ->groupBy('student_sub_mark_gp.student_id')
            ->orderBy('student_info_hsc.class_roll');

        return $query->get();
    }

    private function getSubjectData($session, $groups, $exams, $subjects, $grade_scales)
    {
        $query = StudentSubMarkGp::select([
                'student_info_hsc.class_roll as Student Roll',
                'student_info_hsc.name as Student Name',
                'student_sub_mark_gp.session as Admission Session',
                'subjects.name as Department', 
                'student_info_hsc.groups as Group',
                'student_sub_mark_gp.point as GPA',
                'student_sub_mark_gp.fourth as Without 4th',
                'student_sub_mark_gp.grade as Grade Point',
                'student_sub_mark_gp.total_mark as Mark'
            ])
            ->join('student_info_hsc', 'student_sub_mark_gp.student_id', '=', 'student_info_hsc.id')
            ->join('subjects', 'student_sub_mark_gp.subject_id', '=', 'subjects.id')
            ->when($session, fn($q) => $q->where('student_sub_mark_gp.session', $session))
            ->when($groups, fn($q) => $q->where('student_sub_mark_gp.group_id', $groups))
            ->when($exams, fn($q) => $q->where('student_sub_mark_gp.exam_id', $exams))
            ->when($subjects, fn($q) => $q->where('student_sub_mark_gp.subject_id', $subjects))
            ->when($grade_scales, function($q) use($grade_scales) {
                $this->applyGradeScaleFilter($q, $grade_scales, 'point');
            })
            ->groupBy('student_sub_mark_gp.student_id')
            ->orderBy('student_info_hsc.class_roll');

        return $query->get();
    }


    private function applyGradeScaleFilter($query, $grade_scales, $field)
    {
        switch ($grade_scales) {
            case '3.00':
            case '3.50':
                $query->whereBetween($field, [$grade_scales, $grade_scales + 0.49]);
                break;
            case 'p':
                $query->where($field, '>=', 1.00);
                break;
            case 'F':
                $query->where($field, '<', 1.00);
                break;
            default:
                $query->whereBetween($field, [$grade_scales, $grade_scales + 0.99]);
        }
    }
}

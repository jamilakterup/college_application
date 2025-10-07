<?php

namespace App\Exports\HscResult;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectWiseResultExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Return the collection of data for export.
     */
    public function collection()
    {
        return collect($this->report);
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'Subject',
            'Exam',
            'Total Students',
            'Pass/Fail',
            'Pass %',
            'Fail %',
            'A+',
            'A',
            'A-',
            'B',
            'C',
            'D',
            'F'
        ];
    }

    /**
     * Map data for each row in the Excel file.
     */
    public function map($row): array
    {
        $totalStudents = $row['total_students'] ?? 0;
        $passCount = $row['pass_count'] ?? 0;
        $failCount = $row['fail_count'] ?? 0;
        
        // Calculate percentages
        $passPercentage = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;
        $failPercentage = $totalStudents > 0 ? round(($failCount / $totalStudents) * 100, 2) : 0;

        return [
            $row['subject_name'] ?? '',
            $row['exam_name'] ?? '',
            $totalStudents,
            $passCount . '/' . $failCount,
            $passPercentage . '%',
            $failPercentage . '%',
            $row['grade_counts']['A+'] ?? 0,
            $row['grade_counts']['A'] ?? 0,
            $row['grade_counts']['A-'] ?? 0,
            $row['grade_counts']['B'] ?? 0,
            $row['grade_counts']['C'] ?? 0,
            $row['grade_counts']['D'] ?? 0,
            $row['grade_counts']['F'] ?? 0,
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Extend the styling to include the new percentage columns
        $sheet->getStyle('A1:M1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:M1')->getFill()->applyFromArray([
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E2E2E2'],
        ]);

        return [];
    }
}

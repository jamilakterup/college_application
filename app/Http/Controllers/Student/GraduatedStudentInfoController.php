<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GraduatedStudentInfo;

class GraduatedStudentInfoController extends Controller
{
    /**
     * Show graduated students
     */
    public function index()
    {
        // Get graduated students (adjust column if needed)
        $students = GraduatedStudentInfo::orderBy('id', 'desc')
            ->paginate(50);

        return view('BackEnd.student.graduated-student-info.index', compact('students'));
    }

    /**
     * Download CSV
     */
    public function downloadCsv()
    {
        $students = GraduatedStudentInfo::all();

        $fileName = 'graduated_students_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($students) {

            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'ID',
                'Image',
                'Name',
                'Class Roll',
                'HSC Roll',
                'Session',
                'Institution',
                'Mobile No'
            ]);

            // Data
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->photo,
                    $student->name,
                    $student->class_roll,
                    $student->hsc_roll,
                    $student->session,
                    $student->institution_name,
                    $student->mobile,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

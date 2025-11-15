<?php

namespace App\Http\Controllers\Student\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\StudentInfoHsc;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Carbon\Carbon;
use ZipArchive;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with(['student', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('BackEnd.student.certificates.testimonial.index', compact('testimonials'));
    }

    public function create()
    {
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.testimonial.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'admission_roll' => 'required',
            'academic_year' => 'required|string|max:20',
            'class_name' => 'required|string|max:100',
            'ref_no' => 'nullable|string|max:100',
            'admission_date' => 'nullable|date',
            'registration_no' => 'nullable|string|max:50',
            'student_type' => 'nullable|string|max:50',
            'gpa' => 'nullable|string|max:50',
            'exam_year' => 'nullable|string|max:50',
            'study_period_from' => 'nullable|date',
            'study_period_to' => 'nullable|date'
        ]);

        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $testimonial = new Testimonial();
        $testimonial->fill($request->all());
        $testimonial->student_name = $student->name;
        $testimonial->father_name = $student->father_name;
        $testimonial->mother_name = $student->mother_name;
        $testimonial->roll_no = $student->class_roll;
        $testimonial->student_type = $student->student_type;
        $testimonial->ref_no = $request->ref_no ?? $testimonial->generateRefNo();
        $testimonial->admission_roll = $request->admission_roll;
        $testimonial->issued_by = auth()->id();
        $testimonial->save();

        return redirect()->route('certificates.testimonial.index')
            ->with('success', 'Testimonial created successfully');
    }

    public function show($id)
    {
        $testimonial = Testimonial::with(['student', 'issuedBy'])->findOrFail($id);
        return view('BackEnd.student.certificates.testimonial.show', compact('testimonial'));
    }

    public function edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.testimonial.edit', compact('testimonial', 'students'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ref_no' => 'nullable|string',
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'class_name' => 'required|string|max:100',
            'admission_date' => 'nullable|date',
            'registration_no' => 'nullable|string|max:50',
            'admission_roll' => 'nullable|string|max:50',
            'student_type' => 'nullable|string|max:50',
            'gpa' => 'nullable|string|max:50',
            'exam_year' => 'nullable|string|max:50',
            'study_period_from' => 'nullable|date',
            'study_period_to' => 'nullable|date'
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $testimonial->fill($request->all());
        $testimonial->student_name = $student->name;
        $testimonial->father_name = $student->father_name;
        $testimonial->mother_name = $student->mother_name;
        $testimonial->roll_no = $student->class_roll;
        $testimonial->save();

        return redirect()->route('certificates.testimonial.index')
            ->with('success', 'Testimonial updated successfully');
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return redirect()->route('certificates.testimonial.index')
            ->with('success', 'Testimonial deleted successfully');
    }

    public function generatePdf($id)
    {
        try {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 120);

            $testimonial = Testimonial::with(['student.admitted_student', 'issuedBy'])->findOrFail($id);

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'default_font' => 'dejavusans'
            ]);

            // Set background using mPDF methods
            $backgroundPath = public_path('img/testimonial-bg.jpg');
            if (file_exists($backgroundPath)) {
                $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
            }

            $html = view('certificates.testimonial', compact('testimonial'))->render();
            $mpdf->WriteHTML($html);

            $filename = 'testimonial_' . str_replace('/', '_', $testimonial->ref_no) . '.pdf';
            return $mpdf->Output($filename, 'I');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    // Search functionality
    public function search(Request $request)
    {
        $query = Testimonial::with(['student', 'issuedBy']);

        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('ref_no')) {
            $query->where('ref_no', 'like', '%' . $request->ref_no . '%');
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $testimonials = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('BackEnd.student.certificates.testimonial.index', compact('testimonials'));
    }

    // Bulk operations
    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:testimonials,id'
        ]);

        Testimonial::whereIn('id', $request->ids)->delete();

        return redirect()->route('certificates.testimonial.index')
            ->with('success', count($request->ids) . ' testimonials deleted successfully');
    }

    public function bulkStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:testimonials,id',
            'status' => 'required|in:active,inactive,cancelled'
        ]);

        Testimonial::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('certificates.testimonial.index')
            ->with('success', count($request->ids) . ' testimonials updated successfully');
    }

    // Batch PDF Download functionality
    public function bulkPdfDownload(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:testimonials,id'
        ]);

        try {
            $testimonials = Testimonial::with(['student', 'issuedBy'])
                ->whereIn('id', $request->ids)
                ->get();

            if ($testimonials->isEmpty()) {
                return redirect()->back()->with('error', 'No testimonials found for download.');
            }

            // Create temporary directory for PDFs
            $tempDir = storage_path('app/temp/testimonials_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($testimonials as $testimonial) {
                try {
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4-L',
                        'margin_left' => 0,
                        'margin_right' => 0,
                        'margin_top' => 0,
                        'margin_bottom' => 0,
                        'default_font' => 'dejavusans'
                    ]);

                    // Set background using mPDF methods
                    $backgroundPath = public_path('img/testimonial-bg.jpg');
                    if (file_exists($backgroundPath)) {
                        $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                        $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
                    }

                    $html = view('certificates.testimonial', compact('testimonial'))->render();
                    $mpdf->WriteHTML($html);

                    $filename = 'testimonial_' . str_replace('/', '_', $testimonial->ref_no) . '.pdf';
                    $filepath = $tempDir . '/' . $filename;
                    $mpdf->Output($filepath, 'F');

                    $pdfFiles[] = [
                        'path' => $filepath,
                        'name' => $filename
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error generating PDF for {$testimonial->student_name}: " . $e->getMessage();
                }
            }

            if (empty($pdfFiles)) {
                // Clean up temp directory
                $this->cleanupTempDir($tempDir);
                return redirect()->back()->with('error', 'No PDFs could be generated. Errors: ' . implode(', ', $errors));
            }

            // Create ZIP file
            $zipFilename = 'testimonials_batch_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = $tempDir . '/' . $zipFilename;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                $this->cleanupTempDir($tempDir);
                return redirect()->back()->with('error', 'Could not create ZIP file.');
            }

            foreach ($pdfFiles as $file) {
                $zip->addFile($file['path'], $file['name']);
            }
            $zip->close();

            // Return download response and schedule cleanup
            return response()->download($zipPath, $zipFilename, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            if (isset($tempDir)) {
                $this->cleanupTempDir($tempDir);
            }
            return redirect()->back()->with('error', 'Error creating batch PDF download: ' . $e->getMessage());
        }
    }

    private function cleanupTempDir($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $filePath = $dir . '/' . $file;
                if (is_file($filePath)) {
                    unlink($filePath);
                }
            }
            rmdir($dir);
        }
    }

    // CSV Upload functionality
    public function uploadForm()
    {
        return view('BackEnd.student.certificates.testimonial.upload');
    }

    public function uploadCsv(Request $request)
    {
        $this->validate($request, [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData);

            $expectedHeaders = ['ref_no', 'student_id', 'issue_date', 'academic_year', 'class_name', 'gpa', 'exam_year',  'attendance_percentage'];

            if (array_diff($expectedHeaders, $header)) {
                return redirect()->back()->with('error', 'CSV format is incorrect. Please check the sample format.');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                if (count($row) != count($header)) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": Invalid number of columns";
                    continue;
                }

                $data = array_combine($header, $row);

                // Validate student exists
                $student = StudentInfoHsc::find($data['student_id']);
                if (!$student) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": Student ID {$data['student_id']} not found";
                    continue;
                }

                try {
                    $testimonial = new Testimonial();
                    $testimonial->student_id = $data['student_id'];
                    $testimonial->student_name = $student->name;
                    $testimonial->father_name = $student->father_name;
                    $testimonial->mother_name = $student->mother_name;
                    $testimonial->roll_no = $student->class_roll;
                    $testimonial->student_type = $student->student_type;
                    $testimonial->issue_date = ($data['issue_date']) ? Carbon::parse($data['issue_date'])->format('Y-m-d') : null;
                    $testimonial->academic_year = $data['academic_year'];
                    $testimonial->class_name = $data['class_name'];
                    $testimonial->gpa = $data['gpa'];
                    $testimonial->exam_year = $data['exam_year'];
                    $testimonial->ref_no = $data['ref_no'] ?? $testimonial->generateRefNo();
                    $testimonial->issued_by = auth()->id();
                    $testimonial->save();

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Upload completed. Success: {$successCount}, Errors: {$errorCount}";
            if (!empty($errors)) {
                $message .= "\nErrors:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "\n... and " . (count($errors) - 10) . " more errors.";
                }
            }

            return redirect()->route('certificates.testimonial.index')
                ->with($errorCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing CSV: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="testimonial_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ref_no',
                'student_id',
                'issue_date',
                'academic_year',
                'class_name',
                'gpa',
                'exam_year',
                'student_type',
                'attendance_percentage'
            ]);

            // Sample data
            fputcsv($file, [
                '123',
                '202001',
                '2024-01-15',
                '2023-2024',
                'HSC 1st Year',
                '5',
                '2025',
                'regular',
                '95%'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

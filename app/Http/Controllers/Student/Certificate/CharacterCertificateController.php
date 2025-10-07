<?php

namespace App\Http\Controllers\Student\Certificate;

use App\Http\Controllers\Controller;
use App\Models\CharacterCertificate;
use App\Models\StudentInfoHsc;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Carbon\Carbon;
use ZipArchive;

class CharacterCertificateController extends Controller
{
    public function index()
    {
        $certificates = CharacterCertificate::with(['student', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('BackEnd.student.certificates.character.index', compact('certificates'));
    }

    public function create()
    {
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.character.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'class_name' => 'required|string|max:100',
            'certificate_no' => 'nullable|string|max:100',
            'registration_no' => 'nullable|string|max:50',
            'study_period_from' => 'nullable|date',
            'study_period_to' => 'nullable|date'
        ]);

        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $certificate = new CharacterCertificate();
        $certificate->fill($request->all());
        $certificate->student_name = $student->name;
        $certificate->father_name = $student->father_name;
        $certificate->mother_name = $student->mother_name;
        $certificate->roll_no = $student->class_roll;
        $certificate->certificate_no = $request->certificate_no ?? $certificate->generateCertificateNo();
        $certificate->issued_by = auth()->id();
        $certificate->save();

        return redirect()->route('certificates.character.index')
            ->with('success', 'Character Certificate created successfully');
    }

    public function show($id)
    {
        $certificate = CharacterCertificate::with(['student', 'issuedBy'])->findOrFail($id);
        return view('BackEnd.student.certificates.character.show', compact('certificate'));
    }

    public function edit($id)
    {
        $certificate = CharacterCertificate::findOrFail($id);
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.character.edit', compact('certificate', 'students'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'class_name' => 'required|string|max:100',
            'certificate_no' => 'required|string|max:100',
            'registration_no' => 'nullable|string|max:50',
            'study_period_from' => 'nullable|date',
            'study_period_to' => 'nullable|date'
        ]);

        $certificate = CharacterCertificate::findOrFail($id);
        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $certificate->fill($request->all());
        $certificate->student_name = $student->name;
        $certificate->father_name = $student->father_name;
        $certificate->mother_name = $student->mother_name;
        $certificate->roll_no = $student->class_roll;
        $certificate->save();

        return redirect()->route('certificates.character.index')
            ->with('success', 'Character Certificate updated successfully');
    }

    public function destroy($id)
    {
        $certificate = CharacterCertificate::findOrFail($id);
        $certificate->delete();

        return redirect()->route('certificates.character.index')
            ->with('success', 'Character Certificate deleted successfully');
    }

    public function generatePdf($id)
    {
        try {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 120);

            $certificate = CharacterCertificate::with(['student', 'issuedBy'])->findOrFail($id);

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'default_font' => 'dejavusans'
            ]);

            // Set background using mPDF methods
            $backgroundPath = public_path('img/character-certificate-bg.jpg');
            if (file_exists($backgroundPath)) {
                $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
            }

            $html = view('certificates.character_certificate', compact('certificate'))->render();
            $mpdf->WriteHTML($html);

            $filename = 'character_certificate_' . str_replace('/', '_', $certificate->certificate_no) . '.pdf';
            return $mpdf->Output($filename, 'I');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    // Search functionality
    public function search(Request $request)
    {
        $query = CharacterCertificate::with(['student', 'issuedBy']);

        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('certificate_no')) {
            $query->where('certificate_no', 'like', '%' . $request->certificate_no . '%');
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('character_rating')) {
            $query->where('character_rating', $request->character_rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('BackEnd.student.certificates.character.index', compact('certificates'));
    }

    // Bulk operations
    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:character_certificates,id'
        ]);

        CharacterCertificate::whereIn('id', $request->ids)->delete();

        return redirect()->route('certificates.character.index')
            ->with('success', count($request->ids) . ' character certificates deleted successfully');
    }

    public function bulkStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:character_certificates,id',
            'status' => 'required|in:active,inactive,cancelled'
        ]);

        CharacterCertificate::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('certificates.character.index')
            ->with('success', count($request->ids) . ' character certificates updated successfully');
    }

    // Batch PDF Download functionality
    public function bulkPdfDownload(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:character_certificates,id'
        ]);

        try {
            $certificates = CharacterCertificate::with(['student', 'issuedBy'])
                ->whereIn('id', $request->ids)
                ->get();

            if ($certificates->isEmpty()) {
                return redirect()->back()->with('error', 'No character certificates found for download.');
            }

            // Create temporary directory for PDFs
            $tempDir = storage_path('app/temp/character_certificates_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($certificates as $certificate) {
                try {
                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left' => 0,
                        'margin_right' => 0,
                        'margin_top' => 0,
                        'margin_bottom' => 0,
                        'default_font' => 'dejavusans'
                    ]);

                    // Set background using mPDF methods
                    $backgroundPath = public_path('img/character-certificate-bg.jpg');
                    if (file_exists($backgroundPath)) {
                        $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                        $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
                    }

                    $html = view('certificates.character_certificate', compact('certificate'))->render();
                    $mpdf->WriteHTML($html);

                    $filename = 'character_certificate_' . str_replace('/', '_', $certificate->certificate_no) . '.pdf';
                    $filepath = $tempDir . '/' . $filename;
                    $mpdf->Output($filepath, 'F');

                    $pdfFiles[] = [
                        'path' => $filepath,
                        'name' => $filename
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error generating PDF for {$certificate->student_name}: " . $e->getMessage();
                }
            }

            if (empty($pdfFiles)) {
                // Clean up temp directory
                $this->cleanupTempDir($tempDir);
                return redirect()->back()->with('error', 'No PDFs could be generated. Errors: ' . implode(', ', $errors));
            }

            // Create ZIP file
            $zipFilename = 'character_certificates_batch_' . date('Y-m-d_H-i-s') . '.zip';
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

    // Statistics
    public function statistics()
    {
        $stats = [
            'total' => CharacterCertificate::count(),
            'active' => CharacterCertificate::where('status', 'active')->count(),
            'inactive' => CharacterCertificate::where('status', 'inactive')->count(),
            'cancelled' => CharacterCertificate::where('status', 'cancelled')->count(),
            'this_month' => CharacterCertificate::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_year' => CharacterCertificate::whereYear('created_at', Carbon::now()->year)->count(),
            'by_rating' => CharacterCertificate::selectRaw('character_rating, COUNT(*) as count')
                ->groupBy('character_rating')
                ->pluck('count', 'character_rating')
                ->toArray()
        ];

        return response()->json($stats);
    }

    // CSV Upload functionality
    public function uploadForm()
    {
        return view('BackEnd.student.certificates.character.upload');
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

            // Expected CSV format
            $expectedHeaders = ['certificate_no', 'student_id', 'issue_date', 'academic_year', 'class_name'];

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
                    $certificate = new CharacterCertificate();
                    $certificate->student_id = $data['student_id'];
                    $certificate->student_name = $student->name;
                    $certificate->father_name = $student->father_name;
                    $certificate->mother_name = $student->mother_name;
                    $certificate->roll_no = $student->class_roll;
                    $certificate->issue_date = ($data['issue_date']) ? date('Y-m-d', strtotime($data['issue_date'])) : null;
                    $certificate->academic_year = $data['academic_year'];
                    $certificate->class_name = $data['class_name'];
                    $certificate->certificate_no = $data['certificate_no'] ?? $certificate->generateCertificateNo();
                    $certificate->issued_by = auth()->id();
                    $certificate->save();

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

            return redirect()->route('certificates.character.index')
                ->with($errorCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing CSV: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="character_certificate_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'certificate_no',
                'student_id',
                'issue_date',
                'academic_year',
                'class_name'
            ]);

            // Sample data
            fputcsv($file, [
                'CC-123',
                '1',
                '2024-01-15',
                '2023-2024',
                'HSC 1st Year'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

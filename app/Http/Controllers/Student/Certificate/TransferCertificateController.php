<?php

namespace App\Http\Controllers\Student\Certificate;

use App\Http\Controllers\Controller;
use App\Models\TransferCertificate;
use App\Models\StudentInfoHsc;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Carbon\Carbon;
use ZipArchive;

class TransferCertificateController extends Controller
{
    public function index()
    {
        $certificates = TransferCertificate::with(['student', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('BackEnd.student.certificates.transfer.index', compact('certificates'));
    }

    public function create()
    {
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups', 'birth_date', 'religion', 'admission_date')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.transfer.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'admission_date' => 'required|date',
            'leaving_date' => 'required|date|after_or_equal:admission_date',
            'reason_for_leaving' => 'required|string|max:255',
            'leaving_fees_upto' => 'required|string|max:255',
        ]);

        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $certificate = new TransferCertificate();
        $certificate->fill($request->all());
        $certificate->student_name = $student->name;
        $certificate->father_name = $student->father_name;
        $certificate->mother_name = $student->mother_name;
        $certificate->roll_no = $student->class_roll;
        if (!$request->admission_date && $student->admission_date) {
            $certificate->admission_date = $student->admission_date;
        }

        $certificate->tc_no = $request->tc_no ?? $certificate->generateTcNo();
        $certificate->issued_by = auth()->id();
        $certificate->save();

        return redirect()->route('certificates.transfer.index')
            ->with('success', 'Transfer Certificate created successfully');
    }

    public function show($id)
    {
        $certificate = TransferCertificate::with(['student', 'issuedBy'])->findOrFail($id);
        return view('BackEnd.student.certificates.transfer.show', compact('certificate'));
    }

    public function edit($id)
    {
        $certificate = TransferCertificate::findOrFail($id);
        $students = StudentInfoHsc::with('admitted_student')
            ->select('id', 'name', 'father_name', 'mother_name', 'class_roll', 'session', 'groups', 'birth_date', 'religion', 'admission_date')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('BackEnd.student.certificates.transfer.edit', compact('certificate', 'students'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:student_info_hsc,id',
            'issue_date' => 'required|date',
            'tc_no' => 'required|string',
            'admission_date' => 'required|date',
            'leaving_date' => 'required|date|after_or_equal:admission_date',
            'reason_for_leaving' => 'required|string|max:255',
            'leaving_fees_upto' => 'required|string|max:255'
        ]);

        $certificate = TransferCertificate::findOrFail($id);
        $student = StudentInfoHsc::with('admitted_student')->findOrFail($request->student_id);

        $certificate->fill($request->all());
        $certificate->student_name = $student->name;
        $certificate->father_name = $student->father_name;
        $certificate->mother_name = $student->mother_name;
        $certificate->roll_no = $student->class_roll;
        $certificate->save();

        return redirect()->route('certificates.transfer.index')
            ->with('success', 'Transfer Certificate updated successfully');
    }

    public function destroy($id)
    {
        $certificate = TransferCertificate::findOrFail($id);
        $certificate->delete();

        return redirect()->route('certificates.transfer.index')
            ->with('success', 'Transfer Certificate deleted successfully');
    }

    public function generatePdf($id)
    {
        try {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 120);

            $certificate = TransferCertificate::with(['student', 'issuedBy'])->findOrFail($id);

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
            $backgroundPath = public_path('img/tc_bg.jpg');
            if (file_exists($backgroundPath)) {
                $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
            }


            $html = view('certificates.transfer_certificate', compact('certificate'))->render();
            $mpdf->WriteHTML($html);

            $filename = 'transfer_certificate_' . str_replace('/', '_', $certificate->tc_no) . '.pdf';
            return $mpdf->Output($filename, 'I');
        } catch (\Exception $e) {
            // \Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    // Search functionality
    public function search(Request $request)
    {
        $query = TransferCertificate::with(['student', 'issuedBy']);

        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('tc_no')) {
            $query->where('tc_no', 'like', '%' . $request->tc_no . '%');
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leaving_date_from')) {
            $query->whereDate('leaving_date', '>=', $request->leaving_date_from);
        }

        if ($request->filled('leaving_date_to')) {
            $query->whereDate('leaving_date', '<=', $request->leaving_date_to);
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('BackEnd.student.certificates.transfer.index', compact('certificates'));
    }

    // Bulk operations
    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:transfer_certificates,id'
        ]);

        TransferCertificate::whereIn('id', $request->ids)->delete();

        return redirect()->route('certificates.transfer.index')
            ->with('success', count($request->ids) . ' transfer certificates deleted successfully');
    }

    public function bulkStatusUpdate(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:transfer_certificates,id',
            'status' => 'required|in:active,inactive,cancelled'
        ]);

        TransferCertificate::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('certificates.transfer.index')
            ->with('success', count($request->ids) . ' transfer certificates updated successfully');
    }

    // Batch PDF Download functionality
    public function bulkPdfDownload(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:transfer_certificates,id'
        ]);

        try {
            $certificates = TransferCertificate::with(['student', 'issuedBy'])
                ->whereIn('id', $request->ids)
                ->get();

            if ($certificates->isEmpty()) {
                return redirect()->back()->with('error', 'No transfer certificates found for download.');
            }

            // Create temporary directory for PDFs
            $tempDir = storage_path('app/temp/transfer_certificates_' . time());
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

                    $backgroundPath = public_path('img/tc_bg.jpg');
                    if (file_exists($backgroundPath)) {
                        $mpdf->SetDefaultBodyCSS('background', "url('$backgroundPath')");
                        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                        $mpdf->SetDefaultBodyCSS('background-image-resolution', 'from-image');
                    }

                    $html = view('certificates.transfer_certificate', compact('certificate'))->render();
                    $mpdf->WriteHTML($html);

                    $filename = 'transfer_certificate_' . str_replace('/', '_', $certificate->tc_no) . '.pdf';
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
            $zipFilename = 'transfer_certificates_batch_' . date('Y-m-d_H-i-s') . '.zip';
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
            'total' => TransferCertificate::count(),
            'active' => TransferCertificate::where('status', 'active')->count(),
            'inactive' => TransferCertificate::where('status', 'inactive')->count(),
            'cancelled' => TransferCertificate::where('status', 'cancelled')->count(),
            'this_month' => TransferCertificate::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_year' => TransferCertificate::whereYear('created_at', Carbon::now()->year)->count(),
            'dues_paid' => TransferCertificate::where('dues_paid', 'yes')->count(),
            'dues_pending' => TransferCertificate::where('dues_paid', 'no')->count(),
        ];

        return response()->json($stats);
    }

    // Export functionality
    public function export(Request $request)
    {
        $query = TransferCertificate::with(['student', 'issuedBy']);

        // Apply filters if provided
        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        $filename = 'transfer_certificates_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($certificates) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'TC No',
                'Student Name',
                'Father Name',
                'Mother Name',
                'Roll No',
                'Session',
                'Last Class',
                'Leaving Date',
                'Reason',
                'Dues Paid',
                'Issue Date',
                'Status'
            ]);

            foreach ($certificates as $certificate) {
                fputcsv($file, [
                    $certificate->tc_no,
                    $certificate->student_name,
                    $certificate->father_name,
                    $certificate->mother_name,
                    $certificate->roll_no,
                    $certificate->session,
                    $certificate->last_attended_class,
                    $certificate->leaving_date->format('Y-m-d'),
                    $certificate->reason_for_leaving,
                    ucfirst($certificate->dues_paid),
                    $certificate->issue_date->format('Y-m-d'),
                    ucfirst($certificate->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // CSV Upload functionality
    public function uploadForm()
    {
        return view('BackEnd.student.certificates.transfer.upload');
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
            $expectedHeaders = ['tc_no', 'student_id', 'issue_date', 'admission_date', 'leaving_date', 'reason_for_leaving', 'leaving_fees_upto'];

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
                    $certificate = new TransferCertificate();
                    $certificate->student_id = $data['student_id'];
                    $certificate->student_name = $student->name;
                    $certificate->father_name = $student->father_name;
                    $certificate->mother_name = $student->mother_name;
                    $certificate->roll_no = $student->class_roll;
                    $certificate->issue_date = ($data['issue_date']) ? Carbon::parse($data['issue_date']) : null;
                    $certificate->admission_date = ($data['admission_date']) ? Carbon::parse($data['admission_date']) : null;
                    $certificate->leaving_date = ($data['leaving_date']) ? Carbon::parse($data['leaving_date']) : null;
                    $certificate->reason_for_leaving = $data['reason_for_leaving'];
                    $certificate->leaving_fees_upto = $data['leaving_fees_upto'];
                    $certificate->tc_no = $data['tc_no'] ?? $certificate->generateTcNo();
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

            return redirect()->route('certificates.transfer.index')
                ->with($errorCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing CSV: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transfer_certificate_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'tc_no',
                'student_id',
                'issue_date',
                'admission_date',
                'leaving_date',
                'reason_for_leaving',
                'leaving_fees_upto'
            ]);

            // Sample data
            fputcsv($file, [
                'TC-123',
                '2020001',
                '2024-01-15',
                '2023-01-10',
                '2024-01-10',
                'Transfer to another institution',
                'January'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

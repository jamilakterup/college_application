<?php

namespace App\Http\Controllers\StudentPanel;

use Log;
use DB;
use Mpdf\Mpdf;
use Storage;
use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\StudentDocument;
use App\Models\DocumentPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentController extends Controller
{
    /**
     * Display a listing of available documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function documents()
    {
        $student = Auth::guard('student')->user();
        
        // Get all document types applicable to the student's course and session
        $availableDocumentTypes = DocumentType::where('course', $student->course)
            ->where('session', $student->session)
            ->where('is_active', true)
            ->get();
        
        // Get all paid documents for this student
        $paidDocuments = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'paid')
            ->with('documentType', 'studentDocument')
            ->get();
        
        // Create a collection of paid document type IDs
        $paidDocumentTypeIds = $paidDocuments->pluck('document_type_id')->toArray();
        
        // Group available documents by type (testimonial, prottoyon, character_certificate)
        $groupedDocuments = $availableDocumentTypes->groupBy('type');
        
        return view('student.document.index', compact('groupedDocuments', 'paidDocuments', 'paidDocumentTypeIds', 'student'));
    }
    
    /**
     * Display the document details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDocument($id)
    {
        $documentType = DocumentType::findOrFail($id);
        $student = Auth::user();
        
        // Check if the student has already paid for this document
        $payment = DocumentPayment::where('student_id', $student->id)
            ->where('document_type_id', $id)
            ->where('status', 'paid')
            ->first();
            
        // Get the document if it exists
        $document = StudentDocument::where('student_id', $student->id)
            ->where('document_type_id', $id)
            ->first();
            
        return view('student.document.details', compact('documentType', 'payment', 'document'));
    }
    
    /**
     * Download a document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument($id)
    {
        $document = StudentDocument::findOrFail($id);
        $student = Auth::guard('student')->user();

        if($document->status == 'Pending') {
            return redirect()->back()->with('error', 'Document is still pending approval. Please Contact to College Authority.');
        }
        
        // Check if the student owns this document
        if ($document->student_id !== $student->id) {
            return redirect()->back()->with('error', 'You do not have permission to download this document.');
        }
        
        // Check if the student has paid for this document
        $payment = DocumentPayment::where('student_id', $student->id)
            ->where('document_type_id', $document->document_type_id)
            ->where('status', 'paid')
            ->first();
            
        if (!$payment) {
            return redirect()->route('student.payment.create', $document->document_type_id)
                ->with('error', 'You need to pay for this document before downloading.');
        }
        
        // Check if the file exists
        if (!Storage::exists($document->file_path)) {
            // If the file doesn't exist, try to regenerate it
            try {
                $documentType = DocumentType::findOrFail($document->document_type_id);
                $invoice = Invoice::where('refference_id', $payment->id)
                    ->where('reference_model', DocumentPayment::class)
                    ->where('roll', $student->class_roll)
                    ->latest()
                    ->first();

                if ($invoice) {
                    $filePath = $this->generateDocumentPdf($student, $documentType, $invoice);
                    // Update document record with new file path
                    $document->file_path = $filePath;
                    $document->file_name = basename($filePath);
                    $document->save();
                } else {
                    return redirect()->back()->with('error', 'Document file could not be generated. Please contact support.');
                }
            } catch (\Exception $e) {
                \Log::error('Error regenerating document for download: ' . $e->getMessage(), [
                    'document_id' => $document->id,
                    'student_id' => $student->id,
                    'exception' => $e
                ]);
                
                return redirect()->back()->with('error', 'Document file could not be found or generated. Please contact support.');
            }
        }
        
        // Increment download count
        $payment->download_count = ($payment->download_count ?? 0) + 1;
        $payment->save();
        
        // Return the file for download
        return Storage::download(
            $document->file_path, 
            $document->file_name ?? 'document.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
    
    /**
     * Show the payment form.
     *
     * @param  int  $documentTypeId
     * @return \Illuminate\Http\Response
     */
    public function createPayment($documentTypeId)
    {
        $documentType = DocumentType::findOrFail($documentTypeId);
        $student = Auth::user();
        
        // Check if there's an existing valid payment
        $existingPayment = DocumentPayment::where('student_id', $student->id)
            ->where('document_type_id', $documentTypeId)
            ->where('status', 'paid')
            ->first();
            
        if ($existingPayment) {
            return redirect()->route('student.document.show', $documentTypeId)
                ->with('info', 'You have already paid for this document.');
        }

        // Check if a document payment exists or create a new one
        $documentPayment = DocumentPayment::where('student_id', $student->id)
            ->where('document_type_id', $documentTypeId)
            ->first();
        
        if (!$documentPayment) {
            $documentPayment = new DocumentPayment();
            $documentPayment->student_id = $student->id;
            $documentPayment->document_type_id = $documentTypeId;
            $documentPayment->amount = $documentType->price;
            $documentPayment->status = 'pending';
            $documentPayment->transaction_id = 'TRX-' . time() . '-' . $student->class_roll;
            $documentPayment->status = 'pending';
            $documentPayment->save();
        }

        // Check if an invoice exists
        $invoice = Invoice::where('refference_id', $documentPayment->id)
            ->where('reference_model', DocumentPayment::class)
            ->where('roll', $student->class_roll)
            ->latest()
            ->first();
        
        // If invoice doesn't exist, create a new one
        if (!$invoice) {
            $invoice = new Invoice();
            $invoice->refference_id = $documentPayment->id;
            $invoice->reference_model = DocumentPayment::class;
            $invoice->roll = $student->class_roll;
            $invoice->name = $student->name;
            $invoice->father_name = $student->fathers_name ?? null;
            $invoice->mobile = $student->mobile ?? null;
            $invoice->type = 'others_fee';
            $invoice->auto_id = 'DOC-' . time() . '-' . $student->id;
            $invoice->total_amount = $documentType->price;
            $invoice->trx_id = $documentPayment->transaction_id;
            $invoice->registration_no = $student->registration_no ?? '';
            $invoice->student_id = $student->id;
            $invoice->student_type = $student->student_type ?? null;
            $invoice->remarks = 'Document Request: ' . $documentType->title;
            $invoice->date_start = now()->format('Y-m-d');
            $invoice->date_end = now()->addDays(7)->format('Y-m-d');
            $invoice->institute_code = 'naogc';
            $invoice->status = 'Pending';
            $invoice->slip_name = $documentType->title;
            $invoice->slip_type = 'document';
            $invoice->pro_group = $student->department ?? '';
            $invoice->subject = $student->subject ?? null;
            $invoice->level = $student->level ?? null;
            $invoice->admission_session = $student->session ?? '';
            $invoice->update_date = now();
            $invoice->save();
            
            $documentPayment->invoice_id = $invoice->id;
            $documentPayment->save();
        }
        
        return view('student.document.payment', compact('documentType', 'invoice', 'student', 'documentPayment'));
    }
    
    /**
     * Process the payment confirmation with database transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $documentTypeId
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment(Request $request, $documentTypeId)
    {
        $documentType = DocumentType::findOrFail($documentTypeId);
        $student = Auth::guard('student')->user();
        
        // Find existing document payment
        $documentPayment = DocumentPayment::where('student_id', $student->id)
            ->where('document_type_id', $documentTypeId)
            ->where('status', 'pending')
            ->latest()
            ->first();
            
        if (!$documentPayment) {
            return redirect()->back()->with('error', 'Payment record not found. Please try again.');
        }
        
        // Find the associated invoice
        $invoice = Invoice::where('refference_id', $documentPayment->id)
            ->where('reference_model', DocumentPayment::class)
            ->where('roll', $student->class_roll)
            ->latest()
            ->first();
        
        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found. Please try again.');
        }

        // Begin database transaction
        try {
            DB::beginTransaction();

            if ($invoice->status == 'Paid') {
                // Update payment status
                $documentPayment->status = 'paid';
                $documentPayment->paid_at = Carbon::now();
                $documentPayment->download_count = 0; // Initialize download count
                $documentPayment->save();

                // Find the document if it exists or create a new one
                $document = StudentDocument::where('student_id', $student->id)
                    ->where('document_type_id', $documentTypeId)
                    ->first();
                
                if (!$document) {
                    // Create a new document record
                    $document = new StudentDocument();
                    $document->student_id = $student->id;
                    $document->document_type_id = $documentTypeId;
                    $document->status = 'pending'; // Document is immediately pending for approval
                    
                    try {
                        // Generate PDF document
                        $filePath = $this->generateDocumentPdf($student, $documentType, $invoice);
                        
                        // Set file information
                        $document->file_path = $filePath;
                        $document->file_name = basename($filePath);
                        
                        // Store metadata about the document
                        $metadata = [
                            'payment_id' => $documentPayment->id,
                            'invoice_id' => $invoice->id,
                            'generated_at' => Carbon::now()->toDateTimeString(),
                            'document_type' => $documentType->type,
                            'document_title' => $documentType->title,
                        ];
                        
                        $document->metadata = json_encode($metadata);
                        $document->save();
                        
                        // Update the document payment with the document ID
                        $documentPayment->student_document_id = $document->id;
                        $documentPayment->save();
                    } catch (\Exception $e) {
                        // Log the error but continue with the transaction
                        \Log::error('Error generating document PDF: ' . $e->getMessage(), [
                            'student_id' => $student->id,
                            'document_type_id' => $documentTypeId,
                            'exception' => $e
                        ]);
                        
                        // Set document as pending generation
                        $document->status = 'pending_generation';
                        $document->save();
                    }
                }
                
                // Commit the transaction
                DB::commit();
                
                return redirect()->route('student.document.show', $documentTypeId)
                    ->with('success', 'Payment successful! Your document is ready for download.');
            } else {
                // If payment is not successful, rollback any changes
                DB::rollBack();
                
                // Log the failed payment attempt
                \Log::warning('Payment confirmation failed', [
                    'student_id' => $student->id,
                    'document_type_id' => $documentTypeId,
                    'invoice_id' => $invoice->id,
                    'invoice_status' => $invoice->status
                ]);
                
                return redirect()->back()->with('error', 'Payment failed. Please try again.');
            }
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollBack();
            
            // Log the error
            \Log::error('Error during payment confirmation: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'document_type_id' => $documentTypeId,
                'exception' => $e
            ]);
            
            return redirect()->back()->with('error', 'An error occurred during payment processing. Please try again or contact support.');
        }
    }

    /**
     * Generate a PDF document using mPDF and delete the QR code file after output.
     *
     * @param mixed $student
     * @param DocumentType $documentType
     * @param Invoice $invoice
     * @return string The file path of the generated PDF
     * @throws \Exception
     */
    protected function generateDocumentPdf($student, DocumentType $documentType, Invoice $invoice): string
    {
        try {
            // Initialize mPDF with Bengali font support
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'margin_header' => 10,
                'margin_footer' => 10,
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'fontDir' => array_merge(
                    (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                    [public_path('fonts')]
                ),
                'fontdata' => array_merge(
                    (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'],
                    [
                        'bangla' => [
                            'R'  => 'SolaimanLipi_20-04-07.ttf',
                            'B'  => 'SolaimanLipi_Bold_10-03-12.ttf',
                            'I'  => 'SolaimanLipi_20-04-07.ttf',
                            'useOTL' => 0xFF,
                            'useKashida' => 75,
                        ],
                        'garamond' => ['R' => 'garamond.ttf'],
                    ]
                ),
                'default_font' => 'bangla',
            ]);

            // Set document metadata
            $mpdf->SetTitle("{$documentType->title} - {$student->name}");
            $mpdf->SetAuthor(config('app.name'));
            $mpdf->SetCreator(config('app.name'));
            $mpdf->ignore_invalid_utf8 = true;
            $mpdf->showImageErrors = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            // Set watermark
            $logoPath = public_path('upload/sites/' . config('settings.site_logo'));
            if (file_exists($logoPath)) {
                $mpdf->SetWatermarkImage(asset('upload/sites/' . config('settings.site_logo')), 0.09, [150, 150]);
                $mpdf->showWatermarkImage = true;
            }

            // Get college logo
            $logoData = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';

            // Get principal signature
            $signaturePath = public_path('img/principal_sig.png');

            // Get student photo
            $studentPhoto = $student->photo && Storage::exists($student->photo)
                ? 'data:image/jpeg;base64,' . base64_encode(Storage::get($student->photo))
                : '';

            // Generate unique document ID
            $documentId = "DOC-{$documentType->id}-{$student->id}-" . time();

            // Generate QR code
            $qrContent = "Student: {$student->name}, Roll: " . ($student->roll ?? $student->roll_number ?? $student->class_roll ?? 'N/A') .
                ", Reg: " . ($student->registration_no ?? $student->registration_number ?? 'N/A') .
                ", Certificate ID: {$documentId}";
            $qrCodePath = "qrcodes/{$documentId}.png";
            $qrCodeFullPath = storage_path("app/public/{$qrCodePath}");

            // Ensure directory exists
            if (!file_exists(dirname($qrCodeFullPath))) {
                mkdir(dirname($qrCodeFullPath), 0755, true);
            }

            // Generate and save QR code as PNG
            QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($qrContent, $qrCodeFullPath);

            // Generate HTML content based on document type
            $html = $this->getDocumentHtmlTemplate($documentType->type, [
                'student' => $student,
                'documentType' => $documentType,
                'invoice' => $invoice,
                'logo' => $logoData,
                'signature' => $signaturePath,
                'qrCodePath' => asset('storage/' . $qrCodePath),
                'studentPhoto' => $studentPhoto,
                'date' => Carbon::now()->format('d F, Y'),
                'documentId' => $documentId,
            ]);

            // Write HTML to the PDF
            $mpdf->WriteHTML($html);

            // Generate a unique filename for the PDF
            $filename = strtolower("{$documentType->type}_{$student->class_roll}_" . time() . '.pdf');
            $filePath = "public/documents/{$filename}";

            // Save the PDF to storage
            $pdfContent = $mpdf->Output('', 'S');
            Storage::put($filePath, $pdfContent);

            // Delete the QR code file after PDF generation
            try {
                if (Storage::exists($qrCodePath)) {
                    Storage::delete($qrCodePath);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete QR code file: ' . $e->getMessage(), [
                    'qr_code_path' => $qrCodePath,
                    'student_id' => $student->id,
                    'document_type_id' => $documentType->id,
                ]);
            }

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'document_type_id' => $documentType->id,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    /**
     * Get the HTML template for a specific document type
     *
     * @param string $documentType
     * @param array $data
     * @return string
     */
    protected function getDocumentHtmlTemplate($documentType, $data)
    {
        // Get the student's course from the data
        $studentCourse = strtolower($data['student']->course ?? 'default');
        
        // Map document types to their respective blade templates based on course
        $templateMap = [
            'hsc' => [
                'testimonial' => 'pdf.document.hsc.testimonial',
                'character_certificate' => 'pdf.document.character_certificate',
                'prottoyon' => 'pdf.document.hsc.prottoyon',
                'transcript' => 'pdf.document.hsc.transcript',
                'provisional_certificate' => 'pdf.document.hsc.provisional_certificate',
            ],
            'honours' => [
                'testimonial' => 'pdf.document.honours.testimonial',
                'character_certificate' => 'pdf.document.honours.character_certificate',
                'prottoyon' => 'pdf.document.honours.prottoyon',
                'transcript' => 'pdf.document.honours.transcript',
                'provisional_certificate' => 'pdf.document.honours.provisional_certificate',
            ],
            'masters' => [
                'testimonial' => 'pdf.document.masters.testimonial',
                'character_certificate' => 'pdf.document.masters.character_certificate',
                'prottoyon' => 'pdf.document.masters.prottoyon',
                'transcript' => 'pdf.document.masters.transcript',
                'provisional_certificate' => 'pdf.document.masters.provisional_certificate',
            ],
            'degree' => [
                'testimonial' => 'pdf.document.degree.testimonial',
                'character_certificate' => 'pdf.document.degree.character_certificate',
                'prottoyon' => 'pdf.document.degree.prottoyon',
                'transcript' => 'pdf.document.degree.transcript',
                'provisional_certificate' => 'pdf.document.degree.provisional_certificate',
            ],
            // Default templates for any course not specifically defined
            'default' => [
                'testimonial' => 'pdf.document.default.testimonial',
                'character_certificate' => 'pdf.document.default.character_certificate',
                'prottoyon' => 'pdf.document.default.prottoyon',
                'transcript' => 'pdf.document.default.transcript',
                'provisional_certificate' => 'pdf.document.default.provisional_certificate',
            ]
        ];

        // Try to get the course-specific template
        if (isset($templateMap[$studentCourse][$documentType])) {
            $templateName = $templateMap[$studentCourse][$documentType];
        }
        // Fall back to default course templates if specific course exists but template doesn't
        elseif (isset($templateMap[$studentCourse])) {
            $templateName = $templateMap['default'][$documentType] ?? 'pdf.document.default.document';
        }
        // Fall back to generic templates if neither course nor template exists
        else {
            $templateName = $templateMap['default'][$documentType] ?? 'pdf.document.default.document';
        }

        // Check if the view exists
        if (!view()->exists($templateName)) {
            // Log that we're using a fallback template
            \Log::info("Template {$templateName} not found. Using default template.", [
                'document_type' => $documentType,
                'student_course' => $studentCourse
            ]);
            
            // Use the most generic fallback
            $templateName = 'pdf.document.default.document';
            
            // If even that doesn't exist, create a very basic template on the fly
            if (!view()->exists($templateName)) {
                return $this->generateBasicTemplate($data);
            }
        }
        // Render the blade template with the provided data
        return view($templateName, $data)->render();
    }

    /**
     * Generate a basic template as a last resort if no template files exist
     *
     * @param array $data
     * @return string
     */
    protected function generateBasicTemplate($data)
    {
        $student = $data['student'];
        $documentType = $data['documentType'];
        $logo = $data['logo'] ?? '';
        $signature = $data['signature'] ?? '';
        $date = $data['date'] ?? date('d F, Y');
        $documentId = $data['documentId'] ?? 'DOC-' . time();
        
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>' . $documentType->title . '</title>
            <style>
                body { font-family: sans-serif; line-height: 1.6; color: #333; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { max-width: 80px; margin-bottom: 10px; }
                .title { font-size: 20px; font-weight: bold; text-align: center; margin: 20px 0; }
                .content { margin: 20px 0; }
                .signature { margin-top: 50px; text-align: right; }
                .footer { text-align: center; font-size: 12px; color: #777; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                ' . ($logo ? '<img src="' . $logo . '" alt="Logo" class="logo">' : '') . '
                <h1>' . config('app.name', 'College Name') . '</h1>
                <p>' . config('app.address', 'College Address') . '</p>
            </div>
            
            <div class="title">' . $documentType->title . '</div>
            
            <div class="content">
                <p>This is to certify that <strong>' . $student->name . '</strong>, 
                son/daughter of <strong>' . ($student->fathers_name ?? 'N/A') . '</strong>, 
                is a student of this institution with ID <strong>' . $student->class_roll . '</strong>.</p>
                
                <p>Department: ' . ($student->department ?? 'N/A') . '<br>
                Session: ' . ($student->session ?? 'N/A') . '<br>
                Registration No: ' . ($student->registration_no ?? 'N/A') . '</p>
                
                <p>This document is issued upon request on ' . $date . '.</p>
            </div>
            
            <div class="signature">
                ' . ($signature ? '<img src="' . $signature . '" alt="Signature" style="max-width: 150px;">' : '') . '
                <p>Principal<br>' . config('app.name', 'College Name') . '</p>
            </div>
            
            <div class="footer">
                <p>Document ID: ' . $documentId . '<br>
                This document is electronically generated and does not require a physical signature.</p>
            </div>
        </body>
        </html>';
    }
}

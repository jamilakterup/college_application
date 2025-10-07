<?php
        
namespace App\Http\Controllers;

use DB;
use Mpdf\Mpdf;
use App\Models\Invoice;
use App\Models\FeesApplication;
use App\Models\PayslipHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeesPaymentController extends Controller
{
    public $config, $courseLevelOptions, $groupSubjectOptions;

    function __construct(){
        $this->config = DB::table('fees_configurations')->first();
        if (!$this->config) {
            abort(404, 'Fees payment configuration not found.');
        }
        $this->middleware(function ($request, $next) {
            if ($this->config->status == 0) {
                abort(402, 'Fees payment is currently disabled.');
            }
            return $next($request);
        });

        $this->loadCourseLevelOptions();
    }

    private function loadCourseLevelOptions()
    {
        $this->courseLevelOptions = json_decode($this->config->levels, true) ?? [];

        if (empty($this->courseLevelOptions)) {
            abort(404, 'No course levels available.');
        }

        $this->courseLevelOptions = array_combine($this->courseLevelOptions, $this->courseLevelOptions);
    }

    public function index()
    {
        return view('fees-payment.index', ['config' => $this->config, 'courseLevelOptions' => $this->courseLevelOptions]);
    }

    /**
     * Get the fields used for eligibility checking
     * @return array
     */
    public function getEligibilityCheckerFields(){
        return ['registration_id', 'current_level', 'academic_session'];
    }

    public function checkEligibility(Request $request)
    {
        $this->validate($request, [
            'registration_id' => 'required|numeric',
            'current_level' => 'required|in:' . implode(',', array_keys($this->courseLevelOptions)),
            'academic_session' => 'required'
        ]);

        // Format dates for comparison
        $startDate = date('Y-m-d', strtotime($this->config->opening_date));
        $endDate = date('Y-m-d', strtotime($this->config->clossing_date));
        $currentDate = date('Y-m-d');

        // Default to not eligible
        $isEligible = false;
        $eligibilityMessage = 'Fees payment is currently disabled.';

        // Check eligibility based on status and date range
        if ($this->config->status == 1) {
            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                $isEligible = true;
                $eligibilityMessage = 'You are eligible to pay fees.';
            } else {
                $eligibilityMessage = 'Fees payment is not available during this period.';
            }
        }

        // If not eligible, forget any existing eligibility data to prevent stale data
        if (!$isEligible) {
            session()->forget('eligibleData');
            return redirect()->back()
                ->withInput()
                ->with('error', $eligibilityMessage);
        }

        // Get eligibility checker fields
        $eligibilityFields = $this->getEligibilityCheckerFields();

        // Prepare session data
        $sessionData = [
            'is_eligible' => $isEligible,
            'opening_date' => $startDate,
            'clossing_date' => $endDate, // Fixed typo
            'current_date' => $currentDate
        ];

        // Add eligibility fields to session data
        foreach ($eligibilityFields as $field) {
            $sessionData[$field] = $request->input($field);
        }

        session()->put('eligibleData', $sessionData);

        // Check if there's an existing paid application
        try {
            $query = FeesApplication::with('invoice')->where('status', 'Paid');
            // Build the query for JSON conditions
            $query->where(function ($q) use ($request, $eligibilityFields) {
                foreach ($eligibilityFields as $field) {
                    $value = trim($request->input($field));
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, ?)) = ?", ["$.\"{$field}\"", (string) $value]);
                }
            });

            $paidApplication = $query->latest()->first();

            if ($paidApplication) {
                $eligibilityMessage = 'Download Your Payslip';
                return redirect()->route('fees-payment.confirmation', ['application_id' => $paidApplication->id])
                    ->with('success', $eligibilityMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Error checking for paid applications: ' . $e->getMessage());
        }

        return redirect()->route('fees-payment.form')
            ->with('success', $eligibilityMessage);
    }

    public function showForm()
    {
        $eligibleData = session()->get('eligibleData');

        if (!$eligibleData) {
            return redirect()->route('fees-payment.index')
                            ->with('error', 'You are not eligible to pay fees.');
        }
        
        // Get form fields from configuration
        $formFields = json_decode($this->config->form_fields, true) ?? [];

        if (empty($formFields)) {
            abort(402, 'Form fields not found.');
        }

        // Prepare field configuration with validation rules
        $fieldConfig = [];
        $requiredFields = json_decode($this->config->required_fields ?? '[]', true) ?? [];
        
        foreach ($formFields as $field) {
            $fieldConfig[$field] = [
                'name' => $field,
                'label' => ucwords(str_replace('_', ' ', $field)),
                'required' => in_array($field, $requiredFields),
                'type' => $this->getInputType($field)
            ];
        }

        $this->loadFormParticles();

        return view('fees-payment.form', [
            'config' => $this->config,
            'eligibleData' => $eligibleData,
            'fieldConfig' => $fieldConfig,
            'groupSubjectOptions' => $this->groupSubjectOptions
        ]);
    }

    private function loadFormParticles()
    {
        $level = session()->get('eligibleData.current_level', '');
        
        // Set options based on education level keywords
        if (str_contains($level, 'HSC')) {
            $this->groupSubjectOptions = selective_hsc_groups();
        } elseif (preg_match('/(Honours|Masters)/', $level)) {
            $this->groupSubjectOptions = selective_multiple_subject();
        }elseif (preg_match('/(Degree)/', $level)) {
            $this->groupSubjectOptions = selective_degree_subjects();
        } else {
            $this->groupSubjectOptions = ['science' => 'Science', 'arts' => 'Arts', 'commerce' => 'Commerce'];
        }
    }

    /**
     * Process the submitted form
     */
    public function submitForm(Request $request)
    {
        $eligibleData = session()->get('eligibleData');

        if (!$eligibleData) {
            return redirect()->route('fees-payment.index')
                            ->with('error', 'You are not eligible to pay fees.');
        }

        // Get form fields and required fields from configuration
        $formFields = json_decode($this->config->form_fields, true) ?? [];
        $requiredFields = json_decode($this->config->required_fields ?? '[]', true) ?? [];

        // Build validation rules
        $rules = [];
        foreach ($formFields as $field) {
            if (in_array($field, $requiredFields)) {
                $rules[$field] = 'required';
                
                // Add specific validation rules based on field type
                if ($field === 'mobile') {
                    $rules[$field] .= '|numeric|digits_between:10,15';
                } elseif ($field === 'date_of_birth') {
                    $rules[$field] .= '|date|before:today';
                } elseif ($field === 'email') {
                    $rules[$field] .= '|email';
                }
            }
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        // Extract eligibility checker fields for reference
        $referenceData = [];
        foreach ($this->getEligibilityCheckerFields() as $field) {
            if (isset($eligibleData[$field])) {
                $referenceData[$field] = $eligibleData[$field];
            }
        }

        try {
            // Create new fees application
            $feesApplication = new FeesApplication();
            $feesApplication->name = $request->name;
            $feesApplication->father_name = $request->father_name;
            $feesApplication->mother_name = $request->mother_name;
            $feesApplication->date_of_birth = $request->date_of_birth ? date('Y-m-d', strtotime($request->date_of_birth)) : null;
            $feesApplication->gender = $request->gender;
            $feesApplication->mobile = $request->mobile;
            $feesApplication->group_dept = $request->group_dept;
            $feesApplication->reference_data = json_encode($referenceData);
            $feesApplication->status = 'Pending';
            $feesApplication->save();

            return redirect()->route('fees-payment.payment-view', ['application_id' => $feesApplication->id])
                            ->with('success', 'Your fees payment information has been submitted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating fees application: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'An error occurred while processing your application. Please try again.')
                            ->withInput();
        }
    }

    public function paymentView(Request $request)
{
    $applicationId = $request->application_id;
    
    try {
        // Validate application_id
        if (!$applicationId) {
            throw new \Exception('Application ID is required');
        }

        // Fetch fees application
        $feesApplication = FeesApplication::findOrFail($applicationId);
        
        // Decode JSON reference data
        $referenceData = json_decode($feesApplication->reference_data, true);
        
        if (empty($referenceData)) {
            throw new \Exception('Invalid or empty reference data');
        }

        // Extract reference data
        $academicSession = $referenceData['academic_session'] ?? null;
        $currentLevel = $referenceData['current_level'] ?? null;
        $groupDept = $feesApplication->group_dept ?? null;

        // Fetch and filter records
        $headers = PayslipHeader::with(['payslipgenerators', 'payslipitems'])
        ->where('type', 'fees_payment')
        ->when($academicSession, function ($query) use ($academicSession) {
            $query->where(function ($q) use ($academicSession) {
                $q->where('session', '0')
                ->orWhere('session', $academicSession)
                ->orWhere('session', 'like', "%{$academicSession}%");
            });
        })
        ->when($groupDept, function ($query) use ($groupDept) {
            $query->where(function ($q) use ($groupDept) {
                $q->where('group_dept', '0')
                ->orWhere('group_dept', $groupDept)
                ->orWhere('group_dept', 'like', "%{$groupDept}%")
                ->orWhere('subject', '0')
                ->orWhere('subject', $groupDept)
                ->orWhere('subject', 'like', "%{$groupDept}%");
            });
        })
        ->when($currentLevel, function ($query) use ($currentLevel) {
            $query->where(function ($q) use ($currentLevel) {
                $q->where('level', '0')
                ->orWhere('level', $currentLevel)
                ->orWhere('level', 'like', "%{$currentLevel}%");
            });
        })
        ->get();

        return view('fees-payment.payment-view', [
            'config' => $this->config,
            'headers' => $headers,
            'feesApplication' => $feesApplication
        ]);

    } catch (\Exception $e) {
        dd($e->getMessage());
        \Log::error('Error in payment view: ' . $e->getMessage(), [
            'application_id' => $applicationId,
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('fees-payment.index')
            ->with('error', 'An error occurred while loading payment options. Please try again.');
    }
}

    public function submitPaymentInformation(Request $request)
    {
        $this->validate($request, [
            'header_id' => 'required|exists:payslipheaders,id',
            'fees_application_id' => 'required|exists:fees_applications,id'
        ]);

        try {
            $headerId = $request->header_id;
            $feesApplication = FeesApplication::findOrFail($request->fees_application_id);
            $header = PayslipHeader::with('payslipgenerators', 'payslipitems')->findOrFail($headerId);

            $invoice = new Invoice();
            $invoice->name = $feesApplication->name;
            $invoice->father_name = $feesApplication->father_name ?? null;
            $invoice->header_id = $headerId;
            $invoice->type = 'fees_payment';
            $invoice->total_amount = $header->payslipgenerators->sum('fees');
            
            // Decode the JSON reference data
            $referenceData = json_decode($feesApplication->reference_data);
            
            $invoice->roll = $referenceData->registration_id ?? null;
            $invoice->admission_session = $referenceData->academic_session ?? null;
            $invoice->mobile = $feesApplication->mobile ?? null;
            $invoice->level = $referenceData->current_level ?? null;
            $invoice->reference_model = 'App\Models\FeesApplication';
            $invoice->refference_id = $feesApplication->id;
            $invoice->date_start = $this->config->opening_date;
            $invoice->date_end = $this->config->clossing_date;
            $invoice->save();

            return redirect()->route('fees-payment.confirmation', ['application_id' => $feesApplication->id]);
        } catch (\Exception $e) {
            \Log::error('Error in submit payment: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'An error occurred while processing your payment. Please try again.')
                            ->withInput();
        }
    }

    public function confirmation(Request $request)
    {
        $applicationId = $request->application_id;
        try {
            $feesApplication = FeesApplication::with('invoice')->findOrFail($applicationId);
            if($feesApplication->status == 'Pending'){
                session()->flash('warning', 'Your payment is not completed. Please make payment first.');
            }
            return view('fees-payment.confirmation', [
                'config' => $this->config,
                'feesApplication' => $feesApplication,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in confirmation: ' . $e->getMessage());
            return redirect()->route('fees-payment.index')
                            ->with('error', 'An error occurred while loading your confirmation. Please check your payment status.');
        }
    }

    public function downloadSlip(Request $request)
    {
        // Validate the request
        $request->validate([
            'application_id' => 'required|exists:fees_applications,id',
        ]);

        // Fetch the fees application
        $feesApplication = FeesApplication::findOrFail($request->application_id);

        // Check if the application is paid
        if ($feesApplication->status !== 'Paid') {
            abort(402, 'Payment not completed');
        }

        // Initialize mPDF
        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'default_font' => 'times',
            ]);
            $mpdf->ignore_invalid_utf8 = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;

            // Generate HTML from view
            $html = view('fees-payment.download-slip', compact('feesApplication'))->render();

            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            // Define file path and name
            $filename = $feesApplication->id . '_fees_payment_slip.pdf';
            $file_path = public_path('download/files/' . $filename);

            // Ensure the directory exists
            $directory = dirname($file_path);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save PDF to file
            $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);

            // Force download and delete file after sending
            return response()->download($file_path, $filename)->deleteFileAfterSend(true);
        } catch (\Mpdf\MpdfException $e) {
            \Log::error('mPDF error: ' . $e->getMessage());
            abort(500, 'Error generating PDF');
        } catch (\Exception $e) {
            \Log::error('Error in downloadSlip: ' . $e->getMessage());
            abort(500, 'An unexpected error occurred');
        }
    }

    /**
     * Determine the appropriate input type for a field
     */
    private function getInputType($field)
    {
        switch ($field) {
            case 'date_of_birth':
                return 'date';
            case 'gender':
                return 'select';
            case 'mobile':
                return 'tel';
            case 'email':
                return 'email';
            case 'group_dept':
                return 'select';
            default:
                return 'text';
        }
    }
}
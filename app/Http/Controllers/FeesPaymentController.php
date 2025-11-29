<?php
        
namespace App\Http\Controllers;

use DB;
use Mpdf\Mpdf;
use App\Models\Invoice;
use App\Models\FeesApplication;
use App\Models\PayslipHeader;
use App\Models\FeesConfiguration;
use App\Models\Student;
use App\Services\Fees\FeesEligibilityIntegrationService;
use App\Services\PaymentActionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class FeesPaymentController extends Controller
{
    public $groupSubjectOptions, $eligibleRecord;

    function __construct(){
        // No longer load a single config in constructor
        // Configuration will be selected by user or loaded from session
    }

    /**
     * Get the currently selected configuration from session
     * @return FeesConfiguration|null
     */
    private function getSelectedConfig()
    {
        $configId = session()->get('selected_config_id');
        if (!$configId) {
            return null;
        }
        return FeesConfiguration::find($configId);
    }

    /**
     * Ensure a configuration is selected, redirect to index if not
     */
    private function requireSelectedConfig()
    {
        $config = $this->getSelectedConfig();
        if (!$config) {
            abort(404, 'Please select a fees configuration first.');
        }
        return $config;
    }

    /**
     * Show list of active configurations for user to select
     */
    public function index()
    {
        // Get all active configurations
        $configurations = FeesConfiguration::where('status', 1)->get();
        
        if ($configurations->isEmpty()) {
            abort(404, 'No active fees payment configurations available at this time.');
        }
        
        return view('fees-payment.select-configuration', [
            'configurations' => $configurations
        ]);
    }

    /**
     * Handle configuration selection
     */
    public function selectConfiguration(Request $request)
    {
        $this->validate($request, [
            'configuration_id' => 'required|exists:fees_configurations,id'
        ]);
        
        $config = FeesConfiguration::findOrFail($request->configuration_id);
        
        // Verify configuration is active and within date range
        if ($config->status != 1) {
            return redirect()->route('fees-payment.index')
                ->with('error', 'The selected configuration is not currently active.');
        }
        
        $now = now();
        if ($now->lt($config->opening_date) || $now->gt($config->clossing_date)) {
            return redirect()->route('fees-payment.index')
                ->with('error', 'The selected configuration is not available at this time.');
        }
        
        // Store selected configuration in session
        session()->put('selected_config_id', $config->id);
        
        // Clear any previous eligibility data
        session()->forget(['eligibleQueryData', 'eligibleRecord']);
        
        return redirect()->route('fees-payment.eligibility-form')
            ->with('success', 'Payment header is selected. Please enter your details to check eligibility.');
    }

    /**
     * Show eligibility check form for selected configuration
     */
    public function eligibilityForm()
    {
        $config = $this->requireSelectedConfig();
        
        return view('fees-payment.index', [
            'config' => $config
        ]);
    }

    /**
     * Get the fields used for eligibility checking
     * @return array
     */
    public function getEligibilityCheckerFields(){
        return ['reference_id', 'configuration_id'];
    }

    public function checkEligibility(Request $request)
    {
        $config = $this->requireSelectedConfig();
        
        $this->validate($request, [
            'reference_id' => 'required|numeric'
        ]);

        // Format dates for comparison
        $startDate = $config->opening_date->format('Y-m-d');
        $endDate = $config->clossing_date->format('Y-m-d');
        $currentDate = date('Y-m-d');

        // Default to not eligible
        $isEligible = false;
        $eligibilityMessage = 'Fees payment is currently disabled.';

        // Check eligibility based on status and date range
        if ($config->status == 1) {
            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                $isEligible = true;
                $eligibilityMessage = 'You are eligible to pay fees.';
            } else {
                $eligibilityMessage = 'Fees payment is not available during this period.';
            }
        }

        if ($config->check_eligible_list) {
            // Get the course model class from configuration
            $courseClass = $config->course;
            
            // Try new FeesEligibility system first, then fall back to old FeesEligible
            $eligibleRecord = FeesEligibilityIntegrationService::checkEligibility(
                $request->reference_id,
                $config->academic_session,
                $config->level,
                $courseClass
            );
        
            if (!$eligibleRecord) {
                $isEligible = false;
                $eligibilityMessage = 'You are not Eligible for Fees Payment.';
            } else {
                // Check session match with configuration
                if (isset($eligibleRecord->academic_session) && $eligibleRecord->academic_session !== $config->academic_session) {
                    $isEligible = false;
                    $eligibilityMessage = 'Session Not Matched for your ID.';
                } 
                // Check level match with configuration
                elseif (isset($eligibleRecord->current_level) && $eligibleRecord->current_level !== $config->level) {
                    $isEligible = false;
                    $eligibilityMessage = 'Level Not Matched for your ID.';
                }
                else {
                    // Store eligible record in session
                    session()->put('eligibleRecord', $eligibleRecord);
                }
            }
        }

        // If not eligible, forget any existing eligibility data to prevent stale data
        if (!$isEligible) {
            session()->forget('eligibleQueryData');
            session()->forget('eligibleRecord');
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
            'clossing_date' => $endDate,
            'current_date' => $currentDate,
            'configuration_id' => $config->id,
            'reference_id' => $request->reference_id,
            'current_level' => $config->level,
            'academic_session' => $config->academic_session
        ];

        session()->put('eligibleQueryData', $sessionData);
        
        // Check if there's an existing paid application for this configuration and reference
        try {
            $query = FeesApplication::with('invoice')->where('status', 'Paid');
            // Build the query for JSON conditions
            $query->where(function ($q) use ($request, $config) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.reference_id')) = ?", [(string) $request->reference_id])
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.configuration_id')) = ?", [(string) $config->id]);
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
        $config = $this->requireSelectedConfig();
        $eligibleQueryData = session()->get('eligibleQueryData');
        $this->eligibleRecord = session()->get('eligibleRecord');

        if (!$eligibleQueryData) {
            return redirect()->route('fees-payment.index')
                            ->with('error', 'You are not eligible to pay fees.');
        }
        
        // Get form fields from configuration
        $formFields = is_array($config->form_fields) ? $config->form_fields : (json_decode($config->form_fields, true) ?? []);

        if (empty($formFields)) {
            abort(402, 'Form fields not found.');
        }

        // Prepare field configuration with validation rules
        $fieldConfig = [];
        $requiredFields = is_array($config->required_fields) ? $config->required_fields : (json_decode($config->required_fields ?? '[]', true) ?? []);
        
        foreach ($formFields as $field) {
            $fieldConfig[$field] = [
                'name' => $field,
                'label' => ucwords(str_replace('_', ' ', $field)),
                'required' => in_array($field, $requiredFields),
                'type' => $this->getInputType($field)
            ];
        }

        $this->loadFormParticles($config);

        return view('fees-payment.form', [
            'config' => $config,
            'eligibleQueryData' => $eligibleQueryData,
            'eligibleRecord' => $this->eligibleRecord,
            'fieldConfig' => $fieldConfig,
            'groupSubjectOptions' => $this->groupSubjectOptions
        ]);
    }

    private function loadFormParticles($config)
    {
        $level = $config->level;
        
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
        $config = $this->requireSelectedConfig();
        $eligibleQueryData = session()->get('eligibleQueryData');
        $this->eligibleRecord = session()->get('eligibleRecord');

        if (!$eligibleQueryData) {
            return redirect()->route('fees-payment.index')
                            ->with('error', 'You are not eligible to pay fees.');
        }

        // Get form fields and required fields from configuration
        $formFields = is_array($config->form_fields) ? $config->form_fields : (json_decode($config->form_fields, true) ?? []);
        $requiredFields = is_array($config->required_fields) ? $config->required_fields : (json_decode($config->required_fields ?? '[]', true) ?? []);

        // Build validation rules
        $rules = [];
        foreach ($formFields as $field) {
            if (in_array($field, $requiredFields)) {
                $rules[$field] = 'required';
                
                // Add specific validation rules based on field type
                if ($field === 'mobile') {
                    $rules[$field] .= '|string|regex:/^01[0-9]{9}$/';
                } elseif ($field === 'registration_id') {
                    $rules[$field] .= '|string';
                } elseif ($field === 'date_of_birth') {
                    $rules[$field] .= '|date|before:today';
                } elseif ($field === 'email') {
                    $rules[$field] .= '|email';
                } elseif ($field === 'class_roll') {
                    $rules[$field] .= '|string|max:50';
                } elseif ($field === 'group_dept') {
                    $rules[$field] .= '|string|max:100';
                } elseif ($field === 'gender') {
                    $rules[$field] .= '|string|in:Male,Female,Other';
                } elseif (in_array($field, ['name', 'father_name', 'mother_name', 'fathers_name', 'mothers_name'])) {
                    $rules[$field] .= '|string|max:255';
                } elseif (in_array($field, ['name_bn', 'fathers_name_bn', 'mothers_name_bn', 'post_office_bn', 'upazila_bn', 'district_bn'])) {
                    $rules[$field] .= '|string|max:255';
                } elseif (in_array($field, ['course', 'session', 'level', 'village'])) {
                    $rules[$field] .= '|string|max:255';
                }
            }
        }

        // Custom validation messages
        $messages = [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Mobile number must be valid (e.g., 01700000000)',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be a past date.',
            'name.required' => 'Name is required.',
            'father_name.required' => 'Father\'s name is required.',
            'mothers_name.required' => 'Mother\'s name is required.',
            'fathers_name.required' => 'Father\'s name is required.',
            'mother_name.required' => 'Mother\'s name is required.',
            'group_dept.required' => 'Group/Department is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be Male, Female, or Other.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        // Extract eligibility checker fields for reference
        $referenceData = [];
        foreach ($this->getEligibilityCheckerFields() as $field) {
            if (isset($eligibleQueryData[$field])) {
                $referenceData[$field] = $eligibleQueryData[$field];
            }
        }

        try {
            // Handle new student registration (when student_type is null)
            if ($request->filled('is_new_student')) {
                // Map field names (form field => database column)
                $fieldMapping = [
                    'father_name' => 'fathers_name',
                    'mother_name' => 'mothers_name',
                    'registration_id' => 'registration_no',
                ];
                
                // Create new student record
                $student = new Student();
                $student->name = $request->name;
                $student->name_bn = $request->name_bn ?? $request->name;
                $student->student_id = $request->reference_id; // Use reference_id as student_id
                $student->mobile = $request->mobile;
                
                // Generate default password from mobile if not provided
                $password = $request->filled('password') ? $request->password : substr($request->mobile, -6);
                $student->password = Hash::make($password);
                $student->normal_password = $password;
                
                // Map and save fields
                $student->fathers_name = $request->input('father_name', $request->input('fathers_name', ''));
                $student->fathers_name_bn = $request->input('fathers_name_bn', $student->fathers_name);
                $student->mothers_name = $request->input('mother_name', $request->input('mothers_name', ''));
                $student->mothers_name_bn = $request->input('mothers_name_bn', $student->mothers_name);
                $student->date_of_birth = $request->date_of_birth ?? null;
                $student->gender = $request->gender ?? null;
                $student->group_dept = $request->group_dept ?? null;
                $student->village = $request->village ?? '';
                $student->post_office_bn = $request->post_office_bn ?? '';
                $student->upazila_bn = $request->upazila_bn ?? '';
                $student->district_bn = $request->district_bn ?? '';
                
                // Determine course from config or form
                $courseMapping = [
                    'App\Models\StudentInfoHsc' => 'hsc',
                    'App\\Models\\StudentInfoHsc' => 'hsc',
                    'App\Models\StudentInfoHons' => 'honours',
                    'App\\Models\\StudentInfoHons' => 'honours',
                    'App\Models\StudentInfoDegree' => 'degree',
                    'App\\Models\\StudentInfoDegree' => 'degree',
                    'App\Models\StudentInfoMasters' => 'masters',
                    'App\\Models\\StudentInfoMasters' => 'masters',
                ];
                
                $student->course = $request->course ?? ($courseMapping[$config->course] ?? 'hsc');
                $student->session = $request->session ?? $config->academic_session;
                $student->level = $request->level ?? $config->level;
                $student->class_roll = $request->class_roll ?? $request->reference_id;
                $student->registration_no = $request->input('registration_id', $request->input('registration_no', ''));
                $student->is_approved = 0; // Not approved initially
                $student->save();
                
                \Log::info('New student registered during fee payment', [
                    'student_id' => $student->id,
                    'reference_id' => $request->reference_id,
                    'mobile' => $student->mobile,
                    'course' => $student->course
                ]);
            }
            
            // Create new fees application
            $feesApplication = new FeesApplication();
            
            // Get student info from eligibleRecord
            $studentInfo = $this->eligibleRecord && isset($this->eligibleRecord->student_info) ? $this->eligibleRecord->student_info : null;
            
            // Field mapping: application field => [student_info fields to try]
            $fieldMapping = [
                'name' => ['name'],
                'father_name' => ['father_name'],
                'mother_name' => ['mother_name'],
                'gender' => ['gender'],
                'mobile' => ['contact_no', 'mobile', 'guardian_contact'],
                'group_dept' => ['dept_name', 'group_dept', 'faculty_name'],
            ];
            
            foreach ($fieldMapping as $appField => $studentFields) {
                if ($request->filled($appField)) {
                    $feesApplication->$appField = $request->$appField;
                } elseif ($studentInfo) {
                    // Try each possible field name
                    $value = null;
                    foreach ($studentFields as $studentField) {
                        $value = is_object($studentInfo) ? ($studentInfo->$studentField ?? null) : ($studentInfo[$studentField] ?? null);
                        if ($value) break; // Stop if we found a value
                    }
                    $feesApplication->$appField = $value;
                } else {
                    $feesApplication->$appField = null;
                }
            }
            
            if ($request->filled('date_of_birth')) {
                $feesApplication->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
            } elseif ($studentInfo) {
                $dob = is_object($studentInfo) ? ($studentInfo->date_of_birth ?? $studentInfo->birth_date ?? null) : ($studentInfo['date_of_birth'] ?? $studentInfo['birth_date'] ?? null);
                $feesApplication->date_of_birth = $dob ? date('Y-m-d', strtotime($dob)) : null;
            }
            
            $feesApplication->registration_id = $request->filled('registration_id') ? $request->registration_id : null;
            $feesApplication->reference_data = json_encode($referenceData);
            $feesApplication->configuration_id = $config->id;
            $feesApplication->type = $config->type; // Copy type from configuration
            $feesApplication->reference_id = $eligibleQueryData['reference_id'] ?? null;
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
        $config = $this->requireSelectedConfig();
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

            // Use configuration values for filtering
            $academicSession = $config->academic_session;
            $currentLevel = $config->level;
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
                'config' => $config,
                'headers' => $headers,
                'feesApplication' => $feesApplication
            ]);

        } catch (\Exception $e) {
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
        $config = $this->requireSelectedConfig();
        
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
            
            $invoice->roll = $referenceData->reference_id ?? null;
            $invoice->admission_session = $config->academic_session;
            $invoice->mobile = $feesApplication->mobile ?? null;
            $invoice->level = $config->level;
            $invoice->reference_model = 'App\Models\FeesApplication';
            $invoice->refference_id = $feesApplication->id;
            $invoice->date_start = $config->opening_date->format('Y-m-d');
            $invoice->date_end = $config->clossing_date->format('Y-m-d');
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
            
            // Get config from reference data or session
            $referenceData = json_decode($feesApplication->reference_data, true);
            $configId = $referenceData['configuration_id'] ?? session()->get('selected_config_id');
            $config = FeesConfiguration::find($configId);
            
            if (!$config) {
                // Fallback to any active config if none found
                $config = FeesConfiguration::active()->first();
            }
            
            if($feesApplication->status == 'Pending'){
                session()->flash('warning', 'Your payment is not completed. Please make payment first.');
            } elseif ($feesApplication->status == 'Paid' && $feesApplication->isActionPending()) {
                // Process post-payment action if payment is completed and action is pending
                $paymentActionService = new PaymentActionService();
                $actionProcessed = $paymentActionService->processPaymentAction($feesApplication);
                
                if ($actionProcessed) {
                    session()->flash('success', 'Payment completed and student record updated successfully!');
                } else {
                    \Log::warning("Post-payment action failed for application #{$feesApplication->id}");
                }
            }
            
            // Get updated student level if promotion happened
            $updatedLevel = null;
            if ($feesApplication->status == 'Paid' && $config && $config->course) {
                try {
                    $courseClass = $config->course;
                    $studentId = $feesApplication->reference_id;
                    
                    if (class_exists($courseClass) && $studentId) {
                        // Use the same logic as PaymentActionService to find student
                        $instance = new $courseClass();
                        $table = $instance->getTable();
                        $columns = \Schema::getColumnListing($table);
                        
                        $possibleColumns = ['student_id', 'registration_id', 'roll', 'ref_id', 'reference_id', 'admission_roll', 'id'];
                        
                        $query = $courseClass::query();
                        $hasCondition = false;
                        
                        foreach ($possibleColumns as $column) {
                            if (in_array($column, $columns)) {
                                if ($hasCondition) {
                                    $query->orWhere($column, $studentId);
                                } else {
                                    $query->where($column, $studentId);
                                    $hasCondition = true;
                                }
                            }
                        }
                        
                        $student = $hasCondition ? $query->first() : null;
                        
                        if ($student && isset($student->current_level)) {
                            $updatedLevel = $student->current_level;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error fetching updated level: ' . $e->getMessage());
                }
            }
            
            return view('fees-payment.confirmation', [
                'config' => $config,
                'feesApplication' => $feesApplication,
                'updatedLevel' => $updatedLevel,
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
        
        // Get config from reference data
        $referenceData = json_decode($feesApplication->reference_data, true);
        $configId = $referenceData['configuration_id'] ?? null;
        $config = $configId ? FeesConfiguration::find($configId) : FeesConfiguration::active()->first();
        
        if (!$config) {
            abort(404, 'Configuration not found');
        }

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
            $html = view('fees-payment.download-slip', compact('feesApplication' , 'config'))->render();

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
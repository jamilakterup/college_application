<?php

namespace App\Http\Controllers\Student\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeesEligibilityService;
use App\Models\Invoice;
use App\Models\FeesApplication;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeesPaymentController extends Controller
{
    protected $eligibilityService;

    public function __construct(
        FeesEligibilityService $eligibilityService
    ) {
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Display fees payment report page
     */
    public function report()
    {
        return view('BackEnd.student.fees.payment.report');
    }

    /**
     * Get payment data for DataTables (Invoice-based)
     */
    public function getReportData(Request $request)
    {
        $query = Invoice::with('header')->where('reference_model', 'App\\Models\\FeesApplication');

        // Apply filters
        if ($request->filled('student_type')) {
            $query->where('type', $request->student_type);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->get();

        return DataTables::of($invoices)
            ->addColumn('student_info', function ($row) {
                return "{$row->name} (ID: {$row->roll})";
            })
            ->addColumn('payment_status', function ($row) {
                // Get status from fees_application
                $application = FeesApplication::find($row->refference_id);
                $status = $application ? $application->status : 'Unknown';
                
                $badges = [
                    'Paid' => 'success',
                    'Pending' => 'warning',
                    'Failed' => 'danger'
                ];
                $badge = $badges[$status] ?? 'secondary';
                return "<span class='badge badge-{$badge}'>{$status}</span>";
            })
            ->addColumn('formatted_amount', function ($row) {
                return '৳ ' . number_format($row->total_amount, 2);
            })
            ->addColumn('formatted_date', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })
            ->editColumn('type', function ($row) {
                return strtoupper($row->type);
            })
            ->addColumn('slip_name', function ($row) {
                return $row->slip_name ?? 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $application = FeesApplication::find($row->refference_id);
                if ($application && $application->status === 'Paid') {
                    return '<a href="' . route('fees-payment.confirmation', ['application_id' => $application->id]) . '" class="btn btn-sm btn-info" target="_blank">View Slip</a>';
                }
                return '<span class="text-muted">Pending</span>';
            })
            ->rawColumns(['payment_status', 'actions'])
            ->make(true);
    }

    /**
     * Check student eligibility for payment
     * Supports both student_id and registration_id
     */
    public function checkEligibility(Request $request)
    {
        $request->validate([
            'student_identifier' => 'required|string', // Can be student_id or registration_no
            'student_type' => 'required|in:hsc,honours,degree,masters',
            'session' => 'nullable|string',
            'search_by' => 'nullable|in:id,registration' // Optional: specify search type
        ]);

        try {
            // Map student type to model class
            $studentTypeMap = [
                'hsc' => 'App\\Models\\StudentInfoHsc',
                'honours' => 'App\\Models\\StudentInfoHons',
                'degree' => 'App\\Models\\StudentInfoDegree',
                'masters' => 'App\\Models\\StudentInfoMasters'
            ];

            $studentClass = $studentTypeMap[$request->student_type] ?? null;
            if (!$studentClass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid student type'
                ]);
            }

            // Get student by identifier (auto-detect or use search_by)
            $searchBy = $request->input('search_by', 'auto');
            
            if ($searchBy === 'registration') {
                // Force search by registration_no
                $student = $this->eligibilityService->getStudentDetails(
                    $request->student_identifier,
                    $studentClass,
                    true // byRegistrationId = true
                );
            } else {
                // Auto-detect (try both)
                $student = $this->eligibilityService->getStudentByIdentifier(
                    $request->student_identifier,
                    $studentClass
                );
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found with provided identifier'
                ]);
            }

            // Check eligibility using student's actual ID
            $isEligible = $this->eligibilityService->checkEligibility(
                $student->id,
                $studentClass,
                $request->session
            );

            return response()->json([
                'success' => true,
                'eligible' => $isEligible,
                'student' => [
                    'id' => $student->id,
                    'registration_id' => $student->registration_id ?? 'N/A',
                    'name' => $student->name ?? $student->student_name ?? 'N/A',
                    'session' => $student->session ?? 'N/A',
                    'level' => $student->current_level ?? $student->level ?? 'N/A'
                ],
                'message' => $isEligible ? 'Student is eligible for fees payment' : 'Student is not eligible for fees payment'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking eligibility: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export payment report
     */
    public function exportReport(Request $request)
    {
        $query = Invoice::with('header')->where('reference_model', 'App\\Models\\FeesApplication');

        // Apply same filters as getReportData
        if ($request->filled('student_type')) {
            $query->where('type', $request->student_type);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        try {
            $invoices = $query->latest()->get();
            
            $exportData = $invoices->map(function($invoice) {
                $application = FeesApplication::find($invoice->refference_id);
                
                return [
                    'Invoice ID' => $invoice->id,
                    'Student ID' => $invoice->roll,
                    'Student Name' => $invoice->name,
                    'Student Type' => strtoupper($invoice->type),
                    'Session' => $invoice->session,
                    'Level' => $invoice->level,
                    'Slip Name' => $invoice->slip_name ?? 'N/A',
                    'Amount' => $invoice->total_amount,
                    'Payment Status' => $application ? $application->status : 'Unknown',
                    'Start Date' => $invoice->start_date ?? 'N/A',
                    'End Date' => $invoice->end_date ?? 'N/A',
                    'Created At' => $invoice->created_at->format('d M Y, h:i A')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $exportData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting report: ' . $e->getMessage()
            ]);
        }
    }
}


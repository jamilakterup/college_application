<?php
    
namespace App\Http\Controllers\Student\Report;

use App\Http\Controllers\Controller;
use App\Models\FeesApplication;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class FeesPaymentReportController extends Controller
{
    protected $path = 'BackEnd.student.';
    
    /**
     * Display the fees payment report page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->path.'fees-payment.report');
    }

    /**
     * Apply filters to the query based on request parameters
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyFilters($query, Request $request)
    {
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Level filter
        if ($request->filled('level')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.current_level')) = ?", [$request->level]);
        }

        // Academic session filter
        if ($request->filled('academic_session')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.academic_session')) = ?", [$request->academic_session]);
        }
        
        // Group/Department filters (supporting both 'group_dept' and 'dept' parameters)
        if ($request->filled('group_dept')) {
            $query->where('group_dept', $request->group_dept);
        }
        
        if ($request->filled('dept')) {
            $query->where('group_dept', $request->dept);
        }

        // Registration ID filter
        if ($request->filled('registration_id')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.registration_id')) LIKE ?", ["%{$request->registration_id}%"]);
        }

        // Name filter
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', "%{$request->name}%");
        }

        // Mobile filter
        if ($request->filled('mobile')) {
            $query->where('mobile', 'LIKE', "%{$request->mobile}%");
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Amount range filters
        if ($request->filled('min_amount')) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->where('total_amount', '>=', $request->min_amount);
            });
        }

        if ($request->filled('max_amount')) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->where('total_amount', '<=', $request->max_amount);
            });
        }

        // Payment date range filters (for paid applications)
        if ($request->filled('payment_date_from')) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->whereDate('updated_at', '>=', $request->payment_date_from);
            })->where('status', 'Paid');
        }

        if ($request->filled('payment_date_to')) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->whereDate('updated_at', '<=', $request->payment_date_to);
            })->where('status', 'Paid');
        }

        return $query;
    }

    /**
     * Get filter options for dropdowns
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions()
    {
        try {
            $options = [
                'statuses' => [
                    'Paid' => 'Paid',
                    'Pending' => 'Pending',
                    'Failed' => 'Failed'
                ],
                'genders' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                    'Other' => 'Other'
                ],
                'levels' => FeesApplication::selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.current_level')) as level")
                    ->whereNotNull('reference_data')
                    ->pluck('level')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->mapWithKeys(function ($level) {
                        return [$level => $level];
                    }),
                'academic_sessions' => FeesApplication::selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.academic_session')) as session")
                    ->whereNotNull('reference_data')
                    ->pluck('session')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->mapWithKeys(function ($session) {
                        return [$session => $session];
                    }),
                'departments' => FeesApplication::select('group_dept')
                    ->whereNotNull('group_dept')
                    ->distinct()
                    ->pluck('group_dept')
                    ->filter()
                    ->sort()
                    ->values()
                    ->mapWithKeys(function ($dept) {
                        return [$dept => $dept];
                    })
            ];

            return response()->json([
                'success' => true,
                'data' => $options
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter options.'
            ], 500);
        }
    }

    /**
     * Get fees payment data for DataTables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $query = FeesApplication::where(function ($query) use ($request) {
                $this->applyFilters($query, $request);
            })->with(['invoice'])
            ->select([
                'id',
                'name',
                'father_name',
                'mother_name',
                'date_of_birth',
                'gender',
                'mobile',
                'group_dept',
                'reference_data',
                'status',
                'created_at',
                'updated_at'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('registration_id', function ($row) {
                $referenceData = json_decode($row->reference_data, true);
                return $referenceData['registration_id'] ?? 'N/A';
            })
            ->addColumn('academic_session', function ($row) {
                $referenceData = json_decode($row->reference_data, true);
                return $referenceData['academic_session'] ?? 'N/A';
            })
            ->addColumn('current_level', function ($row) {
                $referenceData = json_decode($row->reference_data, true);
                return $referenceData['current_level'] ?? 'N/A';
            })
            ->addColumn('total_amount', function ($row) {
                return $row->invoice ? '৳' . number_format($row->invoice->total_amount, 2) : 'N/A';
            })
            ->addColumn('txnid', function ($row) {
                return $row->invoice->txnid ?? 'N/A';
            })
            ->addColumn('payment_date', function ($row) {
                if ($row->invoice && $row->status === 'Paid') {
                    return $row->invoice->updated_at->format('d M Y, h:i A');
                }
                return 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = 'secondary'; // Default class
                switch ($row->status) {
                    case 'Paid':
                        $badgeClass = 'success';
                        break;
                    case 'Pending':
                        $badgeClass = 'warning';
                        break;
                    case 'Failed':
                        $badgeClass = 'danger';
                        break;
                }
                return '<span class="badge badge-' . $badgeClass . '">' . $row->status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group" role="group">';
                
                // View button
                $actions .= '<button type="button" class="btn btn-sm btn-info view-details" 
                            data-id="' . $row->id . '" title="View Details">
                            <i class="fas fa-eye"></i>
                            </button>';
                
                // Download slip button (only for paid applications)
                if ($row->status === 'Paid') {
                    $actions .= '<a href="' . route('fees-payment.download-slip', ['application_id' => $row->id]) . '" 
                                class="btn btn-sm btn-success" title="Download Slip" target="_blank">
                                <i class="fas fa-download"></i>
                                </a>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->filterColumn('registration_id', function ($query, $keyword) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.registration_id')) LIKE ?", ["%$keyword%"]);
            })
            ->filterColumn('academic_session', function ($query, $keyword) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.academic_session')) LIKE ?", ["%$keyword%"]);
            })
            ->filterColumn('current_level', function ($query, $keyword) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.current_level')) LIKE ?", ["%$keyword%"]);
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Get application details for modal view
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails(Request $request)
    {
        try {
            $application = FeesApplication::with(['invoice.header.payslipgenerators', 'invoice.header.payslipitems'])
                ->findOrFail($request->id);
            
            $referenceData = json_decode($application->reference_data, true);
            
            $details = [
                'id' => $application->id,
                'name' => $application->name,
                'father_name' => $application->father_name,
                'mother_name' => $application->mother_name,
                'date_of_birth' => $application->date_of_birth ? Carbon::parse($application->date_of_birth)->format('d M Y') : 'N/A',
                'gender' => $application->gender,
                'mobile' => $application->mobile,
                'group_dept' => $application->group_dept,
                'status' => $application->status,
                'registration_id' => $referenceData['registration_id'] ?? 'N/A',
                'academic_session' => $referenceData['academic_session'] ?? 'N/A',
                'current_level' => $referenceData['current_level'] ?? 'N/A',
                'created_at' => $application->created_at->format('d M Y, h:i A'),
                'updated_at' => $application->updated_at->format('d M Y, h:i A'),
            ];
            
            // Add invoice details if available
            if ($application->invoice) {
                $details['invoice'] = [
                    'total_amount' => number_format($application->invoice->total_amount, 2),
                    'payment_date' => $application->invoice->updated_at->format('d M Y, h:i A'),
                    'header_title' => $application->invoice->header->title ?? 'N/A',
                    'txnid' => $application->invoice->txnid ?? 'N/A',
                ];
                
                // Add fee breakdown if available
                if ($application->invoice->header && $application->invoice->header->payslipgenerators) {
                    $details['fee_breakdown'] = $application->invoice->header->payslipgenerators->map(function ($generator) {
                        return [
                            'title' => $generator->payslipheader->title ?? $generator->title,
                            'fees' => number_format($generator->fees, 2)
                        ];
                    });
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $details
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found or error occurred.'
            ], 404);
        }
    }

    /**
     * Get summary statistics with optional filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary(Request $request = null)
    {
        try {
            $baseQuery = FeesApplication::query();
            
            // Apply filters if request is provided
            if ($request) {
                $this->applyFilters($baseQuery, $request);
            }
            
            $summary = [
                'total_applications' => (clone $baseQuery)->count(),
                'paid_applications' => (clone $baseQuery)->where('status', 'Paid')->count(),
                'pending_applications' => (clone $baseQuery)->where('status', 'Pending')->count(),
                'failed_applications' => (clone $baseQuery)->where('status', 'Failed')->count(),
                'total_revenue' => (clone $baseQuery)->whereHas('invoice')
                    ->where('status', 'Paid')
                    ->with('invoice')
                    ->get()
                    ->sum('invoice.total_amount'),
                'today_applications' => (clone $baseQuery)->whereDate('created_at', today())->count(),
                'this_month_applications' => (clone $baseQuery)->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching summary data.'
            ], 500);
        }
    }

    /**
     * Export fees payment report
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        try {
            $query = FeesApplication::with(['invoice']);
            
            // Apply filters similar to getData method
            $this->applyFilters($query, $request);
            
            $applications = $query->get();
            
            $filename = 'fees_payment_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($applications) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Name',
                    'Father Name',
                    'Mother Name',
                    'Registration ID',
                    'Academic Session',
                    'Current Level',
                    'Group/Dept',
                    'Mobile',
                    'Gender',
                    'Date of Birth',
                    'Status',
                    'Total Amount',
                    'Transaction ID',
                    'Application Date',
                    'Payment Date'
                ]);
                
                // Add data rows
                foreach ($applications as $application) {
                    $referenceData = json_decode($application->reference_data, true);
                    
                    fputcsv($file, [
                        $application->id,
                        $application->name,
                        $application->father_name,
                        $application->mother_name,
                        $referenceData['registration_id'] ?? 'N/A',
                        $referenceData['academic_session'] ?? 'N/A',
                        $referenceData['current_level'] ?? 'N/A',
                        $application->group_dept,
                        $application->mobile,
                        $application->gender,
                        $application->date_of_birth ? Carbon::parse($application->date_of_birth)->format('d M Y') : 'N/A',
                        $application->status,
                        $application->invoice ? $application->invoice->total_amount : 'N/A',
                        $application->invoice->txnid ?? 'N/A',
                        $application->created_at->format('d M Y, h:i A'),
                        ($application->invoice && $application->status === 'Paid') ? $application->invoice->updated_at->format('d M Y, h:i A') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    /**
     * Export fees payment report as Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = FeesApplication::with(['invoice']);
            $this->applyFilters($query, $request);
            $applications = $query->get();
            
            $filename = 'fees_payment_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            
            return Excel::download(new FeesPaymentExport($applications), $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export fees payment report as PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = FeesApplication::with(['invoice']);
            $this->applyFilters($query, $request);
            $applications = $query->get();
            
            $pdf = PDF::loadView('student.fees-payment.report-pdf', compact('applications'));
            
            $filename = 'fees_payment_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get chart data for dashboard
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly'); // daily, weekly, monthly, yearly
            
            $chartData = [];
            
            switch ($period) {
                case 'daily':
                    $chartData = $this->getDailyChartData();
                    break;
                case 'weekly':
                    $chartData = $this->getWeeklyChartData();
                    break;
                case 'monthly':
                    $chartData = $this->getMonthlyChartData();
                    break;
                case 'yearly':
                    $chartData = $this->getYearlyChartData();
                    break;
            }
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data.'
            ], 500);
        }
    }

    /**
     * Get daily chart data for last 30 days
     *
     * @return array
     */
    private function getDailyChartData()
    {
        $data = [];
        $startDate = now()->subDays(29);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $applications = FeesApplication::whereDate('created_at', $date)->count();
            $revenue = FeesApplication::whereHas('invoice')
                ->where('status', 'Paid')
                ->whereDate('created_at', $date)
                ->with('invoice')
                ->get()
                ->sum('invoice.total_amount');
            
            $data[] = [
                'date' => $date->format('M d'),
                'applications' => $applications,
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }

    /**
     * Get weekly chart data for last 12 weeks
     *
     * @return array
     */
    private function getWeeklyChartData()
    {
        $data = [];
        $startDate = now()->subWeeks(11)->startOfWeek();
        
        for ($i = 0; $i < 12; $i++) {
            $weekStart = $startDate->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            $applications = FeesApplication::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $revenue = FeesApplication::whereHas('invoice')
                ->where('status', 'Paid')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->with('invoice')
                ->get()
                ->sum('invoice.total_amount');
            
            $data[] = [
                'date' => 'Week ' . $weekStart->format('M d'),
                'applications' => $applications,
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }

    /**
     * Get monthly chart data for last 12 months
     *
     * @return array
     */
    private function getMonthlyChartData()
    {
        $data = [];
        $startDate = now()->subMonths(11)->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $month = $startDate->copy()->addMonths($i);
            $applications = FeesApplication::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $revenue = FeesApplication::whereHas('invoice')
                ->where('status', 'Paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->with('invoice')
                ->get()
                ->sum('invoice.total_amount');
            
            $data[] = [
                'date' => $month->format('M Y'),
                'applications' => $applications,
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }

    /**
     * Get yearly chart data for last 5 years
     *
     * @return array
     */
    private function getYearlyChartData()
    {
        $data = [];
        $startYear = now()->subYears(4)->year;
        
        for ($i = 0; $i < 5; $i++) {
            $year = $startYear + $i;
            $applications = FeesApplication::whereYear('created_at', $year)->count();
            $revenue = FeesApplication::whereHas('invoice')
                ->where('status', 'Paid')
                ->whereYear('created_at', $year)
                ->with('invoice')
                ->get()
                ->sum('invoice.total_amount');
            
            $data[] = [
                'date' => (string)$year,
                'applications' => $applications,
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }

    /**
     * Get status distribution data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusDistribution(Request $request)
    {
        try {
            $query = FeesApplication::query();
            $this->applyFilters($query, $request);
            
            $distribution = $query->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching status distribution.'
            ], 500);
        }
    }

    /**
     * Get level-wise statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLevelStats(Request $request)
    {
        try {
            $query = FeesApplication::query();
            $this->applyFilters($query, $request);
            
            $levelStats = $query->selectRaw("
                JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.current_level')) as level,
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_applications,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_applications
            ")
            ->whereNotNull('reference_data')
            ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(reference_data, '$.current_level'))")
            ->get()
            ->map(function ($item) {
                return [
                    'level' => $item->level,
                    'total_applications' => $item->total_applications,
                    'paid_applications' => $item->paid_applications,
                    'pending_applications' => $item->pending_applications,
                    'success_rate' => $item->total_applications > 0 ? 
                        round(($item->paid_applications / $item->total_applications) * 100, 2) : 0
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $levelStats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching level statistics.'
            ], 500);
        }
    }

    /**
     * Bulk update application status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:fees_applications,id',
            'status' => 'required|in:Paid,Pending,Failed'
        ]);

        try {
            $updated = FeesApplication::whereIn('id', $request->application_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} applications updated successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating applications.'
            ], 500);
        }
    }

    /**
     * Delete multiple applications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:fees_applications,id'
        ]);

        try {
            $deleted = FeesApplication::whereIn('id', $request->application_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} applications deleted successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting applications.'
            ], 500);
        }
    }
}
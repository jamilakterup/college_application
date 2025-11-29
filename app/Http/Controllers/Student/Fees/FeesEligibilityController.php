<?php

namespace App\Http\Controllers\Student\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeesEligibilityService;
use App\Models\FeesEligibility;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeesEligibilityController extends Controller
{
    protected $eligibilityService;

    public function __construct(FeesEligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Display eligibility management page
     */
    public function index()
    {
        return view('BackEnd.student.fees.eligibility.index');
    }

    /**
     * Get eligibility data for DataTables
     */
    public function getData(Request $request)
    {
        $query = FeesEligibility::with(['student']);

        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        return DataTables::of($query)
            ->addColumn('student_info', function ($row) {
                // Use polymorphic relation directly (already eager loaded)
                $student = $row->student;
                if ($student) {
                    return $student->name ?? $student->student_name ?? 'N/A';
                }
                return 'N/A';
            })
            ->addColumn('status', function ($row) {
                $badge = $row->is_active ? 'success' : 'danger';
                $text = $row->is_active ? 'Active' : 'Inactive';
                return "<span class='badge badge-{$badge}'>{$text}</span>";
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="btn-group" role="group">';

                // Toggle Status Button
                $toggleClass = $row->is_active ? 'btn-warning' : 'btn-success';
                $toggleText = $row->is_active ? 'Deactivate' : 'Activate';
                $actions .= "<button class='btn btn-sm {$toggleClass} toggle-status' data-id='{$row->id}' data-status='" . (!$row->is_active) . "'>{$toggleText}</button>";

                // Edit Button
                $actions .= "<button class='btn btn-sm btn-primary edit-eligibility' data-id='{$row->id}'>Edit</button>";

                // Delete Button
                $actions .= "<button class='btn btn-sm btn-danger delete-eligibility' data-id='{$row->id}'>Delete</button>";

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Show create eligibility form
     */
    public function create()
    {
        return view('BackEnd.student.fees.eligibility.create');
    }

    /**
     * Store new eligibility records
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_type' => 'nullable|string|max:255', // Full class name (optional for 2-way system)
            'session' => 'required|string|max:20',
            'student_ids' => 'required|string',
            'academic_year' => 'nullable|string|max:20',
            'level' => 'nullable|string|max:50',
            'is_active' => 'nullable|in:0,1,true,false'
        ]);

        try {
            // Parse student IDs (comma-separated)
            $studentIds = array_map('trim', explode(',', $request->student_ids));
            $studentData = [];

            foreach ($studentIds as $studentId) {
                if (!empty($studentId)) {
                    $studentData[] = [
                        'student_id' => $studentId,
                        'student_type' => $request->student_type,
                        'session' => $request->session,
                        'academic_year' => $request->academic_year,
                        'level' => $request->level,
                        'is_active' => $request->boolean('is_active', true)
                    ];
                }
            }

            $this->eligibilityService->createEligibilityRecords($studentData);

            return response()->json(['success' => true, 'message' => 'Eligibility records created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating eligibility records: ' . $e->getMessage()]);
        }
    }

    /**
     * Update eligibility status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|in:0,1,true,false'
        ]);

        try {
            $result = $this->eligibilityService->updateEligibilityStatus($id, $request->boolean('is_active'));

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Eligibility record not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get eligibility record for editing
     */
    public function edit($id)
    {
        try {
            $eligibility = FeesEligibility::find($id);
            if ($eligibility) {
                return response()->json(['success' => true, 'data' => $eligibility]);
            } else {
                return response()->json(['success' => false, 'message' => 'Eligibility record not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching record: ' . $e->getMessage()]);
        }
    }

    /**
     * Update eligibility record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_type' => 'nullable|string|max:255', // Optional for 2-way system
            'session' => 'required|string|max:20',
            'student_id' => 'required|integer',
            'academic_year' => 'nullable|string|max:20',
            'level' => 'nullable|string|max:50',
            'is_active' => 'nullable|in:0,1,true,false'
        ]);

        try {
            $eligibility = FeesEligibility::find($id);
            if (!$eligibility) {
                return response()->json(['success' => false, 'message' => 'Eligibility record not found']);
            }

            $eligibility->update([
                'student_type' => $request->student_type,
                'session' => $request->session,
                'student_id' => $request->student_id,
                'academic_year' => $request->academic_year,
                'level' => $request->level,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => auth()->id()
            ]);

            return response()->json(['success' => true, 'message' => 'Eligibility record updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating record: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete eligibility record
     */
    public function destroy($id)
    {
        try {
            $eligibility = FeesEligibility::find($id);
            if ($eligibility) {
                $eligibility->delete();
                return response()->json(['success' => true, 'message' => 'Eligibility record deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Eligibility record not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting record: ' . $e->getMessage()]);
        }
    }

    /**
     * Batch enable eligibility
     */
    public function batchEnable(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        try {
            $count = FeesEligibility::whereIn('id', $request->ids)
                ->update([
                    'is_active' => true,
                    'updated_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$count} eligibility records enabled successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enabling records: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batch disable eligibility
     */
    public function batchDisable(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        try {
            $count = FeesEligibility::whereIn('id', $request->ids)
                ->update([
                    'is_active' => false,
                    'updated_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$count} eligibility records disabled successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error disabling records: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batch delete eligibility
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        try {
            $count = FeesEligibility::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} eligibility records deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show CSV upload form
     */
    public function showCsvUpload()
    {
        return view('BackEnd.student.fees.eligibility.csv_upload');
    }

    /**
     * Process CSV upload for eligibility
     */
    public function processCsvUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'student_type' => 'nullable|string|max:255', // Full class name (optional for 2-way system)
            'session' => 'required|string|max:20'
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = [];

            if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
                $header = fgetcsv($handle, 1000, ',');

                // Normalize header names (lowercase and trim)
                $normalizedHeader = array_map(function ($col) {
                    return strtolower(trim($col));
                }, $header);

                // Check if required columns exist
                $requiredColumns = ['student_id', 'academic_year', 'level'];
                $missingColumns = [];

                foreach ($requiredColumns as $required) {
                    $found = false;
                    foreach ($normalizedHeader as $col) {
                        if ($col === $required || str_replace(' ', '_', $col) === $required || str_replace('_', ' ', $required) === $col) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $missingColumns[] = $required;
                    }
                }

                if (!empty($missingColumns)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CSV file is missing required columns: ' . implode(', ', $missingColumns) . '. Required columns are: student_id, academic_year, level, is_active (optional)'
                    ], 422);
                }

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if (count($header) === count($data)) {
                        $csvData[] = array_combine($header, $data);
                    }
                }

                fclose($handle);
            }

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or has no valid data rows'
                ], 422);
            }

            $result = $this->eligibilityService->bulkImportEligibilities(
                $csvData,
                $request->student_type,
                $request->session
            );

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$result['successful']} records imported successfully, {$result['failed']} failed.",
                'details' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummary(Request $request)
    {
        try {
            $query = FeesEligibility::query();

            // Apply filters if provided
            if ($request->filled('student_type')) {
                $query->where('student_type', $request->student_type);
            }

            if ($request->filled('session')) {
                $query->where('session', $request->session);
            }

            $totalEligible = (clone $query)->count();
            $activeEligible = (clone $query)->where('is_active', true)->count();
            $inactiveEligible = (clone $query)->where('is_active', false)->count();
            $sessionsCount = FeesEligibility::distinct('session')->count('session');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_eligible' => $totalEligible,
                    'active_eligible' => $activeEligible,
                    'inactive_eligible' => $inactiveEligible,
                    'sessions_count' => $sessionsCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching summary: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show CSV upload form for registration ID updates
     */
    public function showRegistrationCsvUpload()
    {
        return view('BackEnd.student.fees.eligibility.registration_csv_upload');
    }

    /**
     * Process CSV upload for registration ID updates
     */
    public function processRegistrationCsvUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'student_type' => 'nullable|string|max:255' // Full class name (optional for 2-way system)
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = [];

            if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
                $header = fgetcsv($handle, 1000, ',');

                // Normalize header names (lowercase and trim)
                $normalizedHeader = array_map(function ($col) {
                    return strtolower(trim($col));
                }, $header);

                // Check if required columns exist
                $requiredColumns = ['student_id', 'registration_id'];
                $missingColumns = [];

                foreach ($requiredColumns as $required) {
                    $found = false;
                    foreach ($normalizedHeader as $col) {
                        // Check for exact match, space-to-underscore, or underscore-to-space
                        if (
                            $col === $required ||
                            str_replace(' ', '_', $col) === $required ||
                            str_replace('_', ' ', $required) === $col ||
                            str_replace(' ', '', $col) === str_replace('_', '', $required)
                        ) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $missingColumns[] = $required;
                    }
                }

                if (!empty($missingColumns)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CSV file is missing required columns: ' . implode(', ', $missingColumns) . '. Required columns are: student_id, registration_id'
                    ], 422);
                }

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if (count($header) === count($data)) {
                        $csvData[] = array_combine($header, $data);
                    }
                }

                fclose($handle);
            }

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or has no valid data rows'
                ], 422);
            }

            $result = $this->eligibilityService->updateRegistrationIds($csvData, $request->student_type);

            return response()->json([
                'success' => true,
                'message' => "Update completed. {$result['successful']} registration IDs updated successfully, {$result['failed']} failed.",
                'details' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage()
            ]);
        }
    }
}

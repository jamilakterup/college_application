<?php

namespace App\Http\Controllers\Student\Fees;

use App\Http\Controllers\Controller;
use App\Models\FeesConfiguration;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeesConfigurationController extends Controller
{
    /**
     * Display fees configuration management page
     */
    public function index()
    {
        return view('BackEnd.student.fees.configuration.index');
    }

    /**
     * Get configuration data for DataTables
     */
    public function getData(Request $request)
    {
        $query = FeesConfiguration::query();

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        return DataTables::of($query)
            ->addColumn('type_name', function ($row) {
                return $row->getTypeName();
            })
            ->addColumn('course_name', function ($row) {
                return $row->getCourseName();
            })
            ->addColumn('date_range', function ($row) {
                return $row->opening_date->format('d M Y') . ' - ' . $row->clossing_date->format('d M Y');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->isOpen()) {
                    return '<span class="badge badge-success">Open</span>';
                } elseif ($row->isWithinDateRange()) {
                    return '<span class="badge badge-warning">Closed (Within Range)</span>';
                } elseif ($row->isActive()) {
                    return '<span class="badge badge-info">Active (Out of Range)</span>';
                } else {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="btn-group btn-group-sm" role="group">';

                // Edit Button
                $actions .= '<a href="' . route('student.fees-configuration.edit', $row->id) . '" class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>';

                // Toggle Status Button
                $toggleClass = $row->status ? 'btn-warning' : 'btn-success';
                $toggleIcon = $row->status ? 'fa-times' : 'fa-check';
                $toggleTitle = $row->status ? 'Deactivate' : 'Activate';
                $actions .= '<button class="btn ' . $toggleClass . ' toggle-status" data-id="' . $row->id . '" data-status="' . (!$row->status) . '" title="' . $toggleTitle . '">
                    <i class="fas ' . $toggleIcon . '"></i>
                </button>';

                // Delete Button
                $actions .= '<button class="btn btn-danger delete-config" data-id="' . $row->id . '" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>';

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show create configuration form
     */
    public function create()
    {
        return view('BackEnd.student.fees.configuration.form');
    }

    /**
     * Store new configuration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:general,promotion,admission,form_fillup,registration,exam,certificate,fees_payment,other',
            'course' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:50',
            'academic_session' => 'nullable|string|max:20',
            'opening_date' => 'required|date',
            'clossing_date' => 'required|date|after_or_equal:opening_date',
            'status' => 'boolean',
            'check_eligible_list' => 'boolean',
            'form_fields' => 'nullable|array',
            'required_fields' => 'nullable|array',
        ]);

        try {
            $config = FeesConfiguration::create([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'course' => $validated['course'],
                'level' => $validated['level'] ?? null,
                'academic_session' => $validated['academic_session'] ?? null,
                'opening_date' => $validated['opening_date'],
                'clossing_date' => $validated['clossing_date'],
                'status' => $request->boolean('status', true),
                'check_eligible_list' => $request->boolean('check_eligible_list', false),
                'form_fields' => $validated['form_fields'] ?? [],
                'required_fields' => $validated['required_fields'] ?? [],
            ]);

            return redirect()->route('student.fees-configuration.index')
                ->with('success', 'Fees configuration created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating configuration: ' . $e->getMessage());
        }
    }

    /**
     * Show edit configuration form
     */
    public function edit($id)
    {
        $configuration = FeesConfiguration::findOrFail($id);
        return view('BackEnd.student.fees.configuration.form', compact('configuration'));
    }

    /**
     * Update configuration
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:general,promotion,admission,form_fillup,registration,exam,certificate,fees_payment,other',
            'course' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:50',
            'academic_session' => 'nullable|string|max:20',
            'opening_date' => 'required|date',
            'clossing_date' => 'required|date|after_or_equal:opening_date',
            'status' => 'boolean',
            'check_eligible_list' => 'boolean',
            'form_fields' => 'nullable|array',
            'required_fields' => 'nullable|array',
        ]);

        try {
            $config = FeesConfiguration::findOrFail($id);

            $config->update([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'course' => $validated['course'],
                'level' => $validated['level'] ?? null,
                'academic_session' => $validated['academic_session'] ?? null,
                'opening_date' => $validated['opening_date'],
                'clossing_date' => $validated['clossing_date'],
                'status' => $request->boolean('status', true),
                'check_eligible_list' => $request->boolean('check_eligible_list', false),
                'form_fields' => $validated['form_fields'] ?? [],
                'required_fields' => $validated['required_fields'] ?? [],
            ]);

            return redirect()->route('student.fees-configuration.index')
                ->with('success', 'Fees configuration updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating configuration: ' . $e->getMessage());
        }
    }

    /**
     * Toggle configuration status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        try {
            $config = FeesConfiguration::findOrFail($id);
            $config->update(['status' => $request->boolean('status')]);

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete configuration
     */
    public function destroy($id)
    {
        try {
            $config = FeesConfiguration::findOrFail($id);
            $config->delete();

            return response()->json(['success' => true, 'message' => 'Configuration deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting configuration: ' . $e->getMessage()]);
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummary()
    {
        try {
            $total = FeesConfiguration::count();
            $active = FeesConfiguration::where('status', true)->count();
            $open = FeesConfiguration::active()->count();
            $courses = FeesConfiguration::distinct('course')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'active' => $active,
                    'open' => $open,
                    'courses' => $courses
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching summary: ' . $e->getMessage()]);
        }
    }
}

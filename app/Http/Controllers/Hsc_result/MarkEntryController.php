<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    ClassExam,
    MarkInputConfig,
    Group,
    Classe,
    StudentInfoHsc,
    ConfigExamParticle
};
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Ecm;

class MarkEntryController extends Controller
{
    public function marklist(Request $request)
    {
        $this->validate($request, [
            'session' => 'required',
            'group' => 'required',
            'current_year' => 'required',
            'exam_id' => 'required',
            'subject_id' => 'required',
            'exam_test' => 'required',
            'exam_year' => 'required',
        ], [
            'exam_id.required' => 'The Exam field is required.',
            'subject_id.required' => 'The Subject field is required.'
        ]);

        // Filter and sanitize input
        $filters = ['session', 'group', 'current_year', 'exam_id', 'subject_id', 'exam_test', 'exam_year'];
        $input = array_combine($filters, array_map(function ($field) use ($request) {
            return Ecm::filterInput($field, $request->get($field));
        }, $filters));
        // Validation checks
        if (!$this->validateExamAssignment($input)) {
            return redirect()->back()->withInput()->with('error', 'Exam Not Assigned');
        }

        if (!$this->validateExamDate($input)) {
            return redirect()->back()->withInput()->with('error', 'No exam mark input date set');
        }

        // Get required data
        $group = Group::find($input['group']);
        $currentClass = Classe::find($input['current_year']);
        $configExamParticles = $this->getConfigExamParticles($currentClass, $input);
        // If it's an AJAX request, return DataTable data
        if ($request->ajax()) {
            return $this->getMarklistData($input, $currentClass, $group);
        }

        $modelClass = 'App\Models\Mark';
        $isDisabled = $this->checkModelPermission($modelClass);

        // Regular request - return view
        return view('BackEnd.hsc_result.mark_input.entry', [
            'title' => 'Easy CollegeMate - HSC Result',
            'breadcrumb' => 'hsc_result.mark_input.index:Mark Input|Dashboard',
            'session' => $input['session'],
            'exam_year' => $input['exam_year'],
            'group' => $input['group'],
            'current_level' => $input['current_year'],
            'curr_level' => $currentClass,
            'exam_id' => $input['exam_id'],
            'subject_id' => $input['subject_id'],
            'exam_test_id' => $input['exam_test'],
            'config_exam_particles' => $configExamParticles,
            'isDisabled' => $isDisabled
        ]);
    }

    private function getMarklistData($input, $currentClass, $group)
    {
        // Add index hints for better query optimization
        $studentQuery = StudentInfoHsc::query()
            ->select(['id', 'name']) // Select only needed fields
            ->where([
                ['current_level', '=', $currentClass->name],
                ['session', '=', $input['session']],
                ['groups', '=', $group->name]
            ]);

        // Use chunking for large datasets
        $chunkSize = 1000;
        $studentIds = collect();

        $studentQuery->chunk($chunkSize, function ($students) use (&$studentIds) {
            $studentIds = $studentIds->concat($students->pluck('id'));
        });

        // Cache frequently accessed data
        $cachedStudents = StudentInfoHsc::whereIn('id', $studentIds)
            ->get(['id', 'name', 'class_roll'])
            ->keyBy('id');

        // Define subject fields array
        $subjectFields = [
            'sub1_id',
            'sub2_id',
            'sub3_id',
            'sub4_id',
            'sub5_id',
            'sub6_id',
            'sub21_id',
            'sub22_id',
            'sub23_id',
            'sub24_id',
            'sub25_id',
            'sub26_id',
            'fourth_id',
            'fourth2_id'
        ];

        $query = $this->buildSubjectQuery($subjectFields, $currentClass, $input, $studentIds);
        $configExamParticles = $this->getConfigExamParticles($currentClass, $input);

        $modelClass = 'App\Models\Mark';
        $isDisabled = $this->checkModelPermission($modelClass);
        // Use cached student data in DataTables
        $datatable = DataTables::of($query)
            ->addColumn('checkbox', function ($row) use ($isDisabled) {
                return sprintf(
                    '<input type="checkbox" name="student_id[]" value="%d" class="action-type-a student-checkbox" %s checked>',
                    $row->student_id,
                    $isDisabled ? 'disabled' : ''
                );
            })
            ->addColumn('student_roll', function ($row) use ($cachedStudents) {
                return $cachedStudents[$row->student_id]->class_roll ?? '';
            })
            ->addColumn('student_name', function ($row) use ($cachedStudents) {
                return $cachedStudents[$row->student_id]->name ?? '';
            });

        // Create array to store raw column names
        $rawColumns = ['checkbox'];

        // Add dynamic columns for each particle with prepared statements
        foreach ($configExamParticles as $particle) {
            $particleId = $particle->xmparticle_id;
            $columnName = 'particle_' . $particleId;

            $datatable->addColumn($columnName, function ($row) use ($input, $particle) {
                return $this->generateSingleMarkInput($row, $input, $particle);
            });

            $rawColumns[] = $columnName;
        }

        return $datatable
            ->rawColumns($rawColumns)
            ->make(true);
    }

    private function validateExamAssignment($input)
    {
        return ClassExam::where('exam_id', $input['exam_id'])
            ->where('classe_id', $input['current_year'])
            ->exists();
    }

    private function validateExamDate($input)
    {
        $examConfig = MarkInputConfig::where('exam_year', $input['exam_year'])
            ->where('exam_id', $input['exam_id'])
            ->first();

        if (!$examConfig) {
            return false;
        }

        if ($examConfig->exp_date < date('Y-m-d')) {
            return redirect()->back()->withInput()
                ->with('error', 'This Exam mark input time has been expired');
        }

        return true;
    }

    private function getConfigExamParticles($currentClass, $input)
    {
        return ConfigExamParticle::where([
            'classe_id' => $currentClass->id,
            'group_id' => $input['group'],
            'subject_id' => $input['subject_id']
        ])->with('xmparticle')->get();
    }

    private function buildSubjectQuery($subjectFields, $currentClass, $input, $studentIds)
    {
        $query = null;
        foreach ($subjectFields as $field) {
            $newQuery = DB::table('student_subject_info')
                ->where('current_level', $currentClass->name)
                ->where('group_id', $input['group'])
                ->whereIn('student_id', $studentIds)
                ->where($field, $input['subject_id']);

            $query = $query ? $query->union($newQuery) : $newQuery;
        }

        return $query;
    }

    private function generateSingleMarkInput($row, $input, $particle)
    {
        $markQuery = $input['exam_test'] == 0
            ? \App\Models\Mark::query()
            : \App\Models\ClassTestMark::query();

        $modelClass = get_class($markQuery->getModel());

        $mark = $markQuery
            ->where([
                'student_id' => $row->student_id,
                'exam_year' => $input['exam_year'],
                'group_id' => $input['group'],
                'exam_id' => $input['exam_id'],
                'session' => $input['session'],
                'subject_id' => $input['subject_id'],
                'particle_id' => $particle->xmparticle_id,
            ])
            ->when($input['exam_test'] != 0, function ($q) use ($input) {
                return $q->where('class_test_id', $input['exam_test']);
            })
            ->first();

        $particleId = $particle->xmparticle_id ?? '';
        $studentId = $row->student_id ?? '';
        $markValue = isset($mark) ? $mark->mark : '';
        $placeholder = isset($particle->xmparticle->name) ? $particle->xmparticle->name : '';
        $maxValue = $particle->total ?? '';


        $isDisabled = $this->checkModelPermission($modelClass);

        return sprintf(
            '<input type="text" 
                class="form-control mark-input" 
                data-particle="%s" 
                data-student="%s" 
                value="%s" 
                placeholder="%s" 
                max="%s" 
                %s
                style="width: 100%%">',
            htmlspecialchars($particleId),
            htmlspecialchars($studentId),
            htmlspecialchars($markValue),
            htmlspecialchars($placeholder),
            htmlspecialchars($maxValue),
            $isDisabled ? 'disabled' : ''
        );
    }

    public function checkModelPermission($modelClass)
    {
        $roleId = auth()->user()->roles->first()->id ?? null;

        $modelConfig = DB::table('role_model_configs')
            ->where('model', $modelClass)
            ->where('role_id', $roleId)
            ->first();

        $isDisabled = false;

        if ($modelConfig) {
            $currentDate = now();
            $startDate = $modelConfig->start_date;
            $endDate = $modelConfig->end_date;

            // Disable input if current date is outside the range
            if (($startDate && $currentDate->lt($startDate)) || ($endDate && $currentDate->gt($endDate))) {
                $isDisabled = true;
            }
        }

        return $isDisabled;
    }

    public function saveMark(Request $request)
    {
        try {
            $markModel = $request->exam_test_id == 0
                ? \App\Models\Mark::class
                : \App\Models\ClassTestMark::class;

            $current_level = $request->current_level;

            $data = [
                'student_id' => $request->student_id,
                'exam_year' => $request->exam_year,
                'group_id' => $request->group_id,
                'exam_id' => $request->exam_id,
                'subject_id' => $request->subject_id,
                'particle_id' => $request->particle_id,
                'session' => $request->session
            ];

            if ($request->exam_test_id != 0) {
                $data['class_test_id'] = $request->exam_test_id;
            }

            $mark = $request->mark;
            $converted_mark = null;

            $config_exam_particle = ConfigExamParticle::where('classe_id', $current_level)
                ->where('group_id', $request->group_id)
                ->where('subject_id', $request->subject_id)
                ->where('xmparticle_id', $request->particle_id)->first();

            if (is_null($config_exam_particle)) {
                throw new \Exception("Particle Not Found");
            }

            if (is_numeric($mark)) {
                $converted_mark = ($mark * $config_exam_particle->per_centage) / 100;
            } else {
                $mark = strtoupper($mark);
                $converted_mark = $mark;
            }

            $mark = $markModel::updateOrCreate(
                $data,
                [
                    'mark' => $request->mark,
                    'converted_mark' => $converted_mark
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

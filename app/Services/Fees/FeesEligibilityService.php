<?php

namespace App\Services\Fees;

use App\Models\FeesEligibility;
use Exception;
use Illuminate\Support\Facades\DB;

class FeesEligibilityService
{
    /**
     * Check if a student is eligible for fees payment
     *
     * @param int $studentId
     * @param string $studentType
     * @param string|null $session
     * @return bool
     */
    public function checkEligibility($studentId, $studentType, $session = null)
    {
        return FeesEligibility::isEligible($studentId, $studentType, $session);
    }

    /**
     * Get student details based on student type
     * 
     * @param int|string $studentId Student ID or Registration ID
     * @param string $studentType Full class name (e.g., App\Models\StudentInfoHsc)
     * @param bool $byRegistrationId Whether to search by registration_id instead of id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getStudentDetails($studentId, $studentType, $byRegistrationId = false)
    {
        // $studentType should be full class name
        if (!class_exists($studentType)) {
            return null;
        }

        // Search by registration_id if specified
        if ($byRegistrationId) {
            return $studentType::where('registration_id', $studentId)->first();
        }

        // Default: search by id
        return $studentType::find($studentId);
    }

    /**
     * Get student details by registration ID or student ID (auto-detect)
     * 
     * @param string $identifier Student ID or Registration ID
     * @param string $studentType Full class name (e.g., App\Models\StudentInfoHsc)
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getStudentByIdentifier($identifier, $studentType)
    {
        if (!class_exists($studentType)) {
            return null;
        }

        // First try by ID (if numeric)
        if (is_numeric($identifier)) {
            $student = $studentType::find($identifier);
            if ($student) {
                return $student;
            }
        }

        // Then try by registration_id
        return $studentType::where('registration_id', $identifier)->first();
    }

    /**
     * Create eligibility records for students
     *
     * @param array $studentData
     * @return bool
     */
    public function createEligibilityRecords($studentData)
    {
        try {
            DB::beginTransaction();

            foreach ($studentData as $data) {
                FeesEligibility::updateOrCreate(
                    [
                        'student_id' => $data['student_id'],
                        'student_type' => $data['student_type'],
                        'session' => $data['session']
                    ],
                    [
                        'academic_year' => $data['academic_year'] ?? null,
                        'level' => $data['level'] ?? null,
                        'is_active' => $data['is_active'] ?? true,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id()
                    ]
                );
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update eligibility status
     *
     * @param int $eligibilityId
     * @param bool $isActive
     * @return bool
     */
    public function updateEligibilityStatus($eligibilityId, $isActive)
    {
        $eligibility = FeesEligibility::find($eligibilityId);
        if ($eligibility) {
            $eligibility->update([
                'is_active' => $isActive,
                'updated_by' => auth()->id()
            ]);
            return true;
        }
        return false;
    }

    /**
     * Bulk import eligibilities from CSV data
     *
     * @param array $csvData
     * @param string $studentType
     * @param string $session
     * @return array
     */
    public function bulkImportEligibilities($csvData, $studentType, $session)
    {
        $successful = 0;
        $failed = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                try {
                    // Normalize row keys (case-insensitive matching and trim)
                    $normalizedRow = [];
                    foreach ($row as $key => $value) {
                        $normalizedRow[strtolower(trim($key))] = trim($value);
                    }

                    // Try multiple possible column names for student_id
                    $possibleIdColumns = ['student_id', 'student id', 'studentid', 'id', 'roll'];
                    $studentId = null;

                    foreach ($possibleIdColumns as $column) {
                        if (isset($normalizedRow[$column]) && !empty($normalizedRow[$column])) {
                            $studentId = $normalizedRow[$column];
                            break;
                        }
                    }

                    // Validate required fields
                    if (empty($studentId)) {
                        throw new Exception("Student ID is required. CSV must have one of: " . implode(', ', $possibleIdColumns));
                    }

                    // Check if student exists
                    $student = $this->getStudentDetails($studentId, $studentType);
                    if (!$student) {
                        throw new Exception("Student not found");
                    }

                    FeesEligibility::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'student_type' => $studentType,
                            'session' => $session
                        ],
                        [
                            'academic_year' => $normalizedRow['academic_year'] ?? $normalizedRow['academic year'] ?? null,
                            'level' => $normalizedRow['level'] ?? null,
                            'is_active' => isset($normalizedRow['is_active']) ? (bool)$normalizedRow['is_active'] : true,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id()
                        ]
                    );

                    $successful++;
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'successful' => $successful,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    /**
     * Update student registration IDs from CSV
     *
     * @param array $csvData
     * @param string $studentType
     * @return array
     */
    public function updateRegistrationIds($csvData, $studentType)
    {
        $successful = 0;
        $failed = 0;
        $errors = [];
        try {
            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                try {
                    // Normalize row keys (case-insensitive matching and trim)
                    $normalizedRow = [];
                    foreach ($row as $key => $value) {
                        $normalizedRow[strtolower(trim($key))] = trim($value);
                    }

                    // Try multiple possible column names for student_id
                    $possibleIdColumns = ['student_id', 'student id', 'studentid', 'id', 'roll'];
                    $studentId = null;

                    foreach ($possibleIdColumns as $column) {
                        if (isset($normalizedRow[$column]) && !empty($normalizedRow[$column])) {
                            $studentId = $normalizedRow[$column];
                            break;
                        }
                    }

                    // Try multiple possible column names for registration_id
                    $possibleRegColumns = ['registration_id', 'registration id', 'registrationid', 'registration_no', 'registration no'];
                    $registrationId = null;

                    foreach ($possibleRegColumns as $column) {
                        if (isset($normalizedRow[$column]) && !empty($normalizedRow[$column])) {
                            $registrationId = $normalizedRow[$column];
                            break;
                        }
                    }

                    if (empty($studentId) || empty($registrationId)) {
                        throw new Exception("Student ID and Registration ID are required");
                    }

                    $student = $this->getStudentDetails($studentId, $studentType);
                    if (!$student) {
                        // Get table name for better error message
                        $tableName = 'unknown';
                        if (class_exists($studentType)) {
                            try {
                                $instance = new $studentType();
                                $tableName = $instance->getTable();
                            } catch (\Exception $e) {
                                // Ignore
                            }
                        }
                        throw new Exception("Student with ID '{$studentId}' not found in table '{$tableName}' (Model: {$studentType})");
                    }
                    // Update registration ID
                    $student->update(['registration_id' => $registrationId]);
                    $successful++;
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'successful' => $successful,
            'failed' => $failed,
            'errors' => $errors
        ];
    }
}
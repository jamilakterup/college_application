<?php

namespace App\Services;

use App\Models\FeesApplication;
use App\Models\FeesConfiguration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentActionService
{
    /**
     * Process post-payment action based on configuration type
     *
     * @param FeesApplication $application
     * @return bool
     */
    public function processPaymentAction(FeesApplication $application)
    {
        // Skip if already completed or not paid
        if ($application->action_completed || $application->status !== 'Paid') {
            return false;
        }

        // Skip general payments
        if (!$application->type || $application->type === 'general') {
            return false;
        }

        try {
            DB::beginTransaction();

            $result = false;
            $actionDetails = [];

            switch ($application->type) {
                case FeesConfiguration::TYPE_PROMOTION:
                    $result = $this->processPromotion($application, $actionDetails);
                    break;

                case FeesConfiguration::TYPE_ADMISSION:
                    $result = $this->processAdmission($application, $actionDetails);
                    break;

                default:
                    Log::info("No action defined for payment type: {$application->type}");
                    break;
            }

            if ($result) {
                $application->markActionCompleted($actionDetails);
                DB::commit();
                
                Log::info("Payment action completed for application #{$application->id}", [
                    'type' => $application->type,
                    'details' => $actionDetails
                ]);
                
                return true;
            }

            DB::rollBack();
            return false;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment action failed for application #{$application->id}: " . $e->getMessage(), [
                'exception' => $e,
                'application_id' => $application->id,
                'type' => $application->type
            ]);
            return false;
        }
    }

    /**
     * Process student promotion
     *
     * @param FeesApplication $application
     * @param array &$actionDetails
     * @return bool
     */
    protected function processPromotion(FeesApplication $application, array &$actionDetails)
    {
        // Get configuration to determine course
        $config = $application->configuration;
        if (!$config) {
            Log::warning("Configuration not found for application #{$application->id}");
            return false;
        }

        // Get student ID and course from application
        $studentId = $application->reference_id;
        $courseClass = $config->course;
        $currentLevel = $config->level; // e.g., "HSC 1st Year"

        // Calculate next level based on course
        $nextLevel = $this->calculateNextLevel($courseClass, $currentLevel);
        if (!$nextLevel) {
            Log::warning("Could not calculate next level for {$courseClass} - {$currentLevel}");
            return false;
        }

        // Find student in the appropriate table
        $student = $this->findStudent($courseClass, $studentId);
        if (!$student) {
            Log::warning("Student not found: {$studentId} in {$courseClass}");
            return false;
        }

        // Store old values
        $oldLevel = $student->current_level ?? 'Unknown';
        
        // Update student level
        $student->current_level = $nextLevel;
        $student->save();

        $actionDetails = [
            'action' => 'promotion',
            'student_id' => $studentId,
            'course' => $courseClass,
            'old_level' => $oldLevel,
            'new_level' => $nextLevel,
            'promoted_at' => now()->toDateTimeString(),
        ];

        return true;
    }

    /**
     * Process admission (mark as admitted)
     *
     * @param FeesApplication $application
     * @param array &$actionDetails
     * @return bool
     */
    protected function processAdmission(FeesApplication $application, array &$actionDetails)
    {
        // Get configuration
        $config = $application->configuration;
        if (!$config) {
            return false;
        }

        $studentId = $application->reference_id;
        $courseClass = $config->course;

        // Find student
        $student = $this->findStudent($courseClass, $studentId);
        
        if (!$student) {
            return false;
        }

        // Update admission status if field exists
        if (property_exists($student, 'admission_status')) {
            $student->admission_status = 'Admitted';
            $student->save();
        }

        $actionDetails = [
            'action' => 'admission',
            'student_id' => $studentId,
            'course' => $courseClass,
            'admitted_at' => now()->toDateTimeString(),
        ];

        return true;
    }

    /**
     * Find student by course class and student ID
     *
     * @param string $courseClass
     * @param string $studentId
     * @return mixed
     */
    protected function findStudent($courseClass, $studentId)
    {
        if (!class_exists($courseClass)) {
            return null;
        }

        // Get an instance to check available columns
        $instance = new $courseClass();
        $table = $instance->getTable();
        
        // Get all columns from the table
        $columns = \Schema::getColumnListing($table);
        
        // Possible column names for student identifier (in priority order)
        $possibleColumns = [
            'student_id',
            'registration_id', 
            'roll',
            'ref_id',
            'reference_id',
            'admission_roll',
            'id'
        ];
        
        // Build query dynamically based on existing columns
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
        
        return $hasCondition ? $query->first() : null;
    }

    /**
     * Calculate next level based on current level
     *
     * @param string $courseClass
     * @param string $currentLevel
     * @return string|null
     */
    protected function calculateNextLevel($courseClass, $currentLevel)
    {
        // Level promotion map
        $promotionMap = [
            // HSC
            'HSC 1st Year' => 'HSC 2nd Year',
            
            // Honours
            'Honours 1st Year' => 'Honours 2nd Year',
            'Honours 2nd Year' => 'Honours 3rd Year',
            'Honours 3rd Year' => 'Honours 4th Year',
            
            // Degree
            'Degree 1st Year' => 'Degree 2nd Year',
            'Degree 2nd Year' => 'Degree 3rd Year',
            
            // Masters
            'Masters 1st Year' => 'Masters 2nd Year'
        ];

        return $promotionMap[$currentLevel] ?? null;
    }

    /**
     * Process pending actions for all paid applications
     *
     * @return array
     */
    public function processPendingActions()
    {
        $applications = FeesApplication::pendingActions()->get();
        
        $processed = 0;
        $failed = 0;

        foreach ($applications as $application) {
            if ($this->processPaymentAction($application)) {
                $processed++;
            } else {
                $failed++;
            }
        }

        return [
            'total' => $applications->count(),
            'processed' => $processed,
            'failed' => $failed,
        ];
    }
}

<?php
namespace App\Services\Fees;

use App\Models\FeesEligibility;
use Illuminate\Support\Facades\DB;

class FeesEligibilityIntegrationService
{
    /**
     * Check if a student is eligible using the eligibility system
     *
     * @param int $referenceId
     * @param string $academicSession
     * @param string|null $currentLevel
     * @param string|null $studentType
     * @return object|null Returns eligibility record or null
     */
    public static function checkEligibility($referenceId, $academicSession, $currentLevel = null, $studentType = null)
    {
        $config = DB::table('fees_configurations')->first();
        
        if (!$config || !$config->check_eligible_list) {
            return null;
        }

        // Check FeesEligibility system
        $query = FeesEligibility::where('student_id', $referenceId)
                                ->where('session', $academicSession)
                                ->where('is_active', true);

        if ($currentLevel) {
            $query->where('level', $currentLevel);
        }

        if ($studentType) {
            $query->where('student_type', $studentType);
        }

        $eligibility = $query->with('student')->first();

        if ($eligibility) {
            // Convert to compatible format
            return $eligibility->toFeesEligible();
        }

        return null;
    }

    /**
     * Store eligibility record in session for use in fees payment process
     *
     * @param int $referenceId
     * @param string $academicSession
     * @param string|null $currentLevel
     * @param string|null $studentType
     * @return bool
     */
    public static function storeEligibilityInSession($referenceId, $academicSession, $currentLevel = null, $studentType = null)
    {
        $eligibleRecord = self::checkEligibility($referenceId, $academicSession, $currentLevel, $studentType);

        if ($eligibleRecord) {
            session()->put('eligibleRecord', $eligibleRecord);
            return true;
        }

        return false;
    }

    /**
     * Create invoice for eligible student
     *
     * @param int $studentId
     * @param string $studentType
     * @param string $session
     * @param array $invoiceData
     * @return \App\Models\Invoice|null
     */
    public static function createInvoice($studentId, $studentType, $session, $invoiceData)
    {
        $eligibility = FeesEligibility::where('student_id', $studentId)
                                      ->where('student_type', $studentType)
                                      ->where('session', $session)
                                      ->where('is_active', true)
                                      ->first();

        if (!$eligibility) {
            return null;
        }

        try {
            DB::beginTransaction();

            // Create fees application
            $application = new \App\Models\FeesApplication();
            $application->reference_id = $studentId;
            $application->reference_data = json_encode([
                'student_id' => $studentId,
                'student_type' => $studentType,
                'academic_session' => $session,
                'current_level' => $eligibility->level,
            ]);
            $application->status = 'Pending';
            $application->save();

            // Create invoice
            $invoice = new \App\Models\Invoice();
            $invoice->roll = $studentId;
            $invoice->name = $invoiceData['name'] ?? '';
            $invoice->type = $studentType;
            $invoice->level = $eligibility->level ?? '';
            $invoice->session = $session;
            $invoice->exam_year = $invoiceData['exam_year'] ?? date('Y');
            $invoice->slip_name = $invoiceData['slip_name'] ?? 'Fees Payment';
            $invoice->header_id = $invoiceData['header_id'] ?? null;
            $invoice->start_date = $invoiceData['start_date'] ?? now();
            $invoice->end_date = $invoiceData['end_date'] ?? now()->addDays(30);
            $invoice->total_amount = $invoiceData['total_amount'] ?? 0;
            $invoice->reference_model = get_class($application);
            $invoice->refference_id = $application->id;
            $invoice->save();

            // Update application with invoice reference
            $application->invoice_id = $invoice->id;
            $application->save();

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create invoice: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get payment status for a student
     *
     * @param int $studentId
     * @param string $studentType
     * @param string $session
     * @return string|null Returns 'Paid', 'Pending', or null
     */
    public static function getPaymentStatus($studentId, $studentType, $session)
    {
        $eligibility = FeesEligibility::where('student_id', $studentId)
                                      ->where('student_type', $studentType)
                                      ->where('session', $session)
                                      ->where('is_active', true)
                                      ->first();

        if (!$eligibility) {
            return null;
        }

        $application = $eligibility->feesApplications()
                                  ->with('invoice')
                                  ->latest()
                                  ->first();

        return $application ? $application->status : null;
    }
}


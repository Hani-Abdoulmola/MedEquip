<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Quotation Workflow Service
 * 
 * Centralizes business logic for Quotation lifecycle management.
 * Handles state transitions with database locking to prevent race conditions.
 */
class QuotationWorkflowService
{
    public function __construct(
        private QuotationStateMachine $stateMachine,
        private RfqStateMachine $rfqStateMachine
    ) {}

    /**
     * Submit a quotation (draft → pending)
     * 
     * @param Quotation $quotation
     * @return Quotation
     * @throws InvalidArgumentException
     */
    public function submitQuotation(Quotation $quotation): Quotation
    {
        return DB::transaction(function() use ($quotation) {
            // Validate RFQ is still accepting quotations
            $validation = $this->canAcceptQuotations($quotation->rfq);
            if (!$validation['valid']) {
                throw new InvalidArgumentException($validation['message']);
            }

            // Check for duplicate (defensive, should be prevented by unique constraint)
            $existingQuotation = Quotation::where('rfq_id', $quotation->rfq_id)
                ->where('supplier_id', $quotation->supplier_id)
                ->where('id', '!=', $quotation->id)
                ->first();

            if ($existingQuotation) {
                throw new InvalidArgumentException('لديك عرض سعر موجود لهذا الطلب');
            }

            // Transition to pending
            $this->stateMachine->transition($quotation, 'pending');

            // Notify buyer
            $this->notifyQuotationSubmitted($quotation);

            // Log activity
            $log = activity('quotation_workflow')
                ->performedOn($quotation)
                ->withProperties([
                    'rfq_id' => $quotation->rfq_id,
                    'supplier_id' => $quotation->supplier_id,
                    'total_price' => $quotation->total_price,
                ]);
            
            if (auth()->check()) {
                $log->causedBy(auth()->user());
            }
            
            $log->log('تم تقديم عرض سعر جديد');

            return $quotation->fresh();
        });
    }

    /**
     * Accept a quotation with RFQ locking to prevent race conditions
     * 
     * @param Quotation $quotation
     * @param User $acceptedBy
     * @return Quotation
     * @throws InvalidArgumentException
     */
    public function acceptQuotation(Quotation $quotation, User $acceptedBy): Quotation
    {
        return DB::transaction(function() use ($quotation, $acceptedBy) {
            // CRITICAL: Lock the RFQ row to prevent race conditions
            // This ensures only one quotation can be accepted at a time
            $rfq = Rfq::where('id', $quotation->rfq_id)
                ->lockForUpdate()
                ->first();

            if (!$rfq) {
                throw new InvalidArgumentException('RFQ not found');
            }

            // Validate RFQ state
            if ($rfq->status === 'awarded') {
                throw new InvalidArgumentException('RFQ already awarded to another quotation');
            }

            if (!in_array($rfq->status, ['open', 'closed'])) {
                throw new InvalidArgumentException("Cannot accept quotation: RFQ is in '{$rfq->status}' state");
            }

            // Check if another quotation already accepted
            $existingAccepted = Quotation::where('rfq_id', $rfq->id)
                ->where('status', 'accepted')
                ->exists();

            if ($existingAccepted) {
                throw new InvalidArgumentException('Another quotation already accepted for this RFQ');
            }

            // Transition quotation to accepted
            $this->stateMachine->transition($quotation, 'accepted', [
                'accepted_by' => $acceptedBy->id,
            ]);

            // Auto-reject other pending quotations
            $rejectedCount = Quotation::where('rfq_id', $rfq->id)
                ->where('id', '!=', $quotation->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'تم ترسية الطلب لمورد آخر',
                    'rejected_at' => now(),
                    'rejected_by' => $acceptedBy->id,
                ]);

            // Update RFQ to awarded status
            // Pass the accepted quotation for validation
            $this->rfqStateMachine->transition($rfq, 'awarded', [
                'awarded_quotation_id' => $quotation->id,
                'accepted_quotation' => $quotation, // Pass quotation for validation
            ]);

            // Notify accepted supplier
            $this->notifyQuotationDecision($quotation, 'accepted');

            // Notify rejected suppliers
            $rejectedQuotations = Quotation::where('rfq_id', $rfq->id)
                ->where('id', '!=', $quotation->id)
                ->where('status', 'rejected')
                ->with('supplier.user')
                ->get();

            foreach ($rejectedQuotations as $rejected) {
                $this->notifyQuotationDecision($rejected, 'rejected', $rejected->rejection_reason);
            }

            // Log activity
            activity('quotation_workflow')
                ->performedOn($quotation)
                ->causedBy($acceptedBy)
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'quotation_id' => $quotation->id,
                    'supplier_id' => $quotation->supplier_id,
                    'total_price' => $quotation->total_price,
                    'rejected_count' => $rejectedCount,
                ])
                ->log('تم قبول عرض سعر وترسية الطلب');

            return $quotation->fresh();
        });
    }

    /**
     * Reject a quotation
     * 
     * @param Quotation $quotation
     * @param User $rejectedBy
     * @param string|null $reason
     * @return Quotation
     * @throws InvalidArgumentException
     */
    public function rejectQuotation(Quotation $quotation, User $rejectedBy, ?string $reason = null): Quotation
    {
        return DB::transaction(function() use ($quotation, $rejectedBy, $reason) {
            // Transition to rejected
            $this->stateMachine->transition($quotation, 'rejected', [
                'rejected_by' => $rejectedBy->id,
                'rejection_reason' => $reason ?? 'لم يستوف المعايير المطلوبة',
            ]);

            // Notify supplier
            $this->notifyQuotationDecision($quotation, 'rejected', $reason);

            // Log activity
            activity('quotation_workflow')
                ->performedOn($quotation)
                ->causedBy($rejectedBy)
                ->withProperties([
                    'rfq_id' => $quotation->rfq_id,
                    'reason' => $reason,
                ])
                ->log('تم رفض عرض سعر');

            return $quotation->fresh();
        });
    }

    /**
     * Expire quotations past their validity date or RFQ deadline
     * 
     * @return int Number of quotations expired
     */
    public function expireQuotations(): int
    {
        $expired = 0;

        try {
            DB::beginTransaction();

            // Expire quotations past valid_until date
            $expiredByValidity = Quotation::where('status', 'pending')
                ->whereNotNull('valid_until')
                ->where('valid_until', '<=', now())
                ->get();

            foreach ($expiredByValidity as $quotation) {
                $this->stateMachine->transition($quotation, 'expired');
                $expired++;
            }

            // Expire quotations for closed RFQs
            $expiredByRfq = Quotation::where('status', 'pending')
                ->whereHas('rfq', function($q) {
                    $q->where('status', 'closed')
                      ->orWhere('status', 'cancelled');
                })
                ->get();

            foreach ($expiredByRfq as $quotation) {
                $this->stateMachine->transition($quotation, 'expired');
                $expired++;
            }

            DB::commit();

            if ($expired > 0) {
                Log::info("Expired {$expired} quotations");
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to expire quotations: ' . $e->getMessage());
        }

        return $expired;
    }

    /**
     * Validate if RFQ can accept new quotations
     * 
     * @param Rfq $rfq
     * @return array ['valid' => bool, 'message' => string]
     */
    private function canAcceptQuotations(Rfq $rfq): array
    {
        if ($rfq->status !== 'open') {
            return [
                'valid' => false,
                'message' => 'لا يمكن تقديم عروض أسعار لطلب مغلق أو ملغى.'
            ];
        }

        if ($rfq->deadline && $rfq->deadline->isPast()) {
            return [
                'valid' => false,
                'message' => 'انتهت صلاحية هذا الطلب.'
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Notify buyer when quotation is submitted
     * 
     * @param Quotation $quotation
     * @return void
     */
    private function notifyQuotationSubmitted(Quotation $quotation): void
    {
        if (!$quotation->rfq || !$quotation->rfq->buyer || !$quotation->rfq->buyer->user) {
            return;
        }

        NotificationService::send(
            $quotation->rfq->buyer->user,
            '📝 تم تقديم عرض سعر جديد',
            "تم تقديم عرض سعر جديد من {$quotation->supplier->company_name} لطلب: {$quotation->rfq->title} ({$quotation->rfq->reference_code}).",
            route('buyer.quotations.show', $quotation->id),
            'fas fa-file-invoice-dollar text-success',
            'success'
        );
    }

    /**
     * Notify supplier when quotation is accepted/rejected
     * 
     * @param Quotation $quotation
     * @param string $status 'accepted' or 'rejected'
     * @param string|null $reason Optional rejection reason
     * @return void
     */
    private function notifyQuotationDecision(Quotation $quotation, string $status, ?string $reason = null): void
    {
        if (!$quotation->supplier || !$quotation->supplier->user) {
            return;
        }

        $title = $status === 'accepted' 
            ? '✅ تم قبول عرض السعر'
            : '❌ تم رفض عرض السعر';

        $message = $status === 'accepted'
            ? "تم قبول عرض السعر الخاص بك لطلب: {$quotation->rfq->title} ({$quotation->rfq->reference_code})."
            : "تم رفض عرض السعر الخاص بك لطلب: {$quotation->rfq->title} ({$quotation->rfq->reference_code})." .
              ($reason ? " السبب: {$reason}" : '');

        $icon = $status === 'accepted' 
            ? 'fas fa-check-circle text-success'
            : 'fas fa-times-circle text-danger';

        $type = $status === 'accepted' ? 'success' : 'danger';

        NotificationService::send(
            $quotation->supplier->user,
            $title,
            $message,
            route('supplier.quotations.show', $quotation->id),
            $icon,
            $type
        );
    }
}

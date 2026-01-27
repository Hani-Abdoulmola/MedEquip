<?php

namespace App\Services;

use App\Models\Quotation;
use InvalidArgumentException;

/**
 * Quotation State Machine
 * 
 * Manages state transitions for Quotation lifecycle with strict validation.
 * Ensures deterministic and predictable state changes.
 */
class QuotationStateMachine
{
    /**
     * Define all allowed state transitions
     * 
     * Format: 'current_state' => ['allowed', 'target', 'states']
     */
    private const TRANSITIONS = [
        'draft' => ['pending', 'withdrawn'],
        'pending' => ['accepted', 'rejected', 'expired', 'revised'],
        'revised' => ['pending'], // After revision, goes back to pending
        'accepted' => ['converted'], // After order creation
        'rejected' => [], // Terminal state
        'expired' => [], // Terminal state
        'withdrawn' => [], // Terminal state
        'converted' => [], // Terminal state (order created)
    ];

    /**
     * Check if a state transition is allowed
     * 
     * @param Quotation $quotation
     * @param string $toStatus
     * @return bool
     */
    public function canTransition(Quotation $quotation, string $toStatus): bool
    {
        // Get allowed transitions for current state
        $allowedStates = self::TRANSITIONS[$quotation->status] ?? [];
        
        if (!in_array($toStatus, $allowedStates)) {
            return false;
        }

        // Additional business rule validation
        return match($toStatus) {
            'pending' => $this->canSubmit($quotation),
            'accepted' => $this->canAccept($quotation),
            'rejected' => true, // Can always reject pending quotations
            'expired' => true, // Can always expire (automated)
            'withdrawn' => true, // Supplier can always withdraw draft
            'revised' => $this->canRevise($quotation),
            'converted' => true, // Can convert accepted quotation
            default => false,
        };
    }

    /**
     * Execute a state transition with validation
     * 
     * @param Quotation $quotation
     * @param string $toStatus
     * @param array $additionalData Optional additional data to update
     * @return Quotation
     * @throws InvalidArgumentException
     */
    public function transition(Quotation $quotation, string $toStatus, array $additionalData = []): Quotation
    {
        if (!$this->canTransition($quotation, $toStatus)) {
            throw new InvalidArgumentException(
                "Cannot transition Quotation #{$quotation->id} from '{$quotation->status}' to '{$toStatus}'. " .
                "Allowed transitions: " . implode(', ', $this->getAllowedTransitions($quotation))
            );
        }

        $oldStatus = $quotation->status;
        $updateData = ['status' => $toStatus];

        // Set appropriate timestamps based on transition
        $updateData = array_merge($updateData, match($toStatus) {
            'pending' => ['submitted_at' => $additionalData['submitted_at'] ?? now()],
            'accepted' => [
                'accepted_at' => $additionalData['accepted_at'] ?? now(),
                'accepted_by' => $additionalData['accepted_by'] ?? (auth()->check() ? auth()->id() : null),
            ],
            'rejected' => [
                'rejected_at' => $additionalData['rejected_at'] ?? now(),
                'rejected_by' => $additionalData['rejected_by'] ?? (auth()->check() ? auth()->id() : null),
                'rejection_reason' => $additionalData['rejection_reason'] ?? 'لم يستوف المعايير المطلوبة',
            ],
            'expired' => ['expired_at' => $additionalData['expired_at'] ?? now()],
            'withdrawn' => ['withdrawn_at' => $additionalData['withdrawn_at'] ?? now()],
            'converted' => ['converted_at' => $additionalData['converted_at'] ?? now()],
            default => [],
        });

        // Merge any additional data
        $updateData = array_merge($updateData, $additionalData);

        $quotation->update($updateData);

        // Log the transition
        $log = activity('quotation_workflow')
            ->performedOn($quotation)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $toStatus,
                'reference_code' => $quotation->reference_code,
                'rfq_id' => $quotation->rfq_id,
            ]);
        
        if (auth()->check()) {
            $log->causedBy(auth()->user());
        }
        
        $log->log("Quotation status changed: {$oldStatus} → {$toStatus}");

        return $quotation->fresh();
    }

    /**
     * Get list of allowed transitions from current state
     * 
     * @param Quotation $quotation
     * @return array
     */
    public function getAllowedTransitions(Quotation $quotation): array
    {
        $possibleStates = self::TRANSITIONS[$quotation->status] ?? [];
        
        return array_filter(
            $possibleStates,
            fn($state) => $this->canTransition($quotation, $state)
        );
    }

    /**
     * Get human-readable reason why transition is not allowed
     * 
     * @param Quotation $quotation
     * @param string $toStatus
     * @return string
     */
    public function getTransitionError(Quotation $quotation, string $toStatus): string
    {
        $allowedStates = self::TRANSITIONS[$quotation->status] ?? [];
        
        if (!in_array($toStatus, $allowedStates)) {
            return "Invalid transition from '{$quotation->status}' to '{$toStatus}'";
        }

        return match($toStatus) {
            'pending' => !$this->canSubmit($quotation)
                ? 'Cannot submit quotation: RFQ is not open or deadline has passed'
                : '',
            'accepted' => !$this->canAccept($quotation)
                ? 'Cannot accept quotation: RFQ is not in valid state'
                : '',
            'revised' => !$this->canRevise($quotation)
                ? 'Cannot revise quotation: RFQ deadline has passed or RFQ is closed'
                : '',
            default => '',
        };
    }

    /**
     * Check if quotation can be submitted (draft → pending)
     * 
     * @param Quotation $quotation
     * @return bool
     */
    private function canSubmit(Quotation $quotation): bool
    {
        // Load RFQ if not loaded
        if (!$quotation->relationLoaded('rfq')) {
            $quotation->load('rfq');
        }

        // RFQ must be 'open'
        if ($quotation->rfq->status !== 'open') {
            return false;
        }

        // Deadline must not have passed
        if ($quotation->rfq->deadline && $quotation->rfq->deadline->isPast()) {
            return false;
        }

        // Must have at least one item
        if ($quotation->items()->count() === 0) {
            return false;
        }

        // Total price must be set
        if (!$quotation->total_price || $quotation->total_price <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Check if quotation can be accepted
     * 
     * @param Quotation $quotation
     * @return bool
     */
    private function canAccept(Quotation $quotation): bool
    {
        // Load RFQ if not loaded
        if (!$quotation->relationLoaded('rfq')) {
            $quotation->load('rfq');
        }

        // RFQ must be 'open' or 'closed' (not awarded or cancelled)
        if (!in_array($quotation->rfq->status, ['open', 'closed'])) {
            return false;
        }

        // No other quotation should be accepted for this RFQ
        $existingAccepted = \App\Models\Quotation::where('rfq_id', $quotation->rfq_id)
            ->where('id', '!=', $quotation->id)
            ->where('status', 'accepted')
            ->exists();

        return !$existingAccepted;
    }

    /**
     * Check if quotation can be revised
     * 
     * @param Quotation $quotation
     * @return bool
     */
    private function canRevise(Quotation $quotation): bool
    {
        // Load RFQ if not loaded
        if (!$quotation->relationLoaded('rfq')) {
            $quotation->load('rfq');
        }

        // RFQ must still be 'open'
        if ($quotation->rfq->status !== 'open') {
            return false;
        }

        // Deadline must not have passed
        if ($quotation->rfq->deadline && $quotation->rfq->deadline->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if quotation is in a terminal state
     * 
     * @param Quotation $quotation
     * @return bool
     */
    public function isTerminal(Quotation $quotation): bool
    {
        return in_array($quotation->status, ['rejected', 'expired', 'withdrawn', 'converted']);
    }

    /**
     * Check if quotation can be edited
     * 
     * @param Quotation $quotation
     * @return bool
     */
    public function canEdit(Quotation $quotation): bool
    {
        // Can only edit draft or pending quotations
        if (!in_array($quotation->status, ['draft', 'pending'])) {
            return false;
        }

        // Load RFQ if not loaded
        if (!$quotation->relationLoaded('rfq')) {
            $quotation->load('rfq');
        }

        // RFQ must be 'open'
        if ($quotation->rfq->status !== 'open') {
            return false;
        }

        // Deadline must not have passed
        if ($quotation->rfq->deadline && $quotation->rfq->deadline->isPast()) {
            return false;
        }

        return true;
    }
}

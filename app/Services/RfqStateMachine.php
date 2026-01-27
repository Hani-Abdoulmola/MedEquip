<?php

namespace App\Services;

use App\Models\Rfq;
use InvalidArgumentException;

/**
 * RFQ State Machine
 * 
 * Manages state transitions for RFQ lifecycle with strict validation.
 * Ensures deterministic and predictable state changes.
 */
class RfqStateMachine
{
    /**
     * Define all allowed state transitions
     * 
     * Format: 'current_state' => ['allowed', 'target', 'states']
     */
    private const TRANSITIONS = [
        'draft' => ['open', 'cancelled'],
        'open' => ['closed', 'cancelled', 'awarded'], // Can award directly when quotation accepted
        'closed' => ['awarded', 'open'], // Can reopen if no quotation accepted
        'awarded' => [], // Terminal state
        'cancelled' => [], // Terminal state
    ];

    /**
     * Check if a state transition is allowed
     * 
     * @param Rfq $rfq
     * @param string $toStatus
     * @param array $additionalData Optional data for validation (e.g., 'accepted_quotation')
     * @return bool
     */
    public function canTransition(Rfq $rfq, string $toStatus, array $additionalData = []): bool
    {
        // Get allowed transitions for current state
        $allowedStates = self::TRANSITIONS[$rfq->status] ?? [];
        
        if (!in_array($toStatus, $allowedStates)) {
            return false;
        }

        // Additional business rule validation
        return match($toStatus) {
            'open' => $this->canPublish($rfq),
            'closed' => true, // Can always close from 'open'
            'awarded' => $this->canAward($rfq, $additionalData['accepted_quotation'] ?? null),
            'cancelled' => true, // Can always cancel (unless already terminal)
            default => false,
        };
    }

    /**
     * Execute a state transition with validation
     * 
     * @param Rfq $rfq
     * @param string $toStatus
     * @param array $additionalData Optional additional data to update (can include 'accepted_quotation' for validation)
     * @return Rfq
     * @throws InvalidArgumentException
     */
    public function transition(Rfq $rfq, string $toStatus, array $additionalData = []): Rfq
    {
        // Extract accepted_quotation for validation (don't include in update data)
        $acceptedQuotation = $additionalData['accepted_quotation'] ?? null;
        $validationData = ['accepted_quotation' => $acceptedQuotation];
        
        if (!$this->canTransition($rfq, $toStatus, $validationData)) {
            throw new InvalidArgumentException(
                "Cannot transition RFQ #{$rfq->id} from '{$rfq->status}' to '{$toStatus}'. " .
                "Allowed transitions: " . implode(', ', $this->getAllowedTransitions($rfq))
            );
        }
        
        // Remove accepted_quotation from update data (it's only for validation)
        unset($additionalData['accepted_quotation']);

        $oldStatus = $rfq->status;
        $updateData = ['status' => $toStatus];

        // Set appropriate timestamps based on transition
        $updateData = array_merge($updateData, match($toStatus) {
            'open' => ['published_at' => $additionalData['published_at'] ?? now()],
            'closed' => ['closed_at' => $additionalData['closed_at'] ?? now()],
            'awarded' => [
                'awarded_at' => $additionalData['awarded_at'] ?? now(),
                'closed_at' => $additionalData['closed_at'] ?? now(),
                'awarded_quotation_id' => $additionalData['awarded_quotation_id'] ?? null,
            ],
            'cancelled' => ['cancelled_at' => $additionalData['cancelled_at'] ?? now()],
            default => [],
        });

        // Merge any additional data
        $updateData = array_merge($updateData, $additionalData);

        $rfq->update($updateData);

        // Log the transition
        $log = activity('rfq_workflow')
            ->performedOn($rfq)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $toStatus,
                'reference_code' => $rfq->reference_code,
            ]);
        
        if (auth()->check()) {
            $log->causedBy(auth()->user());
        }
        
        $log->log("RFQ status changed: {$oldStatus} → {$toStatus}");

        return $rfq->fresh();
    }

    /**
     * Get list of allowed transitions from current state
     * 
     * @param Rfq $rfq
     * @return array
     */
    public function getAllowedTransitions(Rfq $rfq): array
    {
        $possibleStates = self::TRANSITIONS[$rfq->status] ?? [];
        
        return array_filter(
            $possibleStates,
            fn($state) => $this->canTransition($rfq, $state)
        );
    }

    /**
     * Get human-readable reason why transition is not allowed
     * 
     * @param Rfq $rfq
     * @param string $toStatus
     * @param array $additionalData Optional data for validation
     * @return string
     */
    public function getTransitionError(Rfq $rfq, string $toStatus, array $additionalData = []): string
    {
        $allowedStates = self::TRANSITIONS[$rfq->status] ?? [];
        
        if (!in_array($toStatus, $allowedStates)) {
            return "Invalid transition from '{$rfq->status}' to '{$toStatus}'";
        }

        return match($toStatus) {
            'open' => !$this->canPublish($rfq) 
                ? 'Cannot publish RFQ: must have at least one item and valid deadline'
                : '',
            'awarded' => !$this->canAward($rfq, $additionalData['accepted_quotation'] ?? null)
                ? 'Cannot award RFQ: no accepted quotation found'
                : '',
            default => '',
        };
    }

    /**
     * Check if RFQ can be published (draft → open)
     * 
     * @param Rfq $rfq
     * @return bool
     */
    private function canPublish(Rfq $rfq): bool
    {
        // Must have at least one item
        if ($rfq->items()->count() === 0) {
            return false;
        }

        // Must have title
        if (empty($rfq->title)) {
            return false;
        }

        // If deadline set, must be in future
        if ($rfq->deadline && $rfq->deadline->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if RFQ can be awarded (closed → awarded or open → awarded)
     * 
     * @param Rfq $rfq
     * @param \App\Models\Quotation|null $acceptedQuotation Optional: pass the quotation being accepted
     * @return bool
     */
    private function canAward(Rfq $rfq, ?\App\Models\Quotation $acceptedQuotation = null): bool
    {
        // If quotation is passed directly, check it
        if ($acceptedQuotation) {
            return $acceptedQuotation->status === 'accepted' 
                && $acceptedQuotation->rfq_id === $rfq->id;
        }
        
        // Otherwise, check if RFQ has at least one accepted quotation
        return $rfq->quotations()
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Check if RFQ is in a terminal state
     * 
     * @param Rfq $rfq
     * @return bool
     */
    public function isTerminal(Rfq $rfq): bool
    {
        return in_array($rfq->status, ['awarded', 'cancelled']);
    }

    /**
     * Check if RFQ can accept new quotations
     * 
     * @param Rfq $rfq
     * @return bool
     */
    public function canAcceptQuotations(Rfq $rfq): bool
    {
        // Must be in 'open' status
        if ($rfq->status !== 'open') {
            return false;
        }

        // Deadline must not have passed
        if ($rfq->deadline && $rfq->deadline->isPast()) {
            return false;
        }

        return true;
    }
}

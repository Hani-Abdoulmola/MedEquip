<?php

namespace App\Services;

use App\Models\Rfq;
use App\Models\Quotation;
use App\Models\Supplier;
use App\Models\Buyer;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * RFQ Workflow Service
 * 
 * Centralizes business logic for RFQ/Quotation workflow between buyers and suppliers.
 * Handles:
 * - Automatic RFQ closing after deadline
 * - Deadline reminders
 * - Status transitions
 * - Notifications
 * - Workflow validation
 */
class RfqWorkflowService
{
    /**
     * Close RFQs that have passed their deadline
     * 
     * @return int Number of RFQs closed
     */
    public static function closeExpiredRfqs(): int
    {
        $closed = 0;
        
        $expiredRfqs = Rfq::where('status', 'open')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now())
            ->get();

        foreach ($expiredRfqs as $rfq) {
            try {
                DB::beginTransaction();
                
                $rfq->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                ]);

                // Notify buyer
                if ($rfq->buyer && $rfq->buyer->user) {
                    NotificationService::send(
                        $rfq->buyer->user,
                        '⏰ انتهت صلاحية طلب عرض السعر',
                        "انتهت صلاحية طلب عرض السعر: {$rfq->title} ({$rfq->reference_code}). تم إغلاقه تلقائياً.",
                        route('buyer.rfqs.show', $rfq->id),
                        'fas fa-clock text-warning',
                        'warning'
                    );
                }

                // Notify suppliers who submitted quotations
                $suppliers = $rfq->quotations()
                    ->with('supplier.user')
                    ->get()
                    ->pluck('supplier.user')
                    ->filter();

                foreach ($suppliers as $supplierUser) {
                    NotificationService::send(
                        $supplierUser,
                        '⏰ انتهت صلاحية طلب عرض السعر',
                        "انتهت صلاحية طلب عرض السعر: {$rfq->title} ({$rfq->reference_code}). تم إغلاقه تلقائياً.",
                        route('supplier.rfqs.show', $rfq->id),
                        'fas fa-clock text-warning',
                        'warning'
                    );
                }

                DB::commit();
                $closed++;
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Failed to close RFQ {$rfq->id}: " . $e->getMessage());
            }
        }

        return $closed;
    }

    /**
     * Send deadline reminders for RFQs approaching deadline
     * 
     * @param int $hoursBefore Number of hours before deadline to send reminder
     * @return int Number of reminders sent
     */
    public static function sendDeadlineReminders(int $hoursBefore = 24): int
    {
        $remindersSent = 0;
        
        $reminderTime = now()->addHours($hoursBefore);
        
        $rfqs = Rfq::where('status', 'open')
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [now(), $reminderTime])
            ->whereDoesntHave('assignedSuppliers', function($q) {
                // Only send to RFQs that haven't been fully viewed by suppliers
                $q->whereNotNull('viewed_at');
            })
            ->get();

        foreach ($rfqs as $rfq) {
            try {
                // Notify buyer
                if ($rfq->buyer && $rfq->buyer->user) {
                    $hoursLeft = now()->diffInHours($rfq->deadline);
                    NotificationService::send(
                        $rfq->buyer->user,
                        '⏰ تذكير: موعد انتهاء طلب عرض السعر قريب',
                        "يتبقى {$hoursLeft} ساعة على انتهاء صلاحية طلب عرض السعر: {$rfq->title} ({$rfq->reference_code}).",
                        route('buyer.rfqs.show', $rfq->id),
                        'fas fa-bell text-info',
                        'info'
                    );
                }

                // Notify assigned suppliers who haven't submitted quotations
                $assignedSuppliers = $rfq->assignedSuppliers()
                    ->whereDoesntHave('quotations', function($q) use ($rfq) {
                        $q->where('rfq_id', $rfq->id);
                    })
                    ->with('user')
                    ->get();

                foreach ($assignedSuppliers as $supplier) {
                    if ($supplier->user) {
                        $hoursLeft = now()->diffInHours($rfq->deadline);
                        NotificationService::send(
                            $supplier->user,
                            '⏰ تذكير: موعد انتهاء طلب عرض السعر قريب',
                            "يتبقى {$hoursLeft} ساعة على انتهاء صلاحية طلب عرض السعر: {$rfq->title} ({$rfq->reference_code}). يرجى تقديم عرضك قريباً.",
                            route('supplier.rfqs.show', $rfq->id),
                            'fas fa-bell text-info',
                            'info'
                        );
                    }
                }

                $remindersSent++;
            } catch (\Throwable $e) {
                Log::error("Failed to send reminder for RFQ {$rfq->id}: " . $e->getMessage());
            }
        }

        return $remindersSent;
    }

    /**
     * Validate if RFQ can accept new quotations
     * 
     * @param Rfq $rfq
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function canAcceptQuotations(Rfq $rfq): array
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
     * Get workflow statistics for a buyer
     * 
     * @param Buyer $buyer
     * @return array
     */
    public static function getBuyerStats(Buyer $buyer): array
    {
        $rfqs = Rfq::where('buyer_id', $buyer->id);
        
        return [
            'total_rfqs' => $rfqs->count(),
            'open_rfqs' => $rfqs->where('status', 'open')->count(),
            'closed_rfqs' => $rfqs->where('status', 'closed')->count(),
            'cancelled_rfqs' => $rfqs->where('status', 'cancelled')->count(),
            'pending_quotations' => Quotation::whereHas('rfq', function($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            })->where('status', 'pending')->count(),
            'accepted_quotations' => Quotation::whereHas('rfq', function($q) use ($buyer) {
                $q->where('buyer_id', $buyer->id);
            })->where('status', 'accepted')->count(),
            'expiring_soon' => $rfqs->where('status', 'open')
                ->whereNotNull('deadline')
                ->whereBetween('deadline', [now(), now()->addDays(3)])
                ->count(),
        ];
    }

    /**
     * Get workflow statistics for a supplier
     * 
     * @param Supplier $supplier
     * @return array
     */
    public static function getSupplierStats(Supplier $supplier): array
    {
        $rfqs = Rfq::availableFor($supplier->id);
        
        return [
            'available_rfqs' => $rfqs->count(),
            'quoted_rfqs' => Rfq::whereHas('quotations', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })->count(),
            'pending_quotations' => Quotation::where('supplier_id', $supplier->id)
                ->where('status', 'pending')->count(),
            'accepted_quotations' => Quotation::where('supplier_id', $supplier->id)
                ->where('status', 'accepted')->count(),
            'rejected_quotations' => Quotation::where('supplier_id', $supplier->id)
                ->where('status', 'rejected')->count(),
            'expiring_soon' => $rfqs->whereNotNull('deadline')
                ->whereBetween('deadline', [now(), now()->addDays(3)])
                ->count(),
        ];
    }

    /**
     * Notify suppliers when new RFQ is published
     * 
     * @param Rfq $rfq
     * @param array $supplierIds Optional: specific supplier IDs to notify
     * @return void
     */
    public static function notifyNewRfq(Rfq $rfq, array $supplierIds = []): void
    {
        if (!$rfq->is_public && empty($supplierIds)) {
            return; // Private RFQ with no assigned suppliers
        }

        $suppliers = empty($supplierIds)
            ? Supplier::where('is_verified', true)->get()
            : Supplier::whereIn('id', $supplierIds)->where('is_verified', true)->get();

        foreach ($suppliers as $supplier) {
            if ($supplier->user) {
                NotificationService::send(
                    $supplier->user,
                    '🆕 طلب عرض سعر جديد',
                    "يوجد طلب عرض سعر جديد: {$rfq->title} ({$rfq->reference_code}). " .
                    ($rfq->deadline ? "الموعد النهائي: " . $rfq->deadline->format('Y-m-d H:i') : ''),
                    route('supplier.rfqs.show', $rfq->id),
                    'fas fa-file-alt text-primary',
                    'info'
                );
            }
        }
    }

    /**
     * Notify buyer when quotation is submitted
     * 
     * @param Quotation $quotation
     * @return void
     */
    public static function notifyQuotationSubmitted(Quotation $quotation): void
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
    public static function notifyQuotationDecision(Quotation $quotation, string $status, ?string $reason = null): void
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

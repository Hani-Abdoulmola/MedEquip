<?php

namespace App\Services;

use App\Models\BuyerCart;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * RFQ Creation Service (Phase 1)
 *
 * Creates RFQs from the RFQ Builder (cart), preserving preferred_supplier_id and max_price.
 */
class RfqCreationService
{
    public function __construct(
        protected RfqBuilderService $builderService,
        protected ReferenceCodeService $referenceService
    ) {}

    /**
     * Create RFQ from builder. Validates, creates RFQ + items, notifies suppliers if public+open, clears builder.
     *
     * @param array{title: string, description?: string, deadline?: string, is_public?: bool, status: 'draft'|'open', save_template?: bool, template_name?: string} $metadata
     * @throws ValidationException
     */
    public function createFromBuilder(BuyerCart $builder, array $metadata): Rfq
    {
        $errors = $this->builderService->validateBuilder($builder);
        if (!empty($errors)) {
            throw ValidationException::withMessages(['builder' => $errors]);
        }

        $cartItems = $builder->items()->with('product')->get();
        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages(['builder' => [__('لا توجد عناصر في منشئ الطلبات.')]]);
        }

        DB::beginTransaction();
        try {
            $rfq = Rfq::create([
                'buyer_id' => $builder->buyer_id,
                'created_by' => auth()->id(),
                'title' => $metadata['title'],
                'description' => $metadata['description'] ?? null,
                'deadline' => isset($metadata['deadline']) ? $metadata['deadline'] : null,
                'is_public' => $metadata['is_public'] ?? true,
                'status' => $metadata['status'] ?? 'draft',
                'reference_code' => $this->referenceService->generateUnique(
                    ReferenceCodeService::PREFIX_RFQ,
                    Rfq::class
                ),
            ]);

            foreach ($cartItems as $cartItem) {
                if (!$cartItem->product) {
                    continue;
                }
                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $cartItem->product_id,
                    'item_name' => $cartItem->product->name,
                    'specifications' => $cartItem->specifications,
                    'quantity' => $cartItem->quantity,
                    'unit' => $cartItem->unit ?? 'وحدة',
                    'preferred_supplier_id' => $cartItem->supplier_id,
                    'max_price' => $cartItem->max_price,
                ]);
            }

            if ($rfq->is_public && $rfq->status === 'open') {
                $this->notifySuppliers($rfq);
            }

            if (!empty($metadata['save_template']) && !empty($metadata['template_name'])) {
                $builder->update([
                    'template_name' => $metadata['template_name'],
                    'is_template' => true,
                    'is_active' => false,
                ]);
            } else {
                $builder->items()->delete();
            }

            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(auth()->user())
                ->withProperties([
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                    'reference_code' => $rfq->reference_code,
                    'items_count' => $rfq->items()->count(),
                    'source' => 'builder',
                ])
                ->log('قام المشتري بإنشاء RFQ من منشئ الطلبات');

            DB::commit();
            return $rfq;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('RfqCreationService::createFromBuilder failed', [
                'buyer_id' => $builder->buyer_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Notify verified suppliers about a new public RFQ.
     */
    public function notifySuppliers(Rfq $rfq): void
    {
        $suppliers = Supplier::where('is_verified', true)
            ->where('is_active', true)
            ->get();

        foreach ($suppliers as $supplier) {
            if ($supplier->user) {
                NotificationService::send(
                    $supplier->user,
                    '🆕 طلب عرض سعر جديد',
                    "يوجد طلب عرض سعر جديد بعنوان: {$rfq->title}.",
                    route('supplier.rfqs.show', $rfq->id)
                );
            }
        }
    }
}

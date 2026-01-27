<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\BuyerCart;
use App\Models\BuyerCartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * RFQ Builder Service (Phase 1: Cart → RFQ Builder)
 *
 * Manages the "RFQ Builder" (formerly cart): add/update/remove items,
 * validate, save/load templates.
 */
class RfqBuilderService
{
    public const CART_SESSION_KEY = 'buyer_rfq_cart';

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_REORDER = 'reorder';
    public const SOURCE_TEMPLATE = 'template';

    public function getOrCreateBuilder(Buyer $buyer): BuyerCart
    {
        return BuyerCart::getOrCreateActive($buyer);
    }

    /**
     * Add product to builder. Validates availability, quantity, preferred supplier.
     *
     * @param array{quantity: int, specifications?: string, unit?: string, supplier_id?: int, max_price?: float} $data
     * @throws \InvalidArgumentException
     */
    public function addProduct(BuyerCart $builder, Product $product, array $data): BuyerCartItem
    {
        if (!$product->is_active || $product->review_status !== 'approved') {
            throw new \InvalidArgumentException(__('هذا المنتج غير متاح حالياً'));
        }

        $qty = (int) ($data['quantity'] ?? 1);
        if ($qty < 1 || $qty > 10000) {
            throw new \InvalidArgumentException(__('الكمية يجب أن تكون بين 1 و 10000'));
        }

        $supplierId = isset($data['supplier_id']) ? (int) $data['supplier_id'] : null;
        if ($supplierId) {
            $offers = $product->suppliers()
                ->where('suppliers.id', $supplierId)
                ->where('product_supplier.status', 'available')
                ->exists();
            if (!$offers) {
                throw new \InvalidArgumentException(__('المورد المختار لا يقدم هذا المنتج'));
            }
        }

        $existing = $builder->items()
            ->where('product_id', $product->id)
            ->where('supplier_id', $supplierId)
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $qty,
                'specifications' => $data['specifications'] ?? $existing->specifications,
                'unit' => $data['unit'] ?? $existing->unit,
                'max_price' => $data['max_price'] ?? $existing->max_price,
            ]);
            return $existing;
        }

        return $builder->items()->create([
            'product_id' => $product->id,
            'quantity' => $qty,
            'specifications' => $data['specifications'] ?? null,
            'unit' => $data['unit'] ?? 'وحدة',
            'supplier_id' => $supplierId,
            'max_price' => isset($data['max_price']) ? (float) $data['max_price'] : null,
        ]);
    }

    /**
     * Update builder item (replace quantity, not cumulative).
     */
    public function updateItem(BuyerCartItem $item, array $data): BuyerCartItem
    {
        $allowed = ['quantity', 'specifications', 'unit', 'supplier_id', 'max_price'];
        $payload = collect($data)->only($allowed)->all();
        if (array_key_exists('quantity', $payload)) {
            $qty = (int) $payload['quantity'];
            if ($qty < 1 || $qty > 10000) {
                throw new \InvalidArgumentException(__('الكمية يجب أن تكون بين 1 و 10000'));
            }
            $payload['quantity'] = $qty;
        }
        $item->update($payload);
        return $item;
    }

    public function removeItem(BuyerCartItem $item): void
    {
        $item->delete();
    }

    public function clearBuilder(BuyerCart $builder): void
    {
        $builder->items()->delete();
    }

    /**
     * Validate all builder items. Returns list of error messages.
     *
     * @return array<string>
     */
    public function validateBuilder(BuyerCart $builder): array
    {
        $errors = [];
        $items = $builder->items()->with('product')->get();

        foreach ($items as $item) {
            $p = $item->product;
            $name = $p ? $p->name : '#' . $item->product_id;
            if (!$p) {
                $errors[] = "المنتج {$name} غير موجود.";
                continue;
            }
            if (!$p->is_active) {
                $errors[] = "المنتج \"{$name}\" لم يعد متوفراً.";
            }
            if ($p->review_status !== 'approved') {
                $errors[] = "المنتج \"{$name}\" غير معتمد للعرض.";
            }
            if ($p->suppliers()->where('product_supplier.status', 'available')->exists() === false) {
                $errors[] = "لا يوجد موردون للمنتج \"{$name}\".";
            }
            if ($item->quantity < 1 || $item->quantity > 10000) {
                $errors[] = "كمية غير صالحة للمنتج \"{$name}\".";
            }
            if ($item->supplier_id) {
                $ok = $p->suppliers()
                    ->where('suppliers.id', $item->supplier_id)
                    ->where('product_supplier.status', 'available')
                    ->exists();
                if (!$ok) {
                    $errors[] = "المورد المفضل للمنتج \"{$name}\" لم يعد يقدمه.";
                }
            }
        }

        return $errors;
    }

    /**
     * Get builder summary with validation flags per item.
     *
     * @return array{items: Collection, summary: array{items_count: int, valid_items: int, invalid_items: int, can_submit: bool}}
     */
    public function getBuilderSummary(BuyerCart $builder): array
    {
        $items = $builder->items()
            ->with(['product.category', 'product.media', 'product.suppliers' => fn($q) => $q->where('product_supplier.status', 'available'), 'supplier'])
            ->get();

        $valid = 0;
        foreach ($items as $item) {
            $item->is_valid = true;
            $item->warnings = [];
            $p = $item->product;
            if (!$p || !$p->is_active || $p->review_status !== 'approved') {
                $item->is_valid = false;
                $item->warnings[] = __('المنتج غير متوفر');
            } elseif ($p->suppliers()->where('product_supplier.status', 'available')->exists() === false) {
                $item->is_valid = false;
                $item->warnings[] = __('لا يوجد موردون');
            } elseif ($item->supplier_id) {
                $ok = $p->suppliers()->where('suppliers.id', $item->supplier_id)->where('product_supplier.status', 'available')->exists();
                if (!$ok) {
                    $item->is_valid = false;
                    $item->warnings[] = __('المورد المفضل لم يعد يقدم المنتج');
                }
            }
            if ($item->is_valid) {
                $valid++;
            }
        }

        $summary = [
            'items_count' => $items->count(),
            'valid_items' => $valid,
            'invalid_items' => $items->count() - $valid,
            'can_submit' => $items->isNotEmpty() && $valid === $items->count(),
        ];

        return ['items' => $items, 'summary' => $summary];
    }

    /**
     * Save builder as template (keeps items, marks is_template).
     */
    public function saveAsTemplate(BuyerCart $builder, string $name): BuyerCart
    {
        $builder->update([
            'template_name' => $name,
            'is_template' => true,
            'is_active' => false,
        ]);
        return $builder;
    }

    /**
     * Load template into current builder (copy items to active builder).
     */
    public function loadTemplate(Buyer $buyer, BuyerCart $template): BuyerCart
    {
        if (!$template->is_template || $template->buyer_id !== $buyer->id) {
            throw new \InvalidArgumentException(__('قالب غير صالح'));
        }
        $active = $this->getOrCreateBuilder($buyer);
        foreach ($template->items as $tItem) {
            $product = $tItem->product;
            if (!$product || !$product->is_active || $product->review_status !== 'approved') {
                continue;
            }
            $this->addProduct($active, $product, [
                'quantity' => $tItem->quantity,
                'specifications' => $tItem->specifications,
                'unit' => $tItem->unit,
                'supplier_id' => $tItem->supplier_id,
                'max_price' => $tItem->max_price ? (float) $tItem->max_price : null,
            ]);
        }
        return $active;
    }

    /**
     * Get saved templates for buyer.
     */
    public function getTemplates(Buyer $buyer): \Illuminate\Database\Eloquent\Collection
    {
        return BuyerCart::where('buyer_id', $buyer->id)
            ->where('is_template', true)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Migrate session cart to database (e.g. on login).
     */
    public function migrateSessionCartIfExists(Buyer $buyer, BuyerCart $builder): void
    {
        $sessionCart = session()->get(self::CART_SESSION_KEY, []);
        if (empty($sessionCart) || $builder->items()->count() > 0) {
            return;
        }
        foreach ($sessionCart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active && $product->review_status === 'approved') {
                $this->addProduct($builder, $product, [
                    'quantity' => $item['quantity'] ?? 1,
                    'specifications' => $item['specifications'] ?? null,
                    'unit' => $item['unit'] ?? 'وحدة',
                    'supplier_id' => $item['supplier_id'] ?? null,
                ]);
            }
        }
        session()->forget(self::CART_SESSION_KEY);
    }
}

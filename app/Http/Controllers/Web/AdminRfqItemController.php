<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\RfqItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin RFQ Items Controller
 *
 * Handles CRUD operations for RFQ items (products/requirements within an RFQ).
 */
class AdminRfqItemController extends Controller
{
    /**
     * Show the form for creating a new RFQ item.
     */
    public function create(Rfq $rfq): View
    {
        $this->authorize('update', $rfq);

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.rfqs.items.create', compact('rfq', 'products'));
    }

    /**
     * Store a newly created RFQ item.
     */
    public function store(Request $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('update', $rfq);

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'specifications' => 'nullable|string|max:5000',
            'quantity' => 'required|integer|min:1|max:999999',
            'unit' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $rfqItem = RfqItem::create([
                'rfq_id' => $rfq->id,
                'product_id' => $validated['product_id'] ?? null,
                'item_name' => $validated['item_name'],
                'specifications' => $validated['specifications'] ?? null,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'] ?? 'وحدة',
            ]);

            // Log activity
            activity('admin_rfq_items')
                ->performedOn($rfqItem)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'item_name' => $rfqItem->item_name,
                    'quantity' => $rfqItem->quantity,
                ])
                ->log('تم إضافة بند جديد إلى الطلب');

            DB::commit();

            return redirect()
                ->route('admin.rfqs.show', $rfq)
                ->with('success', '✅ تم إضافة البند بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ item creation error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'فشل إضافة البند: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified RFQ item.
     */
    public function edit(Rfq $rfq, RfqItem $item): View
    {
        $this->authorize('update', $rfq);

        // Ensure item belongs to this RFQ
        if ($item->rfq_id !== $rfq->id) {
            abort(404, 'البند غير موجود في هذا الطلب');
        }

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.rfqs.items.edit', compact('rfq', 'item', 'products'));
    }

    /**
     * Update the specified RFQ item.
     */
    public function update(Request $request, Rfq $rfq, RfqItem $item): RedirectResponse
    {
        $this->authorize('update', $rfq);

        // Ensure item belongs to this RFQ
        if ($item->rfq_id !== $rfq->id) {
            abort(404, 'البند غير موجود في هذا الطلب');
        }

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:200',
            'specifications' => 'nullable|string|max:5000',
            'quantity' => 'required|integer|min:1|max:999999',
            'unit' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $item->update($validated);

            // Log activity
            activity('admin_rfq_items')
                ->performedOn($item)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                ])
                ->log('تم تحديث بند الطلب');

            DB::commit();

            return redirect()
                ->route('admin.rfqs.show', $rfq)
                ->with('success', '✅ تم تحديث البند بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ item update error', [
                'rfq_id' => $rfq->id,
                'item_id' => $item->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'فشل تحديث البند: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified RFQ item.
     */
    public function destroy(Rfq $rfq, RfqItem $item): RedirectResponse
    {
        $this->authorize('update', $rfq);

        // Ensure item belongs to this RFQ
        if ($item->rfq_id !== $rfq->id) {
            abort(404, 'البند غير موجود في هذا الطلب');
        }

        // Check if item has quotations
        if ($item->quotationItems()->count() > 0) {
            return back()
                ->withErrors(['error' => 'لا يمكن حذف البند - يوجد عروض أسعار مرتبطة به']);
        }

        DB::beginTransaction();

        try {
            $itemName = $item->item_name;
            $item->delete();

            // Log activity
            activity('admin_rfq_items')
                ->performedOn($item)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'item_name' => $itemName,
                ])
                ->log('تم حذف بند من الطلب');

            DB::commit();

            return redirect()
                ->route('admin.rfqs.show', $rfq)
                ->with('success', '✅ تم حذف البند بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ item deletion error', [
                'rfq_id' => $rfq->id,
                'item_id' => $item->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'فشل حذف البند: ' . $e->getMessage()]);
        }
    }
}


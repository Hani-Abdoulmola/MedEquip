<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductReviewController extends Controller
{
    /**
     * صفحة مراجعة المنتج (عرض فقط)
     */
    public function review(Product $product)
    {
        // Authorization check: Only admins can review products
        Gate::authorize('view', $product);
        
        $product->load(['suppliers', 'category', 'creator']);

        return view('admin.products.review', compact('product'));
    }

    /**
     * قبول المنتج
     */
    public function approve(Product $product)
    {
        // CRITICAL FIX: Authorization check - only admins with approve permission can approve products
        Gate::authorize('approve', $product);
        
        $product->update([
            'review_status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->log('تمت الموافقة على المنتج');

        return back()->with('success', 'تم قبول المنتج واعتماده.');
    }

    /**
     * رفض المنتج
     */
    public function reject(Request $request, Product $product)
    {
        // CRITICAL FIX: Authorization check - only admins with reject permission can reject products
        Gate::authorize('reject', $product);
        
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $product->update([
            'review_status' => 'rejected',
            'rejection_reason' => $request->reason,
            'updated_by' => Auth::id(),
        ]);

        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['reason' => $request->reason])
            ->log('تم رفض المنتج');

        return back()->with('success', 'تم رفض المنتج.');
    }

    /**
     * طلب تعديل المنتج
     */
    public function requestChanges(Request $request, Product $product)
    {
        // CRITICAL FIX: Authorization check - only admins with request_changes permission can request changes
        Gate::authorize('requestChanges', $product);
        
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $product->update([
            'review_status' => 'needs_update',
            'review_notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // CRITICAL FIX: Notify supplier who created the product
        if ($product->creator && $product->creator->supplierProfile) {
            \App\Services\NotificationService::send(
                $product->creator,
                '✏ طلب تعديل على منتجك',
                "طلب الإدارة تعديلات على المنتج: {$product->name}. الملاحظات: {$request->notes}",
                route('supplier.products.edit', $product->id)
            );
        }

        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['notes' => $request->notes])
            ->log('تم طلب تعديلات على المنتج');

        return back()->with('success', 'تم إرسال طلب التعديلات للمورد.');
    }
}

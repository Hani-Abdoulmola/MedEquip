<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * صفحة مراجعة المنتج (عرض فقط)
     */
    public function review(Product $product)
    {
        $product->load(['suppliers', 'category', 'creator']);

        return view('admin.products.review', compact('product'));
    }

    /**
     * قبول المنتج
     */
    public function approve(Product $product)
    {
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
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $product->update([
            'review_status' => 'needs_update',
            'review_notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        activity('products')
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['notes' => $request->notes])
            ->log('تم طلب تعديلات على المنتج');

        return back()->with('success', 'تم إرسال طلب التعديلات للمورد.');
    }
}

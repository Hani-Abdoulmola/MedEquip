<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\SupplierReview;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Buyer Review Controller
 *
 * Handles supplier review management for buyers.
 */
class BuyerReviewController extends Controller
{
    /**
     * Display list of buyer's reviews.
     */
    public function index(Request $request): View
    {
        $buyer = Auth::user()->buyerProfile;

        $reviews = SupplierReview::where('buyer_id', $buyer->id)
            ->with(['supplier', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('buyer.reviews.index', compact('reviews'));
    }

    /**
     * Show form to create a new review for a supplier.
     */
    public function create(Request $request): View
    {
        $buyer = Auth::user()->buyerProfile;
        $supplierId = $request->query('supplier');
        $orderId = $request->query('order');

        $supplier = null;
        $order = null;

        // If supplier specified
        if ($supplierId) {
            $supplier = Supplier::where('is_verified', true)
                ->where('is_active', true)
                ->findOrFail($supplierId);

            // Check if buyer already reviewed this supplier
            $existingReview = SupplierReview::where('buyer_id', $buyer->id)
                ->where('supplier_id', $supplier->id)
                ->first();

            if ($existingReview) {
                return redirect()->route('buyer.reviews.edit', $existingReview)
                    ->with('info', 'لديك تقييم سابق لهذا المورد. يمكنك تعديله.');
            }
        }

        // If order specified
        if ($orderId) {
            $order = Order::where('buyer_id', $buyer->id)
                ->where('id', $orderId)
                ->with('supplier')
                ->firstOrFail();

            $supplier = $order->supplier;

            // Check if buyer already reviewed this order
            $existingReview = SupplierReview::where('buyer_id', $buyer->id)
                ->where('order_id', $order->id)
                ->first();

            if ($existingReview) {
                return redirect()->route('buyer.reviews.edit', $existingReview)
                    ->with('info', 'لديك تقييم سابق لهذا الطلب. يمكنك تعديله.');
            }
        }

        // Get suppliers the buyer has ordered from (if no supplier specified)
        $availableSuppliers = [];
        if (!$supplier) {
            $availableSuppliers = Supplier::whereHas('orders', function ($query) use ($buyer) {
                $query->where('buyer_id', $buyer->id)
                    ->where('status', 'delivered');
            })->get();
        }

        return view('buyer.reviews.create', compact('supplier', 'order', 'availableSuppliers'));
    }

    /**
     * Store a new review.
     */
    public function store(Request $request): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'nullable|string|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'would_recommend' => 'boolean',
        ], [
            'supplier_id.required' => 'يرجى اختيار المورد.',
            'overall_rating.required' => 'التقييم العام مطلوب.',
            'overall_rating.min' => 'الحد الأدنى للتقييم هو 1.',
            'overall_rating.max' => 'الحد الأقصى للتقييم هو 5.',
            'review.max' => 'المراجعة يجب ألا تتجاوز 2000 حرف.',
        ]);

        // Check for existing review
        $existingReview = SupplierReview::where('buyer_id', $buyer->id)
            ->where('supplier_id', $validated['supplier_id'])
            ->first();

        if ($existingReview) {
            return redirect()->route('buyer.reviews.edit', $existingReview)
                ->with('info', 'لديك تقييم سابق لهذا المورد. يمكنك تعديله.');
        }

        // Check if this is a verified purchase
        $isVerifiedPurchase = Order::where('buyer_id', $buyer->id)
            ->where('supplier_id', $validated['supplier_id'])
            ->where('status', 'delivered')
            ->exists();

        DB::beginTransaction();

        try {
            $review = SupplierReview::create([
                'supplier_id' => $validated['supplier_id'],
                'buyer_id' => $buyer->id,
                'order_id' => $validated['order_id'] ?? null,
                'overall_rating' => $validated['overall_rating'],
                'quality_rating' => $validated['quality_rating'] ?? null,
                'communication_rating' => $validated['communication_rating'] ?? null,
                'delivery_rating' => $validated['delivery_rating'] ?? null,
                'value_rating' => $validated['value_rating'] ?? null,
                'title' => $validated['title'] ?? null,
                'review' => $validated['review'] ?? null,
                'pros' => $validated['pros'] ?? null,
                'cons' => $validated['cons'] ?? null,
                'would_recommend' => $validated['would_recommend'] ?? false,
                'status' => SupplierReview::STATUS_PENDING,
                'is_verified_purchase' => $isVerifiedPurchase,
            ]);

            // Notify admins about new review
            NotificationService::notifyAdmins(
                '⭐ تقييم جديد للمورد',
                "قام المشتري {$buyer->organization_name} بإضافة تقييم جديد للمورد. يحتاج إلى مراجعة.",
                route('admin.reviews.show', $review->id)
            );

            // Log activity
            activity('supplier_review')
                ->performedOn($review)
                ->causedBy(Auth::user())
                ->withProperties([
                    'supplier_id' => $review->supplier_id,
                    'overall_rating' => $review->overall_rating,
                ])
                ->log('أضاف المشتري تقييماً جديداً للمورد');

            DB::commit();

            return redirect()->route('buyer.reviews.index')
                ->with('success', '✅ تم إرسال تقييمك بنجاح. سيتم نشره بعد مراجعة الإدارة.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer review creation error', [
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إرسال التقييم. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Show a specific review.
     */
    public function show(SupplierReview $review): View
    {
        $buyer = Auth::user()->buyerProfile;

        // Ensure buyer owns this review
        if ($review->buyer_id !== $buyer->id) {
            abort(403, 'غير مصرح لك بعرض هذا التقييم.');
        }

        $review->load(['supplier', 'order', 'moderator']);

        return view('buyer.reviews.show', compact('review'));
    }

    /**
     * Show form to edit a review.
     */
    public function edit(SupplierReview $review): View
    {
        $buyer = Auth::user()->buyerProfile;

        // Ensure buyer owns this review
        if ($review->buyer_id !== $buyer->id) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم.');
        }

        // Check if review can be edited
        if (!$review->canBeEdited()) {
            return redirect()->route('buyer.reviews.show', $review)
                ->with('error', 'لا يمكن تعديل هذا التقييم بعد مرور 7 أيام أو بعد الموافقة عليه.');
        }

        $review->load(['supplier', 'order']);

        return view('buyer.reviews.edit', compact('review'));
    }

    /**
     * Update a review.
     */
    public function update(Request $request, SupplierReview $review): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        // Ensure buyer owns this review
        if ($review->buyer_id !== $buyer->id) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم.');
        }

        // Check if review can be edited
        if (!$review->canBeEdited()) {
            return redirect()->route('buyer.reviews.show', $review)
                ->with('error', 'لا يمكن تعديل هذا التقييم.');
        }

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'nullable|string|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'would_recommend' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $review->update([
                'overall_rating' => $validated['overall_rating'],
                'quality_rating' => $validated['quality_rating'] ?? null,
                'communication_rating' => $validated['communication_rating'] ?? null,
                'delivery_rating' => $validated['delivery_rating'] ?? null,
                'value_rating' => $validated['value_rating'] ?? null,
                'title' => $validated['title'] ?? null,
                'review' => $validated['review'] ?? null,
                'pros' => $validated['pros'] ?? null,
                'cons' => $validated['cons'] ?? null,
                'would_recommend' => $validated['would_recommend'] ?? false,
                'status' => SupplierReview::STATUS_PENDING, // Re-submit for review
            ]);

            // Log activity
            activity('supplier_review')
                ->performedOn($review)
                ->causedBy(Auth::user())
                ->log('قام المشتري بتحديث تقييمه للمورد');

            DB::commit();

            return redirect()->route('buyer.reviews.index')
                ->with('success', '✅ تم تحديث تقييمك بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Buyer review update error', [
                'review_id' => $review->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث التقييم. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Delete a review.
     */
    public function destroy(SupplierReview $review): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        // Ensure buyer owns this review
        if ($review->buyer_id !== $buyer->id) {
            abort(403, 'غير مصرح لك بحذف هذا التقييم.');
        }

        // Only allow deleting pending reviews
        if ($review->status !== SupplierReview::STATUS_PENDING) {
            return redirect()->route('buyer.reviews.index')
                ->with('error', 'لا يمكن حذف التقييمات المعتمدة.');
        }

        try {
            $review->delete();

            return redirect()->route('buyer.reviews.index')
                ->with('success', '✅ تم حذف التقييم بنجاح.');

        } catch (\Throwable $e) {
            Log::error('Buyer review delete error', [
                'review_id' => $review->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف التقييم.']);
        }
    }
}


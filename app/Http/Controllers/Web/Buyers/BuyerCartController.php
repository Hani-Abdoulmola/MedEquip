<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\BuyerCart;
use App\Models\BuyerCartItem;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

/**
 * Buyer Cart Controller
 *
 * Provides shopping cart-like functionality for RFQ building.
 * Buyers can add products from the catalog to a cart, then submit
 * all items as a single RFQ request.
 */
class BuyerCartController extends Controller
{
    private const CART_SESSION_KEY = 'buyer_rfq_cart';

    /**
     * Display the cart contents.
     */
    public function index(): View
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        // Get or create active cart
        $cart = BuyerCart::getOrCreateActive($buyer);

        // Migrate session cart if exists (one-time migration)
        $this->migrateSessionCartIfExists($buyer, $cart);

        // Load cart items with products
        $cartItems = $cart->items()
            ->with(['product.category', 'product.suppliers' => function ($q) {
                $q->where('is_verified', true)->where('is_active', true);
            }, 'supplier'])
            ->get();

        $products = [];
        $totalItems = 0;

        foreach ($cartItems as $item) {
            if ($item->product && $item->product->is_active && $item->product->review_status === 'approved') {
                $products[] = [
                    'item' => $item,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'specifications' => $item->specifications ?? '',
                    'unit' => $item->unit ?? 'وحدة',
                    'supplier_id' => $item->supplier_id,
                ];
                $totalItems += $item->quantity;
            }
        }

        return view('buyer.cart.index', compact('products', 'totalItems', 'cart'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        // Validate product is available
        if (!$product->is_active || $product->review_status !== 'approved') {
            $message = 'هذا المنتج غير متاح حالياً';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return back()->with('error', $message);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10000',
            'specifications' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Get or create active cart
        $cart = BuyerCart::getOrCreateActive($buyer);

        // Check if item already exists
        $existingItem = BuyerCartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('supplier_id', $validated['supplier_id'] ?? null)
            ->first();

        if ($existingItem) {
            // Update existing item
            $existingItem->update([
                'quantity' => $existingItem->quantity + $validated['quantity'],
                'specifications' => $validated['specifications'] ?? $existingItem->specifications,
                'unit' => $validated['unit'] ?? $existingItem->unit,
            ]);
        } else {
            // Create new item
            BuyerCartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'specifications' => $validated['specifications'] ?? null,
                'unit' => $validated['unit'] ?? 'وحدة',
                'supplier_id' => $validated['supplier_id'] ?? null,
            ]);
        }

        $cartCount = $cart->items()->count();
        $message = 'تم إضافة المنتج إلى سلة طلبات العروض';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, BuyerCartItem $cartItem): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $cartItem->cart->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا العنصر');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10000',
            'specifications' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'specifications' => $validated['specifications'] ?? $cartItem->specifications,
            'unit' => $validated['unit'] ?? $cartItem->unit,
            'supplier_id' => $validated['supplier_id'] ?? $cartItem->supplier_id,
        ]);

        $message = 'تم تحديث الكمية';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
    }

    /**
     * Remove a product from the cart (by cart item).
     */
    public function remove(Request $request, BuyerCartItem $cartItem): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer || $cartItem->cart->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لحذف هذا العنصر');
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        $cartCount = $cart->items()->count();
        $message = 'تم حذف المنتج من السلة';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount,
            ]);
        }
        return back()->with('success', $message);
    }

    /**
     * Remove a product from the cart (by product - legacy support).
     */
    public function removeByProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = BuyerCart::getOrCreateActive($buyer);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            return $this->remove($request, $cartItem);
        }

        $message = 'المنتج غير موجود في السلة';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        return back()->with('error', $message);
    }

    /**
     * Update cart item (by product - legacy support).
     */
    public function updateByProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = BuyerCart::getOrCreateActive($buyer);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            return $this->update($request, $cartItem);
        }

        $message = 'المنتج غير موجود في السلة';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        return back()->with('error', $message);
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = BuyerCart::getOrCreateActive($buyer);
        $cart->items()->delete();

        $message = 'تم إفراغ السلة';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'cart_count' => 0]);
        }
        return back()->with('success', $message);
    }

    /**
     * Get cart count for AJAX requests (used in header badge).
     */
    public function count(): JsonResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            return response()->json(['count' => 0]);
        }

        $cart = BuyerCart::where('buyer_id', $buyer->id)
            ->where('is_active', true)
            ->first();

        $count = $cart ? $cart->items()->count() : 0;
        return response()->json(['count' => $count]);
    }

    /**
     * Show the checkout/submit RFQ form.
     */
    public function checkout(): View|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = BuyerCart::getOrCreateActive($buyer);
        $cartItems = $cart->items()
            ->with(['product.category', 'supplier'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'السلة فارغة. أضف منتجات أولاً.');
        }

        $items = [];
        foreach ($cartItems as $item) {
            if ($item->product && $item->product->is_active && $item->product->review_status === 'approved') {
                $items[] = [
                    'item' => $item,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'specifications' => $item->specifications ?? '',
                    'unit' => $item->unit ?? 'وحدة',
                    'supplier_id' => $item->supplier_id,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'لا توجد منتجات صالحة في السلة.');
        }

        return view('buyer.cart.checkout', compact('items', 'cart'));
    }

    /**
     * Submit the cart as an RFQ.
     */
    public function submitRfq(Request $request): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = BuyerCart::getOrCreateActive($buyer);
        $cartItems = $cart->items()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'السلة فارغة');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date|after:today',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,open',
        ]);

        DB::beginTransaction();

        try {
            // Create RFQ
            $rfq = Rfq::create([
                'buyer_id' => $buyer->id,
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'is_public' => $validated['is_public'] ?? true,
                'status' => $validated['status'],
                'reference_code' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_RFQ,
                    Rfq::class
                ),
            ]);

            // Create RFQ items from cart items
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product) {
                    RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'product_id' => $cartItem->product_id,
                        'item_name' => $cartItem->product->name,
                        'specifications' => $cartItem->specifications,
                        'quantity' => $cartItem->quantity,
                        'unit' => $cartItem->unit ?? 'وحدة',
                    ]);
                }
            }

            // Notify verified suppliers about new public RFQ
            if ($rfq->is_public && $rfq->status === 'open') {
                $suppliers = Supplier::where('is_verified', true)->get();
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

            // Log activity
            activity('buyer_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                    'reference_code' => $rfq->reference_code,
                    'items_count' => $cartItems->count(),
                    'source' => 'cart',
                ])
                ->log('قام المشتري بإنشاء RFQ من السلة');

            // Clear cart items after successful submission
            $cart->items()->delete();

            DB::commit();

            return redirect()
                ->route('buyer.rfqs.show', $rfq)
                ->with('success', '✅ تم إنشاء طلب عرض السعر بنجاح من السلة.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Cart RFQ submission error', [
                'buyer_id' => $buyer->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    /**
     * Migrate session cart to database (one-time migration for existing users).
     */
    private function migrateSessionCartIfExists(Buyer $buyer, BuyerCart $cart): void
    {
        $sessionCart = Session::get(self::CART_SESSION_KEY, []);

        if (empty($sessionCart) || $cart->items()->count() > 0) {
            // No session cart or cart already has items, skip migration
            return;
        }

        // Migrate session cart items to database
        foreach ($sessionCart as $productId => $item) {
            $product = Product::find($productId);

            if ($product && $product->is_active && $product->review_status === 'approved') {
                BuyerCartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'] ?? 1,
                    'specifications' => $item['specifications'] ?? null,
                    'unit' => $item['unit'] ?? 'وحدة',
                    'supplier_id' => $item['supplier_id'] ?? null,
                ]);
            }
        }

        // Clear session cart after migration
        Session::forget(self::CART_SESSION_KEY);
    }
}


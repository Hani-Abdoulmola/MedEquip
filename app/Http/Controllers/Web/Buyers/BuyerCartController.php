<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
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
        $cartItems = $this->getCart();
        $products = [];
        $totalItems = 0;

        if (!empty($cartItems)) {
            $productIds = array_keys($cartItems);
            $productsQuery = Product::whereIn('id', $productIds)
                ->where('is_active', true)
                ->where('review_status', 'approved')
                ->with(['category', 'suppliers' => function ($q) {
                    $q->where('is_verified', true)->where('is_active', true);
                }])
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $productId => $item) {
                if ($productsQuery->has($productId)) {
                    $product = $productsQuery->get($productId);
                    $products[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'specifications' => $item['specifications'] ?? '',
                        'unit' => $item['unit'] ?? 'وحدة',
                    ];
                    $totalItems += $item['quantity'];
                }
            }
        }

        return view('buyer.cart.index', compact('products', 'totalItems'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product): JsonResponse|RedirectResponse
    {
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
        ]);

        $cart = $this->getCart();
        $productId = $product->id;

        if (isset($cart[$productId])) {
            // Update existing item
            $cart[$productId]['quantity'] += $validated['quantity'];
            if (!empty($validated['specifications'])) {
                $cart[$productId]['specifications'] = $validated['specifications'];
            }
            if (!empty($validated['unit'])) {
                $cart[$productId]['unit'] = $validated['unit'];
            }
        } else {
            // Add new item
            $cart[$productId] = [
                'quantity' => $validated['quantity'],
                'specifications' => $validated['specifications'] ?? '',
                'unit' => $validated['unit'] ?? 'وحدة',
                'added_at' => now()->toIso8601String(),
            ];
        }

        $this->saveCart($cart);

        $cartCount = count($cart);
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
    public function update(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10000',
            'specifications' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:50',
        ]);

        $cart = $this->getCart();
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $validated['quantity'];
            if (isset($validated['specifications'])) {
                $cart[$productId]['specifications'] = $validated['specifications'];
            }
            if (isset($validated['unit'])) {
                $cart[$productId]['unit'] = $validated['unit'];
            }
            $this->saveCart($cart);

            $message = 'تم تحديث الكمية';
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);
        }

        $message = 'المنتج غير موجود في السلة';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        return back()->with('error', $message);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $cart = $this->getCart();
        $productId = $product->id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);

            $message = 'تم حذف المنتج من السلة';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => count($cart),
                ]);
            }
            return back()->with('success', $message);
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
        $this->saveCart([]);

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
        $cart = $this->getCart();
        return response()->json(['count' => count($cart)]);
    }

    /**
     * Show the checkout/submit RFQ form.
     */
    public function checkout(): View|RedirectResponse
    {
        $cartItems = $this->getCart();

        if (empty($cartItems)) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'السلة فارغة. أضف منتجات أولاً.');
        }

        $productIds = array_keys($cartItems);
        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->with(['category'])
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($cartItems as $productId => $cartItem) {
            if ($products->has($productId)) {
                $product = $products->get($productId);
                $items[] = [
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'specifications' => $cartItem['specifications'] ?? '',
                    'unit' => $cartItem['unit'] ?? 'وحدة',
                ];
            }
        }

        if (empty($items)) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'لا توجد منتجات صالحة في السلة.');
        }

        return view('buyer.cart.checkout', compact('items'));
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

        $cartItems = $this->getCart();

        if (empty($cartItems)) {
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

            // Get products data
            $productIds = array_keys($cartItems);
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Create RFQ items from cart
            foreach ($cartItems as $productId => $cartItem) {
                if ($products->has($productId)) {
                    $product = $products->get($productId);
                    RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'product_id' => $productId,
                        'item_name' => $product->name,
                        'specifications' => $cartItem['specifications'] ?? null,
                        'quantity' => $cartItem['quantity'],
                        'unit' => $cartItem['unit'] ?? 'وحدة',
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
                    'items_count' => count($cartItems),
                    'source' => 'cart',
                ])
                ->log('قام المشتري بإنشاء RFQ من السلة');

            // Clear cart after successful submission
            $this->saveCart([]);

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
     * Get the cart contents from session.
     */
    private function getCart(): array
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Save cart contents to session.
     */
    private function saveCart(array $cart): void
    {
        Session::put(self::CART_SESSION_KEY, $cart);
    }
}


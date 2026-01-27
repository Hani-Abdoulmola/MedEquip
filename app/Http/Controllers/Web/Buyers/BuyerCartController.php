<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\BuyerCartItem;
use App\Models\Product;
use App\Services\RfqBuilderService;
use App\Services\RfqCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Buyer Cart / RFQ Builder Controller (Phase 1)
 *
 * Terminology: "Cart" = RFQ Builder. UI labels use "منشئ طلبات العروض".
 */
class BuyerCartController extends Controller
{
    public function __construct(
        protected RfqBuilderService $builderService,
        protected RfqCreationService $rfqCreationService
    ) {}

    public function index(): View
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $this->builderService->migrateSessionCartIfExists($buyer, $cart);

        $summary = $this->builderService->getBuilderSummary($cart);
        $products = [];
        $totalItems = 0;

        foreach ($summary['items'] as $item) {
            if (!$item->product) {
                continue;
            }
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

        $templates = $this->builderService->getTemplates($buyer);

        return view('buyer.cart.index', [
            'products' => $products,
            'totalItems' => $totalItems,
            'cart' => $cart,
            'summary' => $summary['summary'],
            'templates' => $templates,
        ]);
    }

    /**
     * Load a saved template into active builder (Phase 2).
     */
    public function loadTemplate(\App\Models\BuyerCart $template): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer || $template->buyer_id !== $buyer->id || !$template->is_template) {
            abort(403, 'قالب غير صالح');
        }

        try {
            $this->builderService->loadTemplate($buyer, $template);
            return redirect()->route('buyer.cart.index')
                ->with('success', "تم تحميل القالب '{$template->template_name}' بنجاح.");
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء تحميل القالب: ' . $e->getMessage());
        }
    }

    public function add(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10000',
            'specifications' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'max_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $cart = $this->builderService->getOrCreateBuilder($buyer);
            $this->builderService->addProduct($cart, $product, $validated);
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }

        $cartCount = $cart->items()->count();
        $message = 'تم إضافة المنتج إلى منشئ طلبات العروض';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'cart_count' => $cartCount]);
        }
        return back()->with('success', $message);
    }

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
            'max_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->builderService->updateItem($cartItem, $validated);
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }

        $message = 'تم تحديث الكمية';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
    }

    public function remove(Request $request, BuyerCartItem $cartItem): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer || $cartItem->cart->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لحذف هذا العنصر');
        }

        $cart = $cartItem->cart;
        $this->builderService->removeItem($cartItem);
        $cartCount = $cart->items()->count();
        $message = 'تم حذف المنتج من منشئ الطلبات';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'cart_count' => $cartCount]);
        }
        return back()->with('success', $message);
    }

    public function removeByProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            return $this->remove($request, $cartItem);
        }

        $message = 'المنتج غير موجود في منشئ الطلبات';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        return back()->with('error', $message);
    }

    public function updateByProduct(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            return $this->update($request, $cartItem);
        }

        $message = 'المنتج غير موجود في منشئ الطلبات';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }
        return back()->with('error', $message);
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $this->builderService->clearBuilder($cart);
        $message = 'تم إفراغ منشئ الطلبات';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'cart_count' => 0]);
        }
        return back()->with('success', $message);
    }

    public function count(): JsonResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            return response()->json(['count' => 0]);
        }

        $cart = \App\Models\BuyerCart::where('buyer_id', $buyer->id)->where('is_active', true)->first();
        $count = $cart ? $cart->items()->count() : 0;
        return response()->json(['count' => $count]);
    }

    public function checkout(): View|RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $summary = $this->builderService->getBuilderSummary($cart);

        if ($summary['items']->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'منشئ الطلبات فارغ. أضف منتجات أولاً.');
        }
        if (!$summary['summary']['can_submit']) {
            return redirect()->route('buyer.cart.index')
                ->with('error', 'يوجد عناصر غير صالحة. يرجى مراجعتها أو إزالتها قبل الإرسال.');
        }

        $items = [];
        foreach ($summary['items'] as $item) {
            if ($item->product) {
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

        return view('buyer.cart.checkout', compact('items', 'cart'));
    }

    public function submitRfq(Request $request): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;
        if (!$buyer) {
            abort(403, 'لا يوجد ملف تعريف للمشتري');
        }

        $cart = $this->builderService->getOrCreateBuilder($buyer);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'deadline' => 'nullable|date|after:today',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,open',
            'save_template' => 'sometimes|boolean',
            'template_name' => 'nullable|string|max:255|required_if:save_template,1',
        ]);

        $metadata = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'status' => $validated['status'],
            'save_template' => !empty($validated['save_template']),
            'template_name' => $validated['template_name'] ?? null,
        ];

        try {
            $rfq = $this->rfqCreationService->createFromBuilder($cart, $metadata);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()
            ->route('buyer.rfqs.show', $rfq)
            ->with('success', 'تم إنشاء طلب عرض السعر بنجاح من منشئ الطلبات.');
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Services\NotificationService;
use App\Services\ProductCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin Product Request Controller
 * 
 * Handles admin review of supplier product requests.
 * Supports approve (create new), merge (link existing), and reject actions.
 */
class AdminProductRequestController extends Controller
{
    protected ProductCatalogService $catalogService;

    public function __construct(ProductCatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * Display a listing of product requests.
     */
    public function index(Request $request): View
    {
        // Authorization check
        if (!auth()->user()->can('products.view')) {
            abort(403, 'ليس لديك صلاحية عرض طلبات المنتجات');
        }

        $query = ProductRequest::with(['supplier.user', 'category', 'manufacturer', 'duplicateProduct', 'reviewer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to showing pending requests first
            $query->orderByRaw("FIELD(status, 'pending', 'duplicate') DESC");
        }

        // Filter by supplier
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'pending' => ProductRequest::pending()->count(),
            'duplicate' => ProductRequest::where('status', 'duplicate')->count(),
            'approved' => ProductRequest::approved()->count(),
            'rejected' => ProductRequest::rejected()->count(),
            'merged' => ProductRequest::where('status', 'merged')->count(),
        ];

        return view('admin.product-requests.index', compact('requests', 'stats'));
    }

    /**
     * Show the review page for a product request.
     */
    public function review(ProductRequest $productRequest): View
    {
        if (!auth()->user()->can('products.approve')) {
            abort(403, 'ليس لديك صلاحية مراجعة طلبات المنتجات');
        }

        $productRequest->load(['supplier.user', 'category', 'manufacturer', 'duplicateProduct', 'existingProduct']);

        // Find potential duplicates for comparison
        $potentialDuplicates = $this->catalogService->findDuplicates(
            $productRequest->name,
            $productRequest->brand,
            $productRequest->model
        );

        // Get existing approved products for merge option
        $existingProducts = Product::approved()
            ->where('is_active', true)
            ->when($productRequest->category_id, function ($q) use ($productRequest) {
                $q->where('category_id', $productRequest->category_id);
            })
            ->with(['category', 'manufacturer'])
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('admin.product-requests.review', compact(
            'productRequest',
            'potentialDuplicates',
            'existingProducts'
        ));
    }

    /**
     * Approve a product request (create new canonical product).
     */
    public function approve(Request $request, ProductRequest $productRequest): RedirectResponse
    {
        if (!auth()->user()->can('products.approve')) {
            abort(403, 'ليس لديك صلاحية الموافقة على طلبات المنتجات');
        }

        if (!$productRequest->canBeReviewed()) {
            return back()->withErrors(['error' => 'لا يمكن مراجعة هذا الطلب']);
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $product = $this->catalogService->approveRequest(
                $productRequest,
                Auth::user(),
                $request->admin_notes
            );

            // Log activity
            activity('product_requests')
                ->performedOn($productRequest)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'approved',
                    'product_id' => $product->id,
                ])
                ->log('✅ تمت الموافقة على طلب منتج جديد');

            return redirect()
                ->route('admin.product-requests.index')
                ->with('success', "✅ تمت الموافقة على المنتج: {$product->name}");

        } catch (\Exception $e) {
            Log::error('Product request approval failed', [
                'request_id' => $productRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشلت عملية الموافقة. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Merge a product request with an existing product.
     */
    public function merge(Request $request, ProductRequest $productRequest): RedirectResponse
    {
        if (!auth()->user()->can('products.approve')) {
            abort(403, 'ليس لديك صلاحية دمج طلبات المنتجات');
        }

        if (!$productRequest->canBeReviewed()) {
            return back()->withErrors(['error' => 'لا يمكن مراجعة هذا الطلب']);
        }

        $request->validate([
            'existing_product_id' => 'required|exists:products,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $existingProduct = Product::findOrFail($request->existing_product_id);

            $this->catalogService->mergeRequest(
                $productRequest,
                $existingProduct,
                Auth::user(),
                $request->admin_notes
            );

            // Log activity
            activity('product_requests')
                ->performedOn($productRequest)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'merged',
                    'existing_product_id' => $existingProduct->id,
                ])
                ->log('🔗 تم دمج طلب المنتج مع منتج موجود');

            return redirect()
                ->route('admin.product-requests.index')
                ->with('success', "🔗 تم دمج الطلب مع المنتج: {$existingProduct->name}");

        } catch (\Exception $e) {
            Log::error('Product request merge failed', [
                'request_id' => $productRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشلت عملية الدمج. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Reject a product request.
     */
    public function reject(Request $request, ProductRequest $productRequest): RedirectResponse
    {
        if (!auth()->user()->can('products.reject')) {
            abort(403, 'ليس لديك صلاحية رفض طلبات المنتجات');
        }

        if (!$productRequest->canBeReviewed()) {
            return back()->withErrors(['error' => 'لا يمكن مراجعة هذا الطلب']);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->catalogService->rejectRequest(
                $productRequest,
                Auth::user(),
                $request->rejection_reason,
                $request->admin_notes
            );

            // Log activity
            activity('product_requests')
                ->performedOn($productRequest)
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => 'rejected',
                    'reason' => $request->rejection_reason,
                ])
                ->log('❌ تم رفض طلب المنتج');

            return redirect()
                ->route('admin.product-requests.index')
                ->with('success', '❌ تم رفض الطلب');

        } catch (\Exception $e) {
            Log::error('Product request rejection failed', [
                'request_id' => $productRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشلت عملية الرفض. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Show details of a product request.
     */
    public function show(ProductRequest $productRequest): View
    {
        if (!auth()->user()->can('products.view')) {
            abort(403, 'ليس لديك صلاحية عرض تفاصيل الطلب');
        }

        $productRequest->load([
            'supplier.user',
            'category',
            'manufacturer',
            'duplicateProduct',
            'existingProduct',
            'reviewer',
        ]);

        return view('admin.product-requests.show', compact('productRequest'));
    }
}


<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RfqRequest;
use App\Models\Buyer;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin RFQ Controller
 *
 * Handles complete RFQ management for administrators.
 * Admin can create, view, edit, delete RFQs, monitor their status, and manage assigned suppliers.
 */
class AdminRfqController extends Controller
{
    /**
     * Display list of all RFQs with filtering and statistics.
     */
    public function index(Request $request): View
    {
        $query = Rfq::with(['buyer', 'items', 'quotations', 'assignedSuppliers'])
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by buyer
        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by visibility
        if ($request->filled('visibility')) {
            if ($request->visibility === 'public') {
                $query->where('is_public', true);
            } else {
                $query->where('is_public', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $rfqs = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Rfq::count(),
            'open' => Rfq::where('status', 'open')->count(),
            'closed' => Rfq::where('status', 'closed')->count(),
            'awarded' => Rfq::where('status', 'awarded')->count(),
            'cancelled' => Rfq::where('status', 'cancelled')->count(),
            'total_quotations' => Quotation::count(),
            'pending_quotations' => Quotation::where('status', 'pending')->count(),
        ];

        // For filters
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('admin.rfqs.index', compact('rfqs', 'stats', 'buyers'));
    }

    /**
     * Show the form for creating a new RFQ.
     */
    public function create(): View
    {
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('admin.rfqs.create', [
            'rfq' => new Rfq,
            'buyers' => $buyers,
        ]);
    }

    /**
     * Store a newly created RFQ.
     */
    public function store(RfqRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['reference_code'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_RFQ,
                Rfq::class
            );

            $rfq = Rfq::create($data);

            // Notify buyer
            if ($rfq->buyer && $rfq->buyer->user) {
                NotificationService::send(
                    $rfq->buyer->user,
                    '✅ تم تسجيل طلب عرض السعر',
                    "تم إنشاء RFQ بعنوان {$rfq->title} بنجاح.",
                    route('admin.rfqs.show', $rfq->id)
                );
            }

            // Notify verified suppliers about new RFQ
            if ($rfq->is_public) {
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
            activity('admin_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                    'reference_code' => $rfq->reference_code,
                ])
                ->log('قام المسؤول بإنشاء RFQ جديد');

            DB::commit();

            return redirect()
                ->route('admin.rfqs.index')
                ->with('success', '✅ تم إنشاء طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    /**
     * Display RFQ details with quotations and suppliers.
     */
    public function show(Rfq $rfq): View
    {
        $this->authorize('view', $rfq);

        $rfq->load([
            'buyer.user',
            'items',
            'quotations.supplier',
            'quotations.items',
            'assignedSuppliers',
            'creator',
        ]);

        // Get all verified suppliers for assignment
        $allSuppliers = Supplier::where('is_verified', true)
            ->orderBy('company_name')
            ->get();

        return view('admin.rfqs.show', compact('rfq', 'allSuppliers'));
    }

    /**
     * Show the form for editing the specified RFQ.
     */
    public function edit(Rfq $rfq): View
    {
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('admin.rfqs.edit', compact('rfq', 'buyers'));
    }

    /**
     * Update the specified RFQ.
     */
    public function update(RfqRequest $request, Rfq $rfq): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            if (($data['status'] ?? null) === 'closed' && is_null($rfq->closed_at)) {
                $data['closed_at'] = now();
            }

            $rfq->update($data);

            // Notify suppliers if RFQ is closed
            if ($rfq->status === 'closed') {
                $suppliers = Supplier::where('is_verified', true)->get();
                foreach ($suppliers as $supplier) {
                    if ($supplier->user) {
                        NotificationService::send(
                            $supplier->user,
                            '🚫 تم إغلاق RFQ',
                            "تم إغلاق الطلب: {$rfq->title}.",
                            route('supplier.rfqs.index')
                        );
                    }
                }
            }

            // Log activity
            activity('admin_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'updated_by' => Auth::id(),
                    'rfq_id' => $rfq->id,
                    'rfq_title' => $rfq->title,
                    'status' => $rfq->status,
                    'changes' => $rfq->getChanges(),
                ])
                ->log('قام المسؤول بتحديث RFQ: ' . $rfq->title);

            DB::commit();

            return redirect()
                ->route('admin.rfqs.index')
                ->with('success', '✅ تم تحديث طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ update error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل التحديث: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified RFQ.
     */
    public function destroy(Rfq $rfq): RedirectResponse
    {
        try {
            $rfqTitle = $rfq->title;
            $rfq->delete();

            // Log activity
            activity('admin_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $rfq->id,
                    'rfq_title' => $rfqTitle,
                    'rfq_reference_code' => $rfq->reference_code,
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                ])
                ->log('قام المسؤول بحذف RFQ: ' . $rfqTitle);

            return redirect()
                ->route('admin.rfqs.index')
                ->with('success', '❌ تم حذف طلب عرض السعر بنجاح.');

        } catch (\Throwable $e) {
            Log::error('Admin RFQ deletion error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'فشل الحذف: ' . $e->getMessage()]);
        }
    }

    /**
     * Update RFQ status.
     */
    public function updateStatus(Request $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('updateStatus', $rfq);

        $validated = $request->validate([
            'status' => 'required|in:draft,open,under_review,closed,awarded,cancelled',
        ]);

        $oldStatus = $rfq->status;

        DB::beginTransaction();

        try {
            $rfq->update([
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
                'closed_at' => in_array($validated['status'], ['closed', 'awarded', 'cancelled']) ? now() : null,
            ]);

            // Notify buyer
            if ($rfq->buyer && $rfq->buyer->user) {
                NotificationService::send(
                    $rfq->buyer->user,
                    'تم تحديث حالة طلب عرض السعر',
                    "تم تغيير حالة الطلب '{$rfq->title}' إلى " . $this->getStatusLabel($validated['status']),
                    route('admin.rfqs.show', $rfq)
                );
            }

            // Notify assigned suppliers
            foreach ($rfq->assignedSuppliers as $supplier) {
                if ($supplier->user) {
                    NotificationService::send(
                        $supplier->user,
                        'تم تحديث حالة طلب عرض السعر',
                        "تم تغيير حالة الطلب '{$rfq->title}' إلى " . $this->getStatusLabel($validated['status']),
                        route('supplier.rfqs.show', $rfq)
                    );
                }
            }

            // Log activity
            activity('admin_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                ])
                ->log('قام المسؤول بتغيير حالة RFQ');

            DB::commit();

            return back()->with('success', 'تم تحديث حالة الطلب بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin RFQ status update error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث الحالة']);
        }
    }

    /**
     * Assign suppliers to an RFQ.
     */
    public function assignSuppliers(Request $request, Rfq $rfq): RedirectResponse
    {
        $this->authorize('assignSuppliers', $rfq);

        $validated = $request->validate([
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);

        DB::beginTransaction();

        try {
            // Get current assigned suppliers
            $currentSuppliers = $rfq->assignedSuppliers->pluck('id')->toArray();
            $newSuppliers = $validated['supplier_ids'];

            // Sync suppliers (will add new and remove unselected)
            $syncData = [];
            foreach ($newSuppliers as $supplierId) {
                $syncData[$supplierId] = [
                    'status' => 'invited',
                    'invited_at' => now(),
                ];
            }
            $rfq->assignedSuppliers()->sync($syncData);

            // Notify newly assigned suppliers
            $addedSuppliers = array_diff($newSuppliers, $currentSuppliers);
            foreach ($addedSuppliers as $supplierId) {
                $supplier = Supplier::find($supplierId);
                if ($supplier && $supplier->user) {
                    NotificationService::send(
                        $supplier->user,
                        '📋 تمت دعوتك لتقديم عرض سعر',
                        "تمت دعوتك للمشاركة في طلب عرض السعر: {$rfq->title}",
                        route('supplier.rfqs.show', $rfq),
                        'fas fa-file-invoice-dollar',
                        'info'
                    );
                }
            }

            // Log activity
            activity('admin_rfqs')
                ->performedOn($rfq)
                ->causedBy(Auth::user())
                ->withProperties([
                    'added_suppliers' => $addedSuppliers,
                    'total_suppliers' => count($newSuppliers),
                ])
                ->log('قام المسؤول بتعيين موردين لـ RFQ');

            DB::commit();

            return back()->with('success', 'تم تحديث قائمة الموردين المعينين بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin assign suppliers error', [
                'rfq_id' => $rfq->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تعيين الموردين']);
        }
    }

    /**
     * Toggle RFQ visibility (public/private).
     */
    public function toggleVisibility(Rfq $rfq): RedirectResponse
    {
        $this->authorize('toggleVisibility', $rfq);

        $rfq->update([
            'is_public' => !$rfq->is_public,
            'updated_by' => Auth::id(),
        ]);

        // Log activity
        activity('admin_rfqs')
            ->performedOn($rfq)
            ->causedBy(Auth::user())
            ->withProperties(['is_public' => $rfq->is_public])
            ->log('قام المسؤول بتغيير رؤية RFQ');

        $message = $rfq->is_public ? 'أصبح الطلب عاماً لجميع الموردين' : 'أصبح الطلب خاصاً بالموردين المعينين';

        return back()->with('success', $message);
    }

    /**
     * Get status label in Arabic.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'open' => 'مفتوح',
            'closed' => 'مغلق',
            'awarded' => 'تم الترسية',
            'cancelled' => 'ملغي',
            default => $status,
        };
    }
}


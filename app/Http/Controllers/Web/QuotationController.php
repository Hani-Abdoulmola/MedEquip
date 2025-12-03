<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📋 عرض جميع عروض الأسعار
     */
    public function index()
    {
        $query = Quotation::with(['rfq.buyer', 'supplier'])->latest('id');

        // 🧩 فلترة حسب نوع المستخدم
        $user = auth()->user();
        if ($user && $user->hasRole('Supplier') && $user->supplierProfile) {
            $query->where('supplier_id', $user->supplierProfile->id);
        }

        if ($user && $user->hasRole('Buyer') && $user->buyerProfile) {
            $buyerId = $user->buyerProfile->id;
            $query->whereHas('rfq', fn ($q) => $q->where('buyer_id', $buyerId));
        }

        $quotations = $query->paginate(20);

        return view('quotations.index', compact('quotations'));
    }

    /**
     * ➕ صفحة إنشاء عرض سعر جديد
     */
    public function create()
    {
        $rfqs = Rfq::where('status', 'open')->pluck('title', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('quotations.form', [
            'quotation' => new Quotation,
            'rfqs' => $rfqs,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * 💾 حفظ عرض سعر جديد
     */
    public function store(QuotationRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['reference_code'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_QUOTATION,
                \App\Models\Quotation::class
            );
            $data['created_by'] = Auth::id();

            $quotation = Quotation::create($data);

            // 🔔 إشعار المديرين
            NotificationService::notifyAdmins(
                '📄 عرض سعر جديد',
                'تم إنشاء عرض سعر جديد من المورد '.($quotation->supplier->company_name ?? 'غير معروف'),
                route('quotations.show', $quotation->id)
            );

            // 🔔 إشعار المشتري صاحب الطلب
            if ($quotation->rfq && $quotation->rfq->buyer && $quotation->rfq->buyer->user) {
                NotificationService::send(
                    $quotation->rfq->buyer->user,
                    '💰 تم استلام عرض سعر جديد',
                    'وصل عرض جديد لطلبك: '.$quotation->rfq->title,
                    route('quotations.show', $quotation->id)
                );
            }

            // 🔔 إشعار المورد نفسه
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '✅ تم تسجيل عرضك بنجاح',
                    'تم تسجيل عرض السعر للطلب: '.($quotation->rfq->title ?? 'غير محدد'),
                    route('quotations.show', $quotation->id)
                );
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties([
                    'rfq_id' => $quotation->rfq_id,
                    'supplier_id' => $quotation->supplier_id,
                    'status' => $quotation->status ?? 'draft',
                ])
                ->log('📄 تم إنشاء عرض سعر جديد');

            DB::commit();

            return redirect()
                ->route('quotations.index')
                ->with('success', '✅ تم إضافة عرض السعر بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quotation store error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة عرض السعر: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل عرض سعر
     */
    public function edit(Quotation $quotation)
    {
        $rfqs = Rfq::pluck('title', 'id');
        $suppliers = Supplier::pluck('company_name', 'id');

        return view('quotations.form', compact('quotation', 'rfqs', 'suppliers'));
    }

    /**
     * 🔄 تحديث بيانات عرض السعر
     */
    public function update(QuotationRequest $request, Quotation $quotation)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $quotation->update($data);

            // إشعار المشتري بتحديث العرض
            if ($quotation->rfq && $quotation->rfq->buyer && $quotation->rfq->buyer->user) {
                NotificationService::send(
                    $quotation->rfq->buyer->user,
                    '📦 تم تحديث عرض السعر',
                    'تم تعديل عرض السعر من المورد '.($quotation->supplier->company_name ?? 'غير معروف'),
                    route('quotations.show', $quotation->id)
                );
            }

            activity()
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->withProperties(['updated_by' => Auth::id()])
                ->log('✏️ تم تعديل عرض السعر');

            DB::commit();

            return redirect()
                ->route('quotations.index')
                ->with('success', '✅ تم تحديث عرض السعر بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quotation update error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل تحديث عرض السعر: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف عرض السعر
     */
    public function destroy(Quotation $quotation)
    {
        try {
            $quotation->delete();

            activity()
                ->performedOn($quotation)
                ->causedBy(Auth::user())
                ->log('🗑️ تم حذف عرض السعر');

            // إشعار المورد بالحذف (اختياري)
            if ($quotation->supplier && $quotation->supplier->user) {
                NotificationService::send(
                    $quotation->supplier->user,
                    '⚠️ تم حذف عرض السعر',
                    'تم حذف عرض السعر رقم '.$quotation->reference_code,
                    route('quotations.index')
                );
            }

            return redirect()
                ->route('quotations.index')
                ->with('success', '❌ تم حذف عرض السعر بنجاح');
        } catch (\Throwable $e) {
            Log::error('Quotation delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف عرض السعر: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل عرض السعر
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['rfq.buyer.user', 'supplier.user']);

        return view('quotations.show', compact('quotation'));
    }
}

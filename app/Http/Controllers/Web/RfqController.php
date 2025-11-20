<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RfqRequest;
use App\Models\Buyer;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RfqController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📋 عرض قائمة RFQs مع فلترة وبحث ديناميكي
     */
    public function index(Request $request)
    {
        $query = Rfq::with(['buyer'])->latest('id');

        // 🧠 فلترة حسب نوع المستخدم
        if (auth()->user()->hasRole('Buyer') && auth()->user()->buyerProfile) {
            $query->where('buyer_id', auth()->user()->buyerProfile->id);
        } elseif (auth()->user()->hasRole('Supplier')) {
            $query->where('status', 'open');
        }

        // 🔍 فلترة إضافية
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $rfqs = $query->paginate(20)->withQueryString();
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('rfqs.index', compact('rfqs', 'buyers'));
    }

    /**
     * ➕ إنشاء RFQ جديد
     */
    public function create()
    {
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('rfqs.form', [
            'rfq' => new Rfq,
            'buyers' => $buyers,
        ]);
    }

    /**
     * 💾 حفظ RFQ جديد
     */
    public function store(RfqRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['reference_code'] = ReferenceCodeService::generateUnique(
                ReferenceCodeService::PREFIX_RFQ,
                \App\Models\Rfq::class
            );

            $rfq = Rfq::create($data);

            // 🔔 إشعار الإدارة
            NotificationService::notifyAdmins(
                '📢 طلب عرض سعر جديد',
                "تم إنشاء RFQ جديد بعنوان {$rfq->title} من قبل المشتري ".($rfq->buyer->organization_name ?? 'غير معروف'),
                route('rfqs.show', $rfq->id)
            );

            // 🔔 إشعار المشتري
            if ($rfq->buyer && $rfq->buyer->user) {
                NotificationService::send(
                    $rfq->buyer->user,
                    '✅ تم تسجيل طلب عرض السعر',
                    "تم إنشاء RFQ بعنوان {$rfq->title} بنجاح.",
                    route('rfqs.show', $rfq->id)
                );
            }

            // 🔔 إشعار الموردين الموثقين (فرصة جديدة)
            $suppliers = Supplier::where('is_verified', true)->get();
            foreach ($suppliers as $supplier) {
                if ($supplier->user) {
                    NotificationService::send(
                        $supplier->user,
                        '🆕 طلب عرض سعر جديد',
                        "يوجد طلب عرض سعر جديد بعنوان: {$rfq->title}.",
                        route('rfqs.show', $rfq->id)
                    );
                }
            }

            // 🧾 سجل النشاط
            activity()
                ->performedOn($rfq)
                ->causedBy(auth()->user())
                ->withProperties([
                    'buyer_id' => $rfq->buyer_id,
                    'status' => $rfq->status,
                    'reference_code' => $rfq->reference_code,
                ])
                ->log('📢 تم إنشاء RFQ جديد');

            DB::commit();

            return redirect()
                ->route('rfqs.index')
                ->with('success', '✅ تم إنشاء طلب عرض السعر بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Store RFQ failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: '.$e->getMessage()]);
        }
    }

    /**
     * ✏️ تعديل RFQ
     */
    public function edit(Rfq $rfq)
    {
        $buyers = Buyer::orderBy('organization_name')->pluck('organization_name', 'id');

        return view('rfqs.form', compact('rfq', 'buyers'));
    }

    /**
     * 🔄 تحديث RFQ
     */
    public function update(RfqRequest $request, Rfq $rfq)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            if (($data['status'] ?? null) === 'closed' && is_null($rfq->closed_at)) {
                $data['closed_at'] = now();
            }

            $rfq->update($data);

            // إشعار الموردين بإغلاق أو تعديل الطلب
            if ($rfq->status === 'closed') {
                $suppliers = Supplier::where('is_verified', true)->get();
                foreach ($suppliers as $supplier) {
                    if ($supplier->user) {
                        NotificationService::send(
                            $supplier->user,
                            '🚫 تم إغلاق RFQ',
                            "تم إغلاق الطلب: {$rfq->title}.",
                            route('rfqs.index')
                        );
                    }
                }
            }

            activity()
                ->performedOn($rfq)
                ->causedBy(auth()->user())
                ->withProperties(['updated_by' => auth()->id()])
                ->log('✏️ تم تحديث RFQ');

            DB::commit();

            return redirect()
                ->route('rfqs.index')
                ->with('success', '✅ تم تحديث طلب عرض السعر بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Update RFQ failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل التحديث: '.$e->getMessage()]);
        }
    }

    /**
     * 🗑️ حذف RFQ (Soft Delete)
     */
    public function destroy(Rfq $rfq)
    {
        try {
            $rfq->delete();

            activity()
                ->performedOn($rfq)
                ->causedBy(auth()->user())
                ->log('🗑️ تم حذف RFQ');

            return redirect()
                ->route('rfqs.index')
                ->with('success', '❌ تم حذف طلب عرض السعر بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Delete RFQ failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل الحذف: '.$e->getMessage()]);
        }
    }

    /**
     * 👁️ عرض تفاصيل RFQ
     */
    public function show(Rfq $rfq)
    {
        $rfq->load(['buyer.user', 'quotations.supplier.user']);

        return view('rfqs.show', compact('rfq'));
    }
}

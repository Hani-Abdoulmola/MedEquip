<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationApprovalController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📋 عرض صفحة طلبات التسجيل المعلقة
     */
    public function index()
    {
        // Get pending and rejected suppliers
        $suppliers = Supplier::with('user')
            ->whereIn('is_verified', [false, true])
            ->latest('created_at')
            ->get();

        // Get pending and rejected buyers
        $buyers = Buyer::with('user')
            ->whereIn('is_verified', [false, true])
            ->latest('created_at')
            ->get();

        // Calculate stats
        $stats = [
            'total_pending' => $suppliers->where('is_verified', false)->where('rejection_reason', null)->count() +
                              $buyers->where('is_verified', false)->where('rejection_reason', null)->count(),
            'pending_suppliers' => $suppliers->where('is_verified', false)->where('rejection_reason', null)->count(),
            'pending_buyers' => $buyers->where('is_verified', false)->where('rejection_reason', null)->count(),
            'total_rejected' => $suppliers->whereNotNull('rejection_reason')->count() +
                               $buyers->whereNotNull('rejection_reason')->count(),
        ];

        return view('admin.registrations.pending', compact('suppliers', 'buyers', 'stats'));
    }

    /**
     * ✅ الموافقة على طلب تسجيل
     */
    public function approve(Request $request, string $type, int $id)
    {
        DB::beginTransaction();

        try {
            // Get the model based on type
            $model = $this->getModel($type, $id);

            if (!$model) {
                return back()->withErrors(['error' => 'السجل غير موجود']);
            }

            // Update verification status
            $model->update([
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            // Log activity
            $entityName = $type === 'supplier' ? 'مورد' : 'مشتري';
            $organizationName = $type === 'supplier' ? $model->company_name : $model->organization_name;

            activity('registrations')
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->withProperties([
                    'type' => $type,
                    'organization_name' => $organizationName,
                    'approved_by' => auth()->user()->name,
                ])
                ->log("✅ تمت الموافقة على تسجيل {$entityName}: {$organizationName}");

            // Send notification to user
            if ($model->user) {
                NotificationService::send(
                    $model->user,
                    '✅ تم الموافقة على تسجيلك',
                    "تم الموافقة على طلب تسجيلك في منصة MediTrust. يمكنك الآن الدخول والبدء في استخدام المنصة.",
                    $type === 'supplier' ? route('supplier.dashboard') : route('buyer.dashboard')
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.registrations.pending')
                ->with('success', "✅ تمت الموافقة على تسجيل {$entityName} بنجاح");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Registration approval error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء الموافقة على التسجيل: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * ❌ رفض طلب تسجيل
     */
    public function reject(Request $request, string $type, int $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'يجب إدخال سبب الرفض',
            'rejection_reason.min' => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل',
            'rejection_reason.max' => 'سبب الرفض يجب ألا يتجاوز 1000 حرف',
        ]);

        DB::beginTransaction();

        try {
            // Get the model based on type
            $model = $this->getModel($type, $id);

            if (!$model) {
                return back()->withErrors(['error' => 'السجل غير موجود']);
            }

            // Update rejection status
            $model->update([
                'is_verified' => false,
                'is_active' => false,
                'verified_at' => null,
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Log activity
            $entityName = $type === 'supplier' ? 'مورد' : 'مشتري';
            $organizationName = $type === 'supplier' ? $model->company_name : $model->organization_name;




            activity('registrations')
                ->performedOn($model)
                ->causedBy(auth()->user())
                ->withProperties([
                    'type' => $type,
                    'organization_name' => $organizationName,
                    'rejected_by' => auth()->user()->name,
                    'rejection_reason' => $request->rejection_reason,
                ])
                ->log("❌ تم رفض تسجيل {$entityName}: {$organizationName}");

            // Send notification to user
            if ($model->user) {
                NotificationService::send(
                    $model->user,
                    '❌ تم رفض طلب التسجيل',
                    "تم رفض طلب تسجيلك في منصة MediTrust. السبب: {$request->rejection_reason}",
                    route('auth.waiting-approval')
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.registrations.pending')
                ->with('success', "❌ تم رفض تسجيل {$entityName}");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Registration rejection error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء رفض التسجيل: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * 🔍 الحصول على النموذج بناءً على النوع
     */
    private function getModel(string $type, int $id)
    {
        return match ($type) {
            'supplier' => Supplier::find($id),
            'buyer' => Buyer::find($id),
            default => null,
        };
    }
}

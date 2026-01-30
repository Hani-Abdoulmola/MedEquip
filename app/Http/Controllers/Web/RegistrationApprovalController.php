<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Get ONLY pending suppliers (not verified AND no rejection reason)
        $pendingSuppliers = Supplier::with('user')
            ->where('is_verified', false)
            ->whereNull('rejection_reason')
            ->latest('created_at')
            ->get();

        // Get ONLY pending buyers (not verified AND no rejection reason)
        $pendingBuyers = Buyer::with('user')
            ->where('is_verified', false)
            ->whereNull('rejection_reason')
            ->latest('created_at')
            ->get();

        // Calculate stats
        $stats = [
            'total_pending' => $pendingSuppliers->count() + $pendingBuyers->count(),
            'pending_suppliers' => $pendingSuppliers->count(),
            'pending_buyers' => $pendingBuyers->count(),
            'total_rejected' => Supplier::whereNotNull('rejection_reason')->count() +
                               Buyer::whereNotNull('rejection_reason')->count(),
        ];

        return view('admin.registrations.pending', compact('pendingSuppliers', 'pendingBuyers', 'stats'));
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

            if (! $model) {
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
                ->causedBy(Auth::user())
                ->withProperties([
                    'type' => $type,
                    'organization_name' => $organizationName,
                    'approved_by' => Auth::user()->name,
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

            if (! $model) {
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
                ->causedBy(Auth::user())
                ->withProperties([
                    'type' => $type,
                    'organization_name' => $organizationName,
                    'rejected_by' => Auth::user()->name,
                    'rejection_reason' => $request->rejection_reason,
                ])
                ->log("❌ تم رفض تسجيل {$entityName}: {$organizationName}");

            // Send notification to user
            if ($model->user) {
                NotificationService::send(
                    $model->user,
                    '❌ تم رفض طلب التسجيل',
                    "تم رفض طلب تسجيلك في منصة MedEquip. السبب: {$request->rejection_reason}",
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

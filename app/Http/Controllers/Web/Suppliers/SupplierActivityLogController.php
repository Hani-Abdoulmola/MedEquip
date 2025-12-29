<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Filters\ActivityLogFilter;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Supplier Activity Log Controller
 *
 * Allows suppliers to view their own activity logs with filtering.
 * Only shows activities where the supplier is the causer.
 */
class SupplierActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensure_supplier_profile');
    }

    /**
     * Display supplier's activity logs with filters.
     */
    public function index(Request $request): View
    {
        try {
            $supplier = Auth::user()->supplierProfile;

            if (!$supplier) {
                abort(403, 'لا يوجد ملف تعريف للمورد');
            }

            // Query only activities where supplier is the causer
            $query = ActivityLog::query()
                ->with(['causer', 'subject'])
                ->where('causer_id', Auth::id())
                ->where('causer_type', 'App\Models\User')
                ->latest();

            // Apply filters (reuse existing filter)
            ActivityLogFilter::apply($query, $request);

            // General search
            if ($request->filled('q')) {
                $search = $request->input('q');
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('log_name', 'like', "%{$search}%")
                        ->orWhere('event', 'like', "%{$search}%");
                });
            }

            // Paginate results
            $activities = $query->paginate(25)->withQueryString();

            // Stats for supplier's activities only
            $stats = [
                'total' => ActivityLog::where('causer_id', Auth::id())
                    ->where('causer_type', 'App\Models\User')
                    ->count(),
                'today' => ActivityLog::where('causer_id', Auth::id())
                    ->where('causer_type', 'App\Models\User')
                    ->whereDate('created_at', today())
                    ->count(),
                'this_week' => ActivityLog::where('causer_id', Auth::id())
                    ->where('causer_type', 'App\Models\User')
                    ->where('created_at', '>=', now()->startOfWeek())
                    ->count(),
                'this_month' => ActivityLog::where('causer_id', Auth::id())
                    ->where('causer_type', 'App\Models\User')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
            ];

            // Activity log types for this supplier
            $logTypes = ActivityLog::where('causer_id', Auth::id())
                ->where('causer_type', 'App\Models\User')
                ->select('log_name')
                ->distinct()
                ->pluck('log_name')
                ->filter()
                ->values();

            // Log activity
            activity('supplier_activity_logs')
                ->causedBy(Auth::user())
                ->withProperties([
                    'supplier_id' => $supplier->id,
                    'filters' => $request->only(['log_name', 'event', 'date_from', 'date_to', 'q']),
                ])
                ->log('عرض المورد سجل النشاط');

            return view('supplier.activity.index', compact('activities', 'stats', 'logTypes'));
        } catch (\Throwable $e) {
            Log::error('SupplierActivityLogController index error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحميل سجل النشاط.']);
        }
    }

    /**
     * Display activity log details.
     */
    public function show(ActivityLog $activity): View
    {
        try {
            $supplier = Auth::user()->supplierProfile;

            if (!$supplier) {
                abort(403, 'لا يوجد ملف تعريف للمورد');
            }

            // Verify activity belongs to this supplier
            if ($activity->causer_id !== Auth::id() || $activity->causer_type !== 'App\Models\User') {
                abort(403, 'ليس لديك صلاحية لعرض هذا السجل');
            }

            $activity->load(['causer', 'subject']);

            // Log activity
            activity('supplier_activity_logs')
                ->performedOn($activity)
                ->causedBy(Auth::user())
                ->withProperties([
                    'activity_id' => $activity->id,
                    'log_name' => $activity->log_name,
                    'event' => $activity->event,
                ])
                ->log('عرض المورد تفاصيل سجل النشاط');

            return view('supplier.activity.show', compact('activity'));
        } catch (\Throwable $e) {
            Log::error('SupplierActivityLogController show error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'تعذر عرض تفاصيل هذا السجل.']);
        }
    }
}


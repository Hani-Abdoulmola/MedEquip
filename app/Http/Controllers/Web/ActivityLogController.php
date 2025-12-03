<?php

namespace App\Http\Controllers\Web;

use App\Filters\ActivityLogFilter;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    /**
     * 📋 عرض السجلات مع فلاتر متقدمة
     */
    public function index(Request $request)
    {
        try {
            // Query أساسي مع الـ Relations المهمة
            $query = ActivityLog::query()->with(['causer'])->latest();

            // فلاتر مركزية
            ActivityLogFilter::apply($query, $request);

            // بحث عام (q)
            if ($request->filled('q')) {
                $query->search($request->input('q'));
            }

            // بيانات الجداول مع Pagination
            $activities = $query->paginate(25)->withQueryString();

            // إحصائيات عامة (Top Cards)
            $stats = [
                'total'        => ActivityLog::count(),
                'today'        => ActivityLog::whereDate('created_at', today())->count(),
                'this_week'    => ActivityLog::where('created_at', '>=', now()->startOfWeek())->count(),
                'active_users' => ActivityLog::select('causer_id')->whereNotNull('causer_id')->distinct()->count(),
            ];

            return view('admin.activity.index', compact('activities', 'stats'));
        } catch (\Throwable $e) {
            Log::error('ActivityLog index error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحميل السجلات.']);
        }
    }

    /**
     * 👁️ عرض تفاصيل سجل محدد
     */
    public function show(ActivityLog $activity)
    {
        try {
            $activity->load(['causer', 'subject']);

            return view('admin.activity.show', compact('activity'));
        } catch (\Throwable $e) {
            Log::error('ActivityLog show error: '.$e->getMessage());

            return back()->withErrors(['error' => 'تعذر عرض تفاصيل هذا السجل.']);
        }
    }

    /**
     * 🗑️ حذف سجل واحد (Soft Delete)
     */
    public function destroy(ActivityLog $activity)
    {
        try {
            $id = $activity->id;

            $activity->delete(); // SoftDelete

            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();

            activity('system')
                ->causedBy($authUser)
                ->withProperties(['activity_id' => $id])
                ->log('🗑️ تم حذف سجل نشاط من قبل '.$authUser->name);

            return redirect()
                ->route('admin.activity')
                ->with('success', 'تم حذف السجل بنجاح.');
        } catch (\Throwable $e) {
            Log::error('ActivityLog delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف السجل: '.$e->getMessage()]);
        }
    }

    /**
     * 🧹 حذف جميع السجلات (بشكل آمن)
     * ملاحظة: هنا Soft Delete، لو تبي حذف نهائي استخدم forceDelete أو truncate
     */
    public function clear()
    {
        try {
            $count = ActivityLog::count();

            ActivityLog::query()->delete(); // Soft delete للجميع

            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();

            activity('system')
                ->causedBy($authUser)
                ->withProperties(['count' => $count])
                ->log("🧹 تم مسح {$count} سجل نشاط بواسطة ".$authUser->name);

            return redirect()
                ->route('admin.activity')
                ->with('success', "✅ تم مسح {$count} سجل نشاط بنجاح.");
        } catch (\Throwable $e) {
            Log::error('ActivityLog clear error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل مسح السجلات: '.$e->getMessage()]);
        }
    }
}

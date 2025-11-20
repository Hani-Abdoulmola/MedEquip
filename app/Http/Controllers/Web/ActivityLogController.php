<?php

namespace App\Http\Controllers\Web;

use App\Filters\ActivityLogFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    // Middleware is now defined in routes/web.php for Laravel 12 compatibility

    /**
     * 📋 عرض السجلات مع فلاتر متقدمة
     */
    public function index(Request $request)
    {
        try {
            $query = Activity::query()->with(['causer']);

            // 🔍 استخدام الفلتر المركزي
            $query = ActivityLogFilter::apply($query, $request);

            // 🧠 بحث عام في النص أو الوصف
            if ($request->filled('q')) {
                $keyword = $request->input('q');
                $query->where(function ($qbuilder) use ($keyword) {
                    $qbuilder->where('description', 'like', "%{$keyword}%")
                        ->orWhere('log_name', 'like', "%{$keyword}%");
                });
            }

            $activities = $query->latest()->paginate(25)->withQueryString();

            return view('admin.activity.index', compact('activities'));
        } catch (\Throwable $e) {
            Log::error('ActivityLog index error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحميل السجلات.']);
        }
    }

    /**
     * 👁️ عرض تفاصيل سجل محدد
     */
    public function show(Activity $activity)
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
     * 🗑️ حذف سجل واحد
     */
    public function destroy(Activity $activity)
    {
        try {
            $activity->delete();

            // 🧾 تسجيل عملية الحذف نفسها
            activity('system')
                ->causedBy(auth()->user())
                ->withProperties(['activity_id' => $activity->id])
                ->log('🗑️ تم حذف سجل نشاط من قبل '.auth()->user()->name);

            return redirect()
                ->route('activity.index')
                ->with('success', 'تم حذف السجل بنجاح.');
        } catch (\Throwable $e) {
            Log::error('ActivityLog delete error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل حذف السجل: '.$e->getMessage()]);
        }
    }

    /**
     * 🧹 حذف جميع السجلات (بشكل آمن)
     */
    public function clear()
    {
        try {
            $count = Activity::count();
            Activity::query()->delete();

            // 🧾 سجل العملية نفسها
            activity('system')
                ->causedBy(auth()->user())
                ->withProperties(['count' => $count])
                ->log("🧹 تم مسح {$count} سجل نشاط بواسطة ".auth()->user()->name);

            return redirect()
                ->route('activity.index')
                ->with('success', "✅ تم مسح {$count} سجل نشاط بنجاح.");
        } catch (\Throwable $e) {
            Log::error('ActivityLog clear error: '.$e->getMessage());

            return back()->withErrors(['error' => 'فشل مسح السجلات: '.$e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Supplier Notification Controller
 *
 * Handles notification viewing and management for suppliers.
 */
class SupplierNotificationController extends Controller
{
    /**
     * Display list of notifications for the supplier.
     */
    public function index(Request $request): View
    {
        try {
            $user = Auth::user();

            $query = $user->notifications();

            // Filter by read status
            if ($request->filled('status')) {
                if ($request->status === 'unread') {
                    $query = $user->unreadNotifications();
                } elseif ($request->status === 'read') {
                    $query = $user->readNotifications();
                }
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("JSON_EXTRACT(data, '$.title') LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE ?", ["%{$search}%"]);
                });
            }

            $notifications = $query->latest()->paginate(20)->withQueryString();

            // Optimized stats calculation using single query
            $allNotifications = $user->notifications();
            $stats = [
                'total' => (clone $allNotifications)->count(),
                'unread' => (clone $allNotifications)->whereNull('read_at')->count(),
                'read' => (clone $allNotifications)->whereNotNull('read_at')->count(),
            ];

            // Log activity
            activity('supplier_notifications')
                ->causedBy($user)
                ->withProperties([
                    'filters' => $request->only(['status', 'from_date', 'to_date', 'search']),
                ])
                ->log('عرض المورد قائمة الإشعارات');

            return view('supplier.notifications.index', compact('notifications', 'stats'));

        } catch (\Throwable $e) {
            Log::error('SupplierNotificationController index error: '.$e->getMessage());

            return view('supplier.notifications.index', [
                'notifications' => collect([])->paginate(20),
                'stats' => ['total' => 0, 'unread' => 0, 'read' => 0],
                'error' => 'حدث خطأ أثناء تحميل الإشعارات. يرجى المحاولة مرة أخرى.',
            ]);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return back()->withErrors(['error' => 'الإشعار غير موجود']);
            }

            if (!$notification->read_at) {
                $notification->markAsRead();

                // Log activity
                activity('supplier_notifications')
                    ->causedBy($user)
                    ->withProperties([
                        'notification_id' => $id,
                        'title' => $notification->data['title'] ?? 'N/A',
                    ])
                    ->log('قام المورد بتحديد إشعار كمقروء');
            }

            return back()->with('success', 'تم تحديد الإشعار كمقروء');

        } catch (\Throwable $e) {
            Log::error('SupplierNotificationController markAsRead error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث حالة الإشعار']);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $unreadCount = $user->unreadNotifications()->count();

            if ($unreadCount > 0) {
                $user->unreadNotifications->markAsRead();

                // Log activity
                activity('supplier_notifications')
                    ->causedBy($user)
                    ->withProperties([
                        'count' => $unreadCount,
                    ])
                    ->log("قام المورد بتحديد {$unreadCount} إشعار كمقروء");
            }

            return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');

        } catch (\Throwable $e) {
            Log::error('SupplierNotificationController markAllAsRead error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث حالة الإشعارات']);
        }
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return back()->withErrors(['error' => 'الإشعار غير موجود']);
            }

            $title = $notification->data['title'] ?? 'N/A';
            $notification->delete();

            // Log activity
            activity('supplier_notifications')
                ->causedBy($user)
                ->withProperties([
                    'notification_id' => $id,
                    'title' => $title,
                ])
                ->log('قام المورد بحذف إشعار');

            return back()->with('success', 'تم حذف الإشعار بنجاح');

        } catch (\Throwable $e) {
            Log::error('SupplierNotificationController destroy error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف الإشعار']);
        }
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $count = $user->notifications()->count();

            if ($count > 0) {
                $user->notifications()->delete();

                // Log activity
                activity('supplier_notifications')
                    ->causedBy($user)
                    ->withProperties([
                        'count' => $count,
                    ])
                    ->log("قام المورد بحذف {$count} إشعار");
            }

            return back()->with('success', 'تم حذف جميع الإشعارات');

        } catch (\Throwable $e) {
            Log::error('SupplierNotificationController destroyAll error: '.$e->getMessage());

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف الإشعارات']);
        }
    }
}


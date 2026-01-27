<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Buyer Notification Controller
 *
 * Handles notification viewing and management for buyers.
 */
class BuyerNotificationController extends Controller
{
    /**
     * Display list of notifications for the buyer.
     */
    public function index(Request $request): View
    {
        try {
            $user = Auth::user();
            $buyer = $user->buyerProfile;

            if (!$buyer) {
                abort(403, 'لا يوجد ملف تعريف للمشتري');
            }

            // Mark all notifications as read when viewing the page (if requested)
            if ($request->has('mark_read') && $request->mark_read === 'true') {
                $user->unreadNotifications->markAsRead();
            }

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

            // Stats calculation
            $allNotifications = $user->notifications();
            $stats = [
                'total' => (clone $allNotifications)->count(),
                'unread' => (clone $allNotifications)->whereNull('read_at')->count(),
                'read' => (clone $allNotifications)->whereNotNull('read_at')->count(),
            ];

            // Log activity
            activity('buyer_notifications')
                ->causedBy($user)
                ->withProperties([
                    'buyer_id' => $buyer->id,
                    'filters' => $request->only(['status', 'from_date', 'to_date', 'search']),
                ])
                ->log('عرض المشتري قائمة الإشعارات');

            return view('buyer.notifications.index', compact('notifications', 'stats'));

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController index error: '.$e->getMessage());

            return view('buyer.notifications.index', [
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
                activity('buyer_notifications')
                    ->causedBy($user)
                    ->withProperties([
                        'notification_id' => $id,
                        'action' => 'mark_as_read',
                    ])
                    ->log('قام المشتري بتحديد إشعار كمقروء');
            }

            // If there's an action URL, redirect to it
            $actionUrl = $notification->data['action_url'] ?? null;
            if ($actionUrl) {
                return redirect($actionUrl);
            }

            return back()->with('success', 'تم تحديد الإشعار كمقروء');

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController markAsRead error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديد الإشعار كمقروء']);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            // Log activity
            activity('buyer_notifications')
                ->causedBy($user)
                ->withProperties([
                    'action' => 'mark_all_as_read',
                ])
                ->log('قام المشتري بتحديد جميع الإشعارات كمقروءة');

            return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController markAllAsRead error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديد الإشعارات كمقروءة']);
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

            $notification->delete();

            // Log activity
            activity('buyer_notifications')
                ->causedBy($user)
                ->withProperties([
                    'notification_id' => $id,
                    'action' => 'delete',
                ])
                ->log('قام المشتري بحذف إشعار');

            return back()->with('success', 'تم حذف الإشعار بنجاح');

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController destroy error: '.$e->getMessage());
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
            $user->notifications()->delete();

            // Log activity
            activity('buyer_notifications')
                ->causedBy($user)
                ->withProperties([
                    'action' => 'delete_all',
                ])
                ->log('قام المشتري بحذف جميع الإشعارات');

            return back()->with('success', 'تم حذف جميع الإشعارات بنجاح');

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController destroyAll error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف الإشعارات']);
        }
    }

    /**
     * Reply to a notification
     */
    public function reply(Request $request, string $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return back()->withErrors(['error' => 'الإشعار غير موجود']);
            }

            $request->validate([
                'message' => ['required', 'string', 'max:5000'],
            ]);

            // Get original sender
            $originalSenderId = $notification->data['sent_by_id'] ?? null;
            if (!$originalSenderId) {
                return back()->withErrors(['error' => 'لا يمكن الرد على هذا الإشعار']);
            }

            $originalSender = \App\Models\User::find($originalSenderId);
            if (!$originalSender) {
                return back()->withErrors(['error' => 'المرسل الأصلي غير موجود']);
            }

            // Create reply title
            $replyTitle = 'رد على: ' . ($notification->data['title'] ?? 'إشعار');

            // Send reply
            \App\Services\NotificationService::sendReply(
                $notification->id,
                $originalSender,
                $replyTitle,
                $request->message,
                null,
                'fas fa-reply text-info',
                'info'
            );

            // Mark original notification as read
            if (!$notification->read_at) {
                $notification->markAsRead();
            }

            // Log activity
            activity('buyer_notifications')
                ->causedBy($user)
                ->withProperties([
                    'notification_id' => $id,
                    'action' => 'reply',
                ])
                ->log('قام المشتري بالرد على إشعار');

            return back()->with('success', 'تم إرسال الرد بنجاح');

        } catch (\Throwable $e) {
            Log::error('BuyerNotificationController reply error: '.$e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ أثناء إرسال الرد']);
        }
    }
}


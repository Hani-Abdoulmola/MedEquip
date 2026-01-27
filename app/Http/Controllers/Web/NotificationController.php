<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications (merged with sent notifications)
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }
        
        // Permission check is handled by route middleware

        // Mark all notifications as read when viewing the page (if requested)
        if ($request->has('mark_read') && $request->mark_read === 'true') {
            $authUser->unreadNotifications->markAsRead();
        }

        // Get filter parameters
        $filter = $request->input('filter', 'all'); // all, unread, read

        // Build query
        $query = $authUser->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Get notifications with pagination
        $notifications = $query->latest()->paginate(20)->withQueryString();

        // Get statistics (refresh after potential mark as read)
        $stats = [
            'total' => $authUser->notifications()->count(),
            'unread' => $authUser->unreadNotifications()->count(),
            'read' => $authUser->notifications()->whereNotNull('read_at')->count(),
            'today' => $authUser->notifications()
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('admin.notifications.index', compact(
            'notifications', 
            'stats', 
            'filter'
        ));
    }

    /**
     * Show the form for creating a new notification
     */
    public function create()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        // Permission check is handled by route middleware

        // Get counts for display
        $supplierCount = \App\Models\User::role('Supplier')->count();
        $buyerCount = \App\Models\User::role('Buyer')->count();

        // Get lists for specific selection
        $suppliers = \App\Models\User::role('Supplier')
            ->with('supplierProfile')
            ->get()
            ->mapWithKeys(function ($user) {
                $name = $user->supplierProfile ? $user->supplierProfile->company_name : $user->name;
                return [$user->id => $name . ' (' . $user->email . ')'];
            });

        $buyers = \App\Models\User::role('Buyer')
            ->with('buyerProfile')
            ->get()
            ->mapWithKeys(function ($user) {
                $name = $user->buyerProfile ? $user->buyerProfile->organization_name : $user->name;
                return [$user->id => $name . ' (' . $user->email . ')'];
            });

        return view('admin.notifications.create', compact('supplierCount', 'buyerCount', 'suppliers', 'buyers'));
    }

    /**
     * Store a newly created notification
     */
    public function store(NotificationRequest $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        // Permission check is handled by route middleware

        $validated = $request->validated();
        $recipients = $validated['recipients'];
        $title = $validated['title'];
        $message = $validated['message'];
        $url = $validated['url'] ?? null;
        $type = $validated['type'] ?? 'info';
        $icon = $validated['icon'] ?? null;
        $recipientIds = $validated['recipient_ids'] ?? null;

        // Determine icon based on type if not provided
        if (!$icon) {
            $icon = match ($type) {
                'success' => 'fas fa-check-circle text-success',
                'warning' => 'fas fa-exclamation-triangle text-warning',
                'error' => 'fas fa-times-circle text-danger',
                'primary' => 'fas fa-info-circle text-primary',
                default => 'fas fa-bell text-info',
            };
        }

        // Send notifications - all notifications are saved in the notifications table
        $result = NotificationService::sendWithTracking(
            $recipients,
            $title,
            $message,
            $url,
            $icon,
            $type,
            $recipientIds
        );

        $recipientText = $result['recipient_type_label'];
        $totalSent = $result['total_sent'];

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', "تم إرسال الإشعار بنجاح إلى {$totalSent} من {$recipientText}");
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        $notification = $authUser->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        $authUser->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        $notification = $authUser->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
        }

        $authUser->notifications()->delete();

        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات');
    }

}

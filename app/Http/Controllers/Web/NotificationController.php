<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('login');
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
        $notifications = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'total' => $authUser->notifications()->count(),
            'unread' => $authUser->unreadNotifications()->count(),
            'read' => $authUser->notifications()->whereNotNull('read_at')->count(),
            'today' => $authUser->notifications()
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats', 'filter'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json(['success' => false], 401);
        }

        $notification = $authUser->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
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
            return response()->json(['success' => false], 401);
        }

        $notification = $authUser->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
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

<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * 📨 إرسال إشعار عام لمستخدم أو مجموعة مستخدمين
     */
    public static function send($recipients, string $title, string $message, ?string $url = null, ?string $icon = null, ?string $type = 'info'): void
    {
        try {
            foreach (self::normalizeRecipients($recipients) as $user) {
                if ($user instanceof User) {
                    $user->notify(new SystemNotification($title, $message, $url, $icon, $type));
                }
            }
        } catch (\Throwable $e) {
            Log::error('NotificationService send() failed: '.$e->getMessage());
        }
    }

    /**
     * 👑 إرسال إشعار لكل المدراء (Admins)
     */
    public static function notifyAdmins(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-shield-alt text-primary'): void
    {
        $admins = User::role('Admin')->get();
        self::send($admins, $title, $message, $url, $icon, 'primary');

        activity('notifications')
            ->causedBy(auth()->user() ?? null)
            ->withProperties(['audience' => 'admins'])
            ->log('📣 تم إرسال إشعار إلى جميع المدراء');
    }

    /**
     * 🧰 إرسال إشعار لكل الموردين (Suppliers)
     */
    public static function notifySuppliers(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-truck text-success'): void
    {
        $suppliers = User::role('Supplier')->get();
        self::send($suppliers, $title, $message, $url, $icon, 'success');

        activity('notifications')
            ->causedBy(auth()->user() ?? null)
            ->withProperties(['audience' => 'suppliers'])
            ->log('📦 تم إرسال إشعار إلى جميع الموردين');
    }

    /**
     * 🏥 إرسال إشعار لكل المشترين (Buyers)
     */
    public static function notifyBuyers(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-shopping-cart text-info'): void
    {
        $buyers = User::role('Buyer')->get();
        self::send($buyers, $title, $message, $url, $icon, 'info');

        activity('notifications')
            ->causedBy(auth()->user() ?? null)
            ->withProperties(['audience' => 'buyers'])
            ->log('🛒 تم إرسال إشعار إلى جميع المشترين');
    }

    /**
     * 🔄 دالة مساعدة لتوحيد أنواع المدخلات (User / Collection / Array)
     */
    protected static function normalizeRecipients($recipients): Collection
    {
        return match (true) {
            $recipients instanceof Collection => $recipients,
            $recipients instanceof User => collect([$recipients]),
            is_array($recipients) => collect($recipients),
            default => collect(),
        };
    }
}

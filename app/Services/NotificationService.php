<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * 📨 إرسال إشعار عام لمستخدم أو مجموعة مستخدمين
     * 
     * @param mixed $recipients User, Collection, or array of Users
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @param string|null $icon
     * @param string $type
     * @param string|null $parentNotificationId UUID of parent notification for replies
     * @return int Number of notifications sent
     */
    public static function send($recipients, string $title, string $message, ?string $url = null, ?string $icon = null, ?string $type = 'info', ?string $parentNotificationId = null): int
    {
        $sentCount = 0;
        try {
            foreach (self::normalizeRecipients($recipients) as $user) {
                if ($user instanceof User) {
                    $user->notify(new SystemNotification($title, $message, $url, $icon, $type, $parentNotificationId));
                    $sentCount++;
                }
            }
        } catch (\Throwable $e) {
            Log::error('NotificationService send() failed: '.$e->getMessage());
        }
        return $sentCount;
    }

    /**
     * 👑 إرسال إشعار لكل المدراء (Admins)
     */
    public static function notifyAdmins(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-shield-alt text-primary'): void
    {
        $admins = User::role('Admin')->get();
        self::send($admins, $title, $message, $url, $icon, 'primary');

        activity('notifications')
            ->causedBy(Auth::user() ?? null)
            ->withProperties(['audience' => 'admins'])
            ->log('📣 تم إرسال إشعار إلى جميع المدراء');
    }

    /**
     * 🧰 إرسال إشعار لكل الموردين (Suppliers)
     * 
     * @param array|null $specificUserIds Optional: specific supplier user IDs to notify
     * @param string $type Notification type (info, success, warning, error, primary)
     */
    public static function notifySuppliers(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-truck text-success', ?string $type = 'success', ?array $specificUserIds = null): int
    {
        if ($specificUserIds) {
            $suppliers = User::role('Supplier')->whereIn('id', $specificUserIds)->get();
        } else {
            $suppliers = User::role('Supplier')->get();
        }
        
        $sentCount = self::send($suppliers, $title, $message, $url, $icon, $type);

        activity('notifications')
            ->causedBy(Auth::user() ?? null)
            ->withProperties(['audience' => 'suppliers', 'count' => $sentCount])
            ->log('📦 تم إرسال إشعار إلى الموردين');

        return $sentCount;
    }

    /**
     * 🏥 إرسال إشعار لكل المشترين (Buyers)
     * 
     * @param array|null $specificUserIds Optional: specific buyer user IDs to notify
     * @param string $type Notification type (info, success, warning, error, primary)
     */
    public static function notifyBuyers(string $title, string $message, ?string $url = null, ?string $icon = 'fas fa-shopping-cart text-info', ?string $type = 'info', ?array $specificUserIds = null): int
    {
        if ($specificUserIds) {
            $buyers = User::role('Buyer')->whereIn('id', $specificUserIds)->get();
        } else {
            $buyers = User::role('Buyer')->get();
        }
        
        $sentCount = self::send($buyers, $title, $message, $url, $icon, $type);

        activity('notifications')
            ->causedBy(Auth::user() ?? null)
            ->withProperties(['audience' => 'buyers', 'count' => $sentCount])
            ->log('🛒 تم إرسال إشعار إلى المشترين');

        return $sentCount;
    }

    /**
     * 📤 إرسال إشعار (للمدراء فقط) - جميع الإشعارات تُحفظ في جدول notifications
     * 
     * @param array $recipientTypes ['suppliers', 'buyers', 'both'] or ['specific'] with recipient_ids
     * @param array|null $recipientIds Specific user IDs if recipient_type is 'specific'
     * @return array Returns array with 'total_sent' and 'recipient_type_label' for success message
     */
    public static function sendWithTracking(array $recipientTypes, string $title, string $message, ?string $url = null, ?string $icon = null, ?string $type = 'info', ?array $recipientIds = null): array
    {
        try {
            $totalSent = 0;
            $recipientType = 'specific';
            $recipientTypeLabel = 'مستلمون محددون';

            // Check if "specific" is selected and has recipient IDs
            $isSpecific = in_array('specific', $recipientTypes) && $recipientIds && count($recipientIds) > 0;
            
            // Check if recipient IDs are provided (even if 'specific' is not in recipientTypes)
            $hasRecipientIds = $recipientIds && count($recipientIds) > 0;

            // Determine recipient type and send notifications
            if (in_array('both', $recipientTypes) && !$hasRecipientIds) {
                $recipientType = 'both';
                $recipientTypeLabel = 'الجميع (موردين ومشترين)';
                $supplierCount = self::notifySuppliers($title, $message, $url, $icon, $type);
                $buyerCount = self::notifyBuyers($title, $message, $url, $icon, $type);
                $totalSent = $supplierCount + $buyerCount;
            } elseif (in_array('suppliers', $recipientTypes)) {
                if ($hasRecipientIds) {
                    // Filter to only suppliers from the selected IDs
                    $supplierUserIds = User::role('Supplier')->whereIn('id', $recipientIds)->pluck('id')->toArray();
                    if (count($supplierUserIds) > 0) {
                        $recipientType = 'specific';
                        $recipientTypeLabel = 'موردين محددين';
                        $totalSent = self::notifySuppliers($title, $message, $url, $icon, $type, $supplierUserIds);
                    } else {
                        // No suppliers in the selected IDs, send to all suppliers
                        $recipientType = 'all_suppliers';
                        $recipientTypeLabel = 'جميع الموردين';
                        $totalSent = self::notifySuppliers($title, $message, $url, $icon, $type);
                    }
                } else {
                    // No specific IDs, send to all suppliers
                    $recipientType = 'all_suppliers';
                    $recipientTypeLabel = 'جميع الموردين';
                    $totalSent = self::notifySuppliers($title, $message, $url, $icon, $type);
                }
            } elseif (in_array('buyers', $recipientTypes)) {
                if ($hasRecipientIds) {
                    // Filter to only buyers from the selected IDs
                    $buyerUserIds = User::role('Buyer')->whereIn('id', $recipientIds)->pluck('id')->toArray();
                    if (count($buyerUserIds) > 0) {
                        $recipientType = 'specific';
                        $recipientTypeLabel = 'مشترين محددين';
                        $totalSent = self::notifyBuyers($title, $message, $url, $icon, $type, $buyerUserIds);
                    } else {
                        // No buyers in the selected IDs, send to all buyers
                        $recipientType = 'all_buyers';
                        $recipientTypeLabel = 'جميع المشترين';
                        $totalSent = self::notifyBuyers($title, $message, $url, $icon, $type);
                    }
                } else {
                    // No specific IDs, send to all buyers
                    $recipientType = 'all_buyers';
                    $recipientTypeLabel = 'جميع المشترين';
                    $totalSent = self::notifyBuyers($title, $message, $url, $icon, $type);
                }
            } elseif ($isSpecific) {
                // Specific users (mixed roles) - only "specific" selected
                $users = User::whereIn('id', $recipientIds)->get();
                $totalSent = self::send($users, $title, $message, $url, $icon, $type);
                $recipientType = 'specific';
                $recipientTypeLabel = 'مستلمون محددون';
            } else {
                // No valid recipients selected
                throw new \InvalidArgumentException('يجب اختيار مستلمين صحيحين');
            }

            // Log activity
            activity('notifications')
                ->causedBy(Auth::user())
                ->withProperties([
                    'recipient_type' => $recipientType,
                    'total_sent' => $totalSent,
                    'title' => $title,
                ])
                ->log('📤 تم إرسال إشعار من قبل المدير');

            return [
                'total_sent' => $totalSent,
                'recipient_type_label' => $recipientTypeLabel,
            ];

        } catch (\Throwable $e) {
            Log::error('NotificationService sendWithTracking() failed: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * 💬 إرسال رد على إشعار
     */
    public static function sendReply(string $parentNotificationId, User $recipient, string $title, string $message, ?string $url = null, ?string $icon = null, ?string $type = 'info'): void
    {
        self::send([$recipient], $title, $message, $url, $icon, $type, $parentNotificationId);
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

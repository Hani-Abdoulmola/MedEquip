<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SystemNotification extends Notification
{
    use Queueable;

    public string $title;

    public string $message;

    public ?string $url;

    public ?string $icon;

    public ?string $type;

    public ?string $parentNotificationId;

    /**
     * 🔔 تهيئة الإشعار العام
     */
    public function __construct(string $title, string $message, ?string $url = null, ?string $icon = null, ?string $type = 'info', ?string $parentNotificationId = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
        $this->type = $type;
        $this->parentNotificationId = $parentNotificationId;
    }

    /**
     * 📡 القنوات المستخدمة (قاعدة البيانات حالياً)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * 💾 البيانات المخزّنة في جدول notifications
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon ?? 'fas fa-bell text-primary',
            'type' => $this->type ?? 'info',
            'sent_by' => Auth::user()->name ?? 'System',
            'sent_by_id' => Auth::id(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'parent_notification_id' => $this->parentNotificationId,
        ];
    }
}

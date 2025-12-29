<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * Determine if the user can view any notifications.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their notifications
    }

    /**
     * Determine if the user can view the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        // Users can only view their own notifications
        return $notification->notifiable_id === $user->id && 
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine if the user can create notifications.
     */
    public function create(User $user): bool
    {
        // Only system (admin) can create notifications
        // This is typically done via NotificationService
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can update the notification.
     */
    public function update(User $user, Notification $notification): bool
    {
        // Users can mark their own notifications as read
        return $notification->notifiable_id === $user->id && 
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine if the user can delete the notification.
     */
    public function delete(User $user, Notification $notification): bool
    {
        // Users can delete their own notifications
        return $notification->notifiable_id === $user->id && 
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine if the user can mark notification as read.
     */
    public function markAsRead(User $user, Notification $notification): bool
    {
        return $this->update($user, $notification);
    }

    /**
     * Determine if the user can mark all notifications as read.
     */
    public function markAllAsRead(User $user): bool
    {
        return true; // All authenticated users can mark their notifications as read
    }
}


<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogPolicy
{
    /**
     * Determine if the user can view any activity logs.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view activity logs
    }

    /**
     * Determine if the user can view the activity log.
     */
    public function view(User $user, ActivityLog $activityLog): bool
    {
        // Admin can view all activity logs
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Users can view activity logs they caused
        if ($activityLog->causer_id === $user->id) {
            return true;
        }

        // Users can view activity logs for their own resources
        if ($activityLog->subject_type === User::class && $activityLog->subject_id === $user->id) {
            return true;
        }

        // Suppliers can view activity logs for their profile
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            if ($activityLog->subject_type === \App\Models\Supplier::class && 
                $activityLog->subject_id === $user->supplierProfile->id) {
                return true;
            }
        }

        // Buyers can view activity logs for their profile
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            if ($activityLog->subject_type === \App\Models\Buyer::class && 
                $activityLog->subject_id === $user->buyerProfile->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can create activity logs.
     */
    public function create(User $user): bool
    {
        // Activity logs are created automatically by the system
        // Users don't create them directly
        return false;
    }

    /**
     * Determine if the user can update the activity log.
     */
    public function update(User $user, ActivityLog $activityLog): bool
    {
        // Activity logs are immutable - cannot be updated
        return false;
    }

    /**
     * Determine if the user can delete the activity log.
     */
    public function delete(User $user, ActivityLog $activityLog): bool
    {
        // Only admin can delete activity logs
        return $user->hasRole('Admin');
    }
}


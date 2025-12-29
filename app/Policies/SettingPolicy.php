<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    /**
     * Determine if the user can view any settings.
     */
    public function viewAny(User $user): bool
    {
        // Only admin can view settings
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can view the setting.
     */
    public function view(User $user, Setting $setting): bool
    {
        // Only admin can view settings
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can create settings.
     */
    public function create(User $user): bool
    {
        // Only admin can create settings
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can update the setting.
     */
    public function update(User $user, Setting $setting): bool
    {
        // Only admin can update settings
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can delete the setting.
     */
    public function delete(User $user, Setting $setting): bool
    {
        // Only admin can delete settings
        return $user->hasRole('Admin');
    }
}


<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;

class BuyerPolicy
{
    /**
     * Determine if the user can view any buyers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('buyers.view');
    }

    /**
     * Determine if the user can view the buyer.
     */
    public function view(User $user, Buyer $buyer): bool
    {
        // Buyers can view their own profile
        if ($user->hasRole('Buyer') && $user->buyerProfile && $buyer->id === $user->buyerProfile->id) {
            return true;
        }

        return $user->can('buyers.view');
    }

    /**
     * Determine if the user can create buyers.
     */
    public function create(User $user): bool
    {
        return $user->can('buyers.create');
    }

    /**
     * Determine if the user can update the buyer.
     */
    public function update(User $user, Buyer $buyer): bool
    {
        // Buyer can update their own profile
        if ($user->hasRole('Buyer') && $user->buyerProfile && $buyer->id === $user->buyerProfile->id) {
            return true;
        }

        return $user->can('buyers.update');
    }

    /**
     * Determine if the user can delete the buyer.
     */
    public function delete(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.delete');
    }

    /**
     * Determine if the user can verify the buyer.
     */
    public function verify(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.verify');
    }

    /**
     * Determine if the user can toggle buyer active status.
     */
    public function toggleActive(User $user, Buyer $buyer): bool
    {
        return $user->can('buyers.toggle_active');
    }
}


<?php

namespace App\Policies;

use App\Models\ProductRequest;
use App\Models\User;

class ProductRequestPolicy
{
    /**
     * Determine if the user can view any product requests.
     */
    public function viewAny(User $user): bool
    {
        // Suppliers can view their own requests
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return true;
        }

        // Admin/Staff need permission
        return $user->can('products.view');
    }

    /**
     * Determine if the user can view the product request.
     */
    public function view(User $user, ProductRequest $productRequest): bool
    {
        // Supplier can view their own requests
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $productRequest->supplier_id === $user->supplierProfile->id;
        }

        // Admin/Staff need permission
        return $user->can('products.view');
    }

    /**
     * Determine if the user can create product requests.
     */
    public function create(User $user): bool
    {
        // Only suppliers can create product requests
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $user->supplierProfile->is_verified && $user->supplierProfile->is_active;
        }

        return false;
    }

    /**
     * Determine if the user can review (approve/merge/reject) the product request.
     */
    public function review(User $user, ProductRequest $productRequest): bool
    {
        return $user->can('products.approve');
    }

    /**
     * Determine if the user can approve the product request.
     */
    public function approve(User $user, ProductRequest $productRequest): bool
    {
        if (!$productRequest->canBeReviewed()) {
            return false;
        }

        return $user->can('products.approve');
    }

    /**
     * Determine if the user can merge the product request with existing product.
     */
    public function merge(User $user, ProductRequest $productRequest): bool
    {
        if (!$productRequest->canBeReviewed()) {
            return false;
        }

        return $user->can('products.approve');
    }

    /**
     * Determine if the user can reject the product request.
     */
    public function reject(User $user, ProductRequest $productRequest): bool
    {
        if (!$productRequest->canBeReviewed()) {
            return false;
        }

        return $user->can('products.reject');
    }

    /**
     * Determine if the user can cancel the product request.
     */
    public function cancel(User $user, ProductRequest $productRequest): bool
    {
        // Supplier can cancel their own pending requests
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $productRequest->supplier_id === $user->supplierProfile->id
                && $productRequest->canBeCancelled();
        }

        return false;
    }
}


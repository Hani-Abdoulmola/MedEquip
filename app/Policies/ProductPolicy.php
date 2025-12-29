<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        // Products are viewable by all authenticated users
        return true;
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // All authenticated users can view products
        return true;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('products.create');
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if (!$user->can('products.update')) {
            return false;
        }

        // Supplier can update their own products
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $product->suppliers()->where('suppliers.id', $user->supplierProfile->id)->exists();
        }

        // Admin/Staff with permission can update any product
        return true;
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->can('products.delete');
    }

    /**
     * Determine if the user can approve the product.
     */
    public function approve(User $user, Product $product): bool
    {
        return $user->can('products.approve');
    }

    /**
     * Determine if the user can reject the product.
     */
    public function reject(User $user, Product $product): bool
    {
        return $user->can('products.reject');
    }

    /**
     * Determine if the user can request changes to the product.
     */
    public function requestChanges(User $user, Product $product): bool
    {
        return $user->can('products.request_changes');
    }
}


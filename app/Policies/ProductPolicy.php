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
        // Gate::before() handles Admin bypass
        return $user->can('products.create');
    }

    /**
     * Determine if the user can update the product.
     * 
     * Suppliers can update products they own.
     * Admin/Staff need the products.update permission.
     */
    public function update(User $user, Product $product): bool
    {
        // Supplier can update their own products
        if ($user->hasRole('Supplier') && $user->supplierProfile) {
            return $user->supplierProfile->products()
                ->where('products.id', $product->id)
                ->exists();
        }

        // Gate::before() handles Admin bypass
        return $user->can('products.update');
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('products.delete');
    }

    /**
     * Determine if the user can approve the product.
     */
    public function approve(User $user, Product $product): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('products.approve');
    }

    /**
     * Determine if the user can reject the product.
     */
    public function reject(User $user, Product $product): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('products.reject');
    }

    /**
     * Determine if the user can request changes to the product.
     */
    public function requestChanges(User $user, Product $product): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('products.request_changes');
    }

    /**
     * Determine if the user can browse products in the catalog.
     * 
     * This is for buyer product browsing - they can only see active, approved products.
     */
    public function browse(User $user): bool
    {
        // All authenticated users can browse the product catalog
        return true;
    }

    /**
     * Determine if the user can add products to favorites.
     * 
     * Only buyers can add products to their favorites list.
     */
    public function favorite(User $user, Product $product): bool
    {
        // Only buyers can add to favorites
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Can only favorite active, approved products
            return $product->is_active && $product->review_status === 'approved';
        }

        return false;
    }

    /**
     * Determine if the user can compare products.
     */
    public function compare(User $user): bool
    {
        // All authenticated users can compare products
        return true;
    }

    /**
     * Determine if the user can create an RFQ from a product.
     * 
     * Only buyers can create RFQs from products.
     */
    public function createRfq(User $user, Product $product): bool
    {
        // Only buyers can create RFQs from products
        if ($user->hasRole('Buyer') && $user->buyerProfile) {
            // Can only create RFQ for active, approved products
            return $product->is_active && $product->review_status === 'approved';
        }

        return false;
    }
}


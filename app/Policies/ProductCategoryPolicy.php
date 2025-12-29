<?php

namespace App\Policies;

use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    /**
     * Determine if the user can view any product categories.
     */
    public function viewAny(User $user): bool
    {
        return true; // Categories are viewable by all authenticated users
    }

    /**
     * Determine if the user can view the product category.
     */
    public function view(User $user, ProductCategory $category): bool
    {
        return true; // All authenticated users can view categories
    }

    /**
     * Determine if the user can create product categories.
     */
    public function create(User $user): bool
    {
        // Only admin can create categories
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can update the product category.
     */
    public function update(User $user, ProductCategory $category): bool
    {
        // Only admin can update categories
        return $user->hasRole('Admin');
    }

    /**
     * Determine if the user can delete the product category.
     */
    public function delete(User $user, ProductCategory $category): bool
    {
        // Only admin can delete categories
        // Should check if category has products before allowing deletion
        return $user->hasRole('Admin');
    }
}


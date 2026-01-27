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
        // Gate::before() handles Admin bypass
        return $user->can('categories.create');
    }

    /**
     * Determine if the user can update the product category.
     */
    public function update(User $user, ProductCategory $category): bool
    {
        // Gate::before() handles Admin bypass
        return $user->can('categories.update');
    }

    /**
     * Determine if the user can delete the product category.
     */
    public function delete(User $user, ProductCategory $category): bool
    {
        // Gate::before() handles Admin bypass
        // Should check if category has products before allowing deletion
        return $user->can('categories.delete');
    }
}


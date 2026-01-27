<?php

namespace App\Providers;

use App\Models\ProductCategory;
use App\Models\Manufacturer;
use App\Models\ProductSupplier;
use App\Observers\ProductSupplierObserver;
use App\Services\BuyerProductService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Listeners\ClearPermissionCache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Spatie Permission event listeners for automatic cache clearing
        Event::listen(
            [
                \Spatie\Permission\Events\PermissionAttached::class,
                \Spatie\Permission\Events\PermissionDetached::class,
                \Spatie\Permission\Events\RoleAttached::class,
                \Spatie\Permission\Events\RoleDetached::class,
            ],
            ClearPermissionCache::class
        );

        // Phase 1: Product min_price / suppliers_count denormalization
        ProductSupplier::observe(ProductSupplierObserver::class);

        // Phase 1: Invalidate buyer product filter cache when categories/manufacturers change
        ProductCategory::observe(\App\Observers\ProductCategoryObserver::class);
        Manufacturer::observe(\App\Observers\ManufacturerObserver::class);

        // Admin bypass using Spatie best practices
        // This is the SINGLE point of Admin authorization bypass
        // Works with all authorization methods: policies, gates, middleware, @can directives
        Gate::before(function ($user, $ability) {
            // Admin role bypasses ALL authorization checks
            if ($user && $user->hasRole('Admin')) {
                return true;
            }
        });

        // Note: Custom @can directive removed - using Spatie's native @can directive
        // Gate::before() ensures Admin passes all @can checks automatically
    }
}

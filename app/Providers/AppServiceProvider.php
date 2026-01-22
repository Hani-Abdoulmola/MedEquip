<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
    }
}

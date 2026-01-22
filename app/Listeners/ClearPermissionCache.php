<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Permission\PermissionRegistrar;

/**
 * Automatically clear permission cache when permissions/roles change
 * 
 * This listener responds to Spatie Permission events:
 * - PermissionAttached
 * - PermissionDetached
 * - RoleAttached
 * - RoleDetached
 * 
 * Ensures permission changes take effect immediately without manual cache flush
 */
class ClearPermissionCache
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        \Log::info('Permission cache cleared', [
            'event' => class_basename($event),
            'model' => $event->model ?? null,
        ]);
    }
}

<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Manufacturer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ManufacturerPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProductCategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\RolePolicy;
use App\Policies\RfqPolicy;
use App\Policies\SettingPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Rfq::class => RfqPolicy::class,
        Quotation::class => QuotationPolicy::class,
        Order::class => OrderPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Payment::class => PaymentPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Product::class => ProductPolicy::class,
        Manufacturer::class => ManufacturerPolicy::class,
        ProductCategory::class => ProductCategoryPolicy::class,
        Buyer::class => BuyerPolicy::class,
        Supplier::class => SupplierPolicy::class,
        User::class => UserPolicy::class,
        Setting::class => SettingPolicy::class,
        Notification::class => NotificationPolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}


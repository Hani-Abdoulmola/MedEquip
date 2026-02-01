<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\BuyerController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProductCategoryController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ProductReviewController;
use App\Http\Controllers\Web\BuyerDashboardController;
use App\Http\Controllers\Web\Suppliers\SupplierDashboardController;
use App\Http\Controllers\Web\Suppliers\SupplierProductController;
use App\Http\Controllers\Web\Suppliers\SupplierRfqController;
use App\Http\Controllers\Web\Suppliers\SupplierOrderController;
use App\Http\Controllers\Web\Suppliers\SupplierProfileController;
use App\Http\Controllers\Web\Suppliers\SupplierNotificationController;
use App\Http\Controllers\Web\Suppliers\SupplierDeliveryController;
use App\Http\Controllers\Web\Suppliers\SupplierInvoiceController;
use App\Http\Controllers\Web\Suppliers\SupplierPaymentController;
use App\Http\Controllers\Web\Suppliers\SupplierActivityLogController;
use App\Http\Controllers\Web\Suppliers\SupplierReportsController;
use App\Http\Controllers\Web\RegistrationApprovalController;
use App\Http\Controllers\Web\AdminRfqController;
use App\Http\Controllers\Web\AdminQuotationController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\DeliveryController;
use App\Http\Controllers\Web\AdminManufacturerController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AdminReportsController;
use App\Http\Controllers\Web\RolePermissionController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Main Dashboard Route - Only for Admins
// Suppliers and Buyers have their own dashboard routes
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Load relationships
    $user->load(['supplierProfile', 'buyerProfile']);

    // Redirect suppliers to their dashboard
    if ($user->supplierProfile) {
        return redirect()->route('supplier.dashboard');
    }

    // Redirect buyers to their dashboard
    if ($user->buyerProfile) {
        return redirect()->route('buyer.dashboard');
    }

    // Admin users see the admin dashboard with real data
    return app(AdminDashboardController::class)->index();
})->middleware(['auth', 'maintenance.allow_admin'])->name('dashboard');

// Waiting Approval Page (for pending/rejected suppliers and buyers)
Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->middleware(['auth', 'maintenance.allow_admin'])->name('auth.waiting-approval');

Route::middleware(['auth', 'maintenance.allow_admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes - Protected by internal user middleware (Admin/Staff with permissions)
    Route::prefix('admin')->name('admin.')->middleware('internal.user')->group(function () {
        // Users Management
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users');
        Route::get('/users/export', [UserController::class, 'export'])->middleware('permission:users.view')->name('users.export');
        Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:users.view')->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.update')->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
        Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->middleware('permission:permissions.view')->name('users.update-permissions');

        // Unified Roles & Permissions Management
        Route::get('/role-permissions', [RolePermissionController::class, 'index'])->middleware('permission:permissions.view')->name('role-permissions.index');
        Route::post('/role-permissions/{user}/assign', [RolePermissionController::class, 'assignPermissions'])->middleware('permission:permissions.view')->name('role-permissions.assign');
        Route::post('/role-permissions/role/{role}/update', [RolePermissionController::class, 'updateRolePermissions'])->middleware('permission:permissions.view')->name('role-permissions.update-role');
        Route::post('/role-permissions/bulk-assign', [RolePermissionController::class, 'bulkAssignPermissions'])->middleware('permission:permissions.view')->name('role-permissions.bulk-assign');

        // Legacy Routes (kept for backward compatibility, redirect to unified page)
        Route::get('/roles', function () {
            return redirect()->route('admin.role-permissions.index');
        })->name('roles.index');
        Route::get('/permissions', function () {
            return redirect()->route('admin.role-permissions.index');
        })->name('permissions.index');


        // Suppliers Management
        Route::get('/suppliers', [SupplierController::class, 'index'])->middleware('permission:suppliers.view')->name('suppliers');
        Route::get('/suppliers/export', [SupplierController::class, 'export'])->middleware('permission:suppliers.view')->name('suppliers.export');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->middleware('permission:suppliers.create')->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->middleware('permission:suppliers.create')->name('suppliers.store');
        Route::post('/suppliers/{supplier}/verify', [SupplierController::class, 'verify'])->middleware('permission:suppliers.verify')->name('suppliers.verify');
        Route::post('/suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->middleware('permission:suppliers.toggle_active')->name('suppliers.toggle-active');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->middleware('permission:suppliers.view')->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->middleware('permission:suppliers.update')->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('permission:suppliers.update')->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:suppliers.delete')->name('suppliers.destroy');


        // Buyers Management
        Route::get('/buyers', [BuyerController::class, 'index'])->middleware('permission:buyers.view')->name('buyers');
        Route::get('/buyers/export', [BuyerController::class, 'export'])->middleware('permission:buyers.view')->name('buyers.export');
        Route::get('/buyers/create', [BuyerController::class, 'create'])->middleware('permission:buyers.create')->name('buyers.create');
        Route::post('/buyers', [BuyerController::class, 'store'])->middleware('permission:buyers.create')->name('buyers.store');
        Route::get('/buyers/{buyer}', [BuyerController::class, 'show'])->middleware('permission:buyers.view')->name('buyers.show');
        Route::get('/buyers/{buyer}/edit', [BuyerController::class, 'edit'])->middleware('permission:buyers.update')->name('buyers.edit');
        Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])->middleware('permission:buyers.update')->name('buyers.update');
        Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])->middleware('permission:buyers.delete')->name('buyers.destroy');
        Route::post('/buyers/{buyer}/toggle-active', [BuyerController::class, 'toggleActive'])->middleware('permission:buyers.toggle_active')->name('buyers.toggle-active');
        Route::post('/buyers/{buyer}/verify', [BuyerController::class, 'verifyBuyer'])->middleware('permission:buyers.verify')->name('buyers.verify');


        // Products Management
        Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products.view')->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('permission:products.view')->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:products.update')->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:products.update')->name('products.update');
        Route::get('/products/{product}/review', [ProductReviewController::class, 'review'])->middleware('permission:products.view')->name('products.review');
        Route::post('/products/{product}/approve', [ProductReviewController::class, 'approve'])->middleware('permission:products.approve')->name('products.approve');
        Route::post('/products/{product}/reject', [ProductReviewController::class, 'reject'])->middleware('permission:products.reject')->name('products.reject');
        Route::post('/products/{product}/request-changes', [ProductReviewController::class, 'requestChanges'])->middleware('permission:products.request_changes')->name('products.request_changes');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete')->name('products.destroy');

        // Product Requests Management (Canonical Catalog Workflow)
        Route::prefix('product-requests')->name('product-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'index'])->middleware('permission:products.view')->name('index');
            Route::get('/{productRequest}', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'show'])->middleware('permission:products.view')->name('show');
            Route::get('/{productRequest}/review', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'review'])->middleware('permission:products.view')->name('review');
            Route::post('/{productRequest}/approve', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'approve'])->middleware('permission:products.approve')->name('approve');
            Route::post('/{productRequest}/merge', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'merge'])->middleware('permission:products.approve')->name('merge');
            Route::post('/{productRequest}/reject', [\App\Http\Controllers\Web\AdminProductRequestController::class, 'reject'])->middleware('permission:products.reject')->name('reject');
        });

        // Diagnostics
        Route::get('/diagnostics/factory-data', [\App\Http\Controllers\Web\AdminDiagnosticsController::class, 'factoryData'])->middleware('permission:products.view')->name('diagnostics.factory-data');

        // Product Categories Management
        Route::get('/categories', [ProductCategoryController::class, 'index'])->middleware('permission:categories.view')->name('categories.index');
        Route::get('/categories/create', [ProductCategoryController::class, 'create'])->middleware('permission:categories.create')->name('categories.create');
        Route::post('/categories', [ProductCategoryController::class, 'store'])->middleware('permission:categories.create')->name('categories.store');
        Route::get('/categories/{category}', [ProductCategoryController::class, 'show'])->middleware('permission:categories.view')->name('categories.show');
        Route::get('/categories/{category}/edit', [ProductCategoryController::class, 'edit'])->middleware('permission:categories.update')->name('categories.edit');
        Route::put('/categories/{category}', [ProductCategoryController::class, 'update'])->middleware('permission:categories.update')->name('categories.update');
        Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy'])->middleware('permission:categories.delete')->name('categories.destroy');

        // Manufacturers Management
        Route::get('/manufacturers', [AdminManufacturerController::class, 'index'])->middleware('permission:manufacturers.view')->name('manufacturers.index');
        Route::get('/manufacturers/create', [AdminManufacturerController::class, 'create'])->middleware('permission:manufacturers.create')->name('manufacturers.create');
        Route::post('/manufacturers', [AdminManufacturerController::class, 'store'])->middleware('permission:manufacturers.create')->name('manufacturers.store');
        Route::get('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'show'])->middleware('permission:manufacturers.view')->name('manufacturers.show');
        Route::get('/manufacturers/{manufacturer}/edit', [AdminManufacturerController::class, 'edit'])->middleware('permission:manufacturers.update')->name('manufacturers.edit');
        Route::put('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'update'])->middleware('permission:manufacturers.update')->name('manufacturers.update');
        Route::delete('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'destroy'])->middleware('permission:manufacturers.delete')->name('manufacturers.destroy');


        // Orders Management
        Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('orders');
        Route::get('/orders/export', [OrderController::class, 'export'])->middleware('permission:orders.view')->name('orders.export');
        Route::get('/orders/create', [OrderController::class, 'create'])->middleware('permission:orders.create')->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->middleware('permission:orders.create')->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->middleware('permission:orders.update')->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:orders.update')->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete')->name('orders.destroy');

        // RFQs Management (Admin Full CRUD + Monitoring)
        Route::get('/rfqs', [AdminRfqController::class, 'index'])->middleware('permission:rfqs.view')->name('rfqs.index');
        Route::get('/rfqs/create', [AdminRfqController::class, 'create'])->middleware('permission:rfqs.create')->name('rfqs.create');
        Route::post('/rfqs', [AdminRfqController::class, 'store'])->middleware('permission:rfqs.create')->name('rfqs.store');
        Route::get('/rfqs/{rfq}', [AdminRfqController::class, 'show'])->middleware('permission:rfqs.view')->name('rfqs.show');
        Route::get('/rfqs/{rfq}/edit', [AdminRfqController::class, 'edit'])->middleware('permission:rfqs.update')->name('rfqs.edit');
        Route::put('/rfqs/{rfq}', [AdminRfqController::class, 'update'])->middleware('permission:rfqs.update')->name('rfqs.update');
        Route::delete('/rfqs/{rfq}', [AdminRfqController::class, 'destroy'])->middleware('permission:rfqs.delete')->name('rfqs.destroy');
        Route::patch('/rfqs/{rfq}/status', [AdminRfqController::class, 'updateStatus'])->middleware('permission:rfqs.update_status')->name('rfqs.update-status');
        Route::patch('/rfqs/{rfq}/visibility', [AdminRfqController::class, 'toggleVisibility'])->middleware('permission:rfqs.toggle_visibility')->name('rfqs.toggle-visibility');
        Route::post('/rfqs/{rfq}/assign-suppliers', [AdminRfqController::class, 'assignSuppliers'])->middleware('permission:rfqs.assign_suppliers')->name('rfqs.assign-suppliers');

        // RFQ Items Management
        Route::get('/rfqs/{rfq}/items/create', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'create'])->middleware('permission:rfqs.update')->name('rfqs.items.create');
        Route::post('/rfqs/{rfq}/items', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'store'])->middleware('permission:rfqs.update')->name('rfqs.items.store');
        Route::get('/rfqs/{rfq}/items/{item}/edit', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'edit'])->middleware('permission:rfqs.update')->name('rfqs.items.edit');
        Route::put('/rfqs/{rfq}/items/{item}', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'update'])->middleware('permission:rfqs.update')->name('rfqs.items.update');
        Route::delete('/rfqs/{rfq}/items/{item}', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'destroy'])->middleware('permission:rfqs.update')->name('rfqs.items.destroy');

        // Quotations Management (Admin Full CRUD + Monitoring)
        Route::get('/quotations', [AdminQuotationController::class, 'index'])->middleware('permission:quotations.view')->name('quotations.index');
        Route::get('/quotations/export', [AdminQuotationController::class, 'export'])->middleware('permission:quotations.view')->name('quotations.export');
        Route::get('/quotations/create', [AdminQuotationController::class, 'create'])->middleware('permission:quotations.create')->name('quotations.create');
        Route::post('/quotations', [AdminQuotationController::class, 'store'])->middleware('permission:quotations.create')->name('quotations.store');
        Route::get('/quotations/compare', [AdminQuotationController::class, 'compare'])->middleware('permission:quotations.compare')->name('quotations.compare');
        Route::get('/quotations/{quotation}', [AdminQuotationController::class, 'show'])->middleware('permission:quotations.view')->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [AdminQuotationController::class, 'edit'])->middleware('permission:quotations.update')->name('quotations.edit');
        Route::put('/quotations/{quotation}', [AdminQuotationController::class, 'update'])->middleware('permission:quotations.update')->name('quotations.update');
        Route::delete('/quotations/{quotation}', [AdminQuotationController::class, 'destroy'])->middleware('permission:quotations.delete')->name('quotations.destroy');
        Route::post('/quotations/{quotation}/accept', [AdminQuotationController::class, 'accept'])->middleware('permission:quotations.accept')->name('quotations.accept');
        Route::post('/quotations/{quotation}/reject', [AdminQuotationController::class, 'reject'])->middleware('permission:quotations.reject')->name('quotations.reject');

        // Invoices Management
        Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('permission:invoices.view')->name('invoices.index');
        Route::get('/invoices/export', [InvoiceController::class, 'export'])->middleware('permission:invoices.export')->name('invoices.export');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->middleware('permission:invoices.create')->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->middleware('permission:invoices.create')->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view')->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('permission:invoices.update')->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:invoices.update')->name('invoices.update');
        Route::post('/invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->middleware('permission:invoices.approve')->name('invoices.approve');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->middleware('permission:invoices.update')->name('invoices.cancel');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('permission:invoices.delete')->name('invoices.destroy');

        // Payments Management
        Route::get('/payments', [PaymentController::class, 'index'])->middleware('permission:payments.view')->name('payments.index');
        Route::get('/payments/export', [PaymentController::class, 'export'])->middleware('permission:payments.export')->name('payments.export');
        Route::get('/payments/print', [PaymentController::class, 'print'])->middleware('permission:payments.view')->name('payments.print');
        Route::get('/payments/create', [PaymentController::class, 'create'])->middleware('permission:payments.create')->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->middleware('permission:payments.create')->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->middleware('permission:payments.view')->name('payments.show');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->middleware('permission:payments.update')->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->middleware('permission:payments.update')->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->middleware('permission:payments.delete')->name('payments.destroy');

        // Deliveries Management
        Route::get('/deliveries', [DeliveryController::class, 'index'])->middleware('permission:deliveries.view')->name('deliveries.index');
        Route::get('/deliveries/export', [DeliveryController::class, 'export'])->middleware('permission:deliveries.view')->name('deliveries.export');
        Route::get('/deliveries/create', [DeliveryController::class, 'create'])->middleware('permission:deliveries.create')->name('deliveries.create');
        Route::post('/deliveries', [DeliveryController::class, 'store'])->middleware('permission:deliveries.create')->name('deliveries.store');
        Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->middleware('permission:deliveries.view')->name('deliveries.show');
        Route::get('/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->middleware('permission:deliveries.update')->name('deliveries.edit');
        Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update'])->middleware('permission:deliveries.update')->name('deliveries.update');
        Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->middleware('permission:deliveries.delete')->name('deliveries.destroy');

        // Reports
        Route::get('/reports', [AdminReportsController::class, 'index'])->middleware('permission:reports.view')->name('reports');

        // Activity Log
        Route::get('/activity', [ActivityLogController::class, 'index'])->middleware('permission:activity_logs.view')->name('activity');
        Route::get('/activity/{activity}', [ActivityLogController::class, 'show'])->middleware('permission:activity_logs.view')->name('activity.show');

        // Registration Approvals
        Route::get('/registrations/pending', [RegistrationApprovalController::class, 'index'])->middleware('permission:suppliers.verify')->name('registrations.pending');
        Route::post('/registrations/{type}/{id}/approve', [RegistrationApprovalController::class, 'approve'])->middleware('permission:suppliers.verify')->name('registrations.approve');
        Route::post('/registrations/{type}/{id}/reject', [RegistrationApprovalController::class, 'reject'])->middleware('permission:suppliers.verify')->name('registrations.reject');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings.view')->name('settings.index');
        Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->middleware('permission:settings.update')->name('settings.update.general');
        Route::post('/settings/email', [SettingController::class, 'updateEmail'])->middleware('permission:settings.update')->name('settings.update.email');
        Route::post('/settings/payment', [SettingController::class, 'updatePayment'])->middleware('permission:settings.update')->name('settings.update.payment');
        Route::post('/settings/security', [SettingController::class, 'updateSecurity'])->middleware('permission:settings.update')->name('settings.update.security');
        Route::post('/settings/email/test', [SettingController::class, 'testEmailConnection'])->middleware('permission:settings.update')->name('settings.email.test');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->middleware('permission:notifications.view')
            ->name('notifications.index');

        Route::get('/notifications/create', [NotificationController::class, 'create'])
            ->middleware('permission:notifications.create')
            ->name('notifications.create');

        Route::post('/notifications', [NotificationController::class, 'store'])
            ->middleware('permission:notifications.create')
            ->name('notifications.store');


        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
            ->middleware('permission:notifications.view')
            ->name('notifications.read');

        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->middleware('permission:notifications.view')
            ->name('notifications.read-all');

        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
            ->middleware('permission:notifications.delete')
            ->name('notifications.destroy');

        Route::delete('/notifications', [NotificationController::class, 'destroyAll'])
            ->middleware('permission:notifications.delete')
            ->name('notifications.destroy-all');
    });

    // Supplier Routes
    Route::prefix('supplier')->name('supplier.')->middleware(['role:Supplier', 'supplier.profile'])->group(function () {
        // Supplier Dashboard
        Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');

        // Supplier Products (Full CRUD)
        Route::resource('products', SupplierProductController::class)->except(['destroy']);
        Route::delete('/products/{product}', [SupplierProductController::class, 'destroy'])->name('products.destroy');

        // Supplier RFQs (Assigned Only)
        Route::get('/rfqs', [SupplierRfqController::class, 'index'])->name('rfqs.index');
        Route::get('/rfqs/{rfq}', [SupplierRfqController::class, 'show'])->name('rfqs.show');

        // Supplier Quotations
        Route::get('/rfqs/{rfq}/quote', [SupplierRfqController::class, 'createQuote'])->name('rfqs.quote.create');
        Route::post('/rfqs/{rfq}/quote', [SupplierRfqController::class, 'storeQuote'])->name('rfqs.quote.store');
        Route::get('/quotations', [SupplierRfqController::class, 'myQuotations'])->name('quotations.index');
        Route::get('/quotations/export', [SupplierRfqController::class, 'exportQuotations'])->name('quotations.export');
        Route::get('/quotations/{quotation}', [SupplierRfqController::class, 'showQuotation'])->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [SupplierRfqController::class, 'editQuote'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [SupplierRfqController::class, 'updateQuote'])->name('quotations.update');
        Route::delete('/quotations/{quotation}', [SupplierRfqController::class, 'destroyQuote'])->name('quotations.destroy');

        // Supplier Orders
        Route::get('/orders', [SupplierOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [SupplierOrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/{order}', [SupplierOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [SupplierOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{order}/create-invoice', [SupplierOrderController::class, 'createInvoice'])->name('orders.create-invoice');

        // Supplier Deliveries
        Route::get('/deliveries', [SupplierDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/create/{order}', [SupplierDeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('/deliveries/{order}', [SupplierDeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('/deliveries/{delivery}', [SupplierDeliveryController::class, 'show'])->name('deliveries.show');
        Route::patch('/deliveries/{delivery}/status', [SupplierDeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::post('/deliveries/{delivery}/proof', [SupplierDeliveryController::class, 'uploadProof'])->name('deliveries.upload-proof');

        // Supplier Invoices
        Route::get('/invoices', [SupplierInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/export', [SupplierInvoiceController::class, 'export'])->name('invoices.export');
        Route::get('/invoices/create', [SupplierInvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [SupplierInvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [SupplierInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [SupplierInvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [SupplierInvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/cancel', [SupplierInvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('/invoices/{invoice}/approve', [SupplierInvoiceController::class, 'approve'])->name('invoices.approve');
        Route::delete('/invoices/{invoice}', [SupplierInvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('/invoices/{invoice}/send', [SupplierInvoiceController::class, 'sendToBuyer'])->name('invoices.send');
        Route::get('/invoices/{invoice}/print', [SupplierInvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/payments', [SupplierInvoiceController::class, 'storePayment'])->name('invoices.payments.store');

        // Supplier Payments
        Route::get('/payments', [SupplierPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [SupplierPaymentController::class, 'show'])->name('payments.show');

        // Supplier Profile
        Route::get('/profile', [SupplierProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [SupplierProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [SupplierProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [SupplierProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/document', [SupplierProfileController::class, 'uploadDocument'])->name('profile.upload-document');
        Route::delete('/profile/document/{mediaId}', [SupplierProfileController::class, 'deleteDocument'])->name('profile.delete-document');

        // Supplier Notifications
        Route::get('/notifications', [SupplierNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [SupplierNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [SupplierNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/reply', [SupplierNotificationController::class, 'reply'])->name('notifications.reply');
        Route::delete('/notifications/{id}', [SupplierNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [SupplierNotificationController::class, 'destroyAll'])->name('notifications.destroy-all');

        // Supplier Activity Logs
        Route::get('/activity', [SupplierActivityLogController::class, 'index'])->name('activity.index');
        Route::get('/activity/{activity}', [SupplierActivityLogController::class, 'show'])->name('activity.show');

        // Supplier Reports
        Route::get('/reports', [SupplierReportsController::class, 'index'])->name('reports.index');

        // Supplier Performance Dashboard
        Route::get('/performance', [\App\Http\Controllers\Web\Suppliers\SupplierPerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{metric}', [\App\Http\Controllers\Web\Suppliers\SupplierPerformanceController::class, 'show'])->name('performance.show');
    });

    // Buyer Routes
    Route::prefix('buyer')->name('buyer.')->middleware(['role:Buyer', 'buyer.verified'])->group(function () {
        // Buyer Dashboard
        Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');

        // Buyer Products (Catalog Browsing)
        Route::get('/products', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'index'])->name('products.index');
        Route::get('/products/compare', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'compare'])->name('products.compare');
        Route::get('/products/favorites', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'favorites'])->name('products.favorites');
        Route::get('/products/{product}', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'show'])->name('products.show');
        Route::post('/products/{product}/favorite', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'toggleFavorite'])->name('products.favorite');
        Route::get('/products/{product}/create-rfq', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'createRfqWithProduct'])->name('products.create-rfq');
        // Phase 3: Price & Stock Alerts
        Route::post('/products/{product}/price-alert', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'setPriceAlert'])->name('products.price-alert');
        Route::delete('/products/{product}/price-alert', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'removePriceAlert'])->name('products.price-alert.remove');
        Route::post('/products/{product}/stock-alert', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'setStockAlert'])->name('products.stock-alert');
        Route::delete('/products/{product}/stock-alert', [\App\Http\Controllers\Web\Buyers\BuyerProductController::class, 'removeStockAlert'])->name('products.stock-alert.remove');

        // Buyer RFQs Management
        Route::get('/rfqs', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'index'])->name('rfqs.index');
        Route::get('/rfqs/create', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'create'])->name('rfqs.create');
        Route::post('/rfqs', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'store'])->name('rfqs.store');
        Route::get('/rfqs/{rfq}', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'show'])->name('rfqs.show');
        Route::get('/rfqs/{rfq}/edit', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'edit'])->name('rfqs.edit');
        Route::put('/rfqs/{rfq}', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'update'])->name('rfqs.update');
        Route::delete('/rfqs/{rfq}', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'destroy'])->name('rfqs.destroy');
        Route::patch('/rfqs/{rfq}/status', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'updateStatus'])->name('rfqs.update-status');

        // Smart RFQ Features
        Route::post('/rfqs/{rfq}/duplicate', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'duplicate'])->name('rfqs.duplicate');
        Route::post('/rfqs/import-csv', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'importCsv'])->name('rfqs.import-csv');
        Route::get('/rfqs/csv-sample/download', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'downloadCsvSample'])->name('rfqs.csv-sample');
        Route::post('/rfqs/estimate-budget', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'estimateBudget'])->name('rfqs.estimate-budget');
        Route::post('/rfqs/suggest-suppliers', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'suggestSuppliers'])->name('rfqs.suggest-suppliers');
        Route::post('/rfqs/suggest-deadline', [\App\Http\Controllers\Web\Buyers\BuyerRfqController::class, 'suggestDeadline'])->name('rfqs.suggest-deadline');

        // RFQ Templates
        Route::get('/rfq-templates', [\App\Http\Controllers\Web\Buyers\BuyerRfqTemplateController::class, 'index'])->name('rfq-templates.index');
        Route::get('/rfq-templates/{template}', [\App\Http\Controllers\Web\Buyers\BuyerRfqTemplateController::class, 'show'])->name('rfq-templates.show');
        Route::post('/rfq-templates/{template}/use', [\App\Http\Controllers\Web\Buyers\BuyerRfqTemplateController::class, 'use'])->name('rfq-templates.use');
        Route::post('/rfqs/{rfq}/save-as-template', [\App\Http\Controllers\Web\Buyers\BuyerRfqTemplateController::class, 'saveFromRfq'])->name('rfqs.save-as-template');
        Route::delete('/rfq-templates/{template}', [\App\Http\Controllers\Web\Buyers\BuyerRfqTemplateController::class, 'destroy'])->name('rfq-templates.destroy');

        // Buyer Quotations Management
        Route::get('/quotations', [\App\Http\Controllers\Web\Buyers\BuyerQuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/compare', [\App\Http\Controllers\Web\Buyers\BuyerQuotationController::class, 'compare'])->name('quotations.compare');
        Route::get('/quotations/{quotation}', [\App\Http\Controllers\Web\Buyers\BuyerQuotationController::class, 'show'])->name('quotations.show');
        Route::post('/quotations/{quotation}/accept', [\App\Http\Controllers\Web\Buyers\BuyerQuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/quotations/{quotation}/reject', [\App\Http\Controllers\Web\Buyers\BuyerQuotationController::class, 'reject'])->name('quotations.reject');

        // Buyer Profile Management
        Route::get('/profile', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/document', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'uploadDocument'])->name('profile.upload-document');
        Route::delete('/profile/document/{mediaId}', [\App\Http\Controllers\Web\Buyers\BuyerProfileController::class, 'deleteDocument'])->name('profile.delete-document');

        // Buyer Orders
        Route::get('/orders', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/history', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{order}', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/reorder', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'reorder'])->name('orders.reorder');
        Route::post('/orders/{order}/add-to-cart', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'addToCart'])->name('orders.add-to-cart');
        Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Web\Buyers\BuyerOrderController::class, 'cancel'])->name('orders.cancel');

        // Buyer Invoices
        Route::get('/invoices', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/acknowledge', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'acknowledge'])->name('invoices.acknowledge');
        Route::post('/invoices/{invoice}/dispute', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'dispute'])->name('invoices.dispute');
        Route::post('/invoices/{invoice}/request-copy', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'requestCopy'])->name('invoices.request-copy');
        Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\Web\Buyers\BuyerInvoiceController::class, 'print'])->name('invoices.print');

        // Buyer Deliveries
        Route::get('/deliveries', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{delivery}', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/confirm', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryController::class, 'confirmReceipt'])->name('deliveries.confirm');

        // Buyer Notifications
        Route::get('/notifications', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/reply', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'reply'])->name('notifications.reply');
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [\App\Http\Controllers\Web\Buyers\BuyerNotificationController::class, 'destroyAll'])->name('notifications.destroy-all');

        // Buyer Suppliers Directory
        Route::get('/suppliers', [\App\Http\Controllers\Web\Buyers\BuyerSupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/{supplier}', [\App\Http\Controllers\Web\Buyers\BuyerSupplierController::class, 'show'])->name('suppliers.show');

        // Buyer Cart / RFQ Builder
        Route::get('/cart', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'add'])->name('cart.add');
        Route::put('/cart/items/{cartItem}', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{cartItem}', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'remove'])->name('cart.remove');
        // Backward compatibility route (for old views)
        Route::put('/cart/update/{product}', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'updateByProduct'])->name('cart.update.legacy');
        Route::delete('/cart/remove/{product}', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'removeByProduct'])->name('cart.remove.legacy');
        Route::delete('/cart/clear', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'clear'])->name('cart.clear');
        Route::get('/cart/count', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'count'])->name('cart.count');
        Route::get('/cart/checkout', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'checkout'])->name('cart.checkout');
        Route::post('/cart/submit-rfq', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'submitRfq'])->name('cart.submit-rfq');
        Route::post('/cart/templates/{template}/load', [\App\Http\Controllers\Web\Buyers\BuyerCartController::class, 'loadTemplate'])->name('cart.template.load');

        // Buyer Reports
        Route::get('/reports', [\App\Http\Controllers\Web\Buyers\BuyerReportsController::class, 'index'])->name('reports.index');

        // Buyer Reviews (Supplier Reviews)
        Route::get('/reviews', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/create', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'store'])->name('reviews.store');
        Route::get('/reviews/{review}', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'show'])->name('reviews.show');
        Route::get('/reviews/{review}/edit', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Web\Buyers\BuyerReviewController::class, 'destroy'])->name('reviews.destroy');

        // Buyer Delivery Tracking & Disputes
        Route::get('/deliveries/{order}/tracking', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'show'])->name('deliveries.tracking');
        Route::get('/deliveries/calendar', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'calendar'])->name('deliveries.calendar');
        Route::get('/deliveries/{order}/create-dispute', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'createDispute'])->name('deliveries.create-dispute');
        Route::post('/deliveries/{order}/disputes', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'storeDispute'])->name('deliveries.store-dispute');
        Route::get('/deliveries/disputes', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'disputes'])->name('deliveries.disputes');
        Route::get('/deliveries/disputes/{dispute}', [\App\Http\Controllers\Web\Buyers\BuyerDeliveryTrackingController::class, 'showDispute'])->name('deliveries.dispute');

        // Legacy routes (redirect for backward compatibility)
        Route::get('/favorites', fn() => redirect()->route('buyer.products.favorites'))->name('favorites');
    });
});

// ========================================
// API Routes (Product Search)
// ========================================
Route::middleware(['auth'])->prefix('api/products')->group(function () {
    Route::get('/search', [\App\Http\Controllers\Api\ProductSearchController::class, 'search']);
    Route::get('/autocomplete', [\App\Http\Controllers\Api\ProductSearchController::class, 'autocomplete']);
    Route::get('/for-supplier', [\App\Http\Controllers\Api\ProductSearchController::class, 'forSupplier']);
});

// Public product filters (no auth required)
Route::get('/api/products/filters', [\App\Http\Controllers\Api\ProductSearchController::class, 'filters']);

require __DIR__.'/auth.php';

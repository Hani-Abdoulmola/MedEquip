<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\PermissionController;

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
})->middleware('auth')->name('dashboard');

// Waiting Approval Page (for pending/rejected suppliers and buyers)
Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->middleware('auth')->name('auth.waiting-approval');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes - Protected by Admin role
    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
        // Users Management
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.update-permissions');

        // Roles Management
        Route::resource('roles', RoleController::class);

        // Permissions Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');


        // Suppliers Management
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
        Route::get('/suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::post('/suppliers/{supplier}/verify', [SupplierController::class, 'verify'])->name('suppliers.verify');
        Route::post('/suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('suppliers.toggle-active');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');


        // Buyers Management
        Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers');
        Route::get('/buyers/export', [BuyerController::class, 'export'])->name('buyers.export');
        Route::get('/buyers/create', [BuyerController::class, 'create'])->name('buyers.create');
        Route::post('/buyers', [BuyerController::class, 'store'])->name('buyers.store');
        Route::get('/buyers/{buyer}', [BuyerController::class, 'show'])->name('buyers.show');
        Route::get('/buyers/{buyer}/edit', [BuyerController::class, 'edit'])->name('buyers.edit');
        Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])->name('buyers.update');
        Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])->name('buyers.destroy');
        Route::post('/buyers/{buyer}/toggle-active', [BuyerController::class, 'toggleActive'])->name('buyers.toggle-active');
        Route::post('/buyers/{buyer}/verify', [BuyerController::class, 'verifyBuyer'])->name('buyers.verify');


        // Products Management
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/review', [ProductReviewController::class, 'review'])->name('products.review');
        Route::post('/products/{product}/approve', [ProductReviewController::class, 'approve'])->name('products.approve');
        Route::post('/products/{product}/reject', [ProductReviewController::class, 'reject'])->name('products.reject');
        Route::post('/products/{product}/request-changes', [ProductReviewController::class, 'requestChanges'])->name('products.request_changes');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


        // Product Categories Management
        Route::get('/categories', [ProductCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [ProductCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [ProductCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [ProductCategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{category}/edit', [ProductCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [ProductCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [ProductCategoryController::class, 'destroy'])->name('categories.destroy');

        // Manufacturers Management
        Route::get('/manufacturers', [AdminManufacturerController::class, 'index'])->name('manufacturers.index');
        Route::get('/manufacturers/create', [AdminManufacturerController::class, 'create'])->name('manufacturers.create');
        Route::post('/manufacturers', [AdminManufacturerController::class, 'store'])->name('manufacturers.store');
        Route::get('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'show'])->name('manufacturers.show');
        Route::get('/manufacturers/{manufacturer}/edit', [AdminManufacturerController::class, 'edit'])->name('manufacturers.edit');
        Route::put('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'update'])->name('manufacturers.update');
        Route::delete('/manufacturers/{manufacturer}', [AdminManufacturerController::class, 'destroy'])->name('manufacturers.destroy');


        // Orders Management
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        // RFQs Management (Admin Full CRUD + Monitoring)
        Route::get('/rfqs', [AdminRfqController::class, 'index'])->name('rfqs.index');
        Route::get('/rfqs/create', [AdminRfqController::class, 'create'])->name('rfqs.create');
        Route::post('/rfqs', [AdminRfqController::class, 'store'])->name('rfqs.store');
        Route::get('/rfqs/{rfq}', [AdminRfqController::class, 'show'])->name('rfqs.show');
        Route::get('/rfqs/{rfq}/edit', [AdminRfqController::class, 'edit'])->name('rfqs.edit');
        Route::put('/rfqs/{rfq}', [AdminRfqController::class, 'update'])->name('rfqs.update');
        Route::delete('/rfqs/{rfq}', [AdminRfqController::class, 'destroy'])->name('rfqs.destroy');
        Route::patch('/rfqs/{rfq}/status', [AdminRfqController::class, 'updateStatus'])->name('rfqs.update-status');
        Route::patch('/rfqs/{rfq}/visibility', [AdminRfqController::class, 'toggleVisibility'])->name('rfqs.toggle-visibility');
        Route::post('/rfqs/{rfq}/assign-suppliers', [AdminRfqController::class, 'assignSuppliers'])->name('rfqs.assign-suppliers');

        // RFQ Items Management
        Route::get('/rfqs/{rfq}/items/create', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'create'])->name('rfqs.items.create');
        Route::post('/rfqs/{rfq}/items', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'store'])->name('rfqs.items.store');
        Route::get('/rfqs/{rfq}/items/{item}/edit', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'edit'])->name('rfqs.items.edit');
        Route::put('/rfqs/{rfq}/items/{item}', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'update'])->name('rfqs.items.update');
        Route::delete('/rfqs/{rfq}/items/{item}', [\App\Http\Controllers\Web\AdminRfqItemController::class, 'destroy'])->name('rfqs.items.destroy');

        // Quotations Management (Admin Full CRUD + Monitoring)
        Route::get('/quotations', [AdminQuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/export', [AdminQuotationController::class, 'export'])->name('quotations.export');
        Route::get('/quotations/create', [AdminQuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [AdminQuotationController::class, 'store'])->name('quotations.store');
        Route::get('/quotations/compare', [AdminQuotationController::class, 'compare'])->name('quotations.compare');
        Route::get('/quotations/{quotation}', [AdminQuotationController::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [AdminQuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [AdminQuotationController::class, 'update'])->name('quotations.update');
        Route::delete('/quotations/{quotation}', [AdminQuotationController::class, 'destroy'])->name('quotations.destroy');
        Route::post('/quotations/{quotation}/accept', [AdminQuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/quotations/{quotation}/reject', [AdminQuotationController::class, 'reject'])->name('quotations.reject');

        // Invoices Management
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // Payments Management
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Deliveries Management
        Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/export', [DeliveryController::class, 'export'])->name('deliveries.export');
        Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
        Route::get('/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('deliveries.update');
        Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

        // Reports
        Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports');

        // Activity Log
        Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity');
        Route::get('/activity/{activity}', [ActivityLogController::class, 'show'])->name('activity.show');

        // Registration Approvals
        Route::get('/registrations/pending', [RegistrationApprovalController::class, 'index'])->name('registrations.pending');
        Route::post('/registrations/{type}/{id}/approve', [RegistrationApprovalController::class, 'approve'])->name('registrations.approve');
        Route::post('/registrations/{type}/{id}/reject', [RegistrationApprovalController::class, 'reject'])->name('registrations.reject');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->name('settings.update.general');
        Route::post('/settings/email', [SettingController::class, 'updateEmail'])->name('settings.update.email');
        Route::post('/settings/payment', [SettingController::class, 'updatePayment'])->name('settings.update.payment');
        Route::post('/settings/security', [SettingController::class, 'updateSecurity'])->name('settings.update.security');
        Route::post('/settings/email/test', [SettingController::class, 'testEmailConnection'])->name('settings.email.test');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');

        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.read-all');

        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
            ->name('notifications.destroy');

        Route::delete('/notifications', [NotificationController::class, 'destroyAll'])
            ->name('notifications.destroy-all');
    });

    // Supplier Routes
    Route::prefix('supplier')->name('supplier.')->middleware('role:Supplier')->group(function () {
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
        Route::get('/invoices/{invoice}', [SupplierInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [SupplierInvoiceController::class, 'download'])->name('invoices.download');

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
        Route::delete('/notifications/{id}', [SupplierNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [SupplierNotificationController::class, 'destroyAll'])->name('notifications.destroy-all');

        // Supplier Activity Logs
        Route::get('/activity', [SupplierActivityLogController::class, 'index'])->name('activity.index');
        Route::get('/activity/{activity}', [SupplierActivityLogController::class, 'show'])->name('activity.show');

        // Supplier Reports
        Route::get('/reports', [SupplierReportsController::class, 'index'])->name('reports.index');
    });

    // Buyer Routes
    Route::prefix('buyer')->name('buyer.')->middleware('role:Buyer')->group(function () {
        // Buyer Dashboard
        Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');

        // Buyer Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Buyer Resources
        Route::get('/favorites', fn() => view('buyer.favorites'))->name('favorites');
        Route::get('/suppliers', fn() => view('buyer.suppliers'))->name('suppliers');
    });
});

require __DIR__.'/auth.php';

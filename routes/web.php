<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\BuyerController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RegistrationApprovalController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Waiting Approval Page (for pending/rejected suppliers and buyers)
Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->middleware('auth')->name('auth.waiting-approval');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Users Management (with permission middleware for Laravel 12)
        Route::get('/users', [UserController::class, 'index'])
            ->name('users')
            ->middleware('permission:view users');
        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create')
            ->middleware('permission:create users');
        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('permission:create users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit')
            ->middleware('permission:edit users');
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('permission:edit users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('permission:delete users');

        // Suppliers Management
        Route::get('/suppliers', [SupplierController::class, 'index'])
            ->name('suppliers')
            ->middleware('permission:view suppliers');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])
            ->name('suppliers.create')
            ->middleware('permission:create suppliers');
        Route::post('/suppliers', [SupplierController::class, 'store'])
            ->name('suppliers.store')
            ->middleware('permission:create suppliers');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
            ->name('suppliers.show')
            ->middleware('permission:view suppliers');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
            ->name('suppliers.edit')
            ->middleware('permission:edit suppliers');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
            ->name('suppliers.update')
            ->middleware('permission:edit suppliers');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
            ->name('suppliers.destroy')
            ->middleware('permission:delete suppliers');

        // Buyers Management
        Route::get('/buyers', [BuyerController::class, 'index'])
            ->name('buyers')
            ->middleware('permission:view buyers');
        Route::get('/buyers/create', [BuyerController::class, 'create'])
            ->name('buyers.create')
            ->middleware('permission:create buyers');
        Route::post('/buyers', [BuyerController::class, 'store'])
            ->name('buyers.store')
            ->middleware('permission:create buyers');
        Route::get('/buyers/{buyer}', [BuyerController::class, 'show'])
            ->name('buyers.show')
            ->middleware('permission:view buyers');
        Route::get('/buyers/{buyer}/edit', [BuyerController::class, 'edit'])
            ->name('buyers.edit')
            ->middleware('permission:edit buyers');
        Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])
            ->name('buyers.update')
            ->middleware('permission:edit buyers');
        Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])
            ->name('buyers.destroy')
            ->middleware('permission:delete buyers');

        // Products Management
        Route::get('/products', [ProductController::class, 'index'])
            ->name('products')
            ->middleware('permission:view products');
        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('products.create')
            ->middleware('permission:create products');
        Route::post('/products', [ProductController::class, 'store'])
            ->name('products.store')
            ->middleware('permission:create products');
        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->name('products.show')
            ->middleware('permission:view products');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware('permission:edit products');
        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update')
            ->middleware('permission:edit products');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware('permission:delete products');

        // Orders Management
        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders')
            ->middleware('permission:view orders');
        Route::get('/orders/create', [OrderController::class, 'create'])
            ->name('orders.create')
            ->middleware('permission:create orders');
        Route::post('/orders', [OrderController::class, 'store'])
            ->name('orders.store')
            ->middleware('permission:create orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show')
            ->middleware('permission:view orders');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])
            ->name('orders.edit')
            ->middleware('permission:edit orders');
        Route::put('/orders/{order}', [OrderController::class, 'update'])
            ->name('orders.update')
            ->middleware('permission:edit orders');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
            ->name('orders.destroy')
            ->middleware('permission:delete orders');

        // Reports
        Route::get('/reports', fn() => view('admin.reports.index'))->name('reports');

        // Activity Log
        Route::get('/activity', [ActivityLogController::class, 'index'])
            ->name('activity')
            ->middleware('permission:view activity logs');
        Route::get('/activity/{activity}', [ActivityLogController::class, 'show'])
            ->name('activity.show')
            ->middleware('permission:view activity logs');

        // Registration Approvals
        Route::get('/registrations/pending', [RegistrationApprovalController::class, 'index'])
            ->name('registrations.pending')
            ->middleware('permission:view users');
        Route::post('/registrations/{type}/{id}/approve', [RegistrationApprovalController::class, 'approve'])
            ->name('registrations.approve')
            ->middleware('permission:edit users');
        Route::post('/registrations/{type}/{id}/reject', [RegistrationApprovalController::class, 'reject'])
            ->name('registrations.reject')
            ->middleware('permission:edit users');

        // Settings
        Route::get('/settings', fn() => view('admin.settings.index'))->name('settings');
    });

    // Supplier Routes (Placeholder)
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/products', fn() => view('dashboard'))->name('products');
        Route::get('/orders', fn() => view('dashboard'))->name('orders');
        Route::get('/sales', fn() => view('dashboard'))->name('sales');
        Route::get('/profile', fn() => view('dashboard'))->name('profile');
    });

    // Buyer Routes (Placeholder)
    Route::prefix('buyer')->name('buyer.')->group(function () {
        Route::get('/orders', fn() => view('dashboard'))->name('orders');
        Route::get('/favorites', fn() => view('dashboard'))->name('favorites');
        Route::get('/suppliers', fn() => view('dashboard'))->name('suppliers');
    });
});

require __DIR__.'/auth.php';

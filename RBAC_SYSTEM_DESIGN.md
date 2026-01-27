# 🔐 Role-Based Access Control (RBAC) System Design
## MedEquip Platform - Complete RBAC Architecture

**Version:** 1.0  
**Date:** 2025-01-23  
**Framework:** Laravel 12.35.1 with Spatie Laravel Permission

---

## 📋 Table of Contents

1. [Permission Model](#1-permission-model)
2. [User Creation & Permission Assignment Workflow](#2-user-creation--permission-assignment-workflow)
3. [Permission List & System Features Mapping](#3-permission-list--system-features-mapping)
4. [Access Control Enforcement Rules](#4-access-control-enforcement-rules)
5. [Frontend Permission Visibility](#5-frontend-permission-visibility)

---

## 1. Permission Model

### 1.1 Core Concepts

**Roles vs Permissions:**
- **Roles** are collections of permissions (e.g., `Admin`, `Staff`, `Supplier`, `Buyer`)
- **Permissions** are granular actions (e.g., `users.view`, `products.create`)
- Users can have **direct permissions** (assigned individually) or **role-based permissions** (inherited from roles)

### 1.2 Permission Evaluation Hierarchy

```
User Permission Check Priority:
1. Direct Permissions (highest priority)
2. Role Permissions (inherited from assigned roles)
3. Admin Role Bypass (only for 'Admin' role, not user_type_id)
```

### 1.3 User Types vs Roles

| User Type | Default Role | Permission Source | Can Manage Users? |
|-----------|-------------|------------------|-------------------|
| Admin (user_type_id = 1) | `Admin` | All permissions (unrestricted) | ✅ Yes |
| Staff (user_type_id = 1) | `Staff` | Explicitly granted by Admin | ❌ No |
| Supplier (user_type_id = 2) | `Supplier` | Predefined limited scope | ❌ No |
| Buyer (user_type_id = 3) | `Buyer` | Predefined limited scope | ❌ No |

**Critical Rule:** `user_type_id === 1` does NOT grant admin privileges. Only users with the `Admin` role have unrestricted access.

---

## 2. User Creation & Permission Assignment Workflow

### 2.1 Admin Creates Staff User

```
Step 1: Admin navigates to /admin/users/create
Step 2: Admin fills user form:
  - Name, Email, Phone, Password
  - User Type: "مدير النظام" (Admin type)
  - Role: "Staff" (selected from dropdown)
Step 3: System creates user with:
  - user_type_id = 1
  - Role: Staff (no default permissions)
Step 4: Admin navigates to /admin/role-permissions
Step 5: Admin selects the Staff user
Step 6: Admin grants specific permissions:
  - Checkboxes for each permission
  - Or apply a permission template
Step 7: System saves permissions to database
Step 8: Staff user can now access only granted features
```

### 2.2 Permission Assignment Methods

**Method 1: Individual Permission Assignment**
```php
$user->givePermissionTo('users.view');
$user->givePermissionTo('products.create');
```

**Method 2: Bulk Permission Assignment**
```php
$user->syncPermissions([
    'users.view',
    'users.create',
    'products.view',
    'products.create'
]);
```

**Method 3: Permission Template Application**
```php
// Using PermissionTemplateService
$templateService->applyTemplateToUser($user, 'product_manager', $merge = false);
```

### 2.3 Staff User Default State

- **No permissions** by default
- **Cannot access any admin features** until permissions are granted
- **Sidebar shows only accessible items** based on permissions
- **Backend routes return 403** if permission check fails

---

## 3. Permission List & System Features Mapping

### 3.1 Complete Permission Matrix

#### User Management Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `users.view` | View users list | إدارة المستخدمين | `/admin/users` |
| `users.create` | Create new users | إدارة المستخدمين | `/admin/users/create` |
| `users.update` | Edit existing users | إدارة المستخدمين | `/admin/users/{id}/edit` |
| `users.delete` | Delete users | إدارة المستخدمين | `/admin/users/{id}` (DELETE) |
| `users.manage_permissions` | Assign permissions to users | الأدوار و الصلاحيات | `/admin/role-permissions` |

#### Supplier Management Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `suppliers.view` | View suppliers list | الموردين | `/admin/suppliers` |
| `suppliers.create` | Create new supplier | الموردين | `/admin/suppliers/create` |
| `suppliers.update` | Edit supplier | الموردين | `/admin/suppliers/{id}/edit` |
| `suppliers.delete` | Delete supplier | الموردين | `/admin/suppliers/{id}` (DELETE) |
| `suppliers.verify` | Verify supplier account | الموردين | `/admin/suppliers/{id}/verify` |
| `suppliers.toggle_active` | Activate/deactivate supplier | الموردين | `/admin/suppliers/{id}/toggle-active` |

#### Buyer Management Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `buyers.view` | View buyers list | المشترين | `/admin/buyers` |
| `buyers.create` | Create new buyer | المشترين | `/admin/buyers/create` |
| `buyers.update` | Edit buyer | المشترين | `/admin/buyers/{id}/edit` |
| `buyers.delete` | Delete buyer | المشترين | `/admin/buyers/{id}` (DELETE) |
| `buyers.verify` | Verify buyer account | المشترين | `/admin/buyers/{id}/verify` |
| `buyers.toggle_active` | Activate/deactivate buyer | المشترين | `/admin/buyers/{id}/toggle-active` |

#### Product Management Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `products.view` | View products catalog | كتالوج المنتجات | `/admin/products` |
| `products.create` | Create new product | كتالوج المنتجات | `/admin/products/create` |
| `products.update` | Edit product | كتالوج المنتجات | `/admin/products/{id}/edit` |
| `products.delete` | Delete product | كتالوج المنتجات | `/admin/products/{id}` (DELETE) |
| `products.approve` | Approve product | طلبات المنتجات | `/admin/product-requests/{id}/approve` |
| `products.reject` | Reject product | طلبات المنتجات | `/admin/product-requests/{id}/reject` |
| `products.request_changes` | Request product changes | طلبات المنتجات | `/admin/product-requests/{id}/request-changes` |

#### Category & Manufacturer Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `categories.view` | View categories | فئات المنتجات | `/admin/categories` |
| `categories.create` | Create category | فئات المنتجات | `/admin/categories/create` |
| `categories.update` | Edit category | فئات المنتجات | `/admin/categories/{id}/edit` |
| `categories.delete` | Delete category | فئات المنتجات | `/admin/categories/{id}` (DELETE) |
| `manufacturers.view` | View manufacturers | الشركات المصنعة | `/admin/manufacturers` |
| `manufacturers.create` | Create manufacturer | الشركات المصنعة | `/admin/manufacturers/create` |
| `manufacturers.update` | Edit manufacturer | الشركات المصنعة | `/admin/manufacturers/{id}/edit` |
| `manufacturers.delete` | Delete manufacturer | الشركات المصنعة | `/admin/manufacturers/{id}` (DELETE) |

#### Order Management Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `orders.view` | View orders list | الطلبات | `/admin/orders` |
| `orders.create` | Create new order | الطلبات | `/admin/orders/create` |
| `orders.update` | Edit order | الطلبات | `/admin/orders/{id}/edit` |
| `orders.delete` | Delete order | الطلبات | `/admin/orders/{id}` (DELETE) |
| `orders.confirm` | Confirm order | الطلبات | `/admin/orders/{id}/confirm` |
| `orders.update_status` | Update order status | الطلبات | `/admin/orders/{id}/status` |

#### RFQ & Quotation Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `rfqs.view` | View RFQs | طلبات عروض الأسعار | `/admin/rfqs` |
| `rfqs.create` | Create RFQ | طلبات عروض الأسعار | `/admin/rfqs/create` |
| `rfqs.update` | Edit RFQ | طلبات عروض الأسعار | `/admin/rfqs/{id}/edit` |
| `rfqs.delete` | Delete RFQ | طلبات عروض الأسعار | `/admin/rfqs/{id}` (DELETE) |
| `rfqs.publish` | Publish RFQ | طلبات عروض الأسعار | `/admin/rfqs/{id}/publish` |
| `rfqs.assign_suppliers` | Assign suppliers to RFQ | طلبات عروض الأسعار | `/admin/rfqs/{id}/assign` |
| `quotations.view` | View quotations | عروض الأسعار | `/admin/quotations` |
| `quotations.compare` | Compare quotations | عروض الأسعار | `/admin/quotations/compare` |
| `quotations.accept` | Accept quotation | عروض الأسعار | `/admin/quotations/{id}/accept` |
| `quotations.reject` | Reject quotation | عروض الأسعار | `/admin/quotations/{id}/reject` |

#### System & Reports Permissions
| Permission | Description | Sidebar Section | Route |
|------------|-------------|-----------------|-------|
| `settings.view` | View system settings | الإعدادات | `/admin/settings` |
| `settings.update` | Update system settings | الإعدادات | `/admin/settings` (PUT) |
| `reports.view` | View reports | التقارير | `/admin/reports` |
| `reports.export` | Export reports | التقارير | `/admin/reports/export` |
| `activity_logs.view` | View activity logs | سجل النشاط | `/admin/activity` |
| `notifications.view` | View notifications | الإشعارات | `/admin/notifications` |
| `permissions.view` | View roles & permissions | الأدوار و الصلاحيات | `/admin/role-permissions` |

### 3.2 Permission Templates for Staff Users

Predefined permission sets for common Staff roles:

**Template: `read_only`**
- `users.view`
- `suppliers.view`
- `buyers.view`
- `products.view`
- `orders.view`
- `rfqs.view`
- `quotations.view`
- `activity_logs.view`
- `reports.view`

**Template: `product_manager`**
- All `products.*` permissions
- All `categories.*` permissions
- All `manufacturers.*` permissions

**Template: `order_manager`**
- All `orders.*` permissions
- All `invoices.*` permissions
- All `deliveries.*` permissions

**Template: `user_manager`**
- All `users.*` permissions
- All `suppliers.*` permissions
- All `buyers.*` permissions

**Template: `full_access`**
- All permissions (Admin-like, but still requires Admin role for user management)

---

## 4. Access Control Enforcement Rules

### 4.1 Backend Authorization

#### Route-Level Protection
```php
// routes/web.php
Route::middleware(['auth', 'permission:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
});

Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
});
```

#### Controller-Level Protection
```php
// app/Http/Controllers/Web/UserController.php
public function index(): View
{
    $this->authorize('viewAny', User::class); // Uses Policy
    
    // Or explicit permission check:
    if (!auth()->user()->can('users.view')) {
        abort(403, 'ليس لديك صلاحية عرض المستخدمين');
    }
    
    // Controller logic...
}
```

#### Policy-Based Authorization
```php
// app/Policies/UserPolicy.php
public function viewAny(User $user): bool
{
    // Admin role bypasses all checks
    if ($user->hasRole('Admin')) {
        return true;
    }
    
    // Staff users need explicit permission
    return $user->can('users.view');
}
```

### 4.2 Admin Bypass Rule

**Critical Implementation:**
```php
// Only check for Admin ROLE, NOT user_type_id
if ($user->hasRole('Admin')) {
    // Grant unrestricted access
    return true;
}

// All other users (including Staff with user_type_id = 1) 
// must have explicit permissions
return $user->can('permission_name');
```

**❌ WRONG:**
```php
if ($user->user_type_id === 1) { // This gives all Staff users admin access!
    return true;
}
```

**✅ CORRECT:**
```php
if ($user->hasRole('Admin')) { // Only Admin role bypasses
    return true;
}
```

### 4.3 Permission Check Flow

```
Request → Middleware → Controller → Policy/Gate
   ↓
1. Check if user has 'Admin' role → YES → Allow
   ↓ NO
2. Check if user has direct permission → YES → Allow
   ↓ NO
3. Check if user's role has permission → YES → Allow
   ↓ NO
4. Return 403 Forbidden
```

---

## 5. Frontend Permission Visibility

### 5.1 Custom @Can Directive

**Implementation:**
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::if('can', function ($permission) {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Admin role bypasses all permission checks
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Check explicit permission
        return $user->can($permission);
    });
}
```

### 5.2 Sidebar Item Wrapping

**Example Sidebar Structure:**
```blade
{{-- resources/views/components/dashboard/sidebar.blade.php --}}
@can('users.view')
    <a href="{{ route('admin.users') }}" class="sidebar-item">
        <span>إدارة المستخدمين</span>
    </a>
@endcan

@can('products.view')
    <a href="{{ route('admin.products.index') }}" class="sidebar-item">
        <span>كتالوج المنتجات</span>
    </a>
@endcan
```

### 5.3 Conditional UI Sections

**Example in Views:**
```blade
{{-- resources/views/admin/products/index.blade.php --}}
@can('products.create')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        إضافة منتج جديد
    </a>
@endcan

@can('products.update')
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary">
        تعديل
    </a>
@endcan

@can('products.delete')
    <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">حذف</button>
    </form>
@endcan
```

### 5.4 Permission-Aware Helper Function

**Updated Sidebar Helper:**
```php
function canAccessMenuItem($item, $isAdmin = false) {
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    
    // Only Admin ROLE bypasses, not user_type_id
    if (!$isAdmin) {
        try {
            $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin');
        } catch (\Exception $e) {
            $isAdmin = false;
        }
    }
    
    // Admin role sees all items
    if ($isAdmin) {
        return true;
    }
    
    // Check explicit permission
    if (isset($item['permission'])) {
        try {
            return $user->can($item['permission']);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    // Check role requirement
    if (isset($item['role'])) {
        try {
            return $user->hasRole($item['role']);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    // Default: deny access
    return false;
}
```

---

## 6. Implementation Checklist

### 6.1 Backend Implementation
- [x] Fix sidebar to check Admin role, not user_type_id
- [x] Create @Can Blade directive
- [x] Update all routes with permission middleware
- [x] Update controllers with authorization checks
- [x] Create/update policies for all resources
- [x] Ensure Admin role bypass works correctly

### 6.2 Frontend Implementation
- [x] Wrap sidebar items with @can directive
- [x] Wrap UI action buttons with @can directive
- [x] Update sidebar helper function
- [x] Test permission visibility for Staff users
- [x] Ensure hidden UI elements don't expose routes

### 6.3 Testing
- [ ] Create Staff user with limited permissions
- [ ] Verify sidebar shows only accessible items
- [ ] Verify backend returns 403 for unauthorized routes
- [ ] Test permission templates
- [ ] Test Admin bypass functionality

---

## 7. Security Best Practices

1. **Never trust frontend checks alone** - Always enforce permissions in backend
2. **Use middleware for route protection** - First line of defense
3. **Use policies for complex authorization** - Business logic layer
4. **Cache permissions** - Use Spatie's permission cache
5. **Audit permission changes** - Log all permission assignments
6. **Regular permission audits** - Review Staff user permissions periodically

---

## 8. Troubleshooting

### Issue: Staff user sees all sidebar items
**Solution:** Check sidebar helper - ensure it checks `hasRole('Admin')` not `user_type_id === 1`

### Issue: Staff user can access routes without permission
**Solution:** Verify route middleware includes `permission:...` middleware

### Issue: Permission check returns false for valid permission
**Solution:** Clear permission cache: `php artisan permission:cache-reset`

---

**Document Status:** ✅ Complete  
**Last Updated:** 2025-01-23  
**Maintained By:** System Architecture Team

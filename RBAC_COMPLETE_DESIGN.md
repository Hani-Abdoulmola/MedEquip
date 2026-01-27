# 🔐 Complete RBAC System Design: Admin → Staff Permission Delegation
## Production-Ready Implementation Based on Existing Codebase Analysis

**Version:** 2.0  
**Date:** 2025-01-23  
**Framework:** Laravel 12.35.1 with Spatie Laravel Permission  
**Focus:** Admin and Staff users only (Buyers/Suppliers out of scope)

---

## 📋 Executive Summary

This document provides a complete RBAC design for Admin → Staff permission delegation, built on top of the existing Spatie Laravel Permission implementation. All design decisions are based on thorough codebase analysis and align with existing patterns.

**Key Findings from Codebase Analysis:**
- ✅ Spatie is properly configured with models, policies, and middleware
- ✅ Routes are protected with `permission:` middleware
- ✅ Policies exist for all resources and use `can()` method
- ⚠️ Staff role has default permissions in seeder (needs removal)
- ⚠️ Sidebar uses helper function but needs @can directives
- ⚠️ Views inconsistently use @can directives
- ✅ RolePermissionController already handles permission assignment

---

## 1. Permission Architecture (Spatie-Compatible)

### 1.1 Current Structure

**Database Tables (Spatie):**
```
permissions
├── id
├── name (e.g., 'users.view')
├── ar_name (Arabic label)
├── guard_name ('web')
└── created_at, updated_at

roles
├── id
├── name ('Admin' or 'Staff')
├── ar_name (Arabic label)
├── guard_name ('web')
└── created_at, updated_at

model_has_permissions (Direct Staff permissions)
├── permission_id
├── model_type ('App\Models\User')
├── model_id (Staff user ID)
└── guard_name

role_has_permissions (Role-based permissions)
├── permission_id
├── role_id
└── guard_name

model_has_roles
├── role_id
├── model_type ('App\Models\User')
├── model_id (User ID)
└── guard_name
```

### 1.2 Permission Evaluation Strategy

**Spatie's Built-in Evaluation (Laravel Gate):**
```php
// Spatie automatically registers permissions with Laravel Gate
$user->can('users.view'); // Checks:
// 1. Direct permissions (model_has_permissions)
// 2. Role permissions (via role_has_permissions)
// 3. Returns true/false
```

**Admin Bypass Implementation:**
```php
// In Policies (app/Policies/UserPolicy.php)
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

**Critical Rule:** Only `hasRole('Admin')` bypasses, NOT `user_type_id === 1`

### 1.3 Permission Storage Strategy

**For Staff Users:**
- **Direct Permissions Only:** Staff users receive permissions via `model_has_permissions` table
- **No Role Permissions:** Staff role should have ZERO permissions in `role_has_permissions`
- **Explicit Assignment:** Admin must explicitly grant each permission

**For Admin Users:**
- **Role-Based Permissions:** Admin role has ALL permissions in `role_has_permissions`
- **No Direct Permissions Needed:** Admin inherits from role

---

## 2. Staff User Creation & Permission Assignment Workflow

### 2.1 Current Workflow (Needs Enhancement)

**Step 1: Admin Creates Staff User**
- Route: `/admin/users/create`
- Form: User type = Staff, Role = Staff (auto-assigned)
- Controller: `UserController::store()`
- Result: User created with Staff role, NO permissions

**Step 2: Admin Assigns Permissions**
- Route: `/admin/role-permissions` (existing)
- Controller: `RolePermissionController::assignPermissions()`
- Method: Direct permissions via `syncPermissions()`
- Result: Staff user has explicit permissions

### 2.2 Enhanced Workflow (Recommended)

**Option A: Inline Permission Assignment (During Creation)**
```
1. Admin fills user form
2. Admin selects permissions from grouped checkboxes
3. User created + permissions assigned in one step
4. Redirect to user list
```

**Option B: Separate Permission Assignment Page (Current)**
```
1. Admin creates user
2. Redirect to /admin/role-permissions?user_id={id}
3. Admin selects permissions
4. Permissions saved
```

**Current Implementation:** Option B is already implemented via `RolePermissionController`

### 2.3 Permission Assignment Interface

**Current Implementation:** `resources/views/admin/role-permissions/index.blade.php`

**Features:**
- User selector dropdown
- Permission groups by module
- Checkboxes for each permission
- Template application
- Bulk assignment

**Enhancement Needed:** Add "Assign Permissions" button in user list/edit page

---

## 3. Permission List & System Features Mapping

### 3.1 Complete Permission Matrix (From UnifiedRolePermissionSeeder)

**Total Permissions:** 60+ permissions

#### User Management (5 permissions)
- `users.view` → Sidebar: "إدارة المستخدمين" → Route: `/admin/users`
- `users.create` → Button: "إضافة مستخدم" → Route: `/admin/users/create`
- `users.update` → Button: "تعديل" → Route: `/admin/users/{id}/edit`
- `users.delete` → Button: "حذف" → Route: `/admin/users/{id}` (DELETE)
- `users.manage_permissions` → Button: "الصلاحيات" → Route: `/admin/role-permissions?user_id={id}`

#### Product Management (7 permissions)
- `products.view` → Sidebar: "كتالوج المنتجات" → Route: `/admin/products`
- `products.create` → Button: "إضافة منتج" → Route: `/admin/products/create`
- `products.update` → Button: "تعديل" → Route: `/admin/products/{id}/edit`
- `products.delete` → Button: "حذف" → Route: `/admin/products/{id}` (DELETE)
- `products.approve` → Button: "موافقة" → Route: `/admin/products/{id}/approve`
- `products.reject` → Button: "رفض" → Route: `/admin/products/{id}/reject`
- `products.request_changes` → Button: "طلب تعديلات" → Route: `/admin/products/{id}/request-changes`

#### Order Management (5 permissions)
- `orders.view` → Sidebar: "الطلبات" → Route: `/admin/orders`
- `orders.create` → Button: "إنشاء طلب" → Route: `/admin/orders/create`
- `orders.update` → Button: "تعديل" → Route: `/admin/orders/{id}/edit`
- `orders.delete` → Button: "حذف" → Route: `/admin/orders/{id}` (DELETE)
- `orders.confirm` → Button: "تأكيد" → Route: `/admin/orders/{id}/confirm`
- `orders.update_status` → Dropdown: "تحديث الحالة" → Route: `/admin/orders/{id}/status`

#### RFQ Management (7 permissions)
- `rfqs.view` → Sidebar: "طلبات عروض الأسعار" → Route: `/admin/rfqs`
- `rfqs.create` → Button: "إنشاء RFQ" → Route: `/admin/rfqs/create`
- `rfqs.update` → Button: "تعديل" → Route: `/admin/rfqs/{id}/edit`
- `rfqs.delete` → Button: "حذف" → Route: `/admin/rfqs/{id}` (DELETE)
- `rfqs.publish` → Button: "نشر" → Route: `/admin/rfqs/{id}/publish`
- `rfqs.assign_suppliers` → Button: "تعيين موردين" → Route: `/admin/rfqs/{id}/assign`
- `rfqs.update_status` → Dropdown: "تحديث الحالة" → Route: `/admin/rfqs/{id}/status`

#### Quotation Management (7 permissions)
- `quotations.view` → Sidebar: "عروض الأسعار" → Route: `/admin/quotations`
- `quotations.submit` → Button: "تقديم عرض" → Route: `/admin/quotations/create`
- `quotations.update` → Button: "تعديل" → Route: `/admin/quotations/{id}/edit`
- `quotations.delete` → Button: "حذف" → Route: `/admin/quotations/{id}` (DELETE)
- `quotations.accept` → Button: "قبول" → Route: `/admin/quotations/{id}/accept`
- `quotations.reject` → Button: "رفض" → Route: `/admin/quotations/{id}/reject`
- `quotations.compare` → Button: "مقارنة" → Route: `/admin/quotations/compare`

#### System & Reports (6 permissions)
- `settings.view` → Sidebar: "الإعدادات" → Route: `/admin/settings`
- `settings.update` → Button: "حفظ" → Route: `/admin/settings` (PUT)
- `reports.view` → Sidebar: "التقارير" → Route: `/admin/reports`
- `reports.export` → Button: "تصدير" → Route: `/admin/reports/export`
- `activity_logs.view` → Sidebar: "سجل النشاط" → Route: `/admin/activity`
- `notifications.view` → Sidebar: "الإشعارات" → Route: `/admin/notifications`
- `permissions.view` → Sidebar: "الأدوار و الصلاحيات" → Route: `/admin/role-permissions`

### 3.2 Permission Groups for UI

**Grouped Structure (From AdminPermissionService):**
```php
$permissionGroups = [
    'users' => [
        'label' => 'المستخدمون',
        'permissions' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage_permissions',
        ],
    ],
    'products' => [
        'label' => 'المنتجات',
        'permissions' => [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.approve',
            'products.reject',
            'products.request_changes',
        ],
    ],
    // ... more groups
];
```

---

## 4. Backend Authorization Rules (Spatie Best Practices)

### 4.1 Route-Level Protection

**Current Implementation (routes/web.php):**
```php
Route::middleware(['auth', 'permission:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
});
```

**✅ Already Implemented:** All admin routes use `permission:` middleware

### 4.2 Controller-Level Protection

**Current Implementation (UserController):**
```php
public function index(): View
{
    $this->authorize('viewAny', User::class); // Uses Policy
    // Controller logic...
}
```

**✅ Already Implemented:** Controllers use `authorize()` with policies

### 4.3 Policy-Based Authorization

**Current Implementation (UserPolicy):**
```php
public function viewAny(User $user): bool
{
    return $user->can('users.view');
}
```

**Enhancement Needed:** Add Admin bypass to all policies

**Enhanced Policy Pattern:**
```php
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

### 4.4 Admin Bypass Gate Before Hook

**Recommended Implementation (AuthServiceProvider):**
```php
public function boot(): void
{
    // Admin role bypasses all Gate checks
    Gate::before(function ($user, $ability) {
        if ($user->hasRole('Admin')) {
            return true;
        }
    });
}
```

**Note:** This is a global bypass. Use with caution. Alternative: Add to each policy method.

---

## 5. Frontend Permission Handling

### 5.1 @Can Blade Directive

**Current Implementation (AppServiceProvider):**
```php
Blade::if('can', function ($permission) {
    $user = auth()->user();
    
    if (!$user) {
        return false;
    }
    
    // Admin role bypasses all permission checks
    if ($user->hasRole('Admin') || $user->hasRole('admin')) {
        return true;
    }
    
    // Check explicit permission
    return $user->can($permission);
});
```

**✅ Already Implemented:** @can directive exists

**Usage:**
```blade
@can('users.view')
    <a href="{{ route('admin.users') }}">إدارة المستخدمين</a>
@endcan
```

### 5.2 Sidebar Rendering

**Current Implementation:** Uses `canAccessMenuItem()` helper function

**Enhancement Needed:** Wrap sidebar items with @can directives

**Current Sidebar Structure:**
```php
$menuItems = [
    'admin' => [
        [
            'route' => 'admin.users',
            'label' => 'إدارة المستخدمين',
            'permission' => 'users.view', // ✅ Already defined
        ],
        // ...
    ],
];
```

**Sidebar Helper (Already Fixed):**
```php
function canAccessMenuItem($item, $isAdmin = false) {
    // Only Admin ROLE bypasses, NOT user_type_id
    if ($user->hasRole('Admin')) {
        return true;
    }
    
    // Check explicit permission
    if (isset($item['permission'])) {
        return $user->can($item['permission']);
    }
    
    return false;
}
```

**✅ Already Fixed:** Sidebar helper checks Admin role correctly

### 5.3 View-Level Control

**Current State:** Some views use @can, but not consistently

**Enhancement Needed:** Add @can to all action buttons

**Example Pattern:**
```blade
@can('users.create')
    <a href="{{ route('admin.users.create') }}" class="btn">إضافة مستخدم</a>
@endcan

@can('users.update')
    <a href="{{ route('admin.users.edit', $user) }}" class="btn">تعديل</a>
@endcan

@can('users.delete')
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">حذف</button>
    </form>
@endcan
```

---

## 6. UI/UX Improvements

### 6.1 Access Denied View

**File:** `resources/views/errors/403.blade.php`

**Design Requirements:**
- Clear error message
- Helpful navigation options
- Permission-aware suggestions
- Professional design matching app theme

### 6.2 Staff User Empty State

**When Staff Has No Permissions:**
- Show empty dashboard with message
- Provide contact information
- Option to logout

### 6.3 Permission Indicators

**In User Profile/Edit Page:**
- Show granted permissions count
- List of active permissions
- Link to permission assignment page

---

## 7. Implementation Plan

### Phase 1: Fix Staff Role Default Permissions ✅

**File:** `database/seeders/UnifiedRolePermissionSeeder.php`

**Change:**
```php
'Staff' => [
    'ar_name' => 'موظف',
    'permissions' => [], // ✅ NO default permissions
],
```

### Phase 2: Enhance Policies with Admin Bypass

**Files:** All Policy files in `app/Policies/`

**Pattern:**
```php
public function viewAny(User $user): bool
{
    if ($user->hasRole('Admin')) {
        return true;
    }
    return $user->can('resource.action');
}
```

### Phase 3: Add @Can to All Views

**Files:** All admin views in `resources/views/admin/`

**Actions:**
- Wrap action buttons with @can
- Wrap form fields with @can
- Wrap navigation links with @can

### Phase 4: Create 403 Error View

**File:** `resources/views/errors/403.blade.php`

### Phase 5: Add Permission Assignment Link

**File:** `resources/views/admin/users/index.blade.php`

**Add:** "الصلاحيات" button in actions column

---

## 8. Critical Implementation Rules

### 8.1 Admin Bypass

**✅ CORRECT:**
```php
if ($user->hasRole('Admin')) {
    return true;
}
```

**❌ WRONG:**
```php
if ($user->user_type_id === 1) { // This gives all Staff users admin access!
    return true;
}
```

### 8.2 Staff Permission Assignment

**✅ CORRECT:**
```php
// Direct permissions only
$user->syncPermissions($permissions);
```

**❌ WRONG:**
```php
// Don't assign permissions to Staff role
$staffRole->syncPermissions($permissions); // This affects ALL Staff users!
```

### 8.3 Permission Checks

**✅ CORRECT:**
```php
// Use Spatie's can() method
if ($user->can('users.view')) {
    // Allow
}
```

**✅ CORRECT:**
```blade
@can('users.view')
    <!-- Content -->
@endcan
```

---

## 9. Testing Checklist

- [ ] Staff user with no permissions sees only dashboard
- [ ] Staff user with limited permissions sees only granted items
- [ ] Admin user sees all items
- [ ] Backend returns 403 for unauthorized routes
- [ ] Permission assignment works correctly
- [ ] Admin bypass works in all policies
- [ ] Sidebar shows only accessible items
- [ ] Views hide unauthorized actions

---

**Status:** ✅ Design Complete  
**Next:** Implementation Phase

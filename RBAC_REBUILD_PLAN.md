# 🔐 RBAC System Rebuild Plan
## Complete Ground-Up Rebuild Using Spatie Best Practices

**Date:** January 23, 2026  
**Status:** Implementation-Ready  
**Laravel Version:** 12.35.1  
**Spatie Permission Version:** Latest

---

## 📋 Executive Summary

This document provides a complete, production-ready plan to rebuild the Roles & Permissions system from the ground up, eliminating all conflicts, inconsistencies, and unreliable authorization logic.

### Current Problems Identified

1. **114 instances** of `user_type_id === 1` checks scattered across codebase
2. **Custom `@can` directive** bypassing Spatie's native authorization
3. **All 18 policies** use `user_type_id === 1` instead of Spatie role checks
4. **Sidebar component** uses `user_type_id === 1` instead of permission checks
5. **No Gate::before()** for Admin bypass (Spatie best practice)
6. **Mixed authorization logic** causing conflicts and cache issues

### Solution Overview

- ✅ Use Spatie's `Gate::before()` for Admin bypass (Spatie best practice)
- ✅ Remove ALL `user_type_id === 1` checks
- ✅ Use Spatie's native `@can` directive
- ✅ Update all policies to use Spatie role checks
- ✅ Make sidebar fully permission-driven
- ✅ Ensure Admin role has ALL permissions
- ✅ Staff users have ZERO default permissions

---

## 🎯 System Architecture

### User Types vs Roles

**User Types** (Database classification):
- `user_type_id = 1`: Admin (System Owner)
- `user_type_id = 2`: Supplier (External User)
- `user_type_id = 3`: Buyer (External User)
- `user_type_id = 4`: Staff (Internal Delegated User)

**Roles** (Spatie Authorization):
- `Admin`: Full system access (all permissions)
- `Staff`: Permission-based access (explicit grants only)
- `Supplier`: Supplier-specific permissions
- `Buyer`: Buyer-specific permissions

**Critical Rule:** `user_type_id` is for **classification only**. Authorization is **100% role/permission-based** via Spatie.

---

## 📊 Codebase Analysis Summary

### ✅ What Works (Keep)

1. **Database Structure**
   - Spatie permission tables are correctly migrated
   - `user_types` table exists and is properly seeded
   - Permission naming convention (`resource.action`) is consistent

2. **Seeders**
   - `UnifiedRolePermissionSeeder` creates all permissions correctly
   - `AdminSeeder` assigns Admin role correctly
   - Admin role receives ALL permissions via role (not direct)

3. **Models**
   - `User` model uses `HasRoles` trait correctly
   - `Role` and `Permission` models extend Spatie correctly
   - No custom authorization overrides in User model

4. **Middleware**
   - Spatie middleware aliases registered in `bootstrap/app.php`
   - `EnsureInternalUser` middleware checks roles correctly

5. **Routes**
   - Routes use `permission:` middleware correctly
   - Route structure is clean and organized

### ❌ What's Broken (Fix)

1. **AppServiceProvider**
   - Custom `@can` directive bypasses Spatie
   - No `Gate::before()` for Admin bypass

2. **All Policies (18 files)**
   - Every policy uses `user_type_id === 1` checks
   - Should use `Gate::before()` instead

3. **Sidebar Component**
   - Uses `user_type_id === 1` checks
   - Should use Spatie permission checks

4. **Controllers**
   - Some controllers check `user_type_id` instead of permissions
   - Should rely on policies/middleware

### 🗑️ What Must Be Removed

1. **All `user_type_id === 1` checks** (114 instances)
2. **Custom `@can` directive** in AppServiceProvider
3. **Direct `user_type_id` checks in policies**
4. **Sidebar `user_type_id` logic**

---

## 🏗️ Implementation Plan

### Phase 1: Core Authorization Foundation

#### Step 1.1: Add Gate::before() for Admin Bypass

**File:** `app/Providers/AppServiceProvider.php`

**Action:** Add `Gate::before()` callback to bypass all checks for Admin role.

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    // ... existing code ...

    // Admin bypass using Spatie best practices
    Gate::before(function ($user, $ability) {
        // Admin role bypasses ALL authorization checks
        if ($user->hasRole('Admin')) {
            return true;
        }
    });

    // Remove custom @can directive - use Spatie's native @can
    // DELETE the Blade::if('can', ...) block
}
```

**Why:** This is Spatie's recommended way to handle super-admin bypass. It works with all authorization methods (policies, gates, middleware, `@can`).

#### Step 1.2: Remove Custom @can Directive

**File:** `app/Providers/AppServiceProvider.php`

**Action:** Delete the custom `Blade::if('can', ...)` block. Spatie's native `@can` will work with `Gate::before()`.

**Before:**
```php
Blade::if('can', function ($permission) {
    $user = auth()->user();
    if (!$user) return false;
    if (($user->user_type_id ?? null) === 1) return true; // ❌ Remove
    return $user->can($permission);
});
```

**After:**
```php
// Deleted - use Spatie's native @can directive
```

---

### Phase 2: Policy Refactoring

#### Step 2.1: Update All Policies

**Files:** All 18 policy files in `app/Policies/`

**Pattern to Replace:**
```php
// ❌ OLD (Remove)
if (($user->user_type_id ?? null) === 1) {
    return true;
}
return $user->can('permission.name');
```

**New Pattern:**
```php
// ✅ NEW (Gate::before() handles Admin bypass)
return $user->can('permission.name');
```

**Policies to Update:**
1. `ActivityLogPolicy.php`
2. `BuyerPolicy.php`
3. `DeliveryPolicy.php`
4. `InvoicePolicy.php`
5. `ManufacturerPolicy.php`
6. `NotificationPolicy.php`
7. `OrderPolicy.php`
8. `PaymentPolicy.php`
9. `PermissionPolicy.php`
10. `ProductCategoryPolicy.php`
11. `ProductPolicy.php`
12. `ProductRequestPolicy.php`
13. `QuotationPolicy.php`
14. `RfqPolicy.php`
15. `RolePolicy.php`
16. `SettingPolicy.php`
17. `SupplierPolicy.php`
18. `UserPolicy.php`

**Example - UserPolicy.php:**
```php
public function viewAny(User $user): bool
{
    // Gate::before() handles Admin bypass
    return $user->can('users.view');
}

public function create(User $user): bool
{
    // Gate::before() handles Admin bypass
    return $user->can('users.create');
}

// ... etc
```

---

### Phase 3: Sidebar Component Refactoring

#### Step 3.1: Update Sidebar Permission Checks

**File:** `resources/views/components/dashboard/sidebar.blade.php`

**Action:** Replace all `user_type_id === 1` checks with Spatie permission checks.

**Before:**
```php
$isCurrentUserAdmin = ($user->user_type_id ?? null) === 1;
$userIsAdmin = ($user->user_type_id ?? null) === 1;
if ($isAdmin) {
    return true; // Bypass
}
```

**After:**
```php
// Remove user_type_id checks
// Use Spatie permission checks only
function canAccessMenuItem($item) {
    $user = auth()->user();
    if (!$user) return false;
    
    // Check permission if specified
    if (isset($item['permission'])) {
        return $user->can($item['permission']);
    }
    
    // Check role if specified
    if (isset($item['role'])) {
        return $user->hasRole($item['role']);
    }
    
    // Default: hide if no explicit permission/role
    return false;
}
```

**Why:** Sidebar should be permission-driven. Admin will see everything because `Gate::before()` allows all checks to pass.

---

### Phase 4: Controller Cleanup

#### Step 4.1: Remove user_type_id Checks from Controllers

**Files:** Controllers that check `user_type_id`

**Action:** Replace with policy authorization or remove if redundant.

**Example - UserController.php:**
```php
// ❌ OLD
$stats = [
    'admin_count' => User::where('user_type_id', 1)->count(),
];

// ✅ NEW (if needed for display)
$stats = [
    'admin_count' => User::role('Admin')->count(),
];
```

**Note:** Most controller logic should rely on policies. Controllers should use `$this->authorize()` or let middleware handle it.

---

### Phase 5: Seeder Verification

#### Step 5.1: Verify Admin Role Has All Permissions

**File:** `database/seeders/UnifiedRolePermissionSeeder.php`

**Status:** ✅ Already correct - Admin role gets all permissions.

**Verification:**
```php
$roles = [
    'Admin' => [
        'ar_name' => 'مدير النظام',
        'permissions' => array_keys($permissions), // ✅ All permissions
    ],
    // ...
];
```

#### Step 5.2: Verify Admin User Gets Admin Role

**File:** `database/seeders/AdminSeeder.php`

**Status:** ✅ Already correct - Admin user gets Admin role.

**Verification:**
```php
$admin->syncRoles(['Admin']); // ✅ Correct
$adminRole->syncPermissions($allPermissions); // ✅ Correct
```

---

### Phase 6: View Updates

#### Step 6.1: Update Blade Views

**Files:** Any Blade views using `user_type_id === 1`

**Action:** Replace with `@can` or `@role` directives.

**Before:**
```blade
@if(($user->user_type_id ?? null) === 1)
    <!-- Admin content -->
@endif
```

**After:**
```blade
@can('permission.name')
    <!-- Content -->
@endcan

@role('Admin')
    <!-- Admin-specific content -->
@endrole
```

**Note:** `Gate::before()` ensures Admin passes all `@can` checks.

---

## 🔒 Permission Architecture

### Permission Naming Convention

**Format:** `{resource}.{action}`

**Examples:**
- `users.view`
- `users.create`
- `users.update`
- `users.delete`
- `products.view`
- `products.approve`

### Permission Structure

**Admin Permissions (Full Universe):**
- All permissions defined in `UnifiedRolePermissionSeeder`
- Admin role has ALL permissions
- Admin bypasses checks via `Gate::before()`

**Staff Permissions:**
- ZERO default permissions
- Admin assigns explicit permissions via UI
- Staff inherits permissions from direct assignment (not role)

**Supplier/Buyer Permissions:**
- Defined in seeder
- Assigned via roles
- Separate from Admin/Staff system

---

## 🧪 Testing Checklist

### Pre-Implementation

- [ ] Backup database
- [ ] Document current permission assignments
- [ ] Note any custom authorization logic

### Post-Implementation

#### Admin Tests
- [ ] Admin can access all routes
- [ ] Admin sees all sidebar items
- [ ] Admin passes all `@can` checks
- [ ] Admin bypasses all policy checks
- [ ] Admin can assign permissions to Staff

#### Staff Tests
- [ ] Staff with no permissions sees empty sidebar
- [ ] Staff with `users.view` sees Users section only
- [ ] Staff cannot access routes without permissions
- [ ] Staff gets 403 on unauthorized routes
- [ ] Staff permissions work after Admin assignment

#### Permission Tests
- [ ] `Gate::before()` works correctly
- [ ] Policies work without `user_type_id` checks
- [ ] Sidebar shows/hides based on permissions
- [ ] `@can` directive works in views
- [ ] Middleware `permission:` works correctly

#### Integration Tests
- [ ] Supplier/Buyer routes still work
- [ ] No broken authorization
- [ ] Cache clears correctly
- [ ] Seeder runs without errors

---

## 🚀 Migration Strategy

### Step-by-Step Execution

1. **Phase 1: Core Foundation** (30 min)
   - Add `Gate::before()` to AppServiceProvider
   - Remove custom `@can` directive
   - Test Admin bypass

2. **Phase 2: Policy Updates** (2 hours)
   - Update all 18 policies
   - Remove `user_type_id === 1` checks
   - Test each policy

3. **Phase 3: Sidebar Refactor** (1 hour)
   - Update sidebar component
   - Test permission-driven menu

4. **Phase 4: Controller Cleanup** (1 hour)
   - Remove `user_type_id` checks
   - Test controllers

5. **Phase 5: View Updates** (1 hour)
   - Update Blade views
   - Test UI

6. **Phase 6: Final Testing** (2 hours)
   - Run full test suite
   - Manual testing
   - Fix any issues

**Total Estimated Time:** 7-8 hours

---

## 📝 Code Examples

### Example 1: Policy Before/After

**Before:**
```php
public function viewAny(User $user): bool
{
    if (($user->user_type_id ?? null) === 1) {
        return true;
    }
    return $user->can('users.view');
}
```

**After:**
```php
public function viewAny(User $user): bool
{
    // Gate::before() handles Admin bypass
    return $user->can('users.view');
}
```

### Example 2: Sidebar Before/After

**Before:**
```php
$isCurrentUserAdmin = ($user->user_type_id ?? null) === 1;
if ($isAdmin) {
    return true; // Bypass
}
return $user->can($item['permission']);
```

**After:**
```php
// Gate::before() ensures Admin passes all checks
return $user->can($item['permission']);
```

### Example 3: Blade View Before/After

**Before:**
```blade
@if(($user->user_type_id ?? null) === 1)
    <button>Admin Action</button>
@endif
```

**After:**
```blade
@can('admin.action')
    <button>Admin Action</button>
@endcan
```

---

## ⚠️ Critical Rules

1. **NEVER use `user_type_id === 1` for authorization**
   - `user_type_id` is classification only
   - Authorization is 100% role/permission-based

2. **ALWAYS use Spatie methods**
   - `$user->can('permission')`
   - `$user->hasRole('Admin')`
   - `@can('permission')` in Blade
   - `@role('Admin')` in Blade

3. **Admin bypass via Gate::before()**
   - Single point of Admin bypass
   - Works with all authorization methods
   - Spatie best practice

4. **Staff has ZERO default permissions**
   - Admin must explicitly grant permissions
   - No inheritance, no defaults

5. **Permission naming is strict**
   - Format: `resource.action`
   - Must match exactly in routes, policies, views

---

## 🔍 Validation Checklist

### Code Quality
- [ ] No `user_type_id === 1` checks remain
- [ ] All policies use `$user->can()`
- [ ] Sidebar uses permission checks only
- [ ] `Gate::before()` is implemented
- [ ] Custom `@can` directive removed

### Functionality
- [ ] Admin bypasses all checks
- [ ] Staff permissions work correctly
- [ ] Supplier/Buyer routes unaffected
- [ ] All routes protected correctly
- [ ] UI shows/hides based on permissions

### Performance
- [ ] Permission cache works correctly
- [ ] No N+1 queries in authorization
- [ ] Cache clears on permission changes

### Security
- [ ] No authorization bypasses
- [ ] Staff cannot access unauthorized routes
- [ ] Admin cannot be restricted
- [ ] Permission checks are consistent

---

## 📚 References

### Spatie Documentation
- [Spatie Permission Package](https://spatie.be/docs/laravel-permission)
- [Gate Before Callbacks](https://laravel.com/docs/authorization#intercepting-gate-checks)
- [Blade Directives](https://spatie.be/docs/laravel-permission/v6/basic-usage/blade-directives)

### Laravel Documentation
- [Authorization](https://laravel.com/docs/authorization)
- [Policies](https://laravel.com/docs/authorization#creating-policies)
- [Gates](https://laravel.com/docs/authorization#gates)

---

## ✅ Final Validation

After implementation, verify:

1. **Admin Access:**
   ```bash
   # Login as Admin
   # Verify: Can access all routes, see all sidebar items
   ```

2. **Staff Access:**
   ```bash
   # Create Staff user with only 'users.view'
   # Verify: Sees only Users section, gets 403 on other routes
   ```

3. **Code Quality:**
   ```bash
   # Search for user_type_id === 1
   grep -r "user_type_id.*===.*1" app/
   # Should return ZERO results
   ```

4. **Permission Cache:**
   ```bash
   php artisan permission:cache-reset
   php artisan cache:clear
   # Verify: Permissions still work
   ```

---

## 🎯 Success Criteria

✅ **Zero `user_type_id === 1` checks in authorization code**  
✅ **Admin bypasses all checks via `Gate::before()`**  
✅ **Staff permissions work correctly**  
✅ **All policies use Spatie methods**  
✅ **Sidebar is permission-driven**  
✅ **No breaking changes**  
✅ **No conflicts or cache issues**  
✅ **Production-ready and maintainable**

---

**End of Document**

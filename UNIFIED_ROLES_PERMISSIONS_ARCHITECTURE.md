# 🏗️ Unified Roles & Permissions Architecture

**Date:** 2025-01-XX  
**Status:** Design Document

---

## 📋 Current Problems Identified

### 1. **Separation Causes Duplication** ❌
- **RoleController** and **PermissionController** are separate
- Separate views: `admin/roles/` and `admin/permissions/`
- Separate routes: `admin.roles.*` and `admin.permissions.*`
- Duplicated logic for permission management

### 2. **Role-Permission Inheritance Conflict** ❌
- **Problem:** Spatie automatically grants role permissions to users
- **Current Behavior:** When user gets a role, they inherit all role permissions
- **Required Behavior:** Roles should be categories only, permissions assigned manually
- **Location:** `UnifiedRolePermissionSeeder` assigns permissions to roles (Admin, Supplier, Buyer)

### 3. **Inconsistent Permission Assignment** ❌
- User creation assigns role (which gives permissions automatically)
- Separate permission assignment in `updatePermissions` method
- No clear separation between role assignment and permission assignment

### 4. **UI/UX Confusion** ❌
- Two separate menu items: "الأدوار" and "الصلاحيات"
- Users don't understand the relationship
- No unified workflow for managing user permissions

---

## 🎯 Unified Architecture Design

### Core Principle: **Roles = Categories, Permissions = Actions**

**Roles:**
- Define user category/type (Admin, Staff, Procurement Officer, etc.)
- **DO NOT** automatically grant permissions
- Used for organization and reporting only

**Permissions:**
- Define what actions users can perform
- Assigned **manually** and **explicitly** by admin
- No automatic inheritance from roles

---

## 🔄 Required User Flow

### Flow 1: User Creation (إدارة المستخدمين)
```
1. Admin → إدارة المستخدمين → إضافة مستخدم جديد
2. Fill basic info (name, email, password, etc.)
3. Select "الدور" from dropdown (required field)
4. Save user
5. User has role but NO permissions yet
```

### Flow 2: Permission Assignment (الأدوار و الصلاحيات)
```
1. Admin → الأدوار و الصلاحيات
2. Select user from dropdown
3. System displays permissions table (grouped by module)
4. Admin manually selects permissions
5. Save → User gets ONLY selected permissions
```

---

## 🛠️ Technical Implementation Strategy

### Strategy: Direct Permissions Only

**Key Decision:** Use Spatie's direct permission assignment and ignore role permissions in authorization checks.

**Implementation:**
1. **Keep role permissions in database** (for reference/documentation)
2. **In Policies:** Check `$user->getDirectPermissions()` instead of `$user->getAllPermissions()`
3. **In UserController:** When assigning role, immediately revoke role permissions
4. **In Unified Controller:** Only assign direct permissions

### Alternative Strategy: Remove Role Permissions

**Key Decision:** Remove all permissions from roles in Seeder, keep roles empty.

**Implementation:**
1. Modify `UnifiedRolePermissionSeeder` to NOT assign permissions to roles
2. Roles become pure categories
3. All permissions assigned directly to users

**Recommendation:** Use **Strategy 1** (Direct Permissions Only) because:
- Maintains backward compatibility
- Allows role permissions for documentation
- More flexible for future needs
- Easier to audit

---

## 📁 File Structure (Unified)

### New Controller: `RolePermissionController`
**Location:** `app/Http/Controllers/Web/RolePermissionController.php`

**Methods:**
- `index()` - Main page: User selector + Permissions table
- `assignPermissions(Request $request, User $user)` - Assign permissions to user
- `getUserPermissions(User $user)` - Get user's current permissions (AJAX)

### New View: `admin/role-permissions/index.blade.php`
**Features:**
- User dropdown selector
- Permissions table (grouped by module, Arabic labels)
- Checkboxes for each permission
- Current user's permissions pre-selected
- Save button

### Merged Routes:
```php
Route::get('/role-permissions', [RolePermissionController::class, 'index'])
    ->name('role-permissions.index');
Route::post('/role-permissions/{user}/assign', [RolePermissionController::class, 'assignPermissions'])
    ->name('role-permissions.assign');
```

### Sidebar Update:
- Remove: "الأدوار" and "الصلاحيات" (separate items)
- Add: "الأدوار و الصلاحيات" (single unified item)

---

## 🔐 Permission Assignment Logic

### Spatie Behavior Understanding

**Spatie's Default Behavior:**
```php
$user->assignRole('Admin');
// User now has ALL permissions from Admin role

$user->givePermissionTo('users.view');
// User has: Role permissions + Direct permission
```

**Our Required Behavior:**
```php
$user->assignRole('Admin');
// User has role but NO permissions yet

$user->syncPermissions(['users.view', 'orders.view']);
// User has ONLY these 2 permissions (no role permissions)
```

### Solution: Custom Permission Check

**Option 1: Override `can()` method in User model**
```php
public function can($permission, $guardName = null)
{
    // Only check direct permissions, ignore role permissions
    return $this->hasDirectPermission($permission, $guardName);
}
```

**Option 2: Custom Policy Base Class**
```php
abstract class BasePolicy
{
    protected function checkDirectPermission(User $user, string $permission): bool
    {
        return $user->hasDirectPermission($permission);
    }
}
```

**Option 3: Service Layer**
```php
class PermissionService
{
    public function userCan(User $user, string $permission): bool
    {
        // Only check direct permissions
        return $user->hasDirectPermission($permission);
    }
}
```

**Recommendation:** Use **Option 1** (Override `can()` method) because:
- Simplest implementation
- Works with existing `@can()` directives
- No need to change all Policies
- Transparent to existing code

---

## 📊 Database Design

### Current Structure (Spatie Standard)
```
roles
  - id, name, ar_name, guard_name

permissions
  - id, name, ar_name, guard_name

role_has_permissions (pivot)
  - role_id, permission_id

model_has_roles (pivot)
  - role_id, model_id, model_type

model_has_permissions (pivot)
  - permission_id, model_id, model_type
```

### Our Usage
- **role_has_permissions:** Keep for documentation, but ignore in authorization
- **model_has_roles:** Use for user categorization
- **model_has_permissions:** Use for actual authorization (direct permissions only)

---

## 🎨 UI/UX Design

### Unified Page Layout

```
┌─────────────────────────────────────────┐
│  الأدوار و الصلاحيات                    │
├─────────────────────────────────────────┤
│                                         │
│  [Select User Dropdown ▼]               │
│  ┌─────────────────────────────────┐   │
│  │ اختر مستخدم...                   │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ Permissions Table                │   │
│  │                                  │   │
│  │ [Module 1]                       │   │
│  │  ☑ Permission 1                 │   │
│  │  ☐ Permission 2                   │   │
│  │                                  │   │
│  │ [Module 2]                       │   │
│  │  ☑ Permission 3                 │   │
│  │  ☐ Permission 4                   │   │
│  │                                  │   │
│  │ [Select All] [Deselect All]      │   │
│  └─────────────────────────────────┘   │
│                                         │
│  [Save Permissions]                    │
│                                         │
└─────────────────────────────────────────┘
```

---

## ✅ Implementation Checklist

### Phase 1: Core Logic
- [ ] Override `can()` method in User model to check direct permissions only
- [ ] Create `RolePermissionController` with unified logic
- [ ] Create unified view `admin/role-permissions/index.blade.php`
- [ ] Update routes to use unified controller

### Phase 2: User Creation Flow
- [ ] Ensure `UserController@store` only assigns role (no permissions)
- [ ] Remove permission assignment from user creation
- [ ] Update `create.blade.php` to show role dropdown only

### Phase 3: Unified UI
- [ ] Update sidebar to show "الأدوار و الصلاحيات" (single item)
- [ ] Remove separate "الأدوار" and "الصلاحيات" menu items
- [ ] Create user selector dropdown in unified view
- [ ] Create permissions table with Arabic labels

### Phase 4: Cleanup
- [ ] Deprecate `RoleController` (keep for backward compatibility or remove)
- [ ] Deprecate `PermissionController` (keep for backward compatibility or remove)
- [ ] Remove old views or redirect to unified view
- [ ] Update documentation

---

## 🧪 Testing Strategy

### Test Scenarios

1. **User Creation:**
   - Create user with role "Staff"
   - Verify user has role but NO permissions
   - Verify `$user->can('users.view')` returns false

2. **Permission Assignment:**
   - Select user in unified page
   - Assign permissions manually
   - Verify user has ONLY assigned permissions
   - Verify role permissions are NOT inherited

3. **Authorization:**
   - User with role "Admin" but no direct permissions
   - Try to access admin pages
   - Should get 403 (no permissions)

4. **Permission Revocation:**
   - Remove permission from user
   - Verify user loses access immediately

---

## 📝 Code Structure Preview

### User Model Override
```php
public function can($permission, $guardName = null)
{
    // Only check direct permissions, ignore role permissions
    return $this->hasDirectPermission($permission, $guardName ?? $this->getDefaultGuardName());
}
```

### RolePermissionController
```php
class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('user_type_id', 1)->get(); // Internal users only
        $selectedUser = $request->user_id ? User::find($request->user_id) : null;
        $permissions = Permission::orderBy('name')->get()->groupBy(...);
        $userPermissions = $selectedUser ? $selectedUser->permissions->pluck('id')->toArray() : [];
        
        return view('admin.role-permissions.index', compact(...));
    }
    
    public function assignPermissions(Request $request, User $user)
    {
        // Only assign direct permissions
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $user->syncPermissions($permissions);
        
        // Revoke any role permissions (ensure clean state)
        $this->revokeRolePermissions($user);
    }
}
```

---

**Next Step:** Implement the unified architecture


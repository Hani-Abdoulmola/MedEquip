# Authorization Architecture Implementation Guide

## 📋 Status: IN PROGRESS

This document tracks the implementation of a complete permission-based authorization system using Spatie Laravel Permission.

---

## ✅ Completed

### 1. Permission Seeder
- ✅ Created `database/seeders/PermissionSeeder.php`
- ✅ Defines all atomic permissions (70+ permissions)
- ✅ Creates Admin, Supplier, Buyer, and Staff roles
- ✅ Assigns all permissions to Admin role

### 2. Policies Updated (Permission-Based)
- ✅ `UserPolicy` - Uses `users.*` permissions
- ✅ `SupplierPolicy` - Uses `suppliers.*` permissions
- ✅ `BuyerPolicy` - Uses `buyers.*` permissions
- ✅ `RfqPolicy` - Uses `rfqs.*` permissions
- ✅ `QuotationPolicy` - Uses `quotations.*` permissions
- ✅ `OrderPolicy` - Uses `orders.*` permissions
- ✅ `ProductPolicy` - Uses `products.*` permissions
- ✅ `InvoicePolicy` - Uses `invoices.*` permissions

**Key Changes:**
- Replaced `$user->hasRole('Admin')` with `$user->can('permission.name')`
- Maintained business logic for ownership checks (Buyer/Supplier)
- Admin/Staff access controlled via permissions

---

## 🚧 In Progress

### 3. Controllers to Create/Update

#### RoleController (NEW)
- `index()` - List all roles
- `create()` - Show create form
- `store()` - Create new role
- `edit()` - Show edit form
- `update()` - Update role
- `destroy()` - Delete role
- `show()` - Show role details with permissions

#### PermissionController (NEW)
- `index()` - List all permissions (grouped by module)
- `show()` - Show permission details

#### UserController (UPDATE)
- Add `updatePermissions()` method
- Update `edit()` to show permission matrix
- Update `store()` and `update()` to handle permissions

---

## 📝 To Do

### 4. Admin Sidebar Updates
- Add "User Management" section with:
  - Users
  - Roles
  - Permissions
- Wrap all items with `@can` directives

### 5. Views to Create

#### Roles Management
- `admin/roles/index.blade.php` - List roles
- `admin/roles/create.blade.php` - Create role
- `admin/roles/edit.blade.php` - Edit role
- `admin/roles/show.blade.php` - Role details

#### Permissions Management
- `admin/permissions/index.blade.php` - List permissions (grouped)
- `admin/permissions/show.blade.php` - Permission details

#### User Permission Assignment
- Update `admin/users/edit.blade.php` - Add permission matrix
- Update `admin/users/create.blade.php` - Add permission selection

### 6. Routes to Add

```php
// Roles Management
Route::resource('roles', RoleController::class);
Route::get('/roles/{role}/permissions', [RoleController::class, 'show'])->name('roles.show');

// Permissions Management
Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');

// User Permissions
Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.update-permissions');
```

### 7. Feature Tests

- Test Staff without permission → 403
- Test Staff with permission → Success
- Test Admin → Unrestricted access
- Test permission assignment
- Test role creation with permissions

---

## 🔑 Permission Matrix

### Users Module
- `users.view`
- `users.create`
- `users.update`
- `users.delete`
- `users.manage_permissions`

### Suppliers Module
- `suppliers.view`
- `suppliers.create`
- `suppliers.update`
- `suppliers.delete`
- `suppliers.verify`
- `suppliers.toggle_active`

### Buyers Module
- `buyers.view`
- `buyers.create`
- `buyers.update`
- `buyers.delete`
- `buyers.verify`
- `buyers.toggle_active`

### RFQs Module
- `rfqs.view`
- `rfqs.create`
- `rfqs.update`
- `rfqs.delete`
- `rfqs.publish`
- `rfqs.assign_suppliers`
- `rfqs.update_status`
- `rfqs.toggle_visibility`

### Quotations Module
- `quotations.view`
- `quotations.submit`
- `quotations.update`
- `quotations.delete`
- `quotations.accept`
- `quotations.reject`
- `quotations.compare`

### Orders Module
- `orders.view`
- `orders.create`
- `orders.update`
- `orders.delete`
- `orders.confirm`
- `orders.update_status`

### Invoices Module
- `invoices.view`
- `invoices.create`
- `invoices.update`
- `invoices.delete`
- `invoices.approve`
- `invoices.download`
- `invoices.export`

### Products Module
- `products.view`
- `products.create`
- `products.update`
- `products.delete`
- `products.approve`
- `products.reject`
- `products.request_changes`

### And more... (See PermissionSeeder.php for complete list)

---

## 🎯 Implementation Strategy

1. **Phase 1: Foundation** ✅
   - Permission seeder
   - Policy updates

2. **Phase 2: Management UI** 🚧
   - RoleController
   - PermissionController
   - User permission assignment

3. **Phase 3: Sidebar & Views**
   - Admin sidebar updates
   - All management views

4. **Phase 4: Testing**
   - Feature tests
   - Integration tests

---

## 📚 Key Principles

1. **Policies are single source of truth** - All authorization logic in policies
2. **Permissions for action-level control** - Granular permissions per action
3. **Roles are optional templates** - Can assign permissions directly
4. **No logic in controllers** - Controllers only call `$this->authorize()`
5. **No logic in views** - Views use `@can` for UI, backend enforces

---

## 🔒 Security Notes

- Admin role has all permissions (assigned in seeder)
- Staff roles get permissions assigned individually
- Supplier/Buyer roles maintain business logic (ownership checks)
- Permission checks happen in policies, not controllers
- Views hide unauthorized actions, but backend always validates

---

**Next Steps:** Continue with RoleController and PermissionController implementation.


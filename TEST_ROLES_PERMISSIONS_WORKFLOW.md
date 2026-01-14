# 🧪 Test Plan: Roles & Permissions Workflow

**Date:** 2025-01-XX  
**Status:** Ready for Testing

---

## ✅ Pre-Test Checklist

- [x] UnifiedRolePermissionSeeder executed successfully
- [x] Permission cache cleared
- [x] All Policies updated to use `can()` instead of `hasRole('Admin')`
- [x] Controllers updated to use Policies
- [x] BaseController created with helper methods

---

## 📋 Test Scenarios

### Scenario 1: Verify Seeder Execution ✅

**Status:** ✅ PASSED

**Test Steps:**
1. Run `php artisan db:seed --class=UnifiedRolePermissionSeeder`
2. Verify output shows:
   - ✅ Created 87 permissions
   - ✅ Created/Updated 4 roles
   - ✅ Assigned permissions to roles

**Expected Result:**
- All permissions created with Arabic names
- Admin role has all permissions
- Supplier role has 12 permissions
- Buyer role has 17 permissions
- Staff role has 0 permissions (assigned individually)

**Actual Result:**
```
✅ Created 87 permissions
✅ Created/Updated 4 roles
✅ Assigned permissions to roles
```

---

### Scenario 2: Verify Permissions Have Arabic Names

**Test Steps:**
1. Login as Admin
2. Navigate to `/admin/permissions`
3. Check that all permissions display in Arabic

**Expected Result:**
- Permission names show Arabic text (e.g., "عرض المستخدمين" instead of "users.view")
- Module labels are in Arabic (e.g., "المستخدمون" instead of "users")

**Manual Test Required:** Navigate to permissions page in browser

---

### Scenario 3: Create Staff User with Limited Permissions

**Test Steps:**
1. Login as Admin
2. Navigate to `/admin/users/create`
3. Create new user:
   - Name: "Test Staff"
   - Email: "staff@test.com"
   - User Type: "مدير النظام" (System Admin)
   - Role: "Staff" (or create custom role)
4. Navigate to user edit page
5. In "إدارة الصلاحيات" section, select only:
   - `users.view`
   - `products.view`
   - `orders.view`
6. Save permissions

**Expected Result:**
- User created successfully
- User has only the 3 selected permissions
- User can view users, products, and orders
- User cannot view invoices, payments, or other restricted areas

**Manual Test Required:** Create user via admin panel

---

### Scenario 4: Verify Staff Cannot Access Supplier/Buyer Management

**Test Steps:**
1. Login as Staff user created in Scenario 3
2. Try to access:
   - `/admin/suppliers` - Should return 403
   - `/admin/buyers` - Should return 403
   - `/admin/users` - Should work (has `users.view`)
   - `/admin/products` - Should work (has `products.view`)

**Expected Result:**
- Staff user gets 403 Forbidden for supplier/buyer pages
- Staff user can access pages they have permissions for

**Manual Test Required:** Login as Staff and test routes

---

### Scenario 5: Verify Admin Can Manage Roles

**Test Steps:**
1. Login as Admin
2. Navigate to `/admin/roles`
3. Click "Create New Role"
4. Create role:
   - Name: "TestRole"
   - Arabic Name: "دور تجريبي"
   - Select some permissions (only admin permissions should be available)
5. Save role
6. Try to select supplier/buyer permissions - should be blocked

**Expected Result:**
- Role created successfully
- Only admin/system permissions are available in dropdown
- Supplier/Buyer permissions are excluded
- Validation error if trying to assign supplier/buyer permissions

**Manual Test Required:** Test role creation via admin panel

---

### Scenario 6: Verify Permission Assignment UI (Arabic)

**Test Steps:**
1. Login as Admin
2. Navigate to `/admin/users/{id}/edit` (any user)
3. Scroll to "إدارة الصلاحيات" section
4. Verify:
   - Module labels are in Arabic (e.g., "المستخدمون", "المنتجات")
   - Permission names are in Arabic (e.g., "عرض المستخدمين")
   - "تحديد الكل" and "إلغاء التحديد" buttons work
5. Select some permissions and save

**Expected Result:**
- All UI text is in Arabic
- Permissions are grouped by module
- Select All / Deselect All works correctly
- Permissions save successfully

**Manual Test Required:** Test permission assignment UI

---

### Scenario 7: Verify Policies Work Correctly

**Test Steps:**
1. Login as Staff user (with limited permissions)
2. Test Policy checks:
   - Try to view invoices: Should fail (no `invoices.view` permission)
   - Try to view orders: Should work (has `orders.view` permission)
   - Try to create products: Should fail (no `products.create` permission)
   - Try to view products: Should work (has `products.view` permission)

**Expected Result:**
- Policies correctly enforce permissions
- 403 errors for unauthorized actions
- Successful access for authorized actions

**Manual Test Required:** Test various routes with Staff user

---

### Scenario 8: Verify Role-Based View Selection

**Test Steps:**
1. Login as Admin
2. Navigate to `/admin/invoices`
3. Verify admin view is displayed (should show admin.invoices.index template)
4. Logout and login as Supplier
5. Navigate to `/invoices` (if accessible)
6. Verify supplier view is displayed (should show invoices.index template)

**Expected Result:**
- Admin sees admin-specific views
- Supplier/Buyer see regular views
- Views are correctly selected based on permissions

**Manual Test Required:** Test view selection for different roles

---

### Scenario 9: Verify Admin Permission Service

**Test Steps:**
1. Login as Admin
2. Navigate to role creation/edit
3. Verify that `AdminPermissionService` correctly filters out supplier/buyer permissions
4. Check that only admin/system permissions are listed

**Expected Result:**
- `AdminPermissionService::getAdminPermissions()` returns only admin permissions
- Supplier/Buyer permissions are excluded
- Service works correctly in RoleController

**Code Verification:** Already implemented in RoleController

---

### Scenario 10: Verify Direct Permission Assignment Overrides Role

**Test Steps:**
1. Create Staff user with Staff role (0 permissions by default)
2. Assign Staff role to user
3. Directly assign `users.view` permission to user
4. Login as Staff user
5. Verify user can view users page

**Expected Result:**
- User can access pages based on direct permissions
- Direct permissions override role permissions (if role has none)
- Spatie correctly merges role + direct permissions

**Manual Test Required:** Test permission assignment

---

## 🔍 Automated Test Commands

### Check Permissions in Database
```bash
php artisan tinker
```

Then run:
```php
// Count permissions
\App\Models\Permission::count(); // Should be 87

// Check Arabic names
\App\Models\Permission::whereNotNull('ar_name')->count(); // Should be 87

// Check Admin role permissions
\App\Models\Role::where('name', 'Admin')->first()->permissions->count(); // Should be 87

// Check Supplier role permissions
\App\Models\Role::where('name', 'Supplier')->first()->permissions->count(); // Should be 12

// Check Buyer role permissions
\App\Models\Role::where('name', 'Buyer')->first()->permissions->count(); // Should be 17

// Check Staff role permissions
\App\Models\Role::where('name', 'Staff')->first()->permissions->count(); // Should be 0
```

### Test Permission Check
```php
$user = \App\Models\User::find(1); // Admin user
$user->can('users.view'); // Should return true
$user->can('invoices.view'); // Should return true

$staff = \App\Models\User::where('email', 'staff@test.com')->first();
$staff->can('users.view'); // Should return true if permission assigned
$staff->can('invoices.view'); // Should return false
```

---

## 📊 Test Results Summary

| Scenario | Status | Notes |
|----------|--------|-------|
| 1. Seeder Execution | ✅ PASSED | Seeder ran successfully |
| 2. Arabic Names | ⏳ PENDING | Manual test required |
| 3. Create Staff User | ⏳ PENDING | Manual test required |
| 4. Staff Access Control | ⏳ PENDING | Manual test required |
| 5. Role Management | ⏳ PENDING | Manual test required |
| 6. Permission UI (Arabic) | ⏳ PENDING | Manual test required |
| 7. Policies | ⏳ PENDING | Manual test required |
| 8. View Selection | ⏳ PENDING | Manual test required |
| 9. Admin Permission Service | ✅ VERIFIED | Code review completed |
| 10. Direct Permission Override | ⏳ PENDING | Manual test required |

---

## 🐛 Known Issues / Notes

1. **View Selection Logic**: Some controllers still use `hasRole('Admin')` for view selection. This is acceptable as it's UI logic, not authorization logic.

2. **NotificationService**: Uses `User::role('Admin')` which is acceptable - it's querying users, not checking permissions.

3. **Old Seeders**: `PermissionSeeder` and `RolePermissionSeeder` are deprecated but still exist. They should not be used.

---

## ✅ Next Steps After Testing

1. **Fix Any Issues Found**: Address any bugs or inconsistencies discovered during testing
2. **Update Documentation**: Update guides based on test results
3. **Performance Testing**: Test with large number of permissions/users
4. **Security Audit**: Verify no permission bypasses exist

---

**Test Execution Date:** _____________  
**Tested By:** _____________  
**Overall Status:** ⏳ IN PROGRESS


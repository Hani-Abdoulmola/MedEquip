# 🔐 RBAC System Testing Guide

**Date:** January 23, 2026  
**Status:** Post-Implementation Verification

---

## 📋 Testing Overview

This guide provides step-by-step instructions to verify the RBAC rebuild implementation is working correctly.

---

## ✅ Test 1: Admin Access Verification

### Objective
Verify Admin can access all routes and see all sidebar items.

### Steps

1. **Login as Admin**
   ```bash
   Email: admin@MedEquip.com
   Password: [Your admin password]
   ```

2. **Verify Sidebar Items**
   - Navigate to `/dashboard`
   - Check that ALL menu items are visible:
     - ✅ إدارة النظام (System Management)
     - ✅ المستخدمون & التسجيلات (Users & Registrations)
     - ✅ المنتجات (Products)
     - ✅ العروض والطلبات (RFQs & Orders)
     - ✅ الفواتير والمدفوعات (Invoices & Payments)
     - ✅ التقارير (Reports)
     - ✅ الإعدادات (Settings)
     - ✅ سجل النشاط (Activity Log)

3. **Test Critical Routes**
   Visit each route and verify 200 OK (not 403):
   - `/admin/users` ✅
   - `/admin/suppliers` ✅
   - `/admin/buyers` ✅
   - `/admin/products` ✅
   - `/admin/orders` ✅
   - `/admin/rfqs` ✅
   - `/admin/quotations` ✅
   - `/admin/role-permissions` ✅
   - `/admin/settings` ✅
   - `/admin/activity` ✅

4. **Verify Permission Checks**
   - All `@can` directives should pass
   - All buttons/actions should be visible
   - No "Access Denied" messages

### Expected Result
✅ Admin sees ALL sidebar items and can access ALL routes without restrictions.

---

## ✅ Test 2: Staff Access with Limited Permissions

### Objective
Create a Staff user with only `users.view` permission and verify they see only the Users section.

### Steps

1. **Create Staff User** (via Admin)
   - Navigate to `/admin/users/create`
   - Fill in:
     - Name: Test Staff
     - Email: staff-test@medequip.com
     - Password: password123
     - User Type: Staff (user_type_id = 4)
     - Role: Staff
   - Save user

2. **Assign Limited Permissions**
   - Navigate to `/admin/role-permissions`
   - Select the Staff user
   - Check ONLY: `users.view`
   - Uncheck all other permissions
   - Save

3. **Login as Staff**
   ```bash
   Email: staff-test@medequip.com
   Password: password123
   ```

4. **Verify Sidebar**
   - Navigate to `/dashboard`
   - Check sidebar - should see:
     - ✅ لوحة التحكم (Dashboard)
     - ✅ إدارة المستخدمين (User Management) - ONLY this section
     - ❌ NO other sections visible

5. **Test Route Access**
   - `/admin/users` ✅ Should work (200 OK)
   - `/admin/products` ❌ Should be 403 Forbidden
   - `/admin/orders` ❌ Should be 403 Forbidden
   - `/admin/suppliers` ❌ Should be 403 Forbidden

6. **Verify Permission Checks**
   - Users section: All buttons visible
   - Other sections: Completely hidden (not just disabled)

### Expected Result
✅ Staff user sees ONLY Users section and can access ONLY `/admin/users` route.

---

## ✅ Test 3: Supplier/Buyer Routes Verification

### Objective
Verify Supplier and Buyer routes still work correctly after RBAC rebuild.

### Steps

#### Supplier Routes

1. **Login as Supplier**
   ```bash
   Email: [Supplier email]
   Password: [Supplier password]
   ```

2. **Test Supplier Routes**
   - `/supplier/dashboard` ✅ Should work
   - `/supplier/products` ✅ Should work
   - `/supplier/orders` ✅ Should work
   - `/supplier/rfqs` ✅ Should work
   - `/supplier/quotations` ✅ Should work

3. **Verify Supplier Cannot Access Admin Routes**
   - `/admin/users` ❌ Should redirect or 403
   - `/admin/products` ❌ Should redirect or 403

#### Buyer Routes

1. **Login as Buyer**
   ```bash
   Email: [Buyer email]
   Password: [Buyer password]
   ```

2. **Test Buyer Routes**
   - `/buyer/dashboard` ✅ Should work
   - `/buyer/products` ✅ Should work
   - `/buyer/orders` ✅ Should work
   - `/buyer/rfqs` ✅ Should work
   - `/buyer/quotations` ✅ Should work

3. **Verify Buyer Cannot Access Admin Routes**
   - `/admin/users` ❌ Should redirect or 403
   - `/admin/products` ❌ Should redirect or 403

### Expected Result
✅ Supplier and Buyer routes work correctly, and they cannot access admin routes.

---

## ✅ Test 4: Monitor Logs for Authorization Errors

### Objective
Check logs for any authorization errors or RBAC implementation issues.

### Steps

1. **Run Log Check Command**
   ```bash
   php artisan rbac:check-logs --hours=24
   ```

2. **Review Output**
   - Should show: "✅ No authorization errors found in logs"
   - Should show: "✅ No RBAC implementation issues detected"

3. **Manual Log Review** (if needed)
   ```bash
   tail -n 100 storage/logs/laravel.log | grep -i "403\|unauthorized\|permission\|authorization"
   ```

4. **Check for Specific Issues**
   - ❌ No `user_type_id === 1` checks in logs
   - ❌ No Gate::before() failures
   - ❌ No unexpected 403 errors for Admin
   - ✅ Expected 403 errors for Staff without permissions (OK)

### Expected Result
✅ No unexpected authorization errors in logs.

---

## 🧪 Automated Testing

### Run Feature Tests

```bash
php artisan test --filter RbacVerificationTest
```

### Run RBAC Test Command

```bash
php artisan rbac:test
```

Expected output:
```
✅ Admin can access all tested permissions
✅ Staff permissions working correctly
✅ Supplier/Buyer roles verified
✅ Gate::before() is working correctly
```

---

## 📊 Test Results Checklist

- [ ] **Test 1: Admin Access**
  - [ ] Admin sees all sidebar items
  - [ ] Admin can access all routes
  - [ ] No 403 errors for Admin

- [ ] **Test 2: Staff Access**
  - [ ] Staff user created successfully
  - [ ] Staff has only `users.view` permission
  - [ ] Staff sees only Users section in sidebar
  - [ ] Staff can access `/admin/users`
  - [ ] Staff gets 403 on other routes

- [ ] **Test 3: Supplier/Buyer**
  - [ ] Supplier routes work
  - [ ] Buyer routes work
  - [ ] Supplier/Buyer cannot access admin routes

- [ ] **Test 4: Logs**
  - [ ] No unexpected authorization errors
  - [ ] No RBAC implementation issues
  - [ ] No `user_type_id === 1` checks in logs

---

## 🐛 Troubleshooting

### Issue: Admin getting 403 errors

**Solution:**
1. Verify Admin has Admin role:
   ```bash
   php artisan tinker
   >>> $admin = User::where('email', 'admin@MedEquip.com')->first();
   >>> $admin->hasRole('Admin');
   ```
2. Clear permission cache:
   ```bash
   php artisan permission:cache-reset
   php artisan cache:clear
   ```

### Issue: Staff seeing all menu items

**Solution:**
1. Verify Staff user has Staff role (not Admin):
   ```bash
   >>> $staff = User::where('email', 'staff-test@medequip.com')->first();
   >>> $staff->roles->pluck('name');
   ```
2. Verify Staff has only assigned permissions:
   ```bash
   >>> $staff->permissions->pluck('name');
   ```

### Issue: Gate::before() not working

**Solution:**
1. Verify AppServiceProvider has Gate::before():
   ```bash
   grep -A 5 "Gate::before" app/Providers/AppServiceProvider.php
   ```
2. Clear all caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan permission:cache-reset
   ```

---

## ✅ Success Criteria

All tests pass when:
1. ✅ Admin bypasses all checks via Gate::before()
2. ✅ Staff permissions work correctly
3. ✅ Supplier/Buyer routes unaffected
4. ✅ No authorization errors in logs
5. ✅ Sidebar is permission-driven
6. ✅ Zero `user_type_id === 1` checks in code

---

**End of Testing Guide**

# 🔐 Login & Verification Logic - Complete Documentation

## Overview
This document explains how suppliers and buyers can login into the system based on admin verification.

## ✅ Core Principle
**Suppliers and Buyers can login ONLY if `is_verified = true`**

The `is_verified` flag is set to `true` in two scenarios:
1. **Admin approves registration request** (via Registration Approval page)
2. **Admin manually creates** supplier/buyer (via Control Panel)

---

## 📋 Registration & Verification Flow

### Scenario 1: User Self-Registration

#### Supplier Self-Registration
1. User fills registration form → `POST /register/supplier`
2. `RegisteredUserController::storeSupplier()` creates:
   - `User` with `user_type_id = 2` (supplier)
   - `Supplier` profile with `is_verified = false`
   - Assigns role `'Supplier'`
3. Auto-login → Redirected to `/waiting-approval`
4. **Status:** `is_verified = false` → **Cannot login** ❌

#### Buyer Self-Registration
1. User fills registration form → `POST /register/buyer`
2. `RegisteredUserController::storeBuyer()` creates:
   - `User` with `user_type_id = 3` (buyer)
   - `Buyer` profile with `is_verified = false`
   - Assigns role `'Buyer'`
3. Auto-login → Redirected to `/waiting-approval`
4. **Status:** `is_verified = false` → **Cannot login** ❌

---

### Scenario 2: Admin Approves Registration Request

#### Process
1. Admin views pending registrations → `/admin/registrations/pending`
2. Admin clicks "Approve" → `POST /admin/registrations/{type}/{id}/approve`
3. `RegistrationApprovalController::approve()` updates:
   ```php
   $model->update([
       'is_verified' => true,      // ✅ Set to true
       'is_active' => true,
       'verified_at' => now(),
       'rejection_reason' => null,
   ]);
   ```
4. Notification sent to user
5. **Status:** `is_verified = true` → **Can login** ✅

---

### Scenario 3: Admin Manually Creates Supplier/Buyer

#### Supplier Creation
1. Admin creates supplier → `POST /admin/suppliers`
2. `SupplierController::store()` creates:
   ```php
   'is_verified' => $data['is_verified'] ?? true,  // ✅ Defaults to true
   'verified_at' => ($data['is_verified'] ?? true) ? now() : null,
   ```
3. Form checkbox defaults to `checked` (true)
4. **Status:** `is_verified = true` → **Can login** ✅

#### Buyer Creation
1. Admin creates buyer → `POST /admin/buyers`
2. `BuyerController::store()` creates:
   ```php
   'is_verified' => $data['is_verified'] ?? true,  // ✅ Defaults to true
   'verified_at' => ($data['is_verified'] ?? true) ? now() : null,
   ```
3. Form checkbox defaults to `checked` (true)
4. **Status:** `is_verified = true` → **Can login** ✅

---

## 🔑 Login Flow

### Step 1: Authentication (`AuthenticatedSessionController::store()`)
1. User submits login form → `POST /login`
2. Credentials validated
3. Session regenerated
4. **Verification Check:**
   - If `supplierProfile` exists and `is_verified = false` → Redirect to `/waiting-approval`
   - If `buyerProfile` exists and `is_verified = false` → Redirect to `/waiting-approval`
   - Otherwise → Proceed to dashboard

### Step 2: Dashboard Access (`routes/web.php`)
1. User accesses → `GET /dashboard`
2. **Verification Check (double-check):**
   - If `supplierProfile` exists and `is_verified = false` → Redirect to `/waiting-approval`
   - If `buyerProfile` exists and `is_verified = false` → Redirect to `/waiting-approval`
   - Otherwise → Show dashboard

---

## 📊 Verification Status Matrix

| Scenario | `is_verified` | Can Login? | Redirect To |
|----------|--------------|------------|-------------|
| User self-registers | `false` | ❌ No | `/waiting-approval` |
| Admin approves request | `true` | ✅ Yes | `/dashboard` |
| Admin manually creates | `true` | ✅ Yes | `/dashboard` |
| Admin rejects request | `false` | ❌ No | `/waiting-approval` |

---

## 🔍 Key Files

### Controllers
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Self-registration
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Login logic
- `app/Http/Controllers/Web/RegistrationApprovalController.php` - Admin approval
- `app/Http/Controllers/Web/SupplierController.php` - Admin creates supplier
- `app/Http/Controllers/Web/BuyerController.php` - Admin creates buyer

### Routes
- `routes/web.php` - Dashboard route with verification check
- `routes/auth.php` - Registration and login routes

### Views
- `resources/views/auth/waiting-approval.blade.php` - Waiting page
- `resources/views/admin/registrations/pending.blade.php` - Approval page

---

## ✅ Summary

**The system works as follows:**

1. ✅ **Admin approves registration** → `is_verified = true` → **Can login**
2. ✅ **Admin manually creates** → `is_verified = true` (default) → **Can login**
3. ❌ **User self-registers** → `is_verified = false` → **Cannot login** (waits for approval)

**Login is allowed ONLY when `is_verified = true`**, regardless of how the user was created.


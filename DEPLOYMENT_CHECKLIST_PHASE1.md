# 🚀 Phase 1 RBAC Fixes - Deployment Checklist

## Pre-Deployment (Development Environment)

### 1. Code Review
- [ ] Review all 6 modified files
- [ ] Verify no syntax errors: `php artisan route:list`
- [ ] Run autoload dump: `composer dump-autoload`
- [ ] Clear all caches:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```

### 2. Database Backup
```bash
# Backup current database
php artisan db:backup  # If you have backup package
# OR manually export via mysqldump/phpMyAdmin
```

### 3. Run Seeders (Development)
```bash
# Re-seed permissions & roles
php artisan db:seed --class=UnifiedRolePermissionSeeder

# Re-seed admin user
php artisan db:seed --class=AdminSeeder
```

### 4. Verify Integrity
```bash
# Run verification command
php artisan permission:verify-admin

# Expected output:
# ✅ Admin has 0 direct permissions (correct)
# ✅ Admin role has all 152 permissions
# 🎉 All checks passed!
```

### 5. Manual Testing
- [ ] Login as admin@MedEquip.com
- [ ] Access /admin/users (should work)
- [ ] Access /admin/role-permissions (should work)
- [ ] Create new Staff user
- [ ] Login as Staff user
- [ ] Verify Staff can see dashboard (read-only views)
- [ ] Login as admin again
- [ ] Assign additional permission to Staff user
- [ ] Logout and login as Staff
- [ ] Verify new permission took effect (no logout needed)

---

## Deployment to Production

### Step 1: Deploy Code
```bash
# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Backup Production Database
```bash
# CRITICAL: Backup before any database changes
php artisan backup:run  # If you have backup package
# OR use your hosting provider's backup tool
```

### Step 3: Run Migrations (if any)
```bash
# Check for pending migrations
php artisan migrate:status

# Run migrations (if needed)
php artisan migrate --force
```

### Step 4: Re-seed Permissions
```bash
# This will update role permissions
php artisan db:seed --class=UnifiedRolePermissionSeeder --force

# This will fix admin user (removes direct permissions)
php artisan db:seed --class=AdminSeeder --force
```

### Step 5: Verify Production
```bash
# Verify admin integrity
php artisan permission:verify-admin

# Clear permission cache
php artisan permission:cache-reset
```

### Step 6: Production Testing
- [ ] Login as admin (main admin account)
- [ ] Verify all admin menu items visible
- [ ] Test creating a new user
- [ ] Test assigning permissions
- [ ] Check logs for errors: `tail -f storage/logs/laravel.log`

---

## Post-Deployment Validation

### 1. Smoke Tests
```bash
# Test admin login
curl -X POST https://your-domain.com/login \
  -d "email=admin@MedEquip.com" \
  -d "password=YOUR_PASSWORD"

# Test admin dashboard
curl https://your-domain.com/admin/users \
  -H "Cookie: your-session-cookie"
```

### 2. User Testing
- [ ] Test 3 admin users (different browsers/incognito)
- [ ] Test 2 staff users
- [ ] Test 1 supplier login
- [ ] Test 1 buyer login
- [ ] Verify no 403 errors on expected pages

### 3. Monitor Logs
```bash
# Monitor for 15 minutes after deployment
tail -f storage/logs/laravel.log | grep -E "ERROR|CRITICAL|Permission"
```

---

## Rollback Plan (If Issues Occur)

### Option 1: Code Rollback
```bash
# Revert to previous commit
git revert HEAD
git push origin main

# Re-deploy old code
composer install --no-dev
php artisan config:cache
php artisan route:cache
```

### Option 2: Database Rollback
```bash
# Restore database from backup
# (Use your hosting provider's restore tool)

# Clear caches
php artisan permission:cache-reset
```

### Option 3: Quick Fix (Admin Can't Login)
```bash
php artisan tinker
```
```php
$admin = User::where('email', 'admin@MedEquip.com')->first();
$admin->assignRole('Admin');
$adminRole = Role::where('name', 'Admin')->first();
$adminRole->syncPermissions(Permission::all());
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
exit;
```

---

## Known Issues & Mitigations

### Issue 1: Staff Users Can't See Anything
**Symptom**: Staff user sees blank dashboard after login  
**Cause**: Staff role has no permissions  
**Fix**:
```bash
php artisan db:seed --class=UnifiedRolePermissionSeeder --force
```

### Issue 2: Permission Changes Don't Apply
**Symptom**: User assigned permission but still gets 403  
**Cause**: Cache not cleared  
**Fix**:
```bash
php artisan permission:cache-reset
```

### Issue 3: Sidebar Shows Wrong Items
**Symptom**: Menu items visible but lead to 403  
**Cause**: Menu items need permission declarations  
**Fix**: Update sidebar.blade.php menu config with explicit permissions

---

## Emergency Contacts

**Developer**: [Your Name]  
**Phone**: [Your Phone]  
**Email**: [Your Email]  

**Deployment Time**: [Fill in deployment datetime]  
**Rollback Window**: 1 hour  
**Expected Downtime**: None (zero-downtime deployment)

---

## Success Criteria

### All Must Pass ✅
- [ ] Admin can login
- [ ] Admin can access all admin areas
- [ ] Admin can create users
- [ ] Admin can assign permissions
- [ ] Staff users can login
- [ ] Staff users see baseline permissions
- [ ] Supplier/Buyer logins unaffected
- [ ] No 500 errors in logs
- [ ] `php artisan permission:verify-admin` passes
- [ ] Permission changes take effect immediately

---

## Timeline

```
T-0:00  Deploy code
T+0:05  Run seeders
T+0:10  Verify admin
T+0:15  Production testing
T+0:30  Monitor logs
T+1:00  Sign-off (or rollback if issues)
```

---

## Sign-off

**Deployed by**: _______________  
**Date/Time**: _______________  
**Issues found**: _______________  
**Status**: [ ] SUCCESS  [ ] ROLLED BACK  

---

**This checklist ensures a safe, verified deployment of Phase 1 RBAC fixes.**

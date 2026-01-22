# 🚀 Deployment Guide for MedEquip

## 📋 Quick Reference

### After Git Pull (Every Time)
```bash
./scripts/deploy.sh
```

### Verify Permissions Work
```bash
php artisan permissions:diagnose admin@medequip.com
```

---

## 🔧 Environment Variables

Add these to your `.env` file:

```env
# Permission Cache Configuration
# Set to false in development, true in production
PERMISSION_CACHE_ENABLED=true

# Cache Driver (use redis in production for better performance)
CACHE_DRIVER=database
```

### Development Environment
```env
PERMISSION_CACHE_ENABLED=false
CACHE_DRIVER=file
APP_DEBUG=true
```

### Production Environment
```env
PERMISSION_CACHE_ENABLED=true
CACHE_DRIVER=redis
APP_DEBUG=false
```

---

## 📦 Manual Deployment Steps

If you can't use the script, run these commands in order:

```bash
# 1. Pull code
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Clear ALL caches (CRITICAL)
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan permission:cache-reset

# 4. Run migrations
php artisan migrate --force

# 5. Reseed permissions (idempotent - safe to run multiple times)
php artisan db:seed --class=UnifiedRolePermissionSeeder --force
php artisan db:seed --class=AdminSeeder --force

# 6. Reset permission cache again
php artisan permission:cache-reset

# 7. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Final cache reset
php artisan permission:cache-reset

# 9. Verify
php artisan permissions:diagnose admin@medequip.com
```

---

## 🧪 Testing Checklist

After deployment, verify:

```bash
# 1. Check admin user
php artisan permissions:diagnose admin@medequip.com
# Expected: All tests PASS

# 2. Test route access
curl http://localhost/admin/users
# Expected: 200 OK (if logged in)

# 3. Check cache
php artisan tinker
>>> cache()->has('spatie.permission.cache')
# Expected: true

# 4. Test permission
>>> $admin = User::where('email', 'admin@medequip.com')->first();
>>> $admin->can('users.view')
# Expected: true
```

---

## ⚠️ Common Issues & Solutions

### Issue: "USER DOES NOT HAVE THE RIGHT PERMISSIONS"

**Solution:**
```bash
# Clear everything
php artisan cache:clear
php artisan permission:cache-reset

# Reseed admin
php artisan db:seed --class=AdminSeeder --force

# Verify
php artisan permissions:diagnose admin@medequip.com
```

### Issue: Different behavior on different machines

**Solution:**
```bash
# On BOTH machines:
# 1. Clear cache
php artisan cache:clear
php artisan permission:cache-reset

# 2. Verify database is same
php artisan migrate:status

# 3. Reseed if needed
php artisan db:seed --class=UnifiedRolePermissionSeeder --force
php artisan db:seed --class=AdminSeeder --force
```

### Issue: Permissions work, then stop working

**Root Cause:** Cache desynchronization

**Solution:**
```bash
# Add to cron (run every hour in production)
0 * * * * cd /path/to/project && php artisan permission:cache-reset

# Or disable cache in development
# Add to .env:
PERMISSION_CACHE_ENABLED=false
```

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] All `.env` secrets are unique and strong
- [ ] `APP_DEBUG=false` in production
- [ ] `PERMISSION_CACHE_ENABLED=true` in production
- [ ] Cache driver is `redis` or `memcached` (not `file`)
- [ ] Admin password is changed from default
- [ ] Database backups are configured
- [ ] SSL certificates are installed
- [ ] Firewall rules are configured

---

## 📊 Monitoring

### Check Permission System Health

```bash
# Daily health check
php artisan permissions:diagnose admin@medequip.com

# Check cache hit rate
php artisan tinker
>>> cache()->get('spatie.permission.cache')
```

### Log Permission Failures

Add to `app/Exceptions/Handler.php`:

```php
use Spatie\Permission\Exceptions\UnauthorizedException;

public function register(): void
{
    $this->reportable(function (UnauthorizedException $e) {
        \Log::warning('Permission denied', [
            'user' => auth()->user()?->email,
            'url' => request()->url(),
            'permission' => $e->getMessage(),
        ]);
    });
}
```

---

## 🚨 Emergency Procedures

### If Admin Loses All Access

```bash
# 1. Access server via SSH/console

# 2. Reset admin permissions
php artisan tinker
>>> $admin = User::where('email', 'admin@medequip.com')->first();
>>> $admin->syncRoles(['Admin']);
>>> $allPerms = Permission::where('guard_name', 'web')->get();
>>> $admin->syncPermissions($allPerms);
>>> app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

# 3. Verify
>>> $admin->can('users.view')  # Should return true

# 4. Exit and test
>>> exit
```

### If Database is Corrupted

```bash
# 1. Backup current state
php artisan db:backup  # If available

# 2. Fresh migration (DESTRUCTIVE!)
php artisan migrate:fresh --seed

# 3. Restore data from backup
# (Restore user data, products, etc.)

# 4. Reseed permissions
php artisan db:seed --class=UnifiedRolePermissionSeeder --force
php artisan db:seed --class=AdminSeeder --force
```

---

## 📚 Additional Resources

- **RBAC Audit Report:** `RBAC_SECURITY_AUDIT_REPORT.md`
- **Spatie Documentation:** https://spatie.be/docs/laravel-permission
- **Laravel Authorization:** https://laravel.com/docs/authorization

---

**Last Updated:** 2026-01-22  
**Maintained By:** Development Team

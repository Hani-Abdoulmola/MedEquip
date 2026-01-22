# 🔒 Permission System - Quick Reference

## 🚀 Quick Start

### After Pulling Code
```bash
./scripts/deploy.sh
```

### Verify Permissions Work
```bash
php artisan permissions:diagnose admin@medequip.com
```

### Expected Result
```
✅ ALL TESTS PASSED - Permissions working correctly!
```

---

## 📋 Common Commands

### Check User Permissions
```bash
php artisan permissions:diagnose [email]
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

### Reseed Admin
```bash
php artisan db:seed --class=AdminSeeder
```

### Deploy After Git Pull
```bash
./scripts/deploy.sh
```

---

## 🔧 Troubleshooting

### "USER DOES NOT HAVE THE RIGHT PERMISSIONS"

**Solution:**
```bash
# 1. Clear caches
php artisan cache:clear
php artisan permission:cache-reset

# 2. Reseed admin
php artisan db:seed --class=AdminSeeder

# 3. Verify
php artisan permissions:diagnose admin@medequip.com
```

### Different Behavior on Different Machines

**Solution:**
```bash
# Run deployment script on BOTH machines
./scripts/deploy.sh
```

### After Database Migration

**Solution:**
```bash
# Always reseed permissions after migrations
php artisan db:seed --class=UnifiedRolePermissionSeeder
php artisan db:seed --class=AdminSeeder
php artisan permission:cache-reset
```

---

## 📚 Documentation

- **Full Analysis:** `RBAC_SECURITY_AUDIT_REPORT.md`
- **Deployment Guide:** `DEPLOYMENT_GUIDE.md`
- **Implementation Summary:** `RBAC_FIXES_IMPLEMENTATION_SUMMARY.md`

---

## ✅ Health Check

Run this weekly:
```bash
php artisan permissions:diagnose admin@medequip.com
```

Should show:
```
✅ ALL TESTS PASSED - Permissions working correctly!
```

---

**Last Updated:** 2026-01-22

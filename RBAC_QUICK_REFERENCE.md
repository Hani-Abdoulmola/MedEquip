# 🚀 RBAC System - Quick Reference Guide

**Last Updated**: January 22, 2026  
**System Version**: Phase 1 + Phase 2 Complete

---

## 🎯 Core Principles

### ✅ DO
- Assign permissions to **roles**, not users (for baseline)
- Use **direct permissions** for user-specific overrides only
- Run `permission:verify-admin` after permission changes
- Check `$user->can('permission.name')` for authorization
- Use permission filtering (AdminPermissionService) for internal users
- Clear cache after manual database changes

### ❌ DON'T
- Call `syncPermissions([])` after `assignRole()`
- Assign direct permissions to Admin user (use role only)
- Check `user_type` for authorization (use roles/permissions)
- Allow multiple internal roles on single user
- Bypass pre-commit hooks (unless emergency)
- Modify Supplier/Buyer role permissions

---

## 🛠️ Common Commands

### Permission Management
```bash
# Verify admin integrity
php artisan permission:verify-admin

# Auto-fix admin issues
php artisan permission:verify-admin --fix

# Clear permission cache
php artisan permission:cache-reset

# Re-seed permissions
php artisan db:seed --class=UnifiedRolePermissionSeeder

# Re-seed admin user
php artisan db:seed --class=AdminSeeder
```

### Testing
```bash
# Run all permission tests
php artisan test --filter=PermissionTest

# Run specific test
php artisan test --filter=admin_user_has_zero_direct_permissions

# Run with stop-on-failure
php artisan test --filter=PermissionTest --stop-on-failure
```

### Git Hooks
```bash
# Install pre-commit hooks
chmod +x scripts/install-git-hooks.sh
./scripts/install-git-hooks.sh

# Check if hooks are installed
ls -la .git/hooks/pre-commit

# Bypass hook (emergency only)
git commit --no-verify
```

---

## 📊 System Architecture

```
User → Roles → Permissions
  ↓      ↓         ↓
  └──────┴─────────┴─── Authorization Check
  
Admin (Role)
├── All 152 permissions (via role_has_permissions)
└── 0 direct permissions (via model_has_permissions)

Staff (Role)
├── 10 baseline permissions (via role_has_permissions)
└── Variable direct permissions (via model_has_permissions)

User Effective Permissions = Role Permissions ∪ Direct Permissions
```

---

## 🔑 Key Endpoints

### Admin UI
- **Users Tab**: `/admin/role-permissions?tab=users`
- **Roles Tab**: `/admin/role-permissions?tab=roles`
- **User Edit**: `/admin/users/{user}/edit`
- **User List**: `/admin/users`

### API Routes
- **Assign User Permissions**: `POST /admin/role-permissions/{user}/assign`
- **Update Role Permissions**: `POST /admin/role-permissions/role/{role}/update`
- **Update User Permissions**: `PUT /admin/users/{user}/permissions`

---

## 📋 Workflow Cheat Sheet

### Create New Internal User
```
1. Admin → Users → Create User
2. Fill form (name, email, password, status)
3. Select role: "Staff" or "Admin"
4. Click "Save"
5. User now has role permissions automatically
6. (Optional) Assign additional permissions:
   Admin → Roles & Permissions → Users Tab → Select User → Check permissions
```

### Modify Role Permissions
```
1. Admin → Roles & Permissions
2. Click "Roles Tab"
3. Select role: "Staff" or "Admin"
4. Check/uncheck permissions
5. Click "Save Role Permissions"
6. ✅ All users with this role immediately inherit changes
```

### Grant Additional Permission to User
```
1. Admin → Roles & Permissions → Users Tab
2. Select user from dropdown
3. Check additional permissions (beyond role)
4. Click "Save Permissions"
5. ✅ User now has role + direct permissions
```

### Remove Permission from User
```
1. Admin → Roles & Permissions → Users Tab
2. Select user
3. Uncheck permission
4. Click "Save Permissions"
5. ✅ Permission removed immediately
```

---

## 🧪 Quick Tests

### Test Admin Integrity
```bash
php artisan tinker
```
```php
$admin = User::where('email', 'admin@MedEquip.com')->first();
$admin->permissions->count(); // Should be 0
$admin->roles->first()->name; // Should be "Admin"
$admin->getAllPermissions()->count(); // Should be 152 (all permissions)
$admin->can('users.view'); // Should be true
exit;
```

### Test Staff User
```bash
php artisan tinker
```
```php
$staff = User::factory()->create();
$staff->assignRole('Staff');
$staff->getAllPermissions()->count(); // Should be 10 (baseline)
$staff->givePermissionTo('products.create'); // Add direct permission
$staff->getAllPermissions()->count(); // Should be 11
exit;
```

### Test Role Changes
```bash
php artisan tinker
```
```php
$user1 = User::factory()->create();
$user2 = User::factory()->create();
$user1->assignRole('Staff');
$user2->assignRole('Staff');

$staffRole = Role::where('name', 'Staff')->first();
$staffRole->givePermissionTo('orders.create');

$user1->hasPermissionTo('orders.create'); // Should be true
$user2->hasPermissionTo('orders.create'); // Should be true
exit;
```

---

## 🚨 Troubleshooting

| Problem | Solution |
|---------|----------|
| Admin can't access anything | `php artisan permission:verify-admin --fix` |
| Staff user sees blank dashboard | Re-seed: `php artisan db:seed --class=UnifiedRolePermissionSeeder` |
| Permission changes don't apply | `php artisan permission:cache-reset` |
| Pre-commit hook fails | Fix code issues or `git commit --no-verify` (emergency) |
| 403 on permission page | Verify user has `permissions.view` |
| Roles tab not showing | Check `$activeTab` variable in controller |
| Tests failing | `php artisan test --filter=PermissionTest --stop-on-failure` |

---

## 📚 Files Reference

### Controllers
- `app/Http/Controllers/Web/UserController.php` - User CRUD + permission assignment
- `app/Http/Controllers/Web/RolePermissionController.php` - Unified permission management

### Services
- `app/Services/AdminPermissionService.php` - Filter admin-only permissions

### Middleware
- `app/Http/Middleware/EnsureInternalUser.php` - Role-based access control

### Commands
- `app/Console/Commands/VerifyAdminPermissions.php` - Verify admin integrity
- `app/Console/Commands/ResetPermissionCache.php` - Clear permission cache

### Tests
- `tests/Feature/PermissionTest.php` - 23 permission tests

### Seeders
- `database/seeders/UnifiedRolePermissionSeeder.php` - Create permissions & roles
- `database/seeders/AdminSeeder.php` - Create admin user

### Views
- `resources/views/admin/role-permissions/index.blade.php` - Unified permission UI

### Config
- `config/permission.php` - Spatie configuration (events enabled)

### Hooks
- `.githooks/pre-commit` - Pre-commit validation
- `scripts/install-git-hooks.sh` - Hook installer

---

## 📞 Support

### Documentation
- Phase 1: `PHASE_1_RBAC_CRITICAL_FIXES.md`
- Phase 2: `PHASE_2_FEATURE_ADDITIONS_COMPLETE.md`
- Deployment: `DEPLOYMENT_CHECKLIST_PHASE1.md`
- This Guide: `RBAC_QUICK_REFERENCE.md`

### Verification Commands
```bash
# Check system health
php artisan permission:verify-admin

# Run tests
php artisan test --filter=PermissionTest

# Check routes
php artisan route:list --path=admin/role-permissions

# View logs
tail -f storage/logs/laravel.log | grep Permission
```

---

## 🎓 Best Practices

1. **Always use roles as baseline**
   - Admin role = all permissions
   - Staff role = read-only baseline
   - Direct permissions = user-specific overrides

2. **Never bypass RBAC**
   - Don't check user_type for authorization
   - Always use $user->can() or policies

3. **Test permission changes**
   - Run `permission:verify-admin` after seeders
   - Run `PermissionTest` suite before commits
   - Manually test in UI after deployment

4. **Clear cache consistently**
   - Events auto-clear cache
   - Manually clear after direct DB changes
   - Always clear after deployment

5. **Use pre-commit hooks**
   - Install once: `./scripts/install-git-hooks.sh`
   - Prevents regression
   - Blocks bad commits automatically

---

**This guide provides everything you need for day-to-day RBAC management.**

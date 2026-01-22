# 🔧 Fix Permission Issues After Pulling Project

When you pull this project to a new machine and face authorization issues, run these commands in order:

## Option 1: Fresh Start (Recommended if data doesn't matter)
```bash
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

## Option 2: Keep Existing Data (Just fix permissions)
```bash
# 1. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Reset Spatie Permission cache
php artisan permission:cache-reset

# 3. Re-run the role/permission seeders
php artisan db:seed --class=UserTypeSeeder
php artisan db:seed --class=UnifiedRolePermissionSeeder
php artisan db:seed --class=AdminSeeder

# 4. Clear cache again
php artisan cache:clear
```

## Option 3: Quick Fix via Tinker
```bash
php artisan tinker
```
Then in Tinker:
```php
// Reset permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Ensure admin has Admin role
$admin = \App\Models\User::where('email', 'admin@MedEquip.com')->first();
$admin->syncRoles(['Admin']);

// Verify
$admin->getRoleNames(); // Should show "Admin"
$admin->getAllPermissions()->pluck('name'); // Should show all permissions
```

## Verify Fix
After running the commands, logout and login again. The admin should now have full access.

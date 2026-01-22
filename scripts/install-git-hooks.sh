#!/bin/bash

# Install Git Hooks for Permission Validation
# Run this script once to install the hooks: chmod +x scripts/install-git-hooks.sh && ./scripts/install-git-hooks.sh

set -e

HOOKS_DIR=".git/hooks"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "🔧 Installing Git Hooks for Permission Validation..."

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo "❌ Error: Not a git repository. Run this script from the project root."
    exit 1
fi

# Create pre-commit hook
cat > "$HOOKS_DIR/pre-commit" << 'EOF'
#!/bin/bash

# Pre-commit hook for Permission Validation
# Automatically checks permission integrity before commits

set -e

echo "🔍 Running permission validation checks..."

# Check if permission seeder was modified
SEEDER_MODIFIED=$(git diff --cached --name-only | grep -E "(UnifiedRolePermissionSeeder|AdminSeeder)" || true)

if [ -n "$SEEDER_MODIFIED" ]; then
    echo "⚠️  Permission seeder modified. Running permission tests..."
    
    # Run permission tests
    php artisan test --filter=PermissionTest --stop-on-failure
    
    if [ $? -ne 0 ]; then
        echo "❌ Permission tests failed. Fix issues before committing."
        echo "💡 Run: php artisan test --filter=PermissionTest"
        exit 1
    fi
    
    echo "✅ Permission tests passed"
fi

# Check if UserController was modified
USER_CONTROLLER_MODIFIED=$(git diff --cached --name-only | grep "UserController.php" || true)

if [ -n "$USER_CONTROLLER_MODIFIED" ]; then
    echo "⚠️  UserController modified. Checking for syncPermissions() misuse..."
    
    # Check for syncPermissions([]) after assignRole
    if git diff --cached app/Http/Controllers/Web/UserController.php | grep -A 5 "assignRole" | grep "syncPermissions(\[\])" > /dev/null; then
        echo "❌ ERROR: Found syncPermissions([]) after assignRole() in UserController"
        echo "   This clears role permissions and violates RBAC design."
        echo "   Remove this line and let users inherit permissions from roles."
        exit 1
    fi
    
    echo "✅ UserController changes look good"
fi

# Check if AdminSeeder was modified
ADMIN_SEEDER_MODIFIED=$(git diff --cached --name-only | grep "AdminSeeder.php" || true)

if [ -n "$ADMIN_SEEDER_MODIFIED" ]; then
    echo "⚠️  AdminSeeder modified. Checking for direct permission assignment..."
    
    # Check for direct permission assignment to admin user
    if git diff --cached database/seeders/AdminSeeder.php | grep '^\+.*\$admin.*syncPermissions' > /dev/null; then
        echo "❌ ERROR: Found direct permission assignment to admin user in AdminSeeder"
        echo "   Admin should inherit permissions from Admin role, not direct assignment."
        echo "   Only assign permissions to \$adminRole, not \$admin."
        exit 1
    fi
    
    echo "✅ AdminSeeder changes look good"
fi

echo "✅ All permission validation checks passed"
exit 0
EOF

# Make pre-commit hook executable
chmod +x "$HOOKS_DIR/pre-commit"

echo "✅ Pre-commit hook installed successfully!"
echo ""
echo "The hook will automatically:"
echo "  • Run permission tests when seeders are modified"
echo "  • Check for syncPermissions([]) misuse in UserController"
echo "  • Prevent direct permission assignment to admin user"
echo ""
echo "To bypass the hook (not recommended): git commit --no-verify"

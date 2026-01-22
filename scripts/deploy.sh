#!/bin/bash

# MedEquip Deployment Script
# Ensures proper cache clearing and permission synchronization

set -e  # Exit on any error

echo "🚀 MedEquip Deployment Started..."
echo "═══════════════════════════════════════"

# Check if we're in the project root
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project root!"
    exit 1
fi

# Pull latest code (optional - uncomment if needed)
# echo "📥 Pulling latest code..."
# git pull origin main

# Install/Update dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --quiet

# Clear ALL caches (CRITICAL for permissions)
echo "🧹 Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# CRITICAL: Reset permission cache
echo "🔑 Resetting permission cache..."
php artisan permission:cache-reset

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Reseed permissions (idempotent - safe to run multiple times)
echo "🌱 Reseeding permissions..."
php artisan db:seed --class=UnifiedRolePermissionSeeder --force

# Reseed admin user (ensures admin has all permissions)
echo "👤 Reseeding admin user..."
php artisan db:seed --class=AdminSeeder --force

# CRITICAL: Reset permission cache again after seeding
echo "🔑 Rebuilding permission cache..."
php artisan permission:cache-reset

# Recache optimized files
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Final permission cache reset (ensures fresh cache)
echo "🔑 Final permission cache reset..."
php artisan permission:cache-reset

echo ""
echo "✅ Deployment Complete!"
echo "═══════════════════════════════════════"
echo ""
echo "🧪 Run diagnostics:"
echo "   php artisan permissions:diagnose admin@medequip.com"
echo ""

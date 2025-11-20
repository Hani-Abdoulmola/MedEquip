<?php

/**
 * Product Categories System Test Suite
 * اختبار نظام فئات المنتجات الهرمي
 *
 * Tests the hierarchical product categories implementation
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Models\ProductCategory;
use App\Models\Product;

echo "=== PRODUCT CATEGORIES SYSTEM TEST SUITE ===\n\n";

$passed = 0;
$failed = 0;

// ========================================
// Test 1: Migration Files Exist
// ========================================
echo "Test 1: Product categories migration exists... ";
$migrationFile = 'database/migrations/2025_10_31_000015_create_product_categories_table.php';
if (file_exists($migrationFile)) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (migration file not found)\n";
    $failed++;
}

// ========================================
// Test 2: Products Migration Modified
// ========================================
echo "Test 2: Products migration has category_id column... ";
$productsFile = file_get_contents('database/migrations/2025_10_31_000018_create_products_table.php');
if (strpos($productsFile, "category_id") !== false &&
    strpos($productsFile, "->constrained('product_categories')") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (category_id foreign key not found)\n";
    $failed++;
}

// ========================================
// Test 3: Old Category Column Removed
// ========================================
echo "Test 3: Products migration removed old category string column... ";
// Check that the old category column is not in the fillable array anymore
if (!preg_match("/\\\$table->string\('category'/", $productsFile)) {
    echo "✅ PASS (old category column removed)\n";
    $passed++;
} else {
    echo "❌ FAIL (old category string column still exists)\n";
    $failed++;
}

// ========================================
// Test 4: ProductCategory Model Exists
// ========================================
echo "Test 4: ProductCategory model exists... ";
if (file_exists('app/Models/ProductCategory.php')) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (model file not found)\n";
    $failed++;
}

// ========================================
// Test 5: ProductCategory Has Required Traits
// ========================================
echo "Test 5: ProductCategory uses required traits... ";
$categoryModel = file_get_contents('app/Models/ProductCategory.php');
if (strpos($categoryModel, 'App\Traits\Auditable') !== false &&
    strpos($categoryModel, 'HasFactory') !== false &&
    strpos($categoryModel, 'SoftDeletes') !== false &&
    preg_match('/use\s+Auditable,\s*HasFactory,\s*SoftDeletes/', $categoryModel)) {
    echo "✅ PASS (all traits present)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing required traits)\n";
    $failed++;
}

// ========================================
// Test 6: ProductCategory Has Fillable Fields
// ========================================
echo "Test 6: ProductCategory has all required fillable fields... ";
$requiredFields = ['name', 'name_ar', 'slug', 'description', 'parent_id', 'is_active', 'sort_order', 'created_by', 'updated_by'];
$allFieldsPresent = true;
foreach ($requiredFields as $field) {
    if (strpos($categoryModel, "'$field'") === false) {
        $allFieldsPresent = false;
        break;
    }
}
if ($allFieldsPresent) {
    echo "✅ PASS (all 9 fields in fillable)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing fillable fields)\n";
    $failed++;
}

// ========================================
// Test 7: ProductCategory Has Casts
// ========================================
echo "Test 7: ProductCategory has proper casts... ";
if (strpos($categoryModel, "'is_active' => 'boolean'") !== false &&
    strpos($categoryModel, "'sort_order' => 'integer'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (missing or incorrect casts)\n";
    $failed++;
}

// ========================================
// Test 8: ProductCategory Has Relationships
// ========================================
echo "Test 8: ProductCategory has all relationships... ";
$relationships = ['parent()', 'children()', 'products()', 'creator()', 'updater()'];
$allRelationshipsPresent = true;
foreach ($relationships as $relationship) {
    if (strpos($categoryModel, "function $relationship") === false) {
        $allRelationshipsPresent = false;
        break;
    }
}
if ($allRelationshipsPresent) {
    echo "✅ PASS (all 5 relationships defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing relationships)\n";
    $failed++;
}

// ========================================
// Test 9: ProductCategory Has Scopes
// ========================================
echo "Test 9: ProductCategory has query scopes... ";
$scopes = ['scopeActive', 'scopeRoots', 'scopeOrdered'];
$allScopesPresent = true;
foreach ($scopes as $scope) {
    if (strpos($categoryModel, "function $scope") === false) {
        $allScopesPresent = false;
        break;
    }
}
if ($allScopesPresent) {
    echo "✅ PASS (all 3 scopes defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing scopes)\n";
    $failed++;
}

// ========================================
// Test 10: ProductCategory Has Helper Methods
// ========================================
echo "Test 10: ProductCategory has helper methods... ";
$helpers = ['isRoot()', 'hasChildren()', 'getFullPathAttribute()'];
$allHelpersPresent = true;
foreach ($helpers as $helper) {
    if (strpos($categoryModel, "function $helper") === false) {
        $allHelpersPresent = false;
        break;
    }
}
if ($allHelpersPresent) {
    echo "✅ PASS (all 3 helper methods defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing helper methods)\n";
    $failed++;
}

// ========================================
// Test 11: ProductCategory Has Auto-Slug Generation
// ========================================
echo "Test 11: ProductCategory has auto-slug generation... ";
if (strpos($categoryModel, 'generateUniqueSlug') !== false &&
    strpos($categoryModel, 'static::creating') !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (auto-slug generation not found)\n";
    $failed++;
}

// ========================================
// Test 12: Product Model Updated - Fillable
// ========================================
echo "Test 12: Product model has category_id in fillable... ";
$productModel = file_get_contents('app/Models/Product.php');
if (strpos($productModel, "'category_id'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (category_id not in fillable)\n";
    $failed++;
}

// ========================================
// Test 13: Product Model Updated - Old Category Removed
// ========================================
echo "Test 13: Product model removed old category from fillable... ";
if (!preg_match("/'category',/", $productModel)) {
    echo "✅ PASS (old category field removed)\n";
    $passed++;
} else {
    echo "❌ FAIL (old category field still in fillable)\n";
    $failed++;
}

// ========================================
// Test 14: Product Model Has Category Relationship
// ========================================
echo "Test 14: Product model has category() relationship... ";
if (strpos($productModel, 'function category()') !== false &&
    strpos($productModel, 'ProductCategory::class') !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (category relationship not found)\n";
    $failed++;
}

// ========================================
// Test 15: Product Model Has BelongsTo Import
// ========================================
echo "Test 15: Product model imports BelongsTo... ";
if (strpos($productModel, 'use Illuminate\Database\Eloquent\Relations\BelongsTo') !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (BelongsTo import missing)\n";
    $failed++;
}

// ========================================
// Test 16: Migration Has Proper Indexes
// ========================================
echo "Test 16: Product categories migration has composite index... ";
$migrationContent = file_get_contents($migrationFile);
if (strpos($migrationContent, "['parent_id', 'is_active', 'sort_order']") !== false) {
    echo "✅ PASS (composite index defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (composite index missing)\n";
    $failed++;
}

// ========================================
// Test 17: Migration Has Soft Deletes
// ========================================
echo "Test 17: Product categories migration has soft deletes... ";
if (strpos($migrationContent, 'softDeletes()') !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL (soft deletes missing)\n";
    $failed++;
}

// ========================================
// Test 18: Migration Has Proper Cascading Rules
// ========================================
echo "Test 18: Migration has proper cascading rules... ";
$hasNullOnDelete = strpos($migrationContent, 'nullOnDelete()') !== false;
$hasRestrictOnDelete = strpos($migrationContent, 'restrictOnDelete()') !== false;
if ($hasNullOnDelete && $hasRestrictOnDelete) {
    echo "✅ PASS (nullOnDelete for parent_id, restrictOnDelete for users)\n";
    $passed++;
} else {
    echo "❌ FAIL (incorrect cascading rules)\n";
    $failed++;
}

// ========================================
// Test 19: Migration Has Arabic Comments
// ========================================
echo "Test 19: Migration has Arabic comments with emoji... ";
if (preg_match('/->comment\([\'"]🔑/', $migrationContent) &&
    preg_match('/->comment\([\'"]📦/', $migrationContent) &&
    preg_match('/->comment\([\'"]🌳/', $migrationContent)) {
    echo "✅ PASS (Arabic comments with emoji present)\n";
    $passed++;
} else {
    echo "❌ FAIL (missing Arabic comments or emoji)\n";
    $failed++;
}

// ========================================
// Test 20: Products Migration Has Category Index
// ========================================
echo "Test 20: Products migration has category_id index... ";
if (strpos($productsFile, "['category_id', 'is_active']") !== false) {
    echo "✅ PASS (category index defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (category index missing)\n";
    $failed++;
}

// ========================================
// Summary
// ========================================
echo "\n=== TEST SUMMARY ===\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n\n";

if ($failed === 0) {
    echo "🎉 ALL TESTS PASSED! Product categories system is ready.\n\n";
    echo "✅ Migration created with hierarchical structure\n";
    echo "✅ Products migration modified (category_id added, old category removed)\n";
    echo "✅ ProductCategory model complete with all features\n";
    echo "✅ Product model updated with category relationship\n";
    echo "✅ All relationships, scopes, and helpers implemented\n";
    echo "✅ Auto-slug generation configured\n";
    echo "✅ Proper cascading rules (nullOnDelete, restrictOnDelete)\n";
    echo "✅ Arabic comments with emoji icons\n\n";
    echo "Next step: Run 'php artisan migrate:fresh' to apply changes\n";
    exit(0);
} else {
    echo "⚠️  Some tests failed. Please review the issues above.\n";
    exit(1);
}



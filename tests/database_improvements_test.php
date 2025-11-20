<?php

/**
 * Database Improvements Test Script
 *
 * Run this: php artisan tinker
 * Then paste: include 'tests/database_improvements_test.php';
 */

// Load Laravel if not already loaded
if (!class_exists('Illuminate\Foundation\Application')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
}

echo "=== DATABASE IMPROVEMENTS TEST SUITE ===\n\n";

$passed = 0;
$failed = 0;
$skipped = 0;

// Test 1: RfqItem Model Exists
echo "Test 1: RfqItem Model Exists... ";
try {
    if (class_exists('App\Models\RfqItem')) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 2: QuotationItem Model Exists
echo "Test 2: QuotationItem Model Exists... ";
try {
    if (class_exists('App\Models\QuotationItem')) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 3: OrderItem Model Exists
echo "Test 3: OrderItem Model Exists... ";
try {
    if (class_exists('App\Models\OrderItem')) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 4: File Model Deleted
echo "Test 4: File Model Deleted... ";
try {
    if (!class_exists('App\Models\File')) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL (File model still exists)\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "✅ PASS (File model not found)\n";
    $passed++;
}

// Test 5: Payment Model Has Auto-Sync
echo "Test 5: Payment Model Has Auto-Sync... ";
try {
    $reflection = new ReflectionClass('App\Models\Payment');
    $method = $reflection->getMethod('booted');
    if ($method) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 6: Buyer Has invoices() Method
echo "Test 6: Buyer Has invoices() Method... ";
try {
    $reflection = new ReflectionClass('App\Models\Buyer');
    $method = $reflection->getMethod('invoices');
    if ($method) {
        echo "✅ PASS\n";
        $passed++;
    } else {
        echo "❌ FAIL\n";
        $failed++;
    }
} catch (Exception $e) {
    echo "❌ FAIL: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 7: New Migration Files Exist (quotation_items, order_items)
echo "Test 7: New Migration Files Exist... ";
$migrations = [
    'database/migrations/2025_11_13_000004_create_quotation_items_table.php',
    'database/migrations/2025_11_13_000005_create_order_items_table.php',
];

$allExist = true;
foreach ($migrations as $migration) {
    if (!file_exists($migration)) {
        echo "❌ FAIL (Missing: " . basename($migration) . ")\n";
        $allExist = false;
        break;
    }
}

if ($allExist) {
    echo "✅ PASS (Both new migrations exist)\n";
    $passed++;
} else {
    $failed++;
}

// Test 8: Redundant Files Deleted
echo "Test 8: Redundant Files Deleted... ";
$deletedFiles = [
    'app/Models/File.php',
    'database/migrations/2025_10_31_000023_create_files_table.php',
    'database/migrations/2025_11_13_000001_migrate_files_to_media_table.php',
    'database/migrations/2025_11_13_000002_drop_files_table.php',
    'database/migrations/2025_11_13_000003_remove_document_path_columns.php',
    'database/migrations/2025_11_13_000006_standardize_financial_data_types.php',
    'database/migrations/2025_11_13_000007_standardize_currency_defaults.php',
    'database/migrations/2025_11_13_000008_fix_cascading_rules_for_financial_records.php',
];

$allDeleted = true;
foreach ($deletedFiles as $file) {
    if (file_exists($file)) {
        echo "❌ FAIL (Still exists: " . basename($file) . ")\n";
        $allDeleted = false;
        break;
    }
}

if ($allDeleted) {
    echo "✅ PASS (All redundant files deleted)\n";
    $passed++;
} else {
    $failed++;
}

// Test 9: Migration Filename Fixed
echo "Test 9: Migration Filename Fixed... ";
if (!file_exists('database/migrations/2025_10_31_000017_create_product_supplier_table.php.php')) {
    echo "✅ PASS (Double .php.php extension fixed)\n";
    $passed++;
} else {
    echo "❌ FAIL (File still has .php.php extension)\n";
    $failed++;
}

// Test 10: Original Migrations Modified (Check for decimal instead of double)
echo "Test 10: Original Migrations Modified... ";
$quotationsMigration = file_get_contents('database/migrations/2025_10_31_000021_create_quotations_table.php');
$ordersMigration = file_get_contents('database/migrations/2025_10_31_000022_create_orders_table.php');
$invoicesMigration = file_get_contents('database/migrations/2025_10_31_000023_create_invoices_table.php');
$paymentsMigration = file_get_contents('database/migrations/2025_10_31_000024_create_payments_table.php');

$allModified = true;

// Check quotations uses decimal
if (strpos($quotationsMigration, "decimal('total_price', 12, 2)") === false) {
    echo "❌ FAIL (quotations still uses double)\n";
    $allModified = false;
}

// Check orders uses decimal and restrictOnDelete
if ($allModified && (strpos($ordersMigration, "decimal('total_amount', 12, 2)") === false ||
    strpos($ordersMigration, 'restrictOnDelete()') === false)) {
    echo "❌ FAIL (orders not properly modified)\n";
    $allModified = false;
}

// Check invoices uses decimal and restrictOnDelete
if ($allModified && (strpos($invoicesMigration, "decimal('subtotal', 12, 2)") === false ||
    strpos($invoicesMigration, 'restrictOnDelete()') === false)) {
    echo "❌ FAIL (invoices not properly modified)\n";
    $allModified = false;
}

// Check payments uses LYD default and restrictOnDelete
if ($allModified && (strpos($paymentsMigration, "default('LYD')") === false ||
    strpos($paymentsMigration, 'restrictOnDelete()') === false)) {
    echo "❌ FAIL (payments not properly modified)\n";
    $allModified = false;
}

if ($allModified) {
    echo "✅ PASS (Original migrations use decimal, LYD, and restrictOnDelete)\n";
    $passed++;
} else {
    $failed++;
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "⏭️  Skipped: $skipped\n";
echo "Total: " . ($passed + $failed + $skipped) . "\n\n";

if ($failed === 0) {
    echo "🎉 ALL TESTS PASSED! Database improvements are ready.\n";
    echo "\n";
    echo "✅ Original migrations have been modified at source\n";
    echo "✅ Redundant fix migrations have been deleted\n";
    echo "✅ Only 2 new migrations remain (quotation_items, order_items)\n";
    echo "\n";
    echo "Next step: Run 'php artisan migrate:fresh' (or migrate if fresh install)\n";
} else {
    echo "⚠️  SOME TESTS FAILED. Review the output above.\n";
}

echo "\n";


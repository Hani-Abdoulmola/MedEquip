<?php

/**
 * 🧪 APP IMPROVEMENTS TEST SUITE
 * 
 * Tests all fixes implemented from APP_REVIEW_REPORT.md
 * 
 * Phase 1 - Critical Fixes:
 * 1. ProductRequest validation (category -> category_id)
 * 2. FileController deleted
 * 
 * Phase 2 - Important Fixes:
 * 3. ReferenceCodeService created
 * 4. All controllers updated to use ReferenceCodeService
 * 5. Currency validation using model constants
 * 6. ActivityLogFilter used in ActivityLogController
 */

require_once __DIR__.'/../vendor/autoload.php';

use App\Services\ReferenceCodeService;
use App\Models\Order;
use App\Models\Payment;

// Test counter
$tests_passed = 0;
$tests_failed = 0;

function test($description, $callback) {
    global $tests_passed, $tests_failed;
    
    try {
        $result = $callback();
        if ($result) {
            echo "✅ PASS: {$description}\n";
            $tests_passed++;
        } else {
            echo "❌ FAIL: {$description}\n";
            $tests_failed++;
        }
    } catch (Exception $e) {
        echo "❌ ERROR: {$description} - {$e->getMessage()}\n";
        $tests_failed++;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║          APP IMPROVEMENTS TEST SUITE                           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ============================================================================
// PHASE 1: CRITICAL FIXES
// ============================================================================

echo "📋 PHASE 1: CRITICAL FIXES\n";
echo "─────────────────────────────────────────────────────────────────\n\n";

// Test 1: ProductRequest validation updated
test("ProductRequest uses 'category_id' instead of 'category'", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Requests/ProductRequest.php');
    return strpos($file, "'category_id'") !== false && 
           strpos($file, "exists:product_categories,id") !== false;
});

// Test 2: ProductRequest has category_id validation message
test("ProductRequest has category_id validation message", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Requests/ProductRequest.php');
    return strpos($file, "'category_id.exists'") !== false;
});

// Test 3: FileController deleted
test("FileController has been deleted", function() {
    return !file_exists(__DIR__.'/../app/Http/Controllers/Web/FileController.php');
});

echo "\n";

// ============================================================================
// PHASE 2: IMPORTANT FIXES
// ============================================================================

echo "📋 PHASE 2: IMPORTANT FIXES\n";
echo "─────────────────────────────────────────────────────────────────\n\n";

// Test 4: ReferenceCodeService exists
test("ReferenceCodeService class exists", function() {
    return class_exists('App\Services\ReferenceCodeService');
});

// Test 5: ReferenceCodeService has generate method
test("ReferenceCodeService has generate() method", function() {
    return method_exists(ReferenceCodeService::class, 'generate');
});

// Test 6: ReferenceCodeService has generateUnique method
test("ReferenceCodeService has generateUnique() method", function() {
    return method_exists(ReferenceCodeService::class, 'generateUnique');
});

// Test 7: ReferenceCodeService has all prefix constants
test("ReferenceCodeService has all prefix constants", function() {
    return defined('App\Services\ReferenceCodeService::PREFIX_RFQ') &&
           defined('App\Services\ReferenceCodeService::PREFIX_QUOTATION') &&
           defined('App\Services\ReferenceCodeService::PREFIX_ORDER') &&
           defined('App\Services\ReferenceCodeService::PREFIX_INVOICE') &&
           defined('App\Services\ReferenceCodeService::PREFIX_DELIVERY') &&
           defined('App\Services\ReferenceCodeService::PREFIX_PAYMENT');
});

// Test 8: ReferenceCodeService generates correct format
test("ReferenceCodeService generates correct format (PREFIX-YYYYMMDD-XXXXXX)", function() {
    $code = ReferenceCodeService::generate('TEST', 6);
    return preg_match('/^TEST-\d{8}-[A-Z0-9]{6}$/', $code) === 1;
});

// Test 9: RfqController uses ReferenceCodeService
test("RfqController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/RfqController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 10: QuotationController uses ReferenceCodeService
test("QuotationController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/QuotationController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 11: OrderController uses ReferenceCodeService
test("OrderController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/OrderController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 12: InvoiceController uses ReferenceCodeService
test("InvoiceController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/InvoiceController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 13: DeliveryController uses ReferenceCodeService
test("DeliveryController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/DeliveryController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 14: PaymentController uses ReferenceCodeService
test("PaymentController uses ReferenceCodeService", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/PaymentController.php');
    return strpos($file, 'use App\Services\ReferenceCodeService') !== false &&
           strpos($file, 'ReferenceCodeService::generateUnique') !== false;
});

// Test 15: OrderRequest uses Order model constants for currency
test("OrderRequest uses Order model constants for currency validation", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Requests/OrderRequest.php');
    return strpos($file, 'use App\Models\Order') !== false &&
           strpos($file, 'Order::CURRENCY_LYD') !== false &&
           strpos($file, 'Order::CURRENCY_USD') !== false &&
           strpos($file, 'Order::CURRENCY_EUR') !== false;
});

// Test 16: PaymentRequest uses Payment model constants for currency
test("PaymentRequest uses Payment model constants for currency validation", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Requests/PaymentRequest.php');
    return strpos($file, 'use App\Models\Payment') !== false &&
           strpos($file, 'Payment::CURRENCY_LYD') !== false &&
           strpos($file, 'Payment::CURRENCY_USD') !== false &&
           strpos($file, 'Payment::CURRENCY_EUR') !== false;
});

// Test 17: ActivityLogController uses ActivityLogFilter
test("ActivityLogController uses ActivityLogFilter", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/ActivityLogController.php');
    return strpos($file, 'use App\Filters\ActivityLogFilter') !== false &&
           strpos($file, 'ActivityLogFilter::apply') !== false;
});

// Test 18: ActivityLogController removed inline filtering
test("ActivityLogController removed inline filtering code", function() {
    $file = file_get_contents(__DIR__.'/../app/Http/Controllers/Web/ActivityLogController.php');
    // Should NOT have the old inline filtering
    return strpos($file, "if (\$request->filled('user_id'))") === false;
});

echo "\n";

// ============================================================================
// SUMMARY
// ============================================================================

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Tests Passed: {$tests_passed}\n";
echo "❌ Tests Failed: {$tests_failed}\n";
echo "📊 Total Tests:  " . ($tests_passed + $tests_failed) . "\n";
echo "\n";

if ($tests_failed === 0) {
    echo "🎉 ALL TESTS PASSED! All improvements implemented successfully.\n";
    exit(0);
} else {
    echo "⚠️  SOME TESTS FAILED. Please review the failures above.\n";
    exit(1);
}


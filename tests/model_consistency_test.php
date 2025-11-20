#!/usr/bin/env php
<?php

/**
 * Model Consistency Test Suite
 * Tests models for consistency with refactored database schema
 */

echo "=== MODEL CONSISTENCY TEST SUITE ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: QuotationItem calculateTotalPrice() has no float return type
echo "Test 1: QuotationItem::calculateTotalPrice() return type... ";
$quotationItemContent = file_get_contents('app/Models/QuotationItem.php');
if (strpos($quotationItemContent, 'public function calculateTotalPrice(): float') !== false) {
    echo "❌ FAIL (still has ': float' return type)\n";
    $failed++;
} else if (strpos($quotationItemContent, 'public function calculateTotalPrice()') !== false) {
    echo "✅ PASS (no float return type)\n";
    $passed++;
} else {
    echo "❌ FAIL (method not found)\n";
    $failed++;
}

// Test 2: OrderItem calculateTotalPrice() has no float return type
echo "Test 2: OrderItem::calculateTotalPrice() return type... ";
$orderItemContent = file_get_contents('app/Models/OrderItem.php');
if (strpos($orderItemContent, 'public function calculateTotalPrice(): float') !== false) {
    echo "❌ FAIL (still has ': float' return type)\n";
    $failed++;
} else if (strpos($orderItemContent, 'public function calculateTotalPrice()') !== false) {
    echo "✅ PASS (no float return type)\n";
    $passed++;
} else {
    echo "❌ FAIL (method not found)\n";
    $failed++;
}

// Test 3: Order has currency constants
echo "Test 3: Order has currency constants... ";
$orderContent = file_get_contents('app/Models/Order.php');
if (strpos($orderContent, 'const CURRENCY_LYD') !== false &&
    strpos($orderContent, 'const CURRENCY_USD') !== false &&
    strpos($orderContent, 'const CURRENCY_EUR') !== false) {
    echo "✅ PASS (all currency constants defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (currency constants missing)\n";
    $failed++;
}

// Test 4: Payment has currency constants
echo "Test 4: Payment has currency constants... ";
$paymentContent = file_get_contents('app/Models/Payment.php');
if (strpos($paymentContent, 'const CURRENCY_LYD') !== false &&
    strpos($paymentContent, 'const CURRENCY_USD') !== false &&
    strpos($paymentContent, 'const CURRENCY_EUR') !== false) {
    echo "✅ PASS (all currency constants defined)\n";
    $passed++;
} else {
    echo "❌ FAIL (currency constants missing)\n";
    $failed++;
}

// Test 5: Order uses constant for default currency
echo "Test 5: Order uses constant for default currency... ";
if (strpos($orderContent, "'currency' => self::CURRENCY_LYD") !== false ||
    strpos($orderContent, '"currency" => self::CURRENCY_LYD') !== false) {
    echo "✅ PASS (uses self::CURRENCY_LYD)\n";
    $passed++;
} else {
    echo "❌ FAIL (doesn't use constant)\n";
    $failed++;
}

// Test 6: Payment uses constant for default currency
echo "Test 6: Payment uses constant for default currency... ";
if (strpos($paymentContent, "'currency' => self::CURRENCY_LYD") !== false ||
    strpos($paymentContent, '"currency" => self::CURRENCY_LYD') !== false) {
    echo "✅ PASS (uses self::CURRENCY_LYD)\n";
    $passed++;
} else {
    echo "❌ FAIL (doesn't use constant)\n";
    $failed++;
}

// Test 7: Quotation casts total_price to decimal:2
echo "Test 7: Quotation casts total_price to decimal:2... ";
$quotationContent = file_get_contents('app/Models/Quotation.php');
if (strpos($quotationContent, "'total_price' => 'decimal:2'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 8: Order casts total_amount to decimal:2
echo "Test 8: Order casts total_amount to decimal:2... ";
if (strpos($orderContent, "'total_amount' => 'decimal:2'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 9: Invoice casts all financial fields to decimal:2
echo "Test 9: Invoice casts all financial fields to decimal:2... ";
$invoiceContent = file_get_contents('app/Models/Invoice.php');
if (strpos($invoiceContent, "'subtotal' => 'decimal:2'") !== false &&
    strpos($invoiceContent, "'tax' => 'decimal:2'") !== false &&
    strpos($invoiceContent, "'discount' => 'decimal:2'") !== false &&
    strpos($invoiceContent, "'total_amount' => 'decimal:2'") !== false) {
    echo "✅ PASS (all 4 fields use decimal:2)\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 10: Payment casts amount to decimal:2
echo "Test 10: Payment casts amount to decimal:2... ";
if (strpos($paymentContent, "'amount' => 'decimal:2'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 11: QuotationItem casts price fields to decimal:2
echo "Test 11: QuotationItem casts price fields to decimal:2... ";
if (strpos($quotationItemContent, "'unit_price' => 'decimal:2'") !== false &&
    strpos($quotationItemContent, "'total_price' => 'decimal:2'") !== false) {
    echo "✅ PASS\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 12: OrderItem casts all price fields to decimal:2
echo "Test 12: OrderItem casts all price fields to decimal:2... ";
if (strpos($orderItemContent, "'unit_price' => 'decimal:2'") !== false &&
    strpos($orderItemContent, "'subtotal' => 'decimal:2'") !== false &&
    strpos($orderItemContent, "'tax_amount' => 'decimal:2'") !== false &&
    strpos($orderItemContent, "'discount_amount' => 'decimal:2'") !== false &&
    strpos($orderItemContent, "'total_price' => 'decimal:2'") !== false) {
    echo "✅ PASS (all 5 fields use decimal:2)\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

// Test 13: Supplier doesn't have verification_file_path in fillable (check for actual array entry, not comment)
echo "Test 13: Supplier removed verification_file_path... ";
$supplierContent = file_get_contents('app/Models/Supplier.php');
// Look for actual fillable array entry (with comma), not just comment
if (preg_match("/'verification_file_path',/", $supplierContent) ||
    preg_match('/"verification_file_path",/', $supplierContent)) {
    echo "❌ FAIL (still in fillable array)\n";
    $failed++;
} else {
    echo "✅ PASS (removed from fillable)\n";
    $passed++;
}

// Test 14: Buyer doesn't have license_document in fillable (check for actual array entry, not comment)
echo "Test 14: Buyer removed license_document... ";
$buyerContent = file_get_contents('app/Models/Buyer.php');
// Look for actual fillable array entry (with comma), not just comment
if (preg_match("/'license_document',/", $buyerContent) ||
    preg_match('/"license_document",/', $buyerContent)) {
    echo "❌ FAIL (still in fillable array)\n";
    $failed++;
} else {
    echo "✅ PASS (removed from fillable)\n";
    $passed++;
}

// Test 15: Payment has auto-sync observer
echo "Test 15: Payment has auto-sync observer... ";
if (strpos($paymentContent, 'protected static function booted()') !== false &&
    strpos($paymentContent, 'static::creating') !== false &&
    strpos($paymentContent, '$payment->buyer_id = $order->buyer_id') !== false) {
    echo "✅ PASS (auto-sync observer present)\n";
    $passed++;
} else {
    echo "❌ FAIL\n";
    $failed++;
}

echo "\n=== TEST SUMMARY ===\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n\n";

if ($failed === 0) {
    echo "🎉 ALL TESTS PASSED! Models are consistent with refactored schema.\n";
    exit(0);
} else {
    echo "⚠️  Some tests failed. Please review the issues above.\n";
    exit(1);
}


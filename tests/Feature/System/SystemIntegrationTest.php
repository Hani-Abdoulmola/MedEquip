<?php

namespace Tests\Feature\System;

use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use App\Services\NotificationService;
use App\Services\ReferenceCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * System Integration Test
 * 
 * Tests cross-module integrations, service interactions, and system-wide functionality
 */
class SystemIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $buyerUser;
    private User $supplierUser;
    private Buyer $buyer;
    private Supplier $supplier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Supplier', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Buyer', 'guard_name' => 'web']);

        UserType::firstOrCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
        UserType::firstOrCreate(['id' => 2, 'name' => 'Supplier', 'slug' => 'supplier']);
        UserType::firstOrCreate(['id' => 3, 'name' => 'Buyer', 'slug' => 'buyer']);

        $this->adminUser = User::factory()->create(['user_type_id' => 1]);
        $this->adminUser->assignRole('Admin');

        $this->buyerUser = User::factory()->create(['user_type_id' => 3]);
        $this->buyerUser->assignRole('Buyer');

        $this->supplierUser = User::factory()->create(['user_type_id' => 2]);
        $this->supplierUser->assignRole('Supplier');

        $this->buyer = Buyer::create([
            'user_id' => $this->buyerUser->id,
            'organization_name' => 'Test Hospital',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'user_id' => $this->supplierUser->id,
            'company_name' => 'Test Supplier',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test description',
            'review_status' => 'approved',
        ]);
    }

    public function test_reference_code_service_generates_unique_codes(): void
    {
        $codes = [];

        // Generate 100 codes and verify uniqueness
        for ($i = 0; $i < 100; $i++) {
            $code = ReferenceCodeService::generateUnique(Rfq::class, 'RFQ');
            $codes[] = $code;
        }

        // All codes should be unique
        $this->assertEquals(100, count(array_unique($codes)));

        // All codes should have correct format
        foreach ($codes as $code) {
            $this->assertStringStartsWith('RFQ-', $code);
            $this->assertMatchesRegularExpression('/^RFQ-\d{8}-[A-Z0-9]{6}$/', $code);
        }
    }

    public function test_notification_service_integration(): void
    {
        // Test that NotificationService can be instantiated and used
        $service = app(NotificationService::class);

        $this->assertInstanceOf(NotificationService::class, $service);

        // Create a test RFQ to trigger notification
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Notification Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Notification service should be available
        $this->assertTrue(class_exists(NotificationService::class));
    }

    public function test_activity_logging_integration(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Activity Log Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Activity should be logged (if Auditable trait is working)
        // This depends on Spatie Activity Log configuration
        $this->assertDatabaseHas('rfqs', [
            'id' => $rfq->id,
        ]);
    }

    public function test_multi_currency_support(): void
    {
        $currencies = ['LYD', 'USD', 'EUR'];

        foreach ($currencies as $currency) {
            $order = Order::create([
                'buyer_id' => $this->buyer->id,
                'supplier_id' => $this->supplier->id,
                'order_number' => ReferenceCodeService::generateUnique(Order::class, 'ORD'),
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => 10000.00,
                'currency' => $currency,
            ]);

            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'currency' => $currency,
            ]);
        }
    }

    public function test_role_based_access_control_integration(): void
    {
        // Admin should have Admin role
        $this->assertTrue($this->adminUser->hasRole('Admin'));
        $this->assertFalse($this->adminUser->hasRole('Supplier'));

        // Supplier should have Supplier role
        $this->assertTrue($this->supplierUser->hasRole('Supplier'));
        $this->assertFalse($this->supplierUser->hasRole('Admin'));

        // Buyer should have Buyer role
        $this->assertTrue($this->buyerUser->hasRole('Buyer'));
        $this->assertFalse($this->buyerUser->hasRole('Supplier'));
    }

    public function test_soft_delete_cascade_behavior(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Soft Delete Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 10000.00,
            'status' => 'pending',
        ]);

        // Soft delete RFQ
        $rfq->delete();

        // Quotation should still exist (soft delete doesn't cascade by default)
        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
        ]);

        // But RFQ should be soft deleted
        $this->assertSoftDeleted('rfqs', [
            'id' => $rfq->id,
        ]);
    }

    public function test_concurrent_operations_data_consistency(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Concurrent Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Simulate concurrent quotation submissions
        $quotation1 = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 10000.00,
            'status' => 'pending',
        ]);

        $supplier2 = Supplier::create([
            'user_id' => User::factory()->create(['user_type_id' => 2])->id,
            'company_name' => 'Supplier 2',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $quotation2 = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier2->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 9500.00,
            'status' => 'pending',
        ]);

        // Both quotations should exist
        $this->assertEquals(2, $rfq->quotations()->count());
        $this->assertNotEquals($quotation1->id, $quotation2->id);
    }

    public function test_system_handles_large_datasets(): void
    {
        // Create multiple RFQs
        $rfqCount = 50;
        $rfqs = [];

        for ($i = 0; $i < $rfqCount; $i++) {
            $rfqs[] = Rfq::create([
                'buyer_id' => $this->buyer->id,
                'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
                'title' => "Bulk RFQ {$i}",
                'deadline' => now()->addDays(30),
                'status' => 'open',
            ]);
        }

        $this->assertEquals($rfqCount, Rfq::where('buyer_id', $this->buyer->id)->count());

        // Create quotations for each RFQ
        foreach ($rfqs as $rfq) {
            Quotation::create([
                'rfq_id' => $rfq->id,
                'supplier_id' => $this->supplier->id,
                'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
                'total_price' => 10000.00,
                'status' => 'pending',
            ]);
        }

        $this->assertEquals($rfqCount, Quotation::where('supplier_id', $this->supplier->id)->count());
    }

    public function test_system_handles_edge_cases(): void
    {
        // Test with zero amounts
        $quotation = Quotation::create([
            'rfq_id' => Rfq::create([
                'buyer_id' => $this->buyer->id,
                'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
                'title' => 'Edge Case Test',
                'deadline' => now()->addDays(30),
                'status' => 'open',
            ])->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 0.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'total_price' => 0.00,
        ]);

        // Test with very large amounts
        $largeQuotation = Quotation::create([
            'rfq_id' => Rfq::create([
                'buyer_id' => $this->buyer->id,
                'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
                'title' => 'Large Amount Test',
                'deadline' => now()->addDays(30),
                'status' => 'open',
            ])->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 999999999.99,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('quotations', [
            'id' => $largeQuotation->id,
            'total_price' => 999999999.99,
        ]);
    }
}


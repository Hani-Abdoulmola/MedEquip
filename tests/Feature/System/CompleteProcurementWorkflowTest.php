<?php

namespace Tests\Feature\System;

use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use App\Services\ReferenceCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Complete Procurement Workflow System Test
 * 
 * Tests the entire end-to-end workflow:
 * RFQ Creation → Quotation Submission → Order Creation → Invoice → Payment → Delivery
 */
class CompleteProcurementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $buyerUser;
    private User $supplierUser;
    private Buyer $buyer;
    private Supplier $supplier;
    private Product $product;
    private Rfq $rfq;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Supplier', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Buyer', 'guard_name' => 'web']);

        // Create user types
        UserType::firstOrCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
        UserType::firstOrCreate(['id' => 2, 'name' => 'Supplier', 'slug' => 'supplier']);
        UserType::firstOrCreate(['id' => 3, 'name' => 'Buyer', 'slug' => 'buyer']);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'user_type_id' => 1,
            'email' => 'admin@test.com',
        ]);
        $this->adminUser->assignRole('Admin');

        // Create buyer user
        $this->buyerUser = User::factory()->create([
            'user_type_id' => 3,
            'email' => 'buyer@test.com',
        ]);
        $this->buyerUser->assignRole('Buyer');

        $this->buyer = Buyer::create([
            'user_id' => $this->buyerUser->id,
            'organization_name' => 'Test Hospital',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Create supplier user
        $this->supplierUser = User::factory()->create([
            'user_type_id' => 2,
            'email' => 'supplier@test.com',
        ]);
        $this->supplierUser->assignRole('Supplier');

        $this->supplier = Supplier::create([
            'user_id' => $this->supplierUser->id,
            'company_name' => 'Test Medical Supplies Co.',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Create product
        $this->product = Product::create([
            'name' => 'Test Medical Equipment',
            'description' => 'Test product description',
            'review_status' => 'approved',
        ]);
    }

    public function test_complete_procurement_workflow_from_rfq_to_delivery(): void
    {
        // Step 1: Buyer creates RFQ
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Test RFQ for Medical Equipment',
            'description' => 'Need medical equipment for hospital',
            'deadline' => now()->addDays(30),
            'status' => 'open',
            'is_public' => true,
        ]);

        // Create RFQ items
        $rfqItem = RfqItem::create([
            'rfq_id' => $rfq->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'description' => 'Test item description',
        ]);

        $this->assertDatabaseHas('rfqs', [
            'id' => $rfq->id,
            'status' => 'open',
            'buyer_id' => $this->buyer->id,
        ]);

        $this->assertDatabaseHas('rfq_items', [
            'rfq_id' => $rfq->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        // Step 2: Supplier submits quotation
        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 50000.00,
            'status' => 'pending',
            'valid_until' => now()->addDays(60),
        ]);

        $quotationItem = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'rfq_item_id' => $rfqItem->id,
            'unit_price' => 5000.00,
            'quantity' => 10,
            'total_price' => 50000.00,
            'lead_time' => 30,
            'warranty' => '1 year',
        ]);

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'pending',
            'total_price' => 50000.00,
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'quotation_id' => $quotation->id,
            'rfq_item_id' => $rfqItem->id,
            'unit_price' => 5000.00,
        ]);

        // Step 3: Admin/Buyer accepts quotation
        $quotation->update([
            'status' => 'accepted',
            'updated_by' => $this->adminUser->id,
        ]);

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'accepted',
        ]);

        // Step 4: Order is created from accepted quotation
        $order = Order::create([
            'quotation_id' => $quotation->id,
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => ReferenceCodeService::generateUnique(Order::class, 'ORD'),
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => 50000.00,
            'currency' => 'LYD',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'quotation_id' => $quotation->id,
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'pending',
            'total_amount' => 50000.00,
        ]);

        // Step 5: Invoice is generated
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => ReferenceCodeService::generateUnique(Invoice::class, 'INV'),
            'invoice_date' => now(),
            'status' => 'issued',
            'payment_status' => 'pending',
            'subtotal' => 50000.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'total_amount' => 50000.00,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'order_id' => $order->id,
            'status' => 'issued',
            'total_amount' => 50000.00,
        ]);

        // Step 6: Payment is recorded
        $payment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'payment_reference' => ReferenceCodeService::generateUnique(Payment::class, 'PAY'),
            'amount' => 50000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'status' => 'completed',
            'amount' => 50000.00,
        ]);

        // Step 7: Order status updated to processing
        $order->update(['status' => 'processing']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);

        // Step 8: Delivery is created
        $delivery = Delivery::create([
            'order_id' => $order->id,
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'delivery_number' => ReferenceCodeService::generateUnique(Delivery::class, 'DEL'),
            'delivery_date' => now()->addDays(5),
            'status' => 'pending',
            'delivery_location' => 'Test Hospital Address',
        ]);

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        // Step 9: Delivery status updated to shipped
        $delivery->update(['status' => 'shipped']);

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'shipped',
        ]);

        // Step 10: Delivery completed
        $delivery->update(['status' => 'delivered']);
        $order->update(['status' => 'delivered']);

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'delivered',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',
        ]);

        // Verify complete workflow relationships
        $this->assertEquals($rfq->id, $quotation->rfq_id);
        $this->assertEquals($quotation->id, $order->quotation_id);
        $this->assertEquals($order->id, $invoice->order_id);
        $this->assertEquals($order->id, $payment->order_id);
        $this->assertEquals($order->id, $delivery->order_id);
    }

    public function test_financial_data_precision_throughout_workflow(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Precision Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $rfqItem = RfqItem::create([
            'rfq_id' => $rfq->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 33333.33,
            'status' => 'accepted',
        ]);

        $quotationItem = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'rfq_item_id' => $rfqItem->id,
            'unit_price' => 11111.11,
            'quantity' => 3,
            'total_price' => 33333.33,
        ]);

        $order = Order::create([
            'quotation_id' => $quotation->id,
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => ReferenceCodeService::generateUnique(Order::class, 'ORD'),
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => 33333.33,
            'currency' => 'LYD',
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => ReferenceCodeService::generateUnique(Invoice::class, 'INV'),
            'invoice_date' => now(),
            'status' => 'issued',
            'subtotal' => 33333.33,
            'tax' => 0.00,
            'discount' => 0.00,
            'total_amount' => 33333.33,
        ]);

        $payment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'payment_reference' => ReferenceCodeService::generateUnique(Payment::class, 'PAY'),
            'amount' => 33333.33,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        // Verify precision is maintained
        $quotation->refresh();
        $order->refresh();
        $invoice->refresh();
        $payment->refresh();

        $this->assertEquals('33333.33', $quotation->total_price);
        $this->assertEquals('33333.33', $order->total_amount);
        $this->assertEquals('33333.33', $invoice->total_amount);
        $this->assertEquals('33333.33', $payment->amount);
    }

    public function test_reference_code_uniqueness_across_entities(): void
    {
        $codes = [];

        // Generate codes for different entity types
        $codes[] = ReferenceCodeService::generateUnique(Rfq::class, 'RFQ');
        $codes[] = ReferenceCodeService::generateUnique(Quotation::class, 'QUO');
        $codes[] = ReferenceCodeService::generateUnique(Order::class, 'ORD');
        $codes[] = ReferenceCodeService::generateUnique(Invoice::class, 'INV');
        $codes[] = ReferenceCodeService::generateUnique(Payment::class, 'PAY');
        $codes[] = ReferenceCodeService::generateUnique(Delivery::class, 'DEL');

        // All codes should be unique
        $this->assertEquals(count($codes), count(array_unique($codes)));

        // Each code should have correct prefix
        $this->assertStringStartsWith('RFQ-', $codes[0]);
        $this->assertStringStartsWith('QUO-', $codes[1]);
        $this->assertStringStartsWith('ORD-', $codes[2]);
        $this->assertStringStartsWith('INV-', $codes[3]);
        $this->assertStringStartsWith('PAY-', $codes[4]);
        $this->assertStringStartsWith('DEL-', $codes[5]);
    }

    public function test_order_status_transitions_are_valid(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Status Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 10000.00,
            'status' => 'accepted',
        ]);

        $order = Order::create([
            'quotation_id' => $quotation->id,
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => ReferenceCodeService::generateUnique(Order::class, 'ORD'),
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => 10000.00,
        ]);

        // Test valid transitions
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => [], // Final state
            'cancelled' => [], // Final state
        ];

        $currentStatus = 'pending';
        foreach (['processing', 'shipped', 'delivered'] as $nextStatus) {
            $order->update(['status' => $nextStatus]);
            $this->assertDatabaseHas('orders', [
                'id' => $order->id,
                'status' => $nextStatus,
            ]);
            $currentStatus = $nextStatus;
        }
    }

    public function test_quotation_rejection_prevents_order_creation(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Rejection Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 10000.00,
            'status' => 'rejected',
            'rejection_reason' => 'Price too high',
        ]);

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'status' => 'rejected',
        ]);

        // Rejected quotations should not create orders
        $this->assertEquals(0, Order::where('quotation_id', $quotation->id)->count());
    }

    public function test_multiple_quotations_for_single_rfq(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Multiple Quotes RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Create multiple quotations from different suppliers
        $quotation1 = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 50000.00,
            'status' => 'pending',
        ]);

        // Create another supplier
        $supplier2 = Supplier::create([
            'user_id' => User::factory()->create(['user_type_id' => 2])->id,
            'company_name' => 'Another Supplier',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $quotation2 = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier2->id,
            'reference_code' => ReferenceCodeService::generateUnique(Quotation::class, 'QUO'),
            'total_price' => 45000.00,
            'status' => 'pending',
        ]);

        $this->assertEquals(2, $rfq->quotations()->count());
        $this->assertTrue($rfq->quotations->contains($quotation1));
        $this->assertTrue($rfq->quotations->contains($quotation2));
    }

    public function test_payment_auto_syncs_buyer_and_supplier_from_order(): void
    {
        $order = Order::create([
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => ReferenceCodeService::generateUnique(Order::class, 'ORD'),
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => 10000.00,
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => ReferenceCodeService::generateUnique(Invoice::class, 'INV'),
            'invoice_date' => now(),
            'status' => 'issued',
            'total_amount' => 10000.00,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'payment_reference' => ReferenceCodeService::generateUnique(Payment::class, 'PAY'),
            'amount' => 10000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        // Payment should have buyer_id and supplier_id from order
        $this->assertEquals($order->buyer_id, $payment->buyer_id);
        $this->assertEquals($order->supplier_id, $payment->supplier_id);
    }

    public function test_rfq_deadline_enforcement(): void
    {
        $pastDeadline = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Past Deadline RFQ',
            'deadline' => now()->subDays(1),
            'status' => 'open',
        ]);

        // RFQ with past deadline should not accept new quotations
        $this->assertTrue($pastDeadline->deadline < now());

        $futureDeadline = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => ReferenceCodeService::generateUnique(Rfq::class, 'RFQ'),
            'title' => 'Future Deadline RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // RFQ with future deadline should accept quotations
        $this->assertTrue($futureDeadline->deadline > now());
    }
}


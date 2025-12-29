<?php

namespace Tests\Feature\Suppliers;

use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $supplierUser;
    private Supplier $supplier;
    private User $otherSupplierUser;
    private Supplier $otherSupplier;
    private Buyer $buyer;
    private Order $order;
    private Invoice $invoice;

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

        // Create supplier user
        $this->supplierUser = User::factory()->create([
            'user_type_id' => 2, // Supplier
        ]);
        $this->supplierUser->assignRole('Supplier');

        $this->supplier = Supplier::create([
            'user_id' => $this->supplierUser->id,
            'company_name' => 'Test Supplier Company',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Create other supplier (for authorization tests)
        $this->otherSupplierUser = User::factory()->create([
            'user_type_id' => 2,
            'email' => 'other@supplier.com',
        ]);
        $this->otherSupplierUser->assignRole('Supplier');

        $this->otherSupplier = Supplier::create([
            'user_id' => $this->otherSupplierUser->id,
            'company_name' => 'Other Supplier Company',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Create buyer
        $buyerUser = User::factory()->create([
            'user_type_id' => 3, // Buyer
            'email' => 'buyer@test.com',
        ]);
        $buyerUser->assignRole('Buyer');

        $this->buyer = Buyer::create([
            'user_id' => $buyerUser->id,
            'organization_name' => 'Test Buyer Organization',
        ]);

        // Create order
        $this->order = Order::create([
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => 'ORD-001',
            'order_date' => now(),
            'status' => 'delivered',
            'total_amount' => 10000.00,
            'currency' => 'LYD',
        ]);

        // Create invoice
        $this->invoice = Invoice::create([
            'order_id' => $this->order->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now(),
            'status' => 'approved',
            'payment_status' => 'partial',
            'subtotal' => 10000.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'total_amount' => 10000.00,
        ]);
    }

    public function test_supplier_can_view_payments_index(): void
    {
        // Create payments for the supplier
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'invoice_id' => $this->invoice->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index'));

        $response->assertOk();
        $response->assertViewIs('supplier.payments.index');
        $response->assertViewHas('payments');
        $response->assertViewHas('stats');
    }

    public function test_supplier_can_only_see_their_own_payments(): void
    {
        // Create payment for this supplier
        $myPayment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-MINE',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        // Create payment for other supplier
        $otherOrder = Order::create([
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->otherSupplier->id,
            'order_number' => 'ORD-002',
            'order_date' => now(),
            'status' => 'delivered',
            'total_amount' => 20000.00,
        ]);

        Payment::create([
            'supplier_id' => $this->otherSupplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $otherOrder->id,
            'payment_reference' => 'PAY-OTHER',
            'amount' => 20000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index'));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        // Should only see own payment
        $this->assertCount(1, $payments);
        $this->assertEquals('PAY-MINE', $payments->first()->payment_reference);
    }

    public function test_supplier_can_view_payment_details(): void
    {
        $payment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'invoice_id' => $this->invoice->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'completed',
            'transaction_id' => 'TXN-123',
            'paid_at' => now(),
            'notes' => 'Test payment notes',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.show', $payment));

        $response->assertOk();
        $response->assertViewIs('supplier.payments.show');
        $response->assertViewHas('payment');
        $response->assertViewHas('receipts');
        
        $viewPayment = $response->viewData('payment');
        $this->assertEquals($payment->id, $viewPayment->id);
        $this->assertEquals('PAY-001', $viewPayment->payment_reference);
    }

    public function test_supplier_cannot_view_other_supplier_payment(): void
    {
        $otherOrder = Order::create([
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->otherSupplier->id,
            'order_number' => 'ORD-002',
            'order_date' => now(),
            'status' => 'delivered',
            'total_amount' => 20000.00,
        ]);

        $otherPayment = Payment::create([
            'supplier_id' => $this->otherSupplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $otherOrder->id,
            'payment_reference' => 'PAY-OTHER',
            'amount' => 20000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.show', $otherPayment));

        $response->assertForbidden();
    }

    public function test_payments_index_shows_correct_stats(): void
    {
        // Create payments with different statuses
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 3000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-003',
            'amount' => 2000.00,
            'currency' => 'LYD',
            'method' => 'credit_card',
            'status' => 'failed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index'));

        $response->assertOk();
        $stats = $response->viewData('stats');

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(10000.00, $stats['total_amount']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(5000.00, $stats['completed_amount']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['failed']);
    }

    public function test_payments_can_be_filtered_by_status(): void
    {
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 3000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index', ['status' => 'completed']));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(1, $payments);
        $this->assertEquals('completed', $payments->first()->status);
    }

    public function test_payments_can_be_filtered_by_method(): void
    {
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 3000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index', ['method' => 'cash']));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(1, $payments);
        $this->assertEquals('cash', $payments->first()->method);
    }

    public function test_payments_can_be_filtered_by_currency(): void
    {
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 1000.00,
            'currency' => 'USD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index', ['currency' => 'USD']));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(1, $payments);
        $this->assertEquals('USD', $payments->first()->currency);
    }

    public function test_payments_can_be_filtered_by_date_range(): void
    {
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
            'paid_at' => now()->subDays(5),
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 3000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
            'paid_at' => now()->subDays(15),
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index', [
                'date_from' => now()->subDays(10)->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ]));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(1, $payments);
        $this->assertEquals('PAY-001', $payments->first()->payment_reference);
    }

    public function test_payments_can_be_searched(): void
    {
        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
            'transaction_id' => 'TXN-123',
        ]);

        Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-002',
            'amount' => 3000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
            'transaction_id' => 'TXN-456',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index', ['search' => 'TXN-123']));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(1, $payments);
        $this->assertEquals('PAY-001', $payments->first()->payment_reference);
    }

    public function test_payments_index_shows_empty_state_when_no_payments(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index'));

        $response->assertOk();
        $payments = $response->viewData('payments');
        
        $this->assertCount(0, $payments);
        $this->assertTrue($payments->isEmpty());
    }

    public function test_unauthenticated_user_cannot_access_payments(): void
    {
        $response = $this->get(route('supplier.payments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_supplier_profile_cannot_access_payments(): void
    {
        $userWithoutProfile = User::factory()->create([
            'user_type_id' => 2,
            'email' => 'noprofile@test.com',
        ]);
        $userWithoutProfile->assignRole('Supplier');

        $response = $this->actingAs($userWithoutProfile)
            ->get(route('supplier.payments.index'));

        $response->assertForbidden();
    }

    public function test_payment_show_includes_related_order_and_invoice(): void
    {
        $payment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'invoice_id' => $this->invoice->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'bank_transfer',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.show', $payment));

        $response->assertOk();
        $viewPayment = $response->viewData('payment');
        
        $this->assertNotNull($viewPayment->order);
        $this->assertNotNull($viewPayment->invoice);
        $this->assertNotNull($viewPayment->buyer);
        $this->assertEquals($this->order->id, $viewPayment->order->id);
        $this->assertEquals($this->invoice->id, $viewPayment->invoice->id);
    }

    public function test_activity_is_logged_when_viewing_payments_index(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.index'));

        $response->assertOk();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'supplier_payments',
            'description' => 'عرض المورد قائمة المدفوعات',
            'causer_id' => $this->supplierUser->id,
        ]);
    }

    public function test_activity_is_logged_when_viewing_payment_details(): void
    {
        $payment = Payment::create([
            'supplier_id' => $this->supplier->id,
            'buyer_id' => $this->buyer->id,
            'order_id' => $this->order->id,
            'payment_reference' => 'PAY-001',
            'amount' => 5000.00,
            'currency' => 'LYD',
            'method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('supplier.payments.show', $payment));

        $response->assertOk();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'supplier_payments',
            'description' => 'عرض المورد تفاصيل الدفعة: PAY-001',
            'causer_id' => $this->supplierUser->id,
            'subject_id' => $payment->id,
            'subject_type' => Payment::class,
        ]);
    }
}


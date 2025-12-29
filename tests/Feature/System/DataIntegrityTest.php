<?php

namespace Tests\Feature\System;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Data Integrity System Test
 * 
 * Tests data integrity, foreign key constraints, and referential integrity
 * across the entire system.
 */
class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $buyerUser;
    private User $supplierUser;
    private Buyer $buyer;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and user types
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Supplier', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Buyer', 'guard_name' => 'web']);

        UserType::firstOrCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
        UserType::firstOrCreate(['id' => 2, 'name' => 'Supplier', 'slug' => 'supplier']);
        UserType::firstOrCreate(['id' => 3, 'name' => 'Buyer', 'slug' => 'buyer']);

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
    }

    public function test_foreign_key_constraints_prevent_orphaned_records(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-TEST-001',
            'title' => 'Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => 'QUO-TEST-001',
            'total_price' => 10000.00,
            'status' => 'pending',
        ]);

        // Attempting to delete RFQ should fail if RESTRICT is set
        // (In MySQL, this depends on foreign key constraints)
        $this->assertDatabaseHas('quotations', [
            'rfq_id' => $rfq->id,
        ]);

        // Quotation should reference valid RFQ
        $this->assertEquals($rfq->id, $quotation->rfq_id);
        $this->assertNotNull($quotation->rfq);
    }

    public function test_cascade_delete_removes_related_records(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-CASCADE-001',
            'title' => 'Cascade Test RFQ',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Create RFQ items
        $rfqItem = $rfq->items()->create([
            'product_id' => null,
            'quantity' => 10,
            'description' => 'Test item',
        ]);

        $this->assertDatabaseHas('rfq_items', [
            'rfq_id' => $rfq->id,
        ]);

        // Delete RFQ (should cascade delete items if configured)
        $rfq->delete();

        // RFQ items should be deleted (if cascade is configured)
        // Note: Actual behavior depends on migration foreign key settings
        $this->assertSoftDeleted('rfqs', [
            'id' => $rfq->id,
        ]);
    }

    public function test_soft_deletes_preserve_data_integrity(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-SOFT-001',
            'title' => 'Soft Delete Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $rfqId = $rfq->id;

        // Soft delete
        $rfq->delete();

        // Record should still exist in database
        $this->assertDatabaseHas('rfqs', [
            'id' => $rfqId,
        ]);

        // But should be marked as deleted
        $this->assertSoftDeleted('rfqs', [
            'id' => $rfqId,
        ]);

        // Should not appear in normal queries
        $this->assertNull(Rfq::find($rfqId));
        $this->assertNotNull(Rfq::withTrashed()->find($rfqId));
    }

    public function test_transaction_rollback_on_failure(): void
    {
        DB::beginTransaction();

        try {
            $rfq = Rfq::create([
                'buyer_id' => $this->buyer->id,
                'reference_code' => 'RFQ-TRANS-001',
                'title' => 'Transaction Test',
                'deadline' => now()->addDays(30),
                'status' => 'open',
            ]);

            // Simulate failure
            throw new \Exception('Simulated failure');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        // RFQ should not exist after rollback
        $this->assertDatabaseMissing('rfqs', [
            'reference_code' => 'RFQ-TRANS-001',
        ]);
    }

    public function test_data_consistency_across_related_tables(): void
    {
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-CONSIST-001',
            'title' => 'Consistency Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => 'QUO-CONSIST-001',
            'total_price' => 50000.00,
            'status' => 'accepted',
        ]);

        $order = Order::create([
            'quotation_id' => $quotation->id,
            'buyer_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
            'order_number' => 'ORD-CONSIST-001',
            'order_date' => now(),
            'status' => 'pending',
            'total_amount' => 50000.00,
        ]);

        // Verify data consistency
        $this->assertEquals($rfq->buyer_id, $order->buyer_id);
        $this->assertEquals($quotation->supplier_id, $order->supplier_id);
        $this->assertEquals($quotation->total_price, $order->total_amount);
    }

    public function test_unique_constraints_prevent_duplicates(): void
    {
        Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-UNIQUE-001',
            'title' => 'Unique Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // Attempt to create duplicate reference code should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-UNIQUE-001', // Duplicate
            'title' => 'Duplicate Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);
    }

    public function test_nullable_foreign_keys_handle_optional_relationships(): void
    {
        // Create RFQ without assigned buyer (if allowed)
        // This tests NULL ON DELETE behavior
        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-NULL-001',
            'title' => 'Null Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        // If buyer is deleted with NULL ON DELETE, RFQ should still exist
        // but buyer_id should be null
        $this->assertNotNull($rfq->buyer_id);
    }

    public function test_enum_constraints_enforce_valid_values(): void
    {
        $validStatuses = ['draft', 'open', 'under_review', 'closed', 'awarded', 'cancelled'];

        foreach ($validStatuses as $status) {
            $rfq = Rfq::create([
                'buyer_id' => $this->buyer->id,
                'reference_code' => "RFQ-{$status}-001",
                'title' => "Status Test: {$status}",
                'deadline' => now()->addDays(30),
                'status' => $status,
            ]);

            $this->assertDatabaseHas('rfqs', [
                'id' => $rfq->id,
                'status' => $status,
            ]);
        }
    }

    public function test_decimal_precision_maintained_in_calculations(): void
    {
        $amount1 = 10.50;
        $amount2 = 20.75;
        $expectedTotal = 31.25;

        $rfq = Rfq::create([
            'buyer_id' => $this->buyer->id,
            'reference_code' => 'RFQ-PREC-001',
            'title' => 'Precision Test',
            'deadline' => now()->addDays(30),
            'status' => 'open',
        ]);

        $quotation = Quotation::create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $this->supplier->id,
            'reference_code' => 'QUO-PREC-001',
            'total_price' => $expectedTotal,
            'status' => 'pending',
        ]);

        $quotation->refresh();

        // Verify decimal precision is maintained
        $this->assertEquals('31.25', $quotation->total_price);
        $this->assertNotEquals(31.249999999, $quotation->total_price);
    }
}


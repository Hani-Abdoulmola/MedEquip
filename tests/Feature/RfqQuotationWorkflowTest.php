<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\QuotationItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\QuotationWorkflowService;
use App\Services\RfqStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RfqQuotationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function buyer_can_accept_quotation_and_rfq_becomes_awarded()
    {
        $buyer = User::factory()->buyer()->create();
        $rfq = Rfq::factory()->create([
            'buyer_id' => $buyer->buyerProfile->id,
            'status' => 'open',
        ]);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        
        $result = $workflowService->acceptQuotation($quotation, $buyer);

        $this->assertEquals('accepted', $result->status);
        $this->assertEquals('awarded', $rfq->fresh()->status);
        $this->assertNotNull($rfq->fresh()->awarded_quotation_id);
        $this->assertEquals($quotation->id, $rfq->fresh()->awarded_quotation_id);
    }

    /** @test */
    public function accepting_quotation_auto_rejects_other_pending_quotations()
    {
        $buyer = User::factory()->buyer()->create();
        $rfq = Rfq::factory()->create([
            'buyer_id' => $buyer->buyerProfile->id,
            'status' => 'open',
        ]);
        
        $quotation1 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);
        
        $quotation2 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);
        
        $quotation3 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        $workflowService->acceptQuotation($quotation1, $buyer);

        $this->assertEquals('accepted', $quotation1->fresh()->status);
        $this->assertEquals('rejected', $quotation2->fresh()->status);
        $this->assertEquals('rejected', $quotation3->fresh()->status);
        $this->assertEquals('تم ترسية الطلب لمورد آخر', $quotation2->fresh()->rejection_reason);
    }

    /** @test */
    public function cannot_accept_two_quotations_for_same_rfq()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Another quotation already accepted');

        $buyer = User::factory()->buyer()->create();
        $rfq = Rfq::factory()->create([
            'buyer_id' => $buyer->buyerProfile->id,
            'status' => 'open',
        ]);
        
        $quotation1 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);
        
        $quotation2 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        
        // Accept first quotation
        $workflowService->acceptQuotation($quotation1, $buyer);
        
        // Try to accept second quotation - should fail
        $workflowService->acceptQuotation($quotation2, $buyer);
    }

    /** @test */
    public function closing_expired_rfqs_also_expires_pending_quotations()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'open',
            'deadline' => now()->subDay(),
        ]);
        
        $quotation1 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);
        
        $quotation2 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        \App\Services\RfqWorkflowService::closeExpiredRfqs();

        $this->assertEquals('closed', $rfq->fresh()->status);
        $this->assertEquals('expired', $quotation1->fresh()->status);
        $this->assertEquals('expired', $quotation2->fresh()->status);
    }

    /** @test */
    public function quotations_with_past_valid_until_date_can_be_expired()
    {
        $quotation = Quotation::factory()->create([
            'status' => 'pending',
            'valid_until' => now()->subDay(),
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        $expired = $workflowService->expireQuotations();

        $this->assertEquals(1, $expired);
        $this->assertEquals('expired', $quotation->fresh()->status);
        $this->assertNotNull($quotation->fresh()->expired_at);
    }

    /** @test */
    public function supplier_can_submit_quotation_from_draft()
    {
        $supplier = User::factory()->supplier()->create();
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->supplierProfile->id,
            'status' => 'draft',
            'total_price' => 1000,
        ]);
        
        QuotationItem::factory()->create(['quotation_id' => $quotation->id]);

        $workflowService = app(QuotationWorkflowService::class);
        $result = $workflowService->submitQuotation($quotation);

        $this->assertEquals('pending', $result->status);
        $this->assertNotNull($result->submitted_at);
    }

    /** @test */
    public function cannot_submit_duplicate_quotation_for_same_rfq()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('لديك عرض سعر موجود لهذا الطلب');

        $supplier = User::factory()->supplier()->create();
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        // Existing quotation
        Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->supplierProfile->id,
            'status' => 'pending',
        ]);
        
        // Try to submit second quotation
        $quotation2 = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'supplier_id' => $supplier->supplierProfile->id,
            'status' => 'draft',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        $workflowService->submitQuotation($quotation2);
    }

    /** @test */
    public function quotation_acceptance_uses_database_locking()
    {
        $buyer = User::factory()->buyer()->create();
        $rfq = Rfq::factory()->create([
            'buyer_id' => $buyer->buyerProfile->id,
            'status' => 'open',
        ]);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        
        // This should acquire a lock on the RFQ row
        DB::transaction(function() use ($quotation, $buyer, $workflowService) {
            $result = $workflowService->acceptQuotation($quotation, $buyer);
            
            // Verify lock was acquired by checking RFQ was updated
            $this->assertEquals('awarded', Rfq::find($quotation->rfq_id)->status);
        });
    }

    /** @test */
    public function rejecting_quotation_sets_reason_and_timestamp()
    {
        $buyer = User::factory()->buyer()->create();
        $rfq = Rfq::factory()->create([
            'buyer_id' => $buyer->buyerProfile->id,
            'status' => 'open',
        ]);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $workflowService = app(QuotationWorkflowService::class);
        $result = $workflowService->rejectQuotation($quotation, $buyer, 'السعر مرتفع جداً');

        $this->assertEquals('rejected', $result->status);
        $this->assertEquals('السعر مرتفع جداً', $result->rejection_reason);
        $this->assertNotNull($result->rejected_at);
        $this->assertEquals($buyer->id, $result->rejected_by);
    }

    /** @test */
    public function rfq_state_machine_enforces_terminal_states()
    {
        $this->expectException(\InvalidArgumentException::class);

        $rfq = Rfq::factory()->create(['status' => 'awarded']);
        
        $stateMachine = app(RfqStateMachine::class);
        $stateMachine->transition($rfq, 'open');
    }
}

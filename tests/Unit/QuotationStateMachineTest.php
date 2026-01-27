<?php

namespace Tests\Unit;

use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\QuotationItem;
use App\Services\QuotationStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private QuotationStateMachine $stateMachine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = new QuotationStateMachine();
    }

    /** @test */
    public function can_transition_from_draft_to_pending_when_valid()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'draft',
            'total_price' => 1000,
        ]);
        
        QuotationItem::factory()->create(['quotation_id' => $quotation->id]);

        $this->assertTrue($this->stateMachine->canTransition($quotation, 'pending'));
    }

    /** @test */
    public function cannot_submit_quotation_when_rfq_is_closed()
    {
        $rfq = Rfq::factory()->create(['status' => 'closed']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'draft',
            'total_price' => 1000,
        ]);
        
        QuotationItem::factory()->create(['quotation_id' => $quotation->id]);

        $this->assertFalse($this->stateMachine->canTransition($quotation, 'pending'));
    }

    /** @test */
    public function cannot_submit_quotation_without_items()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'draft',
            'total_price' => 1000,
        ]);

        $this->assertFalse($this->stateMachine->canTransition($quotation, 'pending'));
    }

    /** @test */
    public function can_transition_from_pending_to_accepted()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($this->stateMachine->canTransition($quotation, 'accepted'));
    }

    /** @test */
    public function cannot_accept_quotation_when_rfq_is_awarded()
    {
        $rfq = Rfq::factory()->create(['status' => 'awarded']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->assertFalse($this->stateMachine->canTransition($quotation, 'accepted'));
    }

    /** @test */
    public function cannot_accept_when_another_quotation_already_accepted()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        // First accepted quotation
        Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'accepted',
        ]);
        
        // Second pending quotation
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->assertFalse($this->stateMachine->canTransition($quotation, 'accepted'));
    }

    /** @test */
    public function can_transition_from_pending_to_rejected()
    {
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->assertTrue($this->stateMachine->canTransition($quotation, 'rejected'));
    }

    /** @test */
    public function can_transition_from_pending_to_expired()
    {
        $quotation = Quotation::factory()->create(['status' => 'pending']);

        $this->assertTrue($this->stateMachine->canTransition($quotation, 'expired'));
    }

    /** @test */
    public function terminal_states_cannot_transition()
    {
        $terminalStates = ['rejected', 'expired', 'withdrawn', 'converted'];

        foreach ($terminalStates as $status) {
            $quotation = Quotation::factory()->create(['status' => $status]);

            $this->assertTrue($this->stateMachine->isTerminal($quotation));
            $this->assertEmpty($this->stateMachine->getAllowedTransitions($quotation));
        }
    }

    /** @test */
    public function can_edit_draft_quotations()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'draft',
        ]);

        $this->assertTrue($this->stateMachine->canEdit($quotation));
    }

    /** @test */
    public function can_edit_pending_quotations_before_deadline()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'open',
            'deadline' => now()->addDays(7),
        ]);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($this->stateMachine->canEdit($quotation));
    }

    /** @test */
    public function cannot_edit_pending_quotations_after_deadline()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'open',
            'deadline' => now()->subDay(),
        ]);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->assertFalse($this->stateMachine->canEdit($quotation));
    }

    /** @test */
    public function cannot_edit_accepted_quotations()
    {
        $quotation = Quotation::factory()->create(['status' => 'accepted']);

        $this->assertFalse($this->stateMachine->canEdit($quotation));
    }

    /** @test */
    public function transition_sets_submitted_at_when_going_to_pending()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'draft',
            'total_price' => 1000,
        ]);
        
        QuotationItem::factory()->create(['quotation_id' => $quotation->id]);

        $this->stateMachine->transition($quotation, 'pending');

        $quotation->refresh();
        $this->assertEquals('pending', $quotation->status);
        $this->assertNotNull($quotation->submitted_at);
    }

    /** @test */
    public function transition_sets_accepted_at_and_accepted_by_when_accepting()
    {
        $this->actingAs(\App\Models\User::factory()->buyer()->create());
        
        $rfq = Rfq::factory()->create(['status' => 'open']);
        
        $quotation = Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'pending',
        ]);

        $this->stateMachine->transition($quotation, 'accepted', [
            'accepted_by' => auth()->id(),
        ]);

        $quotation->refresh();
        $this->assertEquals('accepted', $quotation->status);
        $this->assertNotNull($quotation->accepted_at);
        $this->assertEquals(auth()->id(), $quotation->accepted_by);
    }
}

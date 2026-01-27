<?php

namespace Tests\Unit;

use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\Quotation;
use App\Models\Buyer;
use App\Services\RfqStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfqStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private RfqStateMachine $stateMachine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = new RfqStateMachine();
    }

    /** @test */
    public function can_transition_from_draft_to_open_when_valid()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'draft',
            'title' => 'Test RFQ',
            'deadline' => now()->addDays(7),
        ]);
        
        RfqItem::factory()->create(['rfq_id' => $rfq->id]);

        $this->assertTrue($this->stateMachine->canTransition($rfq, 'open'));
    }

    /** @test */
    public function cannot_transition_from_draft_to_open_without_items()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'draft',
            'title' => 'Test RFQ',
        ]);

        $this->assertFalse($this->stateMachine->canTransition($rfq, 'open'));
    }

    /** @test */
    public function cannot_transition_from_draft_to_open_with_past_deadline()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'draft',
            'title' => 'Test RFQ',
            'deadline' => now()->subDay(),
        ]);
        
        RfqItem::factory()->create(['rfq_id' => $rfq->id]);

        $this->assertFalse($this->stateMachine->canTransition($rfq, 'open'));
    }

    /** @test */
    public function can_transition_from_open_to_closed()
    {
        $rfq = Rfq::factory()->create(['status' => 'open']);

        $this->assertTrue($this->stateMachine->canTransition($rfq, 'closed'));
    }

    /** @test */
    public function can_transition_from_closed_to_awarded_when_quotation_accepted()
    {
        $rfq = Rfq::factory()->create(['status' => 'closed']);
        
        Quotation::factory()->create([
            'rfq_id' => $rfq->id,
            'status' => 'accepted',
        ]);

        $this->assertTrue($this->stateMachine->canTransition($rfq, 'awarded'));
    }

    /** @test */
    public function cannot_transition_from_closed_to_awarded_without_accepted_quotation()
    {
        $rfq = Rfq::factory()->create(['status' => 'closed']);

        $this->assertFalse($this->stateMachine->canTransition($rfq, 'awarded'));
    }

    /** @test */
    public function cannot_transition_from_awarded_to_any_state()
    {
        $rfq = Rfq::factory()->create(['status' => 'awarded']);

        $this->assertFalse($this->stateMachine->canTransition($rfq, 'open'));
        $this->assertFalse($this->stateMachine->canTransition($rfq, 'closed'));
        $this->assertFalse($this->stateMachine->canTransition($rfq, 'draft'));
        $this->assertTrue($this->stateMachine->isTerminal($rfq));
    }

    /** @test */
    public function cannot_transition_from_cancelled_to_any_state()
    {
        $rfq = Rfq::factory()->create(['status' => 'cancelled']);

        $this->assertFalse($this->stateMachine->canTransition($rfq, 'open'));
        $this->assertFalse($this->stateMachine->canTransition($rfq, 'draft'));
        $this->assertTrue($this->stateMachine->isTerminal($rfq));
    }

    /** @test */
    public function transition_sets_appropriate_timestamps()
    {
        $rfq = Rfq::factory()->create(['status' => 'draft', 'title' => 'Test']);
        RfqItem::factory()->create(['rfq_id' => $rfq->id]);

        $this->stateMachine->transition($rfq, 'open');

        $rfq->refresh();
        $this->assertEquals('open', $rfq->status);
        $this->assertNotNull($rfq->published_at);
    }

    /** @test */
    public function get_allowed_transitions_returns_correct_states()
    {
        $rfq = Rfq::factory()->create(['status' => 'draft', 'title' => 'Test']);
        RfqItem::factory()->create(['rfq_id' => $rfq->id]);

        $allowed = $this->stateMachine->getAllowedTransitions($rfq);

        $this->assertContains('open', $allowed);
        $this->assertContains('cancelled', $allowed);
        $this->assertCount(2, $allowed);
    }

    /** @test */
    public function transition_throws_exception_for_invalid_transition()
    {
        $this->expectException(\InvalidArgumentException::class);

        $rfq = Rfq::factory()->create(['status' => 'awarded']);
        $this->stateMachine->transition($rfq, 'open');
    }

    /** @test */
    public function can_accept_quotations_only_when_open_and_before_deadline()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'open',
            'deadline' => now()->addDays(7),
        ]);

        $this->assertTrue($this->stateMachine->canAcceptQuotations($rfq));
    }

    /** @test */
    public function cannot_accept_quotations_when_deadline_passed()
    {
        $rfq = Rfq::factory()->create([
            'status' => 'open',
            'deadline' => now()->subDay(),
        ]);

        $this->assertFalse($this->stateMachine->canAcceptQuotations($rfq));
    }

    /** @test */
    public function cannot_accept_quotations_when_closed()
    {
        $rfq = Rfq::factory()->create(['status' => 'closed']);

        $this->assertFalse($this->stateMachine->canAcceptQuotations($rfq));
    }
}

<?php

namespace Database\Factories;

use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rfq_id' => Rfq::factory(),
            'supplier_id' => Supplier::factory(),
            'reference_code' => 'QUO-' . fake()->unique()->numerify('######'),
            'total_price' => fake()->randomFloat(2, 1000, 50000),
            'terms' => fake()->paragraph(),
            'status' => 'draft',
            'valid_until' => fake()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'submitted_at' => now()->subDay(),
            'accepted_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'submitted_at' => now()->subDay(),
            'rejected_at' => now(),
            'rejection_reason' => 'Test rejection',
        ]);
    }
}

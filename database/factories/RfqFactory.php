<?php

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'buyer_id' => Buyer::factory(),
            'created_by' => User::factory()->buyer(),
            'reference_code' => 'RFQ-' . fake()->unique()->numerify('######'),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'deadline' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'status' => 'draft',
            'is_public' => true,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'published_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function awarded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'awarded',
            'awarded_at' => now(),
            'closed_at' => now(),
        ]);
    }
}

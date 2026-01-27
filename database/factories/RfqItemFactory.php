<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Rfq;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfqItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rfq_id' => Rfq::factory(),
            'product_id' => Product::factory(),
            'item_name' => fake()->words(3, true),
            'specifications' => fake()->sentence(),
            'quantity' => fake()->numberBetween(1, 100),
            'unit' => 'وحدة',
            'is_approved' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\RfqItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 100);
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        
        return [
            'quotation_id' => Quotation::factory(),
            'rfq_item_id' => RfqItem::factory(),
            'product_id' => Product::factory(),
            'item_name' => fake()->words(3, true),
            'specifications' => fake()->sentence(),
            'quantity' => $quantity,
            'unit' => 'وحدة',
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'lead_time' => fake()->numberBetween(1, 30),
            'warranty' => fake()->randomElement(['1 year', '2 years', '3 years', 'No warranty']),
        ];
    }
}

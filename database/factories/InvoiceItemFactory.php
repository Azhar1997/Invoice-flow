<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'position' => 1,
            'description' => fake()->sentence(3),
            'quantity' => 1,
            'unit_price_amount' => 10000,
            'line_total_amount' => 10000,
        ];
    }
}

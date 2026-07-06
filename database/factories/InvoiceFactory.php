<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $user = User::factory();

        return [
            'user_id' => $user,
            'client_id' => Client::factory()->for($user),
            'public_id' => (string) Str::uuid(),
            'number' => 'INV-'.fake()->unique()->numerify('####'),
            'status' => InvoiceStatus::Draft->value,
            'currency' => 'EUR',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'tax_rate' => 0,
            'discount_rate' => 0,
            'subtotal_amount' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'amount_paid' => 0,
            'balance_due_amount' => 0,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $invoice = Invoice::factory();

        return [
            'user_id' => User::factory(),
            'invoice_id' => $invoice,
            'amount' => 5000,
            'paid_at' => now(),
            'method' => fake()->randomElement(['bank transfer', 'card']),
            'reference' => fake()->bothify('PAY-####'),
            'notes' => fake()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Payment $payment): void {
            if ($payment->invoice && $payment->user_id === null) {
                $payment->user_id = $payment->invoice->user_id;
            }
        })->afterCreating(function (Payment $payment): void {
            if ($payment->invoice && $payment->user_id !== $payment->invoice->user_id) {
                $payment->update(['user_id' => $payment->invoice->user_id]);
            }
        });
    }
}

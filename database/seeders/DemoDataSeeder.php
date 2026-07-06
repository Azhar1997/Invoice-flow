<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\InvoiceStatusManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function __construct(
        private readonly InvoiceStatusManager $statusManager,
    ) {
    }

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo Freelancer',
            'email' => 'demo@invoiceflow.test',
        ]);

        $clients = collect([
            [
                'name' => 'Mila Vermeer',
                'company_name' => 'Northline Studio',
                'email' => 'mila@northline.test',
                'billing_city' => 'Amsterdam',
                'billing_country' => 'Netherlands',
            ],
            [
                'name' => 'Jonas Patel',
                'company_name' => 'Harbor Health',
                'email' => 'jonas@harborhealth.test',
                'billing_city' => 'Rotterdam',
                'billing_country' => 'Netherlands',
            ],
            [
                'name' => 'Avery Brooks',
                'company_name' => 'Cascade Robotics',
                'email' => 'avery@cascade.test',
                'billing_city' => 'Utrecht',
                'billing_country' => 'Netherlands',
            ],
        ])->map(fn (array $attributes): Client => Client::factory()->for($user)->create($attributes));

        $this->createInvoice($user, $clients[0], [
            'number' => 'INV-0001',
            'issue_date' => now()->subDays(3)->toDateString(),
            'due_date' => now()->addDays(11)->toDateString(),
            'sent_at' => null,
            'tax_rate' => 21,
            'discount_rate' => 0,
            'notes' => 'Discovery invoice still in draft review.',
            'terms' => 'Payment due within 14 days.',
            'items' => [
                ['description' => 'Discovery workshop', 'quantity' => 1, 'unit_price_amount' => 45000],
                ['description' => 'UX audit summary', 'quantity' => 1, 'unit_price_amount' => 18000],
            ],
            'payments' => [],
        ]);

        $this->createInvoice($user, $clients[0], [
            'number' => 'INV-0002',
            'issue_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->addDays(4)->toDateString(),
            'sent_at' => now()->subDays(9),
            'tax_rate' => 21,
            'discount_rate' => 5,
            'notes' => 'Phase one delivery sent to client.',
            'terms' => 'Bank transfer preferred.',
            'items' => [
                ['description' => 'Landing page design', 'quantity' => 1, 'unit_price_amount' => 120000],
                ['description' => 'Design QA pass', 'quantity' => 2, 'unit_price_amount' => 15000],
            ],
            'payments' => [],
        ]);

        $this->createInvoice($user, $clients[1], [
            'number' => 'INV-0003',
            'issue_date' => now()->subDays(35)->toDateString(),
            'due_date' => now()->subDays(7)->toDateString(),
            'sent_at' => now()->subDays(34),
            'tax_rate' => 9,
            'discount_rate' => 0,
            'notes' => 'Retainer renewal is overdue.',
            'terms' => 'Late fees apply after 7 days.',
            'items' => [
                ['description' => 'Monthly analytics retainer', 'quantity' => 1, 'unit_price_amount' => 85000],
            ],
            'payments' => [],
        ]);

        $this->createInvoice($user, $clients[2], [
            'number' => 'INV-0004',
            'issue_date' => now()->subDays(21)->toDateString(),
            'due_date' => now()->subDays(6)->toDateString(),
            'sent_at' => now()->subDays(20),
            'tax_rate' => 21,
            'discount_rate' => 0,
            'notes' => 'Implementation sprint fully settled.',
            'terms' => 'Paid via bank transfer.',
            'items' => [
                ['description' => 'Frontend implementation sprint', 'quantity' => 3, 'unit_price_amount' => 65000],
                ['description' => 'Launch support', 'quantity' => 1, 'unit_price_amount' => 25000],
            ],
            'payments' => [
                [
                    'amount' => 150000,
                    'paid_at' => now()->subDays(10),
                    'method' => 'bank transfer',
                    'reference' => 'TRX-2048',
                    'notes' => 'First installment',
                ],
                [
                    'amount' => 116200,
                    'paid_at' => now()->subDays(5),
                    'method' => 'bank transfer',
                    'reference' => 'TRX-2056',
                    'notes' => 'Final settlement',
                ],
            ],
        ]);
    }

    /**
     * @param  array{
     *   number: string,
     *   issue_date: string,
     *   due_date: string,
     *   sent_at: mixed,
     *   tax_rate: int|float,
     *   discount_rate: int|float,
     *   notes: string,
     *   terms: string,
     *   items: array<int, array{description: string, quantity: int|float, unit_price_amount: int}>,
     *   payments: array<int, array{amount: int, paid_at: mixed, method: string, reference: string, notes: string}>
     * } $definition
     */
    private function createInvoice(User $user, Client $client, array $definition): Invoice
    {
        $subtotalAmount = collect($definition['items'])
            ->sum(fn (array $item): int => (int) round($item['quantity'] * $item['unit_price_amount']));
        $discountAmount = (int) round($subtotalAmount * ($definition['discount_rate'] / 100));
        $taxableAmount = max($subtotalAmount - $discountAmount, 0);
        $taxAmount = (int) round($taxableAmount * ($definition['tax_rate'] / 100));
        $totalAmount = $taxableAmount + $taxAmount;
        $amountPaid = collect($definition['payments'])->sum('amount');

        $invoice = Invoice::factory()
            ->for($user)
            ->for($client)
            ->create([
                'public_id' => (string) Str::uuid(),
                'number' => $definition['number'],
                'issue_date' => $definition['issue_date'],
                'due_date' => $definition['due_date'],
                'sent_at' => $definition['sent_at'],
                'tax_rate' => $definition['tax_rate'],
                'discount_rate' => $definition['discount_rate'],
                'subtotal_amount' => $subtotalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'balance_due_amount' => max($totalAmount - $amountPaid, 0),
                'notes' => $definition['notes'],
                'terms' => $definition['terms'],
            ]);

        collect($definition['items'])
            ->values()
            ->each(function (array $item, int $index) use ($invoice): void {
                $invoice->items()->create([
                    'position' => $index + 1,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price_amount' => $item['unit_price_amount'],
                    'line_total_amount' => (int) round($item['quantity'] * $item['unit_price_amount']),
                ]);
            });

        collect($definition['payments'])
            ->each(function (array $payment) use ($invoice, $user): void {
                Payment::factory()->for($user)->for($invoice)->create($payment);
            });

        return $this->statusManager->sync($invoice->fresh(['items', 'payments']));
    }
}

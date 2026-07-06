<?php

namespace App\Services;

use App\Models\Invoice;
use App\Support\Money;
use Illuminate\Support\Collection;

class InvoiceCalculator
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{items: array<int, array<string, mixed>>, subtotal_amount: int, discount_amount: int, tax_amount: int, total_amount: int, balance_due_amount: int}
     */
    public function calculate(array $items, float $taxRate, float $discountRate, int $amountPaid = 0): array
    {
        $normalizedItems = Collection::make($items)
            ->filter(fn (array $item): bool => filled($item['description'] ?? null))
            ->values()
            ->map(function (array $item, int $index): array {
                $quantity = round((float) ($item['quantity'] ?? 1), 2);
                $unitPriceAmount = Money::toMinor($item['unit_price'] ?? 0);
                $lineTotalAmount = (int) round($quantity * $unitPriceAmount);

                return [
                    'position' => $index + 1,
                    'description' => trim((string) $item['description']),
                    'quantity' => number_format($quantity, 2, '.', ''),
                    'unit_price_amount' => $unitPriceAmount,
                    'line_total_amount' => $lineTotalAmount,
                ];
            })
            ->all();

        $subtotalAmount = (int) array_sum(array_column($normalizedItems, 'line_total_amount'));
        $discountAmount = (int) round($subtotalAmount * ($discountRate / 100));
        $taxableAmount = max($subtotalAmount - $discountAmount, 0);
        $taxAmount = (int) round($taxableAmount * ($taxRate / 100));
        $totalAmount = $taxableAmount + $taxAmount;

        return [
            'items' => $normalizedItems,
            'subtotal_amount' => $subtotalAmount,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'balance_due_amount' => max($totalAmount - $amountPaid, 0),
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, subtotal_amount: int, discount_amount: int, tax_amount: int, total_amount: int, balance_due_amount: int}
     */
    public function calculateForInvoice(Invoice $invoice, ?int $amountPaid = null): array
    {
        return $this->calculate(
            $invoice->items
                ->map(fn ($item): array => [
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => Money::fromMinor($item->unit_price_amount),
                ])
                ->all(),
            (float) $invoice->tax_rate,
            (float) $invoice->discount_rate,
            $amountPaid ?? $invoice->amount_paid,
        );
    }
}

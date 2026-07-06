<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'public_id' => $this->public_id,
            'number' => $this->number,
            'status' => $this->status->value,
            'currency' => $this->currency,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'sent_at' => $this->sent_at,
            'paid_at' => $this->paid_at,
            'viewed_at' => $this->viewed_at,
            'tax_rate' => $this->tax_rate,
            'discount_rate' => $this->discount_rate,
            'subtotal_amount' => $this->subtotal_amount,
            'subtotal' => Money::fromMinor($this->subtotal_amount),
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'total' => Money::fromMinor($this->total_amount),
            'amount_paid' => $this->amount_paid,
            'amount_paid_display' => Money::fromMinor($this->amount_paid),
            'balance_due_amount' => $this->balance_due_amount,
            'balance_due' => Money::fromMinor($this->balance_due_amount),
            'notes' => $this->notes,
            'terms' => $this->terms,
            'client' => ClientResource::make($this->whenLoaded('client')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'public_url' => route('public.invoices.show', $this->resource),
        ];
    }
}

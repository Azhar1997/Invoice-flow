<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => Money::fromMinor($this->amount),
            'amount_minor' => $this->amount,
            'paid_at' => $this->paid_at,
            'method' => $this->method,
            'reference' => $this->reference,
            'notes' => $this->notes,
        ];
    }
}

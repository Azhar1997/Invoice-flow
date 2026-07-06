<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price_amount' => $this->unit_price_amount,
            'unit_price' => Money::fromMinor($this->unit_price_amount),
            'line_total_amount' => $this->line_total_amount,
            'line_total' => Money::fromMinor($this->line_total_amount),
        ];
    }
}

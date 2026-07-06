<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class InvoiceStatusManager
{
    public function sync(Invoice $invoice, bool $save = true): Invoice
    {
        $status = InvoiceStatus::Draft;
        $paidAt = null;

        if ($invoice->total_amount > 0 && $invoice->amount_paid >= $invoice->total_amount) {
            $status = InvoiceStatus::Paid;
            $paidAt = $invoice->paid_at ?? now();
        } elseif ($invoice->sent_at !== null) {
            $status = $invoice->due_date->isPast() && ! $invoice->due_date->isToday()
                ? InvoiceStatus::Overdue
                : InvoiceStatus::Sent;
        }

        $invoice->status = $status;
        $invoice->paid_at = $paidAt;

        if ($save) {
            $invoice->save();
        }

        return $invoice;
    }
}
